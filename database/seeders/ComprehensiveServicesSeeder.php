<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;

class ComprehensiveServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Comprehensive Services Seeder...');
        
        // Clear existing services if needed
        if ($this->command->confirm('Do you want to clear existing services before seeding?', false)) {
            Service::truncate();
            $this->command->info('Existing services cleared.');
        }

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

        $this->command->info("Found {$subcategories->count()} service subcategories.");
        $this->command->info("Found {$branches->count()} active branches.");

        $totalServicesCreated = 0;

        // Define services for each subcategory
        $servicesByCategory = $this->getServicesData();

        foreach ($subcategories as $subcategory) {
            $categoryName = $subcategory->name;
            
            if (!isset($servicesByCategory[$categoryName])) {
                $this->command->warn("No services defined for category: {$categoryName}");
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

        $this->command->info("Service seeding completed successfully!");
        $this->command->info("Total services created: {$totalServicesCreated}");
    }

    /**
     * Get comprehensive services data for each subcategory
     */
    private function getServicesData(): array
    {
        return [
            // Artistic Services - Craft workshops
            'Craft workshops' => [
                [
                    'name' => 'Beginner Jewelry Making Workshop',
                    'description' => 'Discover the art of jewelry making in this comprehensive beginner-friendly workshop. Learn essential techniques including wire wrapping, beading, and basic metalworking. You\'ll create your own unique pieces including earrings, bracelets, and pendants using quality materials like sterling silver wire, gemstone beads, and crystals. Our experienced instructors will guide you through each step, from design concepts to finishing techniques. Perfect for those looking to explore a new creative hobby or develop skills for a potential business venture. All materials and tools are provided, and you\'ll leave with 3-4 completed pieces and the knowledge to continue crafting at home.',
                    'price' => 85.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => true,
                ],
                [
                    'name' => 'Advanced Woodworking Masterclass',
                    'description' => 'Take your woodworking skills to the next level with this intensive masterclass designed for intermediate to advanced crafters. Learn sophisticated joinery techniques, advanced tool usage, and precision finishing methods. You\'ll work on a challenging project that incorporates dovetail joints, mortise and tenon connections, and hand-carved details. Our master craftsman will share professional tips for wood selection, grain matching, and achieving museum-quality finishes. This workshop covers both traditional hand tools and modern power tool techniques, ensuring you develop a well-rounded skill set. Perfect for furniture makers, cabinet builders, or anyone passionate about fine woodworking.',
                    'price' => 150.00,
                    'duration' => 360,
                    'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Textile Arts and Fiber Crafts',
                    'description' => 'Explore the rich world of textile arts in this comprehensive workshop covering multiple fiber craft techniques. Learn traditional methods including hand spinning, natural dyeing, basic weaving, and embroidery. You\'ll work with various fibers including wool, cotton, silk, and alpaca, understanding their unique properties and applications. The workshop includes instruction on using a spinning wheel, creating natural dyes from plants and minerals, and setting up a simple loom. Perfect for those interested in sustainable crafting, historical techniques, or developing a deeper connection with textile creation. You\'ll complete several small projects and gain the foundation to pursue any of these crafts further.',
                    'price' => 95.00,
                    'duration' => 240,
                    'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Candle Making and Aromatherapy',
                    'description' => 'Create beautiful, aromatic candles while learning the therapeutic benefits of essential oils in this hands-on workshop. Master the art of candle making using natural soy wax, beeswax, and coconut wax blends. Learn about wick selection, proper pouring techniques, and color blending to create professional-quality candles. The aromatherapy component covers essential oil properties, blending techniques, and creating custom scents for relaxation, energy, or focus. You\'ll make 6-8 candles in various styles including container candles, pillar candles, and tea lights. Perfect for those interested in natural wellness, home décor, or starting a small candle business. All materials provided, plus take-home guides for continued practice.',
                    'price' => 75.00,
                    'duration' => 150,
                    'image' => 'https://images.unsplash.com/photo-1602874801006-e26d3d17d0a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
                [
                    'name' => 'Leatherworking Fundamentals',
                    'description' => 'Learn the timeless craft of leatherworking in this comprehensive introduction to working with leather. Master essential techniques including cutting, stitching, tooling, and finishing leather goods. You\'ll work with high-quality vegetable-tanned leather to create functional items like wallets, belts, or small bags. The workshop covers tool selection and maintenance, leather types and grades, pattern making, and traditional hand-stitching methods. Learn decorative techniques such as stamping, carving, and edge finishing to create professional-looking pieces. Our experienced leather artisan will guide you through each step, ensuring you develop proper technique and safety practices. Perfect for those interested in traditional crafts, sustainable fashion, or creating personalized leather goods.',
                    'price' => 110.00,
                    'duration' => 300,
                    'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
            ],

            // Artistic Services - Painting classes
            'Painting classes' => [
                [
                    'name' => 'Watercolor Landscape Painting',
                    'description' => 'Immerse yourself in the beautiful world of watercolor landscape painting with this comprehensive class designed for all skill levels. Learn fundamental watercolor techniques including wet-on-wet, wet-on-dry, glazing, and color mixing to create stunning natural scenes. You\'ll explore composition principles, perspective, and how to capture light and atmosphere in your paintings. The class covers painting skies, trees, water reflections, and mountains using professional-grade watercolor paints and papers. Our experienced instructor will demonstrate various brush techniques and help you develop your own artistic style. Perfect for beginners wanting to learn watercolor basics or intermediate artists looking to refine their landscape skills. All materials provided including brushes, paints, and watercolor paper.',
                    'price' => 65.00,
                    'duration' => 120,
                    'image' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => true,
                ],
                [
                    'name' => 'Acrylic Portrait Painting Workshop',
                    'description' => 'Master the art of portrait painting using versatile acrylic paints in this intensive workshop. Learn essential techniques for capturing facial features, skin tones, and expressions with accuracy and artistic flair. The class covers facial anatomy, proportion guidelines, color theory for skin tones, and blending techniques specific to acrylic paints. You\'ll work from photo references to create a complete portrait, learning how to build layers, create depth, and achieve realistic textures. Our professional portrait artist will provide individual guidance on brush techniques, color mixing, and problem-solving. Suitable for intermediate to advanced painters who want to develop their portrait skills. This workshop provides a strong foundation for anyone interested in commissioned portrait work or fine art.',
                    'price' => 95.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Abstract Expressionism Exploration',
                    'description' => 'Unleash your creativity and explore the freedom of abstract expressionism in this liberating painting class. Learn to express emotions, ideas, and energy through color, form, and gesture without the constraints of realistic representation. You\'ll experiment with various techniques including palette knife work, dripping, splattering, and gestural brushwork using acrylic and mixed media. The class covers color theory, composition in abstract work, and how to develop your personal artistic voice. Our instructor will guide you through exercises designed to break through creative blocks and develop confidence in non-representational art. Perfect for artists of all levels who want to explore contemporary art forms, develop intuitive painting skills, or simply enjoy the therapeutic benefits of expressive art-making.',
                    'price' => 80.00,
                    'duration' => 150,
                    'image' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
                [
                    'name' => 'Oil Painting Still Life Masterclass',
                    'description' => 'Develop classical painting skills with this comprehensive oil painting still life class. Learn traditional techniques used by master painters including color mixing, glazing, scumbling, and alla prima methods. You\'ll work with professional oil paints to create a detailed still life composition, focusing on light, shadow, texture, and form. The class covers canvas preparation, color temperature, brushwork techniques, and the unique properties of oil paint including blending times and layering methods. Our classically trained instructor will demonstrate time-tested approaches while helping you develop your own artistic interpretation. Perfect for serious art students, professional artists wanting to refine their skills, or anyone passionate about traditional painting methods. This intensive class provides excellent preparation for advanced art studies.',
                    'price' => 120.00,
                    'duration' => 240,
                    'image' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Plein Air Painting Adventure',
                    'description' => 'Experience the joy and challenge of painting outdoors with this plein air painting class that combines artistic instruction with nature exploration. Learn to quickly capture changing light, weather conditions, and natural scenes using portable painting techniques. You\'ll master rapid color mixing, simplified composition methods, and how to work efficiently in various outdoor conditions. The class covers essential plein air equipment, color temperature changes throughout the day, and techniques for finishing paintings in the studio. Our experienced plein air artist will guide you through location selection, setup procedures, and adapting to environmental challenges. Perfect for landscape painters, nature lovers, or anyone wanting to develop observational skills and connect with the environment through art. Weather-appropriate locations selected, and backup indoor options available.',
                    'price' => 85.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
            ],

            // Artistic Services - Photography sessions
            'Photography sessions' => [
                [
                    'name' => 'Professional Portrait Photography',
                    'description' => 'Capture stunning professional portraits with this comprehensive photography session and workshop. Learn advanced lighting techniques, posing guidance, and composition principles used by professional portrait photographers. You\'ll work with professional studio lighting equipment including softboxes, reflectors, and background systems to create magazine-quality portraits. The session covers camera settings for portraits, depth of field control, and post-processing basics using professional software. Our experienced portrait photographer will guide you through directing subjects, creating flattering poses, and capturing genuine expressions. Perfect for aspiring photographers, business professionals needing headshots, or anyone wanting to improve their portrait photography skills. Includes both studio and natural light techniques, with edited digital images provided.',
                    'price' => 150.00,
                    'duration' => 120,
                    'image' => 'https://images.unsplash.com/photo-1554048612-b6a482b224b8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => true,
                ],
                [
                    'name' => 'Wedding Photography Masterclass',
                    'description' => 'Master the art of wedding photography with this comprehensive workshop covering all aspects of capturing one of life\'s most important celebrations. Learn essential techniques for photographing ceremonies, receptions, and intimate moments while working in challenging lighting conditions. The class covers timeline planning, shot lists, working with couples, and managing family group photos efficiently. You\'ll practice with professional wedding photography equipment and learn backup strategies for critical moments. Our experienced wedding photographer will share business insights, client communication skills, and post-processing workflows specific to wedding photography. Perfect for photographers looking to enter the wedding industry or improve their event photography skills. Includes hands-on practice with mock wedding scenarios.',
                    'price' => 200.00,
                    'duration' => 300,
                    'image' => 'https://images.unsplash.com/photo-1519741497674-611481863552?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Nature and Wildlife Photography',
                    'description' => 'Explore the fascinating world of nature and wildlife photography in this outdoor workshop designed for photographers of all levels. Learn specialized techniques for capturing animals in their natural habitat, including telephoto lens usage, camouflage strategies, and ethical wildlife photography practices. The workshop covers macro photography for insects and flowers, landscape composition, and working with natural light throughout the day. You\'ll practice patience, observation skills, and quick reflexes needed for successful wildlife photography. Our expert nature photographer will share field techniques, safety considerations, and conservation awareness. Perfect for nature lovers, outdoor enthusiasts, or photographers wanting to specialize in environmental subjects. Weather-dependent with backup indoor macro photography sessions available.',
                    'price' => 120.00,
                    'duration' => 240,
                    'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Street Photography Workshop',
                    'description' => 'Discover the art of street photography and learn to capture authentic moments of urban life with this dynamic workshop. Master techniques for photographing people, architecture, and street scenes while developing your eye for decisive moments and compelling compositions. The workshop covers camera settings for fast-moving situations, working with available light, and approaching subjects respectfully. You\'ll learn about the legal and ethical aspects of street photography, building confidence to photograph in public spaces. Our experienced street photographer will guide you through various urban environments, teaching you to anticipate action and capture the essence of city life. Perfect for documentary photographers, travel enthusiasts, or anyone interested in photojournalism and social photography.',
                    'price' => 95.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
                [
                    'name' => 'Product Photography for E-commerce',
                    'description' => 'Learn professional product photography techniques essential for e-commerce success in this practical workshop. Master lighting setups, background selection, and styling techniques that make products look their best online. You\'ll work with various product types including jewelry, clothing, electronics, and food items, learning specific approaches for each category. The workshop covers camera settings, white balance, and creating consistent lighting for product catalogs. Our commercial photographer will teach you cost-effective lighting solutions, DIY studio setups, and post-processing workflows for high-volume product photography. Perfect for small business owners, online sellers, or photographers wanting to enter commercial photography. Includes guidance on building a profitable product photography business.',
                    'price' => 110.00,
                    'duration' => 150,
                    'image' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
            ],

            // Artistic Services - Pottery making
            'Pottery making' => [
                [
                    'name' => 'Wheel Throwing for Beginners',
                    'description' => 'Discover the meditative art of pottery wheel throwing in this hands-on beginner class. Learn fundamental techniques for centering clay, opening forms, and pulling walls to create functional pottery pieces like bowls, cups, and vases. You\'ll master the basics of clay preparation, wheel speed control, and proper body positioning for successful throwing. The class covers trimming techniques, handle attachment, and basic glazing principles. Our experienced potter will guide you through each step, helping you develop muscle memory and confidence on the wheel. Perfect for complete beginners or those wanting to refresh their pottery skills. The therapeutic nature of working with clay provides stress relief while developing a valuable artistic skill. All clay, tools, and firing included, with finished pieces ready for pickup after glazing and firing.',
                    'price' => 85.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => true,
                ],
                [
                    'name' => 'Advanced Ceramic Sculpture',
                    'description' => 'Push the boundaries of ceramic art with this advanced sculpture class focusing on large-scale and complex ceramic forms. Learn sophisticated hand-building techniques including coil construction, slab building, and hollow form creation. You\'ll explore surface treatments, texture techniques, and experimental glazing methods to create unique artistic pieces. The class covers armature construction, drying techniques for large pieces, and kiln loading strategies for sculptural work. Our master ceramicist will guide you through conceptual development, helping you translate ideas into clay while solving technical challenges. Perfect for experienced potters ready to explore sculptural possibilities or artists from other mediums wanting to work with clay. Includes instruction on alternative firing techniques and contemporary ceramic art practices.',
                    'price' => 150.00,
                    'duration' => 300,
                    'image' => 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Glazing and Surface Design Workshop',
                    'description' => 'Explore the magical world of ceramic glazes and surface treatments in this comprehensive workshop. Learn glaze chemistry basics, application techniques, and how different glazes interact with clay bodies and firing temperatures. You\'ll experiment with various glazing methods including dipping, brushing, pouring, and resist techniques to create unique surface effects. The workshop covers glaze layering, crystalline glazes, and troubleshooting common glazing problems. Our glaze specialist will demonstrate advanced techniques like raku firing, saggar firing, and alternative atmospheric effects. Perfect for potters wanting to expand their surface treatment knowledge or ceramic artists looking to develop signature glaze effects. Includes extensive testing and documentation to help you recreate successful results in your own work.',
                    'price' => 95.00,
                    'duration' => 240,
                    'image' => 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Functional Pottery Workshop',
                    'description' => 'Create beautiful, functional pottery pieces for everyday use in this practical workshop focused on utilitarian ceramics. Learn to make dinnerware sets, serving pieces, and kitchen accessories that combine beauty with functionality. You\'ll master techniques for creating consistent forms, proper proportions for functional use, and ergonomic considerations for handles and spouts. The workshop covers food-safe glazing, durability testing, and design principles that make pottery both attractive and practical. Our functional potter will share insights about developing a cohesive style, pricing handmade pottery, and building a customer base for functional ceramics. Perfect for potters interested in creating sellable work or anyone wanting to make personalized pottery for their home. Includes business guidance for those interested in selling their pottery.',
                    'price' => 100.00,
                    'duration' => 210,
                    'image' => 'https://images.unsplash.com/photo-1493106819501-66d381c466f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
                [
                    'name' => 'Kids Clay Fun Class',
                    'description' => 'Introduce children to the joy of working with clay in this fun, educational pottery class designed specifically for young artists ages 6-12. Kids will learn basic hand-building techniques through playful projects like pinch pots, coil animals, and slab tiles. The class emphasizes creativity, self-expression, and the satisfaction of creating something with their hands. Children will explore texture tools, stamps, and simple glazing techniques to decorate their pieces. Our patient, experienced instructor creates a supportive environment where kids can experiment freely while learning fundamental clay skills. Perfect for developing fine motor skills, creativity, and confidence in artistic expression. All materials provided, and parents receive tips for continuing clay exploration at home. Finished pieces are fired and ready for pickup within two weeks.',
                    'price' => 45.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
            ],

            // Elderly Care & Companionship Services - Companionship visits
            'Companionship visits' => [
                [
                    'name' => 'Weekly Companionship Sessions',
                    'description' => 'Provide meaningful social interaction and emotional support through regular companionship visits designed to combat loneliness and enhance quality of life for elderly individuals. Our trained companions offer engaging conversation, light activities, and genuine friendship to seniors who may be isolated or have limited social contact. Sessions include playing board games, sharing stories, looking through photo albums, or simply enjoying pleasant conversation over tea. We focus on building trust and rapport while respecting individual preferences and interests. Our companions are background-checked, trained in elderly care basics, and skilled in active listening and empathy. Perfect for families seeking reliable social support for their loved ones or seniors wanting to maintain social connections. Each visit is tailored to the individual\'s interests, mobility level, and emotional needs, ensuring a positive and enriching experience.',
                    'price' => 45.00,
                    'duration' => 120,
                    'image' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Memory Care Companionship',
                    'description' => 'Specialized companionship services for individuals with dementia, Alzheimer\'s, or other memory-related conditions, focusing on maintaining dignity, reducing anxiety, and providing cognitive stimulation. Our memory care companions are trained in dementia care techniques, validation therapy, and creating calm, supportive environments. Activities are designed to engage remaining cognitive abilities while providing comfort and familiarity. Sessions may include reminiscence therapy, simple crafts, music therapy, or gentle physical activities adapted to the individual\'s current abilities. We work closely with families and healthcare providers to ensure consistency and appropriate care approaches. Our companions understand the unique challenges of memory loss and are skilled in redirecting confusion, managing behavioral changes, and maintaining patient, compassionate interactions throughout the progression of the condition.',
                    'price' => 55.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Social Outing Assistance',
                    'description' => 'Accompany elderly individuals on social outings and community activities to help them maintain independence and social connections while ensuring safety and support. Our companions provide transportation assistance, mobility support, and social facilitation during visits to shopping centers, restaurants, cultural events, or medical appointments. We help navigate physical challenges, provide emotional support in social situations, and ensure our clients feel confident and comfortable during outings. Services include assistance with walking, carrying items, communication support, and emergency response if needed. Our companions are trained in elderly mobility assistance, emergency procedures, and social interaction facilitation. Perfect for seniors who want to remain active in their community but need additional support or confidence to participate in social activities safely and enjoyably.',
                    'price' => 65.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Technology Assistance and Digital Companionship',
                    'description' => 'Help elderly individuals stay connected with family and friends through technology while providing patient, personalized instruction in using digital devices and platforms. Our tech-savvy companions teach smartphone usage, video calling, social media basics, and online safety in a supportive, non-judgmental environment. Sessions include hands-on practice with devices, setting up accounts, organizing digital photos, and troubleshooting common issues. We focus on building confidence and independence in technology use while maintaining the human connection that makes learning enjoyable. Our companions understand the unique challenges seniors face with technology and provide step-by-step guidance at a comfortable pace. Perfect for seniors wanting to stay connected with distant family members, explore online interests, or simply feel more confident using modern technology in their daily lives.',
                    'price' => 50.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1581579438747-1dc8d17bbce4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Grief and Loss Support Companionship',
                    'description' => 'Provide compassionate support and companionship for elderly individuals dealing with grief, loss, or major life transitions such as the death of a spouse, moving to assisted living, or declining health. Our specially trained companions offer emotional support, active listening, and gentle encouragement during difficult times. We understand that grief affects everyone differently and provide non-judgmental presence while respecting individual coping styles and timelines. Sessions may include sharing memories, light activities to provide distraction, assistance with practical tasks, or simply sitting quietly together. Our companions are trained in grief support techniques, crisis intervention, and recognizing when additional professional help may be needed. Perfect for families seeking additional emotional support for their loved ones during challenging transitions or for seniors who need someone to talk to during difficult times.',
                    'price' => 60.00,
                    'duration' => 120,
                    'image' => 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            // Elderly Care & Companionship Services - In-home care
            'In-home care' => [
                [
                    'name' => 'Personal Care Assistance',
                    'description' => 'Comprehensive personal care services designed to help elderly individuals maintain dignity and independence while receiving assistance with daily living activities in the comfort of their own homes. Our certified caregivers provide professional support with bathing, dressing, grooming, toileting, and mobility assistance while respecting privacy and individual preferences. We focus on maintaining the highest standards of hygiene, safety, and comfort while encouraging independence wherever possible. Our caregivers are trained in proper body mechanics, infection control, and emergency response procedures. Each care plan is customized to the individual\'s specific needs, health conditions, and family preferences. We work closely with healthcare providers to ensure continuity of care and monitor for changes in condition. Perfect for seniors who want to age in place while receiving the support they need to maintain their quality of life and personal dignity.',
                    'price' => 75.00,
                    'duration' => 240,
                    'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Medication Management and Health Monitoring',
                    'description' => 'Professional medication management and health monitoring services to ensure elderly individuals take medications correctly and maintain optimal health while living independently. Our trained caregivers assist with medication organization, reminder systems, and monitoring for side effects or changes in condition. We maintain detailed records of medication administration, vital signs, and health observations to share with healthcare providers and family members. Services include blood pressure monitoring, blood sugar testing for diabetics, weight tracking, and general health assessments. Our caregivers are trained to recognize signs of medical emergencies and respond appropriately. We coordinate with pharmacies, doctors, and family members to ensure comprehensive care coordination. Perfect for seniors with complex medication regimens, chronic conditions, or those recovering from illness or surgery who need professional health monitoring in their home environment.',
                    'price' => 85.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Household Management and Light Housekeeping',
                    'description' => 'Comprehensive household management services to help elderly individuals maintain a clean, safe, and organized living environment while preserving their independence and comfort at home. Our caregivers provide light housekeeping including dusting, vacuuming, bathroom cleaning, kitchen maintenance, and laundry services. We also assist with meal planning, grocery shopping, and basic meal preparation tailored to dietary restrictions and preferences. Services include organizing medications, managing appointments, and maintaining household supplies. Our team focuses on creating a safe environment by identifying and addressing potential hazards while respecting the individual\'s personal space and belongings. We work with family members to establish routines and preferences that support the senior\'s lifestyle and independence. Perfect for elderly individuals who need assistance maintaining their home environment but want to continue living independently.',
                    'price' => 65.00,
                    'duration' => 180,
                    'image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Specialized Dementia and Alzheimer\'s Care',
                    'description' => 'Specialized in-home care services for individuals with dementia, Alzheimer\'s disease, or other cognitive impairments, focusing on safety, dignity, and quality of life. Our certified dementia care specialists are trained in person-centered care approaches, behavioral management techniques, and creating structured, calming environments. We provide assistance with daily activities while maintaining familiar routines and reducing confusion and anxiety. Our caregivers use validation therapy, redirection techniques, and sensory stimulation to engage clients appropriately. We work closely with families to develop care strategies that honor the individual\'s history, preferences, and remaining abilities. Services include safety monitoring, wandering prevention, medication assistance, and coordination with healthcare providers specializing in dementia care. Perfect for families seeking professional, compassionate care that allows their loved one to remain in familiar surroundings while receiving specialized attention.',
                    'price' => 95.00,
                    'duration' => 240,
                    'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Respite Care for Family Caregivers',
                    'description' => 'Professional respite care services providing temporary relief for family caregivers while ensuring their elderly loved ones receive quality care and supervision in their own homes. Our experienced caregivers step in to provide all necessary care services, allowing family members to take breaks, attend to personal needs, or simply rest and recharge. We maintain the established care routines and preferences while providing professional oversight and assistance. Services can range from a few hours to overnight care, depending on family needs. Our caregivers are trained to handle various care levels from basic companionship to complex medical needs, ensuring seamless care transitions. We provide detailed reports to family members about the care provided and any observations about their loved one\'s condition. Perfect for family caregivers who need regular breaks to maintain their own health and well-being while ensuring their loved one receives consistent, professional care.',
                    'price' => 80.00,
                    'duration' => 300,
                    'image' => 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
        ];
    }
}
