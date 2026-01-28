<?php

namespace App\Http\Controllers;

use App\Models\ManualTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManualTemplateController extends Controller
{
    /**
     * List manual templates for the authenticated shop.
     */
    public function index(): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $templates = ManualTemplate::where('user_id', $shop->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * Create or update a manual template.
     */
    public function save(Request $request): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $id = $request->input('id');
        if ($id === -1) { 
            $id = null;
        }

        $template = ManualTemplate::updateOrCreate(
            [
                'id' => $validated['id'] ?? null,
                'user_id' => $shop->id,
            ],
            [
                'title' => request('title'),
                'message' => request('message'),
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $template,
        ]);
    }

    /**
     * Delete a manual template.
     */
    public function delete(Request $request): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:manual_templates,id'],
        ]);

        $template = ManualTemplate::where('user_id', $shop->id)
            ->where('id', $validated['id'])
            ->first();

        if (! $template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found.',
            ], 404);
        }

        $template->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}

