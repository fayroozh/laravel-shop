<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        if (!Schema::hasColumn('orders', 'user_id')) {
            $table->bigInteger('user_id')->unsigned()->nullable()->after('product_id');
        }
    });
}



public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}

};
