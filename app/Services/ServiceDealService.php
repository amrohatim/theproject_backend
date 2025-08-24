<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Service;

class ServiceDealService
{
    /**
     * Get all active deals for a service.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Support\Collection
     */
    public function getActiveDealsForService(Service $service)
    {
        // Get current date
        $today = now()->format('Y-m-d');

        // Get the vendor (company) user ID for this service
        // Check if service has branch and branch has company
        if (!$service->branch || !$service->branch->company) {
            return collect(); // Return empty collection if no valid vendor
        }

        $vendorId = $service->branch->company->user_id;

        // Find active deals from this vendor
        $deals = Deal::where('user_id', $vendorId)
            ->where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        // Filter deals based on application scope
        $applicableDeals = $deals->filter(function ($deal) use ($service) {
            // Deal applies to all products and services
            if ($deal->applies_to === 'all') {
                return true;
            }

            // Deal applies to specific services
            if ($deal->applies_to === 'services') {
                $serviceIds = is_string($deal->service_ids)
                    ? json_decode($deal->service_ids, true)
                    : $deal->service_ids;

                return in_array($service->id, $serviceIds ?: []);
            }

            // Deal applies to both products and services
            if ($deal->applies_to === 'products_and_services') {
                $serviceIds = is_string($deal->service_ids)
                    ? json_decode($deal->service_ids, true)
                    : $deal->service_ids;

                return in_array($service->id, $serviceIds ?: []);
            }

            // Deal applies to specific categories (check if service category matches)
            if ($deal->applies_to === 'categories') {
                $categoryIds = is_string($deal->category_ids)
                    ? json_decode($deal->category_ids, true)
                    : $deal->category_ids;

                return in_array($service->category_id, $categoryIds ?: []);
            }

            return false;
        });

        return $applicableDeals;
    }

    /**
     * Get the best deal for a service.
     *
     * @param  \App\Models\Service  $service
     * @return \App\Models\Deal|null
     */
    public function getBestDealForService(Service $service)
    {
        $deals = $this->getActiveDealsForService($service);

        if ($deals->isEmpty()) {
            return null;
        }

        // Return the deal with the highest discount percentage
        return $deals->sortByDesc('discount_percentage')->first();
    }

    /**
     * Calculate the discounted price for a service.
     *
     * @param  \App\Models\Service  $service
     * @return array
     */
    public function calculateDiscountedPrice(Service $service)
    {
        $bestDeal = $this->getBestDealForService($service);

        // Use the service price as the base price
        $originalPrice = $service->price;
        $basePrice = $service->price; // Current price before applying deal discount

        if (!$bestDeal) {
            return [
                'original_price' => $originalPrice,
                'discounted_price' => $basePrice,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'has_discount' => false,
                'deal' => null,
            ];
        }

        // Apply deal discount to the base price
        $dealDiscountAmount = ($basePrice * $bestDeal->discount_percentage) / 100;
        $finalPrice = $basePrice - $dealDiscountAmount;

        // Calculate total discount percentage
        $totalDiscountAmount = $originalPrice - $finalPrice;
        $totalDiscountPercentage = ($totalDiscountAmount / $originalPrice) * 100;

        return [
            'original_price' => $originalPrice,
            'discounted_price' => $finalPrice,
            'discount_percentage' => round($totalDiscountPercentage, 2),
            'discount_amount' => $totalDiscountAmount,
            'has_discount' => true,
            'deal' => $bestDeal,
            'deal_discount_percentage' => $bestDeal->discount_percentage,
        ];
    }

    /**
     * Get all services with active deals.
     *
     * @param  int|null  $limit
     * @return \Illuminate\Support\Collection
     */
    public function getServicesWithActiveDeals($limit = null)
    {
        // Get current date
        $today = now()->format('Y-m-d');

        // Get all active deals that apply to services
        $activeDeals = Deal::where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('applies_to', 'services')
            ->get();

        // Get services with deals
        $servicesWithDeals = collect();

        foreach ($activeDeals as $deal) {
            $services = collect();

            // Deal applies to all products and services
            if ($deal->applies_to === 'all') {
                // Get all services from this vendor
                $services = Service::whereHas('branch.company', function ($query) use ($deal) {
                    $query->where('user_id', $deal->user_id);
                })->get();
            }

            // Deal applies to specific services
            elseif ($deal->applies_to === 'services') {
                $serviceIds = is_string($deal->service_ids)
                    ? json_decode($deal->service_ids, true)
                    : $deal->service_ids;

                if (!empty($serviceIds)) {
                    $services = Service::whereIn('id', $serviceIds)->get();
                }
            }



            // Add services to collection with deal information
            foreach ($services as $service) {
                // Use the calculateDiscountedPrice method to ensure consistent discount calculation
                $dealInfo = $this->calculateDiscountedPrice($service);

                // Apply the calculated discount information to the service
                $service->deal = $deal;
                $service->discount_percentage = $dealInfo['discount_percentage'];
                $service->discounted_price = $dealInfo['discounted_price'];
                $service->original_price = $dealInfo['original_price'];
                $service->has_discount = $dealInfo['has_discount'];
                $service->discount_amount = $dealInfo['discount_amount'];

                // Add additional discount information for debugging and UI display
                if (isset($dealInfo['deal_discount_percentage'])) {
                    $service->deal_discount_percentage = $dealInfo['deal_discount_percentage'];
                }

                $servicesWithDeals->push($service);
            }
        }

        // Remove duplicates and get the best deal for each service
        $uniqueServices = $servicesWithDeals->unique('id')->values();

        // Sort by discount percentage (highest first)
        $sortedServices = $uniqueServices->sortByDesc('discount_percentage')->values();

        // Limit results if specified
        if ($limit) {
            $sortedServices = $sortedServices->take($limit);
        }

        return $sortedServices;
    }
}
