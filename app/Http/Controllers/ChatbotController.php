<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatbotLog;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        $baseUrl = config('services.chatbot.api_url', 'http://localhost:8001');
        $this->apiUrl = rtrim($baseUrl, '/');

        // Log for debugging
        Log::info('ChatbotController: Using API URL: ' . $this->apiUrl);
    }

    /**
     * Display chatbot prediction logs
     */
    public function logs(Request $request): View
    {
        $logs = ChatbotLog::with(['user', 'predictedCategory'])
            ->latest()
            ->get();

        $totalQueries = $logs->count();
        $correctPredictions = $logs->where('is_correct', true)->count();
        $wrongPredictions = $logs->filter(function ($log) {
            return $log->is_correct === false;
        })->count();
        $chatbotAccuracy = $totalQueries > 0 ? round(($correctPredictions / $totalQueries) * 100) : 0;

        return view('chatbot.logs', compact('totalQueries', 'correctPredictions', 'wrongPredictions', 'chatbotAccuracy', 'logs'));
    }

    /**
     * Predict category from user query
     */
    public function predict(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:10'
        ]);

        $query = $request->input('query');
        $url = $this->apiUrl . '/predict';
        Log::info('Calling Python API', ['url' => $url, 'query' => $query]);

        try {
            $response = Http::timeout(30)
                ->asJson()
                ->post($url, [
                    'text' => $query,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Log prediction for accuracy tracking
                $categoryId = $data['category_id'] ?? null;

                // cek apakah category benar-benar ada
                $categoryExists = Category::where('id', $categoryId)->exists();

                // fallback ke kategori "lainnya"
                if (!$categoryExists) {
                    $fallbackCategory = Category::where('name', 'lainnya')->first();

                    $categoryId = $fallbackCategory ? $fallbackCategory->id : null;
                }

                ChatbotLog::create([
                    'user_id' => auth()->id(),
                    'user_query' => $query,
                    'predicted_category_id' => $categoryId,
                    'confidence_score' => $data['confidence_score']
                ]);

                return response()->json([
                    'success' => true,
                    'category_id' => $data['category_id'],
                    'category_name' => $data['category_name'],
                    'confidence_score' => $data['confidence_score']
                ]);
            } else {
                Log::error('Python API response not successful', ['status' => $response->status(), 'body' => $response->body()]);

                return response()->json([
                    'success' => false,
                    'message' => 'AI service returned error: ' . $response->status()
                ], 503);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Python API connection error', ['error' => $e->getMessage(), 'url' => $url]);

            return response()->json([
                'success' => false,
                'message' => 'Cannot connect to AI service'
            ], 503);
        } catch (\Exception $e) {
            Log::error('Python API unexpected error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage()
            ], 503);
        }
    }

    public function validatePrediction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'log_id' => 'required|exists:chatbot_logs,id',
            'is_correct' => 'required|boolean',
        ]);

        $log = ChatbotLog::findOrFail($validated['log_id']);
        $log->update(['is_correct' => $validated['is_correct']]);

        return response()->json(['message' => 'Validasi berhasil disimpan']);
    }
}
