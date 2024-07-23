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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('address');
            $table->string('country');
            $table->string('city');
            $table->string('telephone');
            $table->string('res')->nullable();
            $table->string('fax')->nullable();
            $table->string('s_man');
            $table->string('mobile');
            $table->string('strn')->nullable();
            $table->string('ntn')->nullable();
            $table->date('date');
            $table->string('balance_type');
            $table->decimal('opening_balance', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
