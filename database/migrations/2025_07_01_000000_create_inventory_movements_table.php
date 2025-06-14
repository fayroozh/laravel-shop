<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('type'); // 'in', 'out', 'adjustment'
            $table->string('reference_type')->nullable(); // 'order', 'manual', 'return'
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of order or other reference
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained(); // User who made the change
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_movements');
    }
};