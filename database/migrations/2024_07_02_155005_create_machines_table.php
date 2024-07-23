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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->string('code', 255)->unique();
            $table->unsignedBigInteger('manufacturer_id');
            $table->string('name', 255);
            $table->string('number', 255)->unique();
            $table->date('purchased_date');
            $table->date('model_date');
            $table->integer('capacity')->unsigned();
            $table->float('production_speed')->unsigned();
            $table->decimal('price', 15, 2)->unsigned();
            $table->date('warranty');
            $table->json('attachments')->nullable(); // Add this line
            $table->string('remarks', 1000)->nullable();
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
