<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;

class ComprehensiveServicesSeederPart4 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Comprehensive Services Seeder Part 4...');

        // Get all branches
        $branches = Branch::where('status', 'active')->get();
        if ($branches->isEmpty()) {
            $this->command->error('No active branches found. Please run BranchesSeeder first.');
            return;
        }

        // Get all service subcategories (child categories with type 'service')
        $subcategories = Category::where('type', 'service')
            ->whereNotNull('parent_id')
            ->get();

        if ($subcategories->isEmpty()) {
            $this->command->error('No service subcategories found. Please run ServiceCategoriesSeeder first.');
            return;
        }

        $totalServicesCreated = 0;

        // Define services for each subcategory
        $servicesByCategory = $this->getServicesData();

        foreach ($subcategories as $subcategory) {
            $categoryName = $subcategory->name;
            
            if (!isset($servicesByCategory[$categoryName])) {
                continue; // Skip categories not in this part
            }

            $services = $servicesByCategory[$categoryName];
            $this->command->info("Creating services for: {$categoryName}");

            foreach ($services as $serviceData) {
                // Randomly assign to a branch
                $branch = $branches->random();
                
                Service::create([
                    'branch_id' => $branch->id,
                    'category_id' => $subcategory->id,
                    'name' => $serviceData['name'],
                    'price' => $serviceData['price'],
                    'duration' => $serviceData['duration'],
                    'description' => $serviceData['description'],
                    'image' => $serviceData['image'],
                    'rating' => $serviceData['rating'],
                    'is_available' => true,
                    'featured' => $serviceData['featured'] ?? false,
                    'home_service' => $serviceData['home_service'] ?? false,
                ]);
                
                $totalServicesCreated++;
            }
        }

        $this->command->info("Service seeding Part 4 completed successfully!");
        $this->command->info("Total services created in Part 4: {$totalServicesCreated}");
    }

    /**
     * Get comprehensive services data for beauty and salon categories
     */
    private function getServicesData(): array
    {
        return [
            // Makeup Services - Bridal makeup
            'Bridal makeup' => [
                [
                    'name' => 'Complete Bridal Makeup Package',
                    'description' => 'Comprehensive bridal makeup service that creates a flawless, long-lasting look for your special day, including trial session, wedding day application, and touch-up kit. This premium service begins with a detailed consultation to understand your vision, skin type, and wedding theme. You\'ll receive a complete trial session 2-4 weeks before your wedding to perfect the look and make any adjustments. Our certified bridal makeup artists use professional, high-quality products that photograph beautifully and last throughout your entire celebration. The service includes false lashes, contouring, highlighting, and airbrush foundation for a flawless finish. We provide a touch-up kit with lipstick and powder for the reception, plus detailed photos of your final look for future reference. Perfect for brides who want to look and feel absolutely stunning on their wedding day with makeup that complements their dress, venue, and personal style.',
                    'price' => 250.00,
                    'duration' => 120,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => true,
                ],
                [
                    'name' => 'Bridal Party Makeup Services',
                    'description' => 'Coordinated makeup services for the entire bridal party, creating cohesive looks that complement the bride while allowing each person\'s individual beauty to shine. This comprehensive service includes makeup for bridesmaids, mother of the bride, flower girls, and any other special members of the wedding party. Our team of professional makeup artists works efficiently to ensure everyone is ready on time while maintaining the highest quality standards. Each person receives a personalized consultation to determine the most flattering look that coordinates with the overall wedding aesthetic. We use long-wearing, photo-friendly products that look beautiful in person and in photographs. The service includes false lashes for those who want them, and we provide touch-up products for key members of the bridal party. Perfect for creating a cohesive, polished look for the entire wedding party while ensuring everyone feels confident and beautiful.',
                    'price' => 85.00,
                    'duration' => 45,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Destination Wedding Makeup',
                    'description' => 'Specialized makeup service designed for destination weddings, taking into account climate, humidity, and travel considerations to ensure your makeup looks perfect regardless of location. This service includes detailed consultation about your destination\'s climate and venue to select appropriate products and techniques. We use waterproof, humidity-resistant, and long-wearing formulas that can withstand beach ceremonies, tropical climates, or mountain settings. The service includes a trial session using the exact products that will be used on your wedding day, plus detailed instructions for touch-ups and maintenance. We provide a comprehensive emergency kit with all necessary products for the duration of your trip. Our destination wedding specialists understand the unique challenges of different climates and venues, ensuring your makeup looks flawless from ceremony to reception regardless of weather conditions. Perfect for brides planning outdoor ceremonies, beach weddings, or celebrations in challenging climates.',
                    'price' => 300.00,
                    'duration' => 150,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Vintage and Themed Bridal Makeup',
                    'description' => 'Specialized makeup service for themed weddings, creating authentic vintage looks or specific era-inspired makeup that perfectly complements your unique wedding theme. This creative service includes extensive research and consultation to ensure historical accuracy and authenticity for your chosen theme. Whether you\'re planning a 1920s Great Gatsby wedding, 1950s vintage celebration, or bohemian themed ceremony, our specialized artists have the expertise to create the perfect look. We use period-appropriate techniques and color palettes while ensuring the makeup photographs beautifully with modern cameras. The service includes a detailed trial session with multiple look options, historical context and styling tips, and coordination with your hair stylist for a cohesive vintage appearance. Perfect for couples planning themed weddings, vintage enthusiasts, or brides who want a unique, memorable look that tells a story and creates stunning photographs.',
                    'price' => 200.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Bridal Makeup Lessons and DIY Guidance',
                    'description' => 'Comprehensive makeup education service for brides who prefer to do their own wedding makeup, providing professional techniques, product recommendations, and hands-on training for a flawless DIY bridal look. This educational service includes detailed consultation about your skin type, face shape, and desired look, followed by step-by-step instruction in professional makeup techniques. You\'ll learn contouring, highlighting, eye makeup application, and long-wearing techniques that ensure your makeup lasts throughout your wedding day. Our professional makeup artists provide personalized product recommendations within your budget, application tips for photography, and troubleshooting guidance for common issues. The service includes practice sessions, detailed written instructions with photos, and a final trial run before your wedding. Perfect for budget-conscious brides, makeup enthusiasts who enjoy doing their own makeup, or brides who want the confidence and skills to create their own beautiful wedding look.',
                    'price' => 150.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
            ],

            // Makeup Services - Event makeup
            'Event makeup' => [
                [
                    'name' => 'Red Carpet Glamour Makeup',
                    'description' => 'Professional glamour makeup service designed for special events, galas, and red carpet occasions where you want to look absolutely stunning and camera-ready. This high-end service creates dramatic, sophisticated looks using professional techniques and premium products that photograph beautifully under any lighting. You\'ll receive a detailed consultation to understand the event, your outfit, and desired level of drama, followed by expert application that enhances your natural features while creating that coveted red carpet glow. Our celebrity makeup artists use contouring, highlighting, and color theory to create dimension and drama that looks flawless in person and in photographs. The service includes false lashes, precise lip application, and setting techniques that ensure your makeup lasts throughout the entire event. Perfect for galas, award ceremonies, milestone celebrations, or any special occasion where you want to feel like a celebrity and make a memorable impression.',
                    'price' => 120.00,
                    'duration' => 75,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => true,
                ],
                [
                    'name' => 'Corporate Event and Professional Makeup',
                    'description' => 'Polished, professional makeup service designed for business events, conferences, presentations, and corporate photography where you need to look confident and authoritative while maintaining a professional appearance. This service creates sophisticated looks that enhance your natural features without being distracting or overly dramatic. You\'ll receive makeup that photographs well under office lighting and video conferencing, with attention to color choices that complement business attire and professional settings. Our corporate makeup specialists understand the balance between looking polished and maintaining credibility in professional environments. The service includes techniques for looking refreshed and energetic, even during long conference days, plus guidance on touch-up products for maintaining your look throughout extended events. Perfect for executives, speakers, professionals attending important meetings, or anyone who wants to project confidence and competence through their appearance.',
                    'price' => 75.00,
                    'duration' => 45,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Prom and Formal Dance Makeup',
                    'description' => 'Age-appropriate, stunning makeup service designed specifically for prom, homecoming, and other formal dance events, creating looks that are sophisticated yet suitable for young women. This service focuses on enhancing natural beauty while creating the glamour and excitement appropriate for these milestone events. You\'ll receive a consultation that considers your dress color, personal style, and comfort level with makeup, followed by expert application that creates a polished, age-appropriate look. Our specialists understand current trends while ensuring the makeup is suitable for photography and dancing throughout the evening. The service includes false lashes if desired, long-wearing formulas that withstand dancing and celebration, and touch-up guidance for maintaining the look throughout the event. Perfect for high school students attending formal dances, young women wanting to feel confident and beautiful at special events, or parents seeking professional, appropriate makeup for their daughters.',
                    'price' => 65.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Holiday and Seasonal Event Makeup',
                    'description' => 'Festive, themed makeup service for holiday parties, seasonal celebrations, and themed events that incorporates seasonal colors and trends while maintaining elegance and sophistication. This creative service adapts current makeup trends to complement holiday themes, whether you\'re attending a Christmas party, New Year\'s Eve celebration, Halloween event, or seasonal gathering. You\'ll receive makeup that incorporates appropriate seasonal elements like metallic accents for New Year\'s, warm tones for autumn events, or festive colors for holiday celebrations. Our seasonal makeup specialists stay current with trends while ensuring the look is appropriate for the specific event and your personal style. The service includes guidance on coordinating makeup with seasonal outfits and accessories, plus recommendations for maintaining the look throughout evening celebrations. Perfect for holiday party-goers, those attending themed events, or anyone wanting to embrace seasonal beauty trends while looking sophisticated and festive.',
                    'price' => 80.00,
                    'duration' => 50,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
                [
                    'name' => 'Photography and Photoshoot Makeup',
                    'description' => 'Specialized makeup service designed specifically for photography sessions, headshots, and professional photoshoots, using techniques and products that ensure you look flawless under camera lights and in high-resolution images. This technical service requires understanding of how makeup translates to photography, including color theory, contouring for cameras, and products that don\'t create flashback or unwanted shine. You\'ll receive makeup that enhances your features for the camera while looking natural and polished in the final images. Our photography makeup specialists work with lighting conditions, understand different camera requirements, and use professional products specifically chosen for photographic work. The service includes consultation about the type of photography, intended use of images, and any specific requirements from the photographer. Perfect for professional headshots, modeling portfolios, family portraits, or any situation where high-quality photographs are the primary goal.',
                    'price' => 95.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
            ],
        ];
    }
}
