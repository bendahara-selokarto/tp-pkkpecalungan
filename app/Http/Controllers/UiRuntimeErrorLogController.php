<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UiRuntimeErrorLogController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'source' => ['required', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:500'],
        ]);

        Log::warning('ui.runtime_error', [
            'message' => (string) $validated['message'],
            'source' => (string) $validated['source'],
            'url' => (string) ($validated['url'] ?? ''),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        return response()->json(['ok' => true]);
    }
}
