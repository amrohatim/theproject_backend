<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductColor;
use Illuminate\Support\Facades\DB;

class FetchColors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'colors:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and display product colors from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Product Colors Database Query ===');
        $this->newLine();

        try {
            // 1. Check if table exists
            $this->info('1. Checking if product_colors table exists...');
            $tableExists = DB::getSchemaBuilder()->hasTable('product_colors');
            if (!$tableExists) {
                $this->error('❌ product_colors table does not exist');
                return 1;
            }
            $this->info('✅ product_colors table exists');
            $this->newLine();

            // 2. Get total count
            $this->info('2. Total colors in database:');
            $totalCount = ProductColor::count();
            $this->line("Total colors: {$totalCount}");
            $this->newLine();

            // 3. Get unique color names and their counts
            $this->info('3. Color distribution by name:');
            $colorCounts = DB::table('product_colors')
                ->select('name', DB::raw('count(*) as count'))
                ->groupBy('name')
                ->orderBy('count', 'desc')
                ->get();
            
            foreach ($colorCounts as $colorCount) {
                $this->line("  - {$colorCount->name}: {$colorCount->count} entries");
            }
            $this->newLine();

            // 4. Get unique color codes and their counts
            $this->info('4. Color distribution by hex code:');
            $hexCounts = DB::table('product_colors')
                ->select('color_code', DB::raw('count(*) as count'))
                ->groupBy('color_code')
                ->orderBy('count', 'desc')
                ->limit(20)
                ->get();
            
            foreach ($hexCounts as $hexCount) {
                $this->line("  - {$hexCount->color_code}: {$hexCount->count} entries");
            }
            $this->newLine();

            // 5. Show sample of all colors (first 30)
            $this->info('5. Sample of colors in database (first 30):');
            $sampleColors = ProductColor::select('id', 'name', 'color_code', 'product_id')
                ->orderBy('id')
                ->limit(30)
                ->get();
            
            foreach ($sampleColors as $color) {
                $this->line("  - ID: {$color->id}, Name: '{$color->name}', Code: '{$color->color_code}', Product: {$color->product_id}");
            }
            $this->newLine();

            // 6. Check for diverse colors (non-black)
            $this->info('6. Non-black colors:');
            $nonBlackColors = ProductColor::where('color_code', '!=', '#000000')
                ->where('color_code', '!=', '#000')
                ->select('id', 'name', 'color_code')
                ->limit(20)
                ->get();
            
            if ($nonBlackColors->count() > 0) {
                $this->line("Found {$nonBlackColors->count()} non-black colors:");
                foreach ($nonBlackColors as $color) {
                    $this->line("  - ID: {$color->id}, Name: '{$color->name}', Code: '{$color->color_code}'");
                }
            } else {
                $this->error('❌ No non-black colors found in database!');
            }

            $this->newLine();
            $this->info('=== End of Color Database Query ===');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
