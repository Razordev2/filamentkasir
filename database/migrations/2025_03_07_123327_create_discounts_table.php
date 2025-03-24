<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed', 'buy1get1', 'voucher']);
            $table->decimal('value', 10, 2)->nullable();
            $table->integer('quota')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });        
    }
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
