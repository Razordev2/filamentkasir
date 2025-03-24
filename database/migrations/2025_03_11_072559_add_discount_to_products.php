<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('discount_code')->nullable()->after('price');
            $table->decimal('discounted_price', 10, 2)->nullable()->after('discount_code');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_code', 'discounted_price']);
        });
    }
};
