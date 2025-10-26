<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = $perPage > 0 ? min($perPage, 100) : 15;

        $query = CustomerNotification::query()
            ->with(['customer', 'booking', 'orderItem'])
            ->latest();

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('notification_type')) {
            $query->where('notification_type', $request->input('notification_type'));
        }

        return response()->json($query->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        $notification = CustomerNotification::create($data);

        return response()->json($notification->load(['customer', 'booking', 'orderItem']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $notification = CustomerNotification::with(['customer', 'booking', 'orderItem'])
            ->findOrFail($id);

        return response()->json($notification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $notification = CustomerNotification::findOrFail($id);

        $data = $this->validateData($request, true);

        $notification->fill($data);
        $notification->save();

        return response()->json($notification->load(['customer', 'booking', 'orderItem']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $notification = CustomerNotification::findOrFail($id);
        $notification->delete();

        return response()->json(null, 204);
    }

    private function validateData(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'notification_type' => [($isUpdate ? 'sometimes' : 'required'), Rule::in(CustomerNotification::TYPES)],
            'sender_name' => [($isUpdate ? 'sometimes' : 'required'), 'string', 'max:255'],
            'message' => [($isUpdate ? 'sometimes' : 'required'), 'string'],
            'status' => [($isUpdate ? 'sometimes' : 'required'), 'string', 'max:100'],
            'is_opened' => ['sometimes', 'boolean'],
            'order_item_id' => ['nullable', 'integer', 'exists:order_items,id'],
            'booking_id' => ['nullable', 'integer', 'exists:bookings,id'],
            'customer_id' => [($isUpdate ? 'sometimes' : 'required'), 'integer', 'exists:users,id'],
        ];

        return $request->validate($rules);
    }
}
