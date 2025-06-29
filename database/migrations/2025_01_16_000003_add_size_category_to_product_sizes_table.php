<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the table exists before trying to modify it
        if (Schema::hasTable('product_sizes')) {
            Schema::table('product_sizes', function (Blueprint $table) {
                // Add size category reference if it doesn't exist
                if (!Schema::hasColumn('product_sizes', 'size_category_id')) {
                    $table->foreignId('size_category_id')->nullable()->after('product_id')->constrained()->onDelete('set null');
                }

                // Add reference to standardized size (optional, for validation) if it doesn't exist
                if (!Schema::hasColumn('product_sizes', 'standardized_size_id')) {
                    $table->foreignId('standardized_size_id')->nullable()->after('size_category_id')->constrained()->onDelete('set null');
                }

                // Add index for better performance if it doesn't exist
                if (!$this->indexExists('product_sizes', ['product_id', 'size_category_id'])) {
                    $table->index(['product_id', 'size_category_id']);
                }
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists($table, $columns)
    {
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);
        foreach ($indexes as $index) {
            if ($index->getColumns() === $columns) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_sizes')) {
            Schema::table('product_sizes', function (Blueprint $table) {
                // Drop foreign keys if they exist
                if (Schema::hasColumn('product_sizes', 'size_category_id')) {
                    $table->dropForeign(['size_category_id']);
                }
                if (Schema::hasColumn('product_sizes', 'standardized_size_id')) {
                    $table->dropForeign(['standardized_size_id']);
                }

                // Drop index if it exists
                if ($this->indexExists('product_sizes', ['product_id', 'size_category_id'])) {
                    $table->dropIndex(['product_id', 'size_category_id']);
                }

                // Drop columns if they exist
                $columnsToDrop = [];
                if (Schema::hasColumn('product_sizes', 'size_category_id')) {
                    $columnsToDrop[] = 'size_category_id';
                }
                if (Schema::hasColumn('product_sizes', 'standardized_size_id')) {
                    $columnsToDrop[] = 'standardized_size_id';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
