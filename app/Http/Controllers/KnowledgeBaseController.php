<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChatbotLog;
use App\Models\KnowledgeBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KnowledgeBaseController extends Controller
{
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

    public function exportDataset(): JsonResponse
    {
        $knowledgeBase = KnowledgeBase::with('category')->get();

        $csv = "original_text,category_name\n";
        foreach ($knowledgeBase as $kb) {
            $csv .= "\"{$kb->original_text}\",\"{$kb->category->name}\"\n";
        }

        file_put_contents(storage_path('dataset.csv'), $csv);

        return response()->json(['message' => 'Dataset berhasil diexport ke dataset.csv']);
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