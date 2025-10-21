<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserLocation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'service_id',
        'branch_id',
        'booking_number',
        'booking_date',
        'booking_time',
        'duration',
        'price',
        'status',
        'payment_status',
        'payment_method',
        'notes',
        'is_home_service',
        'service_location',
        'customer_location',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'booking_date' => 'date',
        'price' => 'decimal:2',
        'is_home_service' => 'boolean',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service that the booking belongs to.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the branch that the booking belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Customer location snapshot accessor & mutator.
     */
    protected function customerLocation(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value) || $value === '') {
                    return null;
                }

                if ($value instanceof Fluent) {
                    return $value;
                }

                if (is_array($value)) {
                    $payload = $value;
                } elseif (is_string($value)) {
                    $payload = json_decode($value, true);
                } else {
                    $payload = null;
                }

                if (!is_array($payload)) {
                    return null;
                }

                $normalized = $this->normalizeCustomerLocationPayload($payload);

                if (empty($normalized)) {
                    return null;
                }

                return new Fluent($normalized);
            },
            set: function ($value) {
                if (is_null($value) || $value === '') {
                    return [
                        'customer_location' => null,
                    ];
                }

                if ($value instanceof Fluent) {
                    $value = $value->toArray();
                }

                if ($value instanceof UserLocation) {
                    $value = $this->snapshotFromUserLocation($value);
                } elseif (is_numeric($value)) {
                    $userLocation = UserLocation::find($value);
                    $value = $userLocation
                        ? $this->snapshotFromUserLocation($userLocation)
                        : null;
                } elseif (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $value = $decoded;
                    }
                } elseif (is_object($value)) {
                    $value = (array) $value;
                }

                if (is_array($value)) {
                    $normalized = $this->normalizeCustomerLocationPayload($value);

                    return [
                        'customer_location' => empty($normalized)
                            ? null
                            : json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION),
                    ];
                }

                return [
                    'customer_location' => null,
                ];
            }
        );
    }

    /**
     * Normalize a customer location payload to consistent structure.
     */
    protected function normalizeCustomerLocationPayload(array $payload): array
    {
        $latitude = Arr::get($payload, 'latitude');
        $longitude = Arr::get($payload, 'longitude');

        $normalized = [
            'latitude' => $this->castCoordinate($latitude),
            'longitude' => $this->castCoordinate($longitude),
            'address' => Arr::get($payload, 'address'),
            'name' => Arr::get($payload, 'name'),
            'emirate' => Arr::get($payload, 'emirate'),
            'user_location_id' => Arr::get($payload, 'user_location_id'),
            'snapshot_source' => Arr::get($payload, 'snapshot_source'),
        ];

        return array_filter(
            $normalized,
            static fn ($value) => !is_null($value) && $value !== ''
        );
    }

    /**
     * Build a snapshot array for the provided user location.
     */
    protected function snapshotFromUserLocation(UserLocation $location): array
    {
        return $this->normalizeCustomerLocationPayload([
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'address' => $location->address ?? null,
            'name' => $location->name ?? null,
            'emirate' => $location->emirate ?? null,
            'user_location_id' => $location->id,
            'snapshot_source' => 'user_location',
        ]);
    }

    /**
     * Ensure coordinates are stored as floats.
     */
    protected function castCoordinate($value): ?float
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }
}
