<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\KnowledgeBase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeBaseExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_export_knowledge_base_dataset(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Jaringan',
            'slug' => 'jaringan',
            'description' => 'Masalah jaringan',
            'sla_hours' => 24,
        ]);

        KnowledgeBase::create([
            'original_text' => 'Internet lambat, "sering putus"',
            'category_id' => $category->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('knowledge.export-dataset'));

        $response
            ->assertOk();

        $contentType = $response->headers->get('content-type');
        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertIsString($contentType);
        $this->assertIsString($contentDisposition);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $contentType);
        $this->assertStringContainsString('attachment', $contentDisposition);
        $this->assertStringContainsString('.xlsx', $contentDisposition);

        $tempPath = tempnam(sys_get_temp_dir(), 'knowledge_export_test_');
        file_put_contents($tempPath, $response->streamedContent());

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($tempPath));

        $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($tempPath);

        $this->assertIsString($worksheet);
        $this->assertStringContainsString('original_text', $worksheet);
        $this->assertStringContainsString('category_name', $worksheet);
        $this->assertStringContainsString('Internet lambat, &quot;sering putus&quot;', $worksheet);
        $this->assertStringContainsString('Jaringan', $worksheet);
    }
}
