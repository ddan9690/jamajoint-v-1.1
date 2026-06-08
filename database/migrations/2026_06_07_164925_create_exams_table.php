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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('term_id')->constrained('terms');
            $table->foreignId('form_id')->constrained('forms');
            $table->foreignId('grading_system_id')->nullable()->constrained('grading_systems');
            $table->enum('status', ['draft', 'processing', 'finalized'])->default('draft');
            $table->enum('visibility', ['private', 'teachers', 'public'])->default('private');
            $table->enum('mark_submission_mode', ['teachers', 'admins'])->default('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
