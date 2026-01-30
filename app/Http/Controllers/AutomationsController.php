<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Models\Enums\AutomationTrigger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AutomationsController extends Controller
{
    /**
     * Get all automations for the authenticated shop.
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

        $automations = Automation::where('user_id', $shop->id)
            ->with('steps')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $automations,
        ]);
    }

    /**
     * Store a new automation.
     */
    public function store(Request $request): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger' => ['required', Rule::enum(AutomationTrigger::class)],
            'is_active' => 'boolean',
        ]);

        $automation = Automation::create([
            'user_id' => $shop->id,
            'name' => $validated['name'],
            'trigger' => $validated['trigger'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $automation->load('steps');

        return response()->json([
            'success' => true,
            'data' => $automation,
            'message' => 'Automation created successfully.',
        ], 201);
    }

    /**
     * Update an automation (mainly for toggling is_active).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $automation = Automation::where('user_id', $shop->id)
            ->where('id', $id)
            ->first();

        if (! $automation) {
            return response()->json([
                'success' => false,
                'message' => 'Automation not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'trigger' => ['sometimes', Rule::enum(AutomationTrigger::class)],
            'is_active' => 'sometimes|boolean',
        ]);

        $automation->update($validated);
        $automation->load('steps');

        return response()->json([
            'success' => true,
            'data' => $automation,
            'message' => 'Automation updated successfully.',
        ]);
    }

    /**
     * Delete an automation.
     */
    public function destroy(int $id): JsonResponse
    {
        $shop = Auth::user();

        if (! $shop) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $automation = Automation::where('user_id', $shop->id)
            ->where('id', $id)
            ->first();

        if (! $automation) {
            return response()->json([
                'success' => false,
                'message' => 'Automation not found.',
            ], 404);
        }

        $automation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Automation deleted successfully.',
        ]);
    }
}
