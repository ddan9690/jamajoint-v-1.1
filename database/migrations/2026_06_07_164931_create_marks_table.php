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
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('paper_id')->nullable()->constrained('papers');
            $table->foreignId('school_id')->constrained('schools');
            $table->foreignId('form_id')->constrained('forms');
            $table->foreignId('stream_id')->constrained('streams');
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
