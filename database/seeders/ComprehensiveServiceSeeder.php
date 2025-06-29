<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use App\Helpers\UnsplashImageHelper;

class ComprehensiveServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all service subcategories (only child categories with parent_id)
        $subcategories = Category::where('type', 'service')
            ->whereNotNull('parent_id')
            ->get();

        // Get all branches
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->command->error('No branches found. Please run BranchesTableSeeder first.');
            return;
        }

        $this->command->info("Found {$subcategories->count()} service subcategories to populate.");
        $this->command->info("Found {$branches->count()} branches available.");

        // Define comprehensive service data for each subcategory
        $servicesByCategory = $this->getServicesByCategory();

        $totalServicesCreated = 0;

        foreach ($subcategories as $subcategory) {
            $categoryName = $subcategory->name;
            
            if (!isset($servicesByCategory[$categoryName])) {
                $this->command->warn("No services defined for subcategory: {$categoryName}");
                continue;
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

        $this->command->info("Service seeding completed successfully! Created {$totalServicesCreated} services.");
    }

    /**
     * Get comprehensive service data organized by category name
     */
    private function getServicesByCategory(): array
    {
        return [
            // Pottery making services
            'Pottery making' => [
                [
                    'name' => 'Beginner Pottery Workshop',
                    'price' => 45.00,
                    'duration' => 120,
                    'description' => 'Perfect for beginners, this hands-on workshop introduces you to the fundamentals of pottery making. Learn basic techniques including centering clay, pulling walls, and shaping on the pottery wheel. Our experienced instructors will guide you through creating your first ceramic piece, from initial shaping to trimming and finishing. The session includes all materials, tools, and firing of your finished piece. You\'ll discover the therapeutic benefits of working with clay while developing a new creative skill. Class size is limited to ensure personalized attention. Your finished pottery will be ready for pickup within two weeks after glazing and final firing.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('pottery wheel beginner class', 800, 600),
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Advanced Pottery Techniques',
                    'price' => 75.00,
                    'duration' => 180,
                    'description' => 'Designed for intermediate to advanced pottery enthusiasts, this comprehensive workshop focuses on sophisticated techniques and artistic expression. Learn advanced wheel throwing methods, complex glazing techniques, and decorative approaches including sgraffito and slip trailing. Explore different clay bodies and their properties, master trimming and finishing techniques, and develop your personal artistic style. The session covers troubleshooting common pottery problems and achieving consistent results. Students will create multiple pieces using various techniques, with emphasis on developing muscle memory and artistic vision. All materials, tools, and multiple firings are included. Perfect for those looking to refine their skills and push creative boundaries.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('advanced pottery techniques ceramic art', 800, 600),
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Hand-Building Pottery Class',
                    'price' => 55.00,
                    'duration' => 150,
                    'description' => 'Explore the ancient art of hand-building pottery without using a wheel. This engaging class teaches traditional techniques including pinch pots, coil building, and slab construction. Learn to create functional and decorative pieces using only your hands and simple tools. The workshop covers clay preparation, joining techniques, surface texturing, and finishing methods. Students will create bowls, vases, and sculptural pieces while understanding the cultural significance of hand-building across different civilizations. The relaxed pace allows for creativity and experimentation. All materials are provided, including various clay types and texturing tools. Your pieces will be bisque fired and ready for glazing in a follow-up session.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('hand building pottery coil pinch', 800, 600),
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Glazing and Firing Workshop',
                    'price' => 65.00,
                    'duration' => 120,
                    'description' => 'Master the art of glazing and understand the science behind ceramic firing in this specialized workshop. Learn about different glaze types, application techniques, and how firing temperatures affect final results. Explore color theory in ceramics, layering techniques, and special effects like crystalline and reduction glazes. The session includes hands-on practice with brush application, dipping, and pouring techniques. Understand kiln loading, firing schedules, and troubleshooting common glazing problems. Students will glaze multiple test pieces and learn to predict and control glaze outcomes. The workshop includes all glazing materials and firing costs. Perfect for potters wanting to expand their finishing skills and achieve professional-quality results.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('pottery glazing ceramic firing kiln', 800, 600),
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Sculptural Ceramics Workshop',
                    'price' => 85.00,
                    'duration' => 240,
                    'description' => 'Unleash your artistic vision in this intensive sculptural ceramics workshop designed for creative expression beyond functional pottery. Learn advanced hand-building techniques for creating three-dimensional art pieces, including hollow construction methods, armature use, and large-scale building strategies. Explore contemporary ceramic art movements and develop your personal artistic voice. The workshop covers surface treatments, alternative firing techniques, and mixed-media integration. Students will work on individual projects with guidance on concept development, technical execution, and artistic presentation. Learn about the business side of ceramic art, including pricing, exhibition, and marketing your work. All materials, tools, and specialized firing are included in this comprehensive artistic journey.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('sculptural ceramics contemporary art', 800, 600),
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
            ],

            // Photography sessions services
            'Photography sessions' => [
                [
                    'name' => 'Portrait Photography Session',
                    'price' => 150.00,
                    'duration' => 90,
                    'description' => 'Capture your personality and essence with our professional portrait photography session. Perfect for headshots, family portraits, or personal branding photos. Our experienced photographer will work with you to create stunning images that reflect your unique character and style. The session includes professional lighting setup, multiple outfit changes, and various poses and expressions. We provide guidance on wardrobe selection and posing to ensure you look your absolute best. The session takes place in our fully equipped studio with professional backdrop options, or we can arrange an outdoor location shoot for a more natural feel. You\'ll receive 20-30 high-resolution edited images delivered digitally within one week. Additional retouching services available upon request.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('professional portrait photography studio', 800, 600),
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Wedding Photography Package',
                    'price' => 1200.00,
                    'duration' => 480,
                    'description' => 'Preserve your special day with our comprehensive wedding photography package. Our skilled photographer will capture every precious moment from preparation to reception, ensuring no detail is missed. The package includes pre-wedding consultation, engagement session, full wedding day coverage (8 hours), and post-wedding editing. We use professional-grade equipment and backup systems to guarantee your memories are safely captured. Our photojournalistic style combines candid moments with traditional posed shots, creating a complete story of your wedding day. The package includes 300-500 high-resolution edited images, online gallery for sharing with family and friends, and printing rights. We also provide a beautifully designed wedding album as a keepsake. Additional hours and second photographer available for larger weddings.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('wedding photography bride groom ceremony', 800, 600),
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Product Photography Service',
                    'price' => 200.00,
                    'duration' => 120,
                    'description' => 'Showcase your products with professional photography that drives sales and enhances your brand image. Our product photography service is perfect for e-commerce, catalogs, marketing materials, and social media. We specialize in creating clean, attractive images that highlight your product\'s best features and appeal to your target audience. The session includes professional lighting setup, multiple angles and compositions, and various background options including white background, lifestyle settings, and creative arrangements. We handle up to 15 products per session with multiple shots of each item. All images are professionally edited for color correction, exposure, and minor retouching. Delivered in web-optimized and print-ready formats within 3-5 business days.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('product photography ecommerce studio setup', 800, 600),
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Event Photography Coverage',
                    'price' => 300.00,
                    'duration' => 180,
                    'description' => 'Document your special event with professional photography that captures the atmosphere, emotions, and key moments. Perfect for corporate events, parties, celebrations, and social gatherings. Our photographer will blend into your event while capturing candid interactions, important speeches, and memorable moments. We provide comprehensive coverage including arrival shots, key activities, group photos, and departure moments. The service includes pre-event consultation to understand your specific needs and important shots list. We use professional equipment suitable for various lighting conditions and venue types. You\'ll receive 150-200 high-resolution edited images showcasing the best moments of your event. Images are delivered through a secure online gallery within one week, with options for prints and additional editing services.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('event photography corporate party celebration', 800, 600),
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Fashion Photography Shoot',
                    'price' => 400.00,
                    'duration' => 240,
                    'description' => 'Create stunning fashion imagery with our professional fashion photography service. Perfect for models, designers, boutiques, and fashion brands looking to create compelling visual content. Our experienced fashion photographer understands lighting, composition, and styling needed to create magazine-quality images. The shoot includes professional studio setup with various lighting configurations, backdrop options, and styling guidance. We work with you to develop concepts that align with your brand or personal style goals. The session accommodates multiple outfit changes and various poses to create a diverse portfolio. We provide direction on posing, expression, and movement to capture dynamic and engaging images. Includes 40-60 high-resolution edited images with professional retouching, color grading, and artistic enhancement. Perfect for portfolios, marketing campaigns, and social media content.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('fashion photography model studio lighting', 800, 600),
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],

            // Craft workshops services
            'Craft workshops' => [
                [
                    'name' => 'Jewelry Making Workshop',
                    'price' => 65.00,
                    'duration' => 180,
                    'description' => 'Discover the art of jewelry making in this comprehensive hands-on workshop perfect for beginners and intermediate crafters. Learn fundamental techniques including wire wrapping, beading, basic metalworking, and stone setting. Create beautiful, wearable pieces including earrings, bracelets, and pendants using quality materials like sterling silver wire, gemstones, and crystals. Our experienced instructor will guide you through tool usage, design principles, and finishing techniques to achieve professional-looking results. The workshop covers safety procedures, material selection, and care instructions for your finished pieces. All tools and materials are provided, including a starter kit to take home for future projects. You\'ll leave with 3-4 completed jewelry pieces and the knowledge to continue crafting at home. Perfect for those seeking a new creative outlet or looking to start a jewelry-making hobby.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('jewelry making workshop beading wire', 800, 600),
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Woodworking Basics Class',
                    'price' => 85.00,
                    'duration' => 240,
                    'description' => 'Enter the world of woodworking with this comprehensive beginner-friendly class that covers essential skills and safety practices. Learn to use hand tools and power tools safely and effectively, understand different wood types and their properties, and master fundamental techniques like measuring, cutting, joining, and finishing. Create a functional project such as a cutting board, small shelf, or decorative box while learning proper workshop etiquette and safety protocols. The class covers wood selection, grain direction, joinery methods, and various finishing techniques including sanding, staining, and protective coatings. Our experienced woodworker will provide personalized instruction and help you develop confidence with tools and techniques. All materials, tools, and safety equipment are provided. You\'ll complete a finished project to take home and gain the foundation skills needed for future woodworking endeavors.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('woodworking class tools workshop beginner', 800, 600),
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Candle Making Workshop',
                    'price' => 40.00,
                    'duration' => 120,
                    'description' => 'Create beautiful, aromatic candles in this relaxing and creative workshop suitable for all skill levels. Learn about different wax types including soy, beeswax, and paraffin, and discover how each affects burn time, scent throw, and appearance. Master essential techniques including wick selection and placement, temperature control, color blending, and scent layering. Create multiple candles in various styles including container candles, pillar candles, and decorative shapes. The workshop covers safety procedures, troubleshooting common problems, and advanced techniques like marbling and embedding decorative elements. All materials are provided including high-quality waxes, fragrances, dyes, wicks, and containers. You\'ll take home 4-6 finished candles and receive recipes and tips for continuing your candle-making journey at home. Perfect for gifts, home décor, or starting a small business.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('candle making workshop wax pouring scents', 800, 600),
                    'rating' => 4.5,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Soap Making Masterclass',
                    'price' => 70.00,
                    'duration' => 180,
                    'description' => 'Dive into the ancient art of soap making with this comprehensive masterclass covering both cold process and melt-and-pour methods. Learn about saponification, oil properties, and how different ingredients affect the final product\'s texture, lather, and moisturizing properties. Create natural, skin-friendly soaps using quality oils, butters, and botanicals while understanding safety procedures for handling lye and other materials. The class covers recipe formulation, color techniques using natural and synthetic colorants, scenting with essential oils and fragrances, and creative molding and cutting techniques. Explore advanced techniques like swirling, layering, and embedding decorative elements. All safety equipment, ingredients, and molds are provided. You\'ll create 6-8 bars of soap in different styles and receive detailed recipes and safety guidelines for home soap making.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('soap making natural ingredients oils botanicals', 800, 600),
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Macramé Wall Hanging Workshop',
                    'price' => 50.00,
                    'duration' => 150,
                    'description' => 'Learn the trendy art of macramé in this relaxing workshop where you\'ll create a beautiful wall hanging to decorate your home. Master fundamental knots including square knots, half hitches, and gathering knots while understanding cord types, tension control, and pattern reading. Create an intricate wall hanging design incorporating various textures and patterns, with options for adding beads, feathers, or other decorative elements. The workshop covers design principles, measuring and cutting techniques, and finishing methods to ensure your piece hangs beautifully. Our instructor will provide personalized guidance to help you achieve clean, even knots and professional-looking results. All materials are included: high-quality macramé cord, wooden dowel, and decorative elements. You\'ll complete a stunning wall hanging (approximately 24 inches long) and receive pattern guides for creating additional pieces at home. Perfect for beginners wanting to learn this meditative craft.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('macrame wall hanging workshop knots cord', 800, 600),
                    'rating' => 4.4,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],

            // Painting classes services
            'Painting classes' => [
                [
                    'name' => 'Watercolor Painting Workshop',
                    'price' => 55.00,
                    'duration' => 180,
                    'description' => 'Explore the beautiful and versatile medium of watercolor painting in this comprehensive workshop designed for beginners and intermediate artists. Learn fundamental techniques including wet-on-wet, wet-on-dry, glazing, and color mixing while understanding the unique properties of watercolor paints and papers. Create stunning landscapes, florals, and abstract compositions while mastering brush control, water management, and color theory. The workshop covers essential skills like creating gradients, texture techniques, and correcting common mistakes. Our experienced instructor will provide personalized guidance and demonstrate advanced techniques like salt textures, masking, and lifting. All materials are provided including professional-grade watercolor paints, brushes, and various paper types. You\'ll complete 3-4 finished paintings and receive a starter kit to continue practicing at home. Perfect for those seeking a relaxing creative outlet or wanting to develop serious painting skills.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('watercolor painting workshop brushes palette', 800, 600),
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Acrylic Painting Masterclass',
                    'price' => 75.00,
                    'duration' => 240,
                    'description' => 'Master the versatile medium of acrylic painting in this comprehensive masterclass suitable for all skill levels. Learn essential techniques including color mixing, blending, layering, and texture creation while exploring various painting styles from realistic to abstract. Understand canvas preparation, brush selection, and paint consistency for different effects. Create multiple paintings including still life, landscape, and portrait studies while learning composition principles and color theory. The class covers advanced techniques like impasto, glazing, dry brushing, and palette knife work. Explore different acrylic mediums and additives that extend working time, create texture, or achieve special effects. All materials provided including professional-grade acrylic paints, canvases, brushes, and mediums. You\'ll complete 2-3 substantial paintings and receive comprehensive guides for continued learning. Perfect for developing a strong foundation in painting or advancing existing skills.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('acrylic painting masterclass canvas studio', 800, 600),
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Oil Painting Fundamentals',
                    'price' => 95.00,
                    'duration' => 300,
                    'description' => 'Discover the rich tradition of oil painting in this comprehensive fundamentals course covering classical techniques and contemporary approaches. Learn about oil paint properties, mediums, and solvents while understanding safety procedures and proper ventilation. Master essential techniques including alla prima, glazing, scumbling, and impasto while exploring color temperature, value relationships, and atmospheric perspective. Create realistic studies focusing on light, shadow, and form while learning traditional methods used by master painters. The course covers canvas preparation, underpainting techniques, and proper drying procedures. Understand brush care, paint mixing, and studio setup for oil painting. All materials provided including professional oil paints, prepared canvases, brushes, mediums, and safety equipment. You\'ll complete several studies and one major painting while receiving detailed instruction in classical oil painting methods. Perfect for serious art students or hobbyists wanting to master this traditional medium.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('oil painting fundamentals classical techniques studio', 800, 600),
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Abstract Painting Workshop',
                    'price' => 65.00,
                    'duration' => 180,
                    'description' => 'Unleash your creativity in this liberating abstract painting workshop that encourages experimentation and personal expression. Explore non-representational art through various techniques including gestural painting, color field methods, and mixed media approaches. Learn to work intuitively while understanding composition principles, color relationships, and visual balance in abstract work. Experiment with unconventional tools like palette knives, sponges, and found objects to create unique textures and effects. The workshop covers different abstract styles from expressionist to geometric, helping you find your personal artistic voice. Learn to critique and discuss abstract art while developing confidence in non-representational expression. All materials provided including various paints, canvases, and experimental tools. You\'ll create multiple abstract pieces while exploring different approaches and techniques. Perfect for artists wanting to break free from realistic representation or explore contemporary art movements.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('abstract painting workshop contemporary art expression', 800, 600),
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Plein Air Painting Experience',
                    'price' => 85.00,
                    'duration' => 240,
                    'description' => 'Experience the joy of painting outdoors in this plein air workshop that combines artistic instruction with nature appreciation. Learn to capture changing light, weather conditions, and natural beauty while working directly from observation. Master techniques for quick color mixing, simplified composition, and efficient brushwork needed for outdoor painting. Understand how to adapt to changing conditions and work within time constraints while maintaining artistic quality. The workshop covers portable equipment setup, color temperature in natural light, and atmospheric perspective. Learn to edit complex scenes into compelling compositions while capturing the essence of the landscape. Weather backup plans ensure productive sessions regardless of conditions. All outdoor painting equipment provided including portable easels, paints, canvases, and weather protection. You\'ll complete 2-3 outdoor paintings while learning to see and interpret natural light. Perfect for artists wanting to connect with nature and develop observational skills.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('plein air painting outdoor landscape easel', 800, 600),
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],

            // Yoga services
            'Yoga' => [
                [
                    'name' => 'Hatha Yoga Sessions',
                    'price' => 25.00,
                    'duration' => 75,
                    'description' => 'Experience the gentle yet powerful practice of Hatha Yoga, perfect for beginners and those seeking a slower-paced, meditative approach to yoga. This traditional form focuses on physical postures (asanas) and breathing techniques (pranayama) to create balance between mind and body. Each session includes a warm-up sequence, standing and seated poses, gentle backbends and twists, and a relaxing final rest period. Our certified instructor provides detailed alignment cues and modifications for all fitness levels and physical limitations. The practice emphasizes holding poses for several breaths, allowing time to develop strength, flexibility, and mindfulness. Classes include breathing exercises that help reduce stress and improve mental clarity. You\'ll learn fundamental yoga poses that form the foundation for other yoga styles. Perfect for stress relief, improved flexibility, and developing a sustainable yoga practice.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('hatha yoga class meditation poses', 800, 600),
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Vinyasa Flow Classes',
                    'price' => 30.00,
                    'duration' => 60,
                    'description' => 'Flow through dynamic sequences in this energizing Vinyasa yoga class that synchronizes movement with breath. Each class features creative sequences that build heat, strength, and flexibility while maintaining a meditative quality through continuous movement. Learn to transition smoothly between poses using ujjayi breathing, creating a moving meditation that challenges both body and mind. Classes include sun salutations, standing sequences, arm balances, and backbends, with variations offered for different skill levels. The flowing nature of Vinyasa helps develop coordination, endurance, and mental focus while releasing tension and stress. Each class ends with a restorative cool-down and final relaxation. Our experienced instructors provide hands-on adjustments and personalized modifications. Perfect for those seeking a more dynamic yoga practice that builds strength while maintaining the spiritual aspects of yoga.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('vinyasa flow yoga dynamic movement', 800, 600),
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Beginner Yoga Workshop',
                    'price' => 35.00,
                    'duration' => 90,
                    'description' => 'Start your yoga journey with confidence in this comprehensive beginner workshop designed to introduce fundamental concepts, poses, and breathing techniques. Learn proper alignment for essential poses including downward dog, warrior poses, forward folds, and basic twists while understanding the philosophy and benefits of yoga practice. The workshop covers yoga etiquette, breathing techniques, and how to use props like blocks, straps, and bolsters to support your practice. Discover different yoga styles to help you choose the right classes for your goals and preferences. Practice basic sequences you can do at home, and learn to listen to your body and modify poses as needed. The supportive environment encourages questions and provides personalized attention to ensure proper form and prevent injury. You\'ll receive a beginner\'s guide with pose illustrations and home practice sequences. Perfect for complete beginners or those returning to yoga after a long break.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('beginner yoga workshop fundamentals instruction', 800, 600),
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Restorative Yoga Session',
                    'price' => 28.00,
                    'duration' => 75,
                    'description' => 'Deeply relax and restore your nervous system in this gentle, therapeutic yoga practice designed to promote healing and stress relief. Using props like bolsters, blankets, and blocks, you\'ll settle into comfortable, supported poses held for 5-10 minutes each, allowing your body to completely release tension and your mind to find peace. This passive practice activates the parasympathetic nervous system, promoting deep relaxation and natural healing processes. Each session includes gentle breathing exercises, guided meditation, and yoga nidra (yogic sleep) techniques. The practice is suitable for all ages and fitness levels, including those recovering from injury or dealing with chronic stress. Learn self-care techniques you can practice at home to manage stress and improve sleep quality. The nurturing environment and soothing music create a sanctuary for deep relaxation. Perfect for busy professionals, caregivers, or anyone needing to restore balance and energy.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('restorative yoga relaxation props bolsters', 800, 600),
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Power Yoga Intensive',
                    'price' => 35.00,
                    'duration' => 60,
                    'description' => 'Challenge yourself in this athletic, strength-building yoga class that combines traditional poses with fitness-inspired movements. Power yoga emphasizes building lean muscle, improving cardiovascular health, and developing mental resilience through challenging sequences and longer holds. Each class includes dynamic warm-ups, strength-building standing sequences, core work, arm balances, and advanced poses with modifications for different levels. The practice builds heat through continuous movement and focused breathing, promoting detoxification and mental clarity. Learn to use your breath as fuel for challenging poses while developing the mental focus needed to push through physical and mental barriers. Classes incorporate elements from various yoga styles, creating a well-rounded practice that improves strength, flexibility, and endurance. Our experienced instructors provide motivation and technical guidance to help you safely progress in your practice. Perfect for athletes, fitness enthusiasts, or anyone seeking a challenging physical and mental workout.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('power yoga intensive strength athletic', 800, 600),
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
        ];
    }
}
