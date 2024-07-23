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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('father_name');
            $table->string('passport_number');
            $table->unsignedBigInteger('branch_id'); // Ensure this is unsignedBigInteger
            $table->string('reporting_manager');
            $table->string('employement_status');

            $table->string('name');
            $table->string('contact_number');
            $table->string('cnic_number');
            $table->string('email')->unique();
            $table->date('dob');
            $table->string('shift');
            $table->unsignedBigInteger('department_id'); // Ensure this is unsignedBigInteger
            $table->date('hiring_date');
            $table->decimal('salary', 10, 2);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};