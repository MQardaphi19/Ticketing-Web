<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KnowledgeBaseSeeder extends Seeder
{
    /**
     * Seed the knowledge base table from the SQL dump in the project root.
     */
    public function run(): void
    {
        $sqlPath = base_path('knowledge_bases.sql');

        if (! file_exists($sqlPath)) {
            throw new RuntimeException("SQL dump not found: {$sqlPath}");
        }

        $sql = file_get_contents($sqlPath);

        if ($sql === false) {
            throw new RuntimeException("Unable to read SQL dump: {$sqlPath}");
        }

        preg_match_all('/INSERT\s+INTO\s+`knowledge_bases`\s*\([^)]+\)\s*VALUES\s*.*?;/is', $sql, $matches);

        if (empty($matches[0])) {
            throw new RuntimeException('No knowledge_bases insert statements found in SQL dump.');
        }

        DB::transaction(function () use ($matches): void {
            DB::table('knowledge_bases')->delete();

            foreach ($matches[0] as $insertStatement) {
                DB::unprepared($insertStatement);
            }
        });
    }
}
