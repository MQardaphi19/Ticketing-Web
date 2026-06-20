<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChatbotLog;
use App\Models\KnowledgeBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KnowledgeBaseController extends Controller
{
    public function __construct()
    {
        // Second layer of authorization - middleware already checked in routes
        $this->middleware('permission:view knowledge base')->only(['index']);
        $this->middleware('permission:create knowledge base')->only(['store']);
        $this->middleware('permission:edit knowledge base')->only(['update']);
        $this->middleware('permission:delete knowledge base')->only(['destroy']);
        $this->middleware('permission:train')->only(['exportDataset', 'trainModel']);
    }

    public function index(): \Illuminate\View\View
    {
        $totalData = KnowledgeBase::count();
        $totalCategories = Category::count();
        $lastKnowledge = KnowledgeBase::latest()->first();
        $lastTrained = $lastKnowledge ? $lastKnowledge->created_at->format('d M Y, H:i') : '-';

        $totalQueries = ChatbotLog::count();
        $correctPredictions = ChatbotLog::where('is_correct', true)->count();
        $modelAccuracy = $totalQueries > 0 ? round(($correctPredictions / $totalQueries) * 100) : 0;

        $categories = Category::all();
        $knowledgeBase = KnowledgeBase::with('category')->get();

        return view('knowledge.index', compact('totalData', 'totalCategories', 'lastTrained', 'modelAccuracy', 'categories', 'knowledgeBase'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'original_text' => 'required|string',
        ]);

        KnowledgeBase::create($validated);

        return response()->json(['message' => 'Data latih berhasil ditambahkan']);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $knowledge = KnowledgeBase::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'original_text' => 'required|string',
        ]);

        $knowledge->update($validated);

        return response()->json(['message' => 'Data latih berhasil diperbarui']);
    }

    public function destroy(int $id): JsonResponse
    {
        $knowledge = KnowledgeBase::findOrFail($id);

        $knowledge->delete();

        return response()->json(['message' => 'Data latih berhasil dihapus']);
    }

    public function exportDataset(): StreamedResponse
    {
        $knowledgeBase = KnowledgeBase::with('category')->orderBy('id')->get();
        $rows = [
            ['original_text', 'category_name'],
        ];

        foreach ($knowledgeBase as $kb) {
            $rows[] = [
                $kb->original_text,
                $kb->category?->name ?? 'Unknown',
            ];
        }

        $xlsx = $this->buildXlsx($rows);
        $filename = 'knowledge_base_dataset_'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($xlsx): void {
            echo $xlsx;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    /**
     * @param  array<int, array<int, string|null>>  $rows
     */
    private function buildXlsx(array $rows): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'knowledge_dataset_');
        $zip = new \ZipArchive();

        if ($tempPath === false || $zip->open($tempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file Excel.');
        }

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelationshipsXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelationshipsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxWorksheetXml($rows));
        $zip->close();

        $content = file_get_contents($tempPath);
        @unlink($tempPath);

        if ($content === false) {
            abort(500, 'Gagal membaca file Excel.');
        }

        return $content;
    }

    private function xlsxWorksheetXml(array $rows): string
    {
        $sheetRows = '';

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $cells = '';

            foreach ($row as $columnIndex => $value) {
                $cell = $this->excelColumnName($columnIndex + 1).$excelRow;
                $cells .= '<c r="'.$cell.'" t="inlineStr"><is><t xml:space="preserve">'
                    .$this->escapeXml((string) $value)
                    .'</t></is></c>';
            }

            $sheetRows .= '<row r="'.$excelRow.'">'.$cells.'</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheetData>'.$sheetRows.'</sheetData>'
            .'</worksheet>';
    }

    private function excelColumnName(int $number): string
    {
        $name = '';

        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function escapeXml(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? '';

        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    private function xlsxContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'</Types>';
    }

    private function xlsxRootRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function xlsxWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Dataset" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private function xlsxWorkbookRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'</Relationships>';
    }

    public function trainModel(): JsonResponse
    {
        $knowledgeBase = KnowledgeBase::with('category')->get();

        if ($knowledgeBase->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data latih. Silakan tambah data latih terlebih dahulu.',
            ], 422);
        }

        $csvContent = "original_text,category_name\n";
        foreach ($knowledgeBase as $kb) {
            $text = str_replace('"', '""', $kb->original_text);
            $category = $kb->category ? $kb->category->name : 'Unknown';
            $csvContent .= "\"{$text}\",\"{$category}\"\n";
        }

        $csvPath = storage_path('training_data.csv');
        file_put_contents($csvPath, $csvContent);

        $pythonApiUrl = rtrim(config('python-api.url'), '/') . config('python-api.train_endpoint');
        $timeout = config('python-api.timeout', 300);

        try {
            $response = Http::timeout($timeout)
                ->attach('file', file_get_contents($csvPath), 'training_data.csv')
                ->post($pythonApiUrl);

            unlink($csvPath);

            if ($response->successful()) {
                $result = $response->json();
                $accuracy = $result['accuracy'] ?? 0;
                $message = $result['message'] ?? "Model berhasil dilatih dengan akurasi {$accuracy}%";

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'accuracy' => $accuracy,
                    'data_count' => $knowledgeBase->count(),
                    'details' => $result['details'] ?? null,
                ]);
            } else {
                Log::error('Python API training failed: '.$response->body());

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal melatih model. Server Python tidak merespons dengan benar.',
                ], 500);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Python API connection failed: '.$e->getMessage());

            if (file_exists($csvPath)) {
                unlink($csvPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke server Python. Pastikan server Python sedang berjalan di '.config('python-api.url'),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Training error: '.$e->getMessage());

            if (file_exists($csvPath)) {
                unlink($csvPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melatih model: '.$e->getMessage(),
            ], 500);
        }
    }

    public function predict(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $pythonApiUrl = rtrim(config('python-api.url'), '/') . config('python-api.predict_endpoint');

        try {
            $response = Http::timeout(30)->post($pythonApiUrl, [
                'text' => $request->text,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal melakukan prediksi',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }
}
