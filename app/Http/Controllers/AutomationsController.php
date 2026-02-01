<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Models\Enums\AutomationTrigger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\AutomationStep;

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

        // save steps
        $this->saveSteps($automation, $request->input('steps'));

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
        
        // save steps
        $this->saveSteps($automation, $request->input('steps'));

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

    public function show(int $id)
    {
        $shop = Auth::user();

        if (! $shop) {
            return null;
        }

        return response()->json([
            'success' => true,
            'data' => Automation::where('user_id', $shop->id)
            ->where('id', $id)
            ->with('steps')
            ->first()
        ]);
    }

    public function toggle(int $id)
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

        $automation->is_active = request()->input('is_active');
        $automation->save();

        return response()->json([
            'success' => true
        ]);
    }

    private function saveSteps(Automation $automation, $steps)
    {
        $order = 1;
        $automationStepsIds = [];
        foreach ($steps as $step) {
            if (isset($step['id'])) {
                $automationStepsIds[] = $step['id'];
                AutomationStep::updateOrCreate(
                    ['id' => $step['id']],
                    [
                        'automation_id' => $automation->id,
                        'type' => $step['type'],
                        'config' => $step['config'],
                        'wait_time' => $step['wait_time'],
                        'step_order' => $order++,
                    ]
                );
            } else {
                $automationStep = AutomationStep::create([
                    'automation_id' => $automation->id,
                    'type' => $step['type'],
                    'config' => $step['config'],
                    'wait_time' => $step['wait_time'],
                    'step_order' => $order++,
                ]);
                $automationStepsIds[] = $automationStep->id;
            }
            // delete steps that are not in the request
            AutomationStep::where('automation_id', $automation->id)
                ->whereNotIn('id', $automationStepsIds)
                ->delete();
        }
    }
}
