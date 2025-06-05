<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Service;
use App\Helpers\UnsplashImageHelper;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get customer users
        $customers = User::where('role', 'customer')->get();
        
        // If no customers, skip seeding
        if ($customers->isEmpty()) {
            $this->command->info('No customers found. Skipping review seeding.');
            return;
        }
        
        // Get all products
        $products = Product::all();
        
        // If no products, skip product reviews
        if ($products->isEmpty()) {
            $this->command->info('No products found. Skipping product review seeding.');
        } else {
            $this->seedProductReviews($products, $customers);
        }
        
        // Get all services
        $services = Service::all();
        
        // If no services, skip service reviews
        if ($services->isEmpty()) {
            $this->command->info('No services found. Skipping service review seeding.');
        } else {
            $this->seedServiceReviews($services, $customers);
        }
    }
    
    /**
     * Seed product reviews.
     *
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @param \Illuminate\Database\Eloquent\Collection $customers
     * @return void
     */
    private function seedProductReviews($products, $customers)
    {
        foreach ($products as $product) {
            // Create 1-3 reviews per product
            $reviewCount = rand(1, 3);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                // Get a random customer
                $customer = $customers->random();
                
                // Generate 0-3 random images
                $images = $this->generateRandomImages('product review', $product->name, rand(0, 3));
                
                // Create the review
                Review::create([
                    'user_id' => $customer->id,
                    'reviewable_id' => $product->id,
                    'reviewable_type' => Product::class,
                    'rating' => $this->getRandomRating(),
                    'comment' => $this->getRandomComment($product->name, true),
                    'images' => $images,
                    'likes' => rand(0, 50),
                    'is_verified_purchase' => (bool)rand(0, 1),
                ]);
            }
        }
    }
    
    /**
     * Seed service reviews.
     *
     * @param \Illuminate\Database\Eloquent\Collection $services
     * @param \Illuminate\Database\Eloquent\Collection $customers
     * @return void
     */
    private function seedServiceReviews($services, $customers)
    {
        foreach ($services as $service) {
            // Create 1-3 reviews per service
            $reviewCount = rand(1, 3);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                // Get a random customer
                $customer = $customers->random();
                
                // Generate 0-3 random images
                $images = $this->generateRandomImages('service review', $service->name, rand(0, 3));
                
                // Create the review
                Review::create([
                    'user_id' => $customer->id,
                    'reviewable_id' => $service->id,
                    'reviewable_type' => Service::class,
                    'rating' => $this->getRandomRating(),
                    'comment' => $this->getRandomComment($service->name, false),
                    'likes' => rand(0, 50),
                    'is_verified_purchase' => (bool)rand(0, 1),
                ]);
            }
        }
    }
    
    /**
     * Generate random images using Unsplash.
     *
     * @param string $baseSearchTerm
     * @param string $itemName
     * @param int $count
     * @return array
     */
    private function generateRandomImages($baseSearchTerm, $itemName, $count)
    {
        $images = [];
        
        for ($i = 0; $i < $count; $i++) {
            $searchTerm = $baseSearchTerm . ' ' . $itemName;
            $imagePath = UnsplashImageHelper::downloadAndSaveImage(
                $searchTerm, 
                'public/images/reviews/' . strtolower(str_replace(' ', '-', $itemName)) . '-' . ($i + 1) . '.jpg',
                800,
                600
            );
            $images[] = $imagePath;
        }
        
        return $images;
    }
    
    /**
     * Get a random rating between 3.0 and 5.0.
     *
     * @return float
     */
    private function getRandomRating()
    {
        // Generate ratings between 3.0 and 5.0 with 0.5 increments
        $ratings = [3.0, 3.5, 4.0, 4.5, 5.0];
        return $ratings[array_rand($ratings)];
    }
    
    /**
     * Get a random comment for a product or service.
     *
     * @param string $itemName
     * @param bool $isProduct
     * @return string
     */
    private function getRandomComment($itemName, $isProduct)
    {
        $productComments = [
            "I absolutely love the {item}! It exceeded my expectations in every way.",
            "The {item} is great quality and arrived quickly. Very satisfied with my purchase.",
            "This {item} is exactly what I needed. The quality is excellent and it works perfectly.",
            "I've been using the {item} for a few weeks now and I'm very impressed with its performance.",
            "The {item} is well-designed and user-friendly. Definitely worth the money.",
            "I'm really happy with my purchase of the {item}. It's durable and works as advertised.",
            "The {item} has made my life so much easier. I highly recommend it to anyone.",
            "Great product! The {item} is high quality and the price is reasonable.",
            "I bought the {item} as a gift and they loved it! Will definitely shop here again.",
            "The {item} arrived in perfect condition and works flawlessly. Very pleased with my purchase."
        ];
        
        $serviceComments = [
            "The {item} service was excellent! The staff was professional and attentive.",
            "I had a great experience with the {item}. The service was prompt and high-quality.",
            "The {item} exceeded my expectations. I'll definitely be coming back for more sessions.",
            "I'm very satisfied with the {item} service. The results were exactly what I wanted.",
            "The {item} was worth every penny. The staff was knowledgeable and friendly.",
            "I highly recommend the {item} service. It was a relaxing and enjoyable experience.",
            "The {item} was fantastic! The technician was skilled and professional.",
            "I've tried many similar services, but the {item} here is by far the best.",
            "Great service! The {item} was exactly what I needed and the staff was wonderful.",
            "I'm extremely pleased with the {item} service. Will definitely be returning."
        ];
        
        $comments = $isProduct ? $productComments : $serviceComments;
        $comment = $comments[array_rand($comments)];
        
        return str_replace('{item}', $itemName, $comment);
    }
}
