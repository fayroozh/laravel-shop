<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check if the columns exist before trying to drop them to avoid errors
            if (Schema::hasColumn('orders', 'product_id')) {
                // Drop foreign key constraint first if it exists
                // The name of the constraint might vary, you can find it in your DB schema viewer
                // Common naming convention is orders_product_id_foreign
                try {
                    $table->dropForeign(['product_id']);
                } catch (\Exception $e) {
                    // Ignore if the foreign key doesn't exist
                }
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('orders', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('orders', 'price')) {
                $table->dropColumn('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add the columns back if we need to revert the migration
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('quantity')->nullable();
            $table->decimal('price', 8, 2)->nullable();
        });
    }
};