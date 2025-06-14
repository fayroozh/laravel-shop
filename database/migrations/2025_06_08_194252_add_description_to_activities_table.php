<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->string('description');
                $table->string('icon')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->timestamps();
            });
        } else if (!Schema::hasColumn('activities', 'description')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->string('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('activities', 'description')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};