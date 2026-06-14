<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('paper_id')->nullable()->constrained('papers')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->foreignId('stream_id')->constrained('streams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->integer('score');
            $table->timestamps();

            $table->index(['exam_id', 'school_id'], 'idx_exam_school');
            $table->index(['exam_id', 'stream_id'], 'idx_exam_stream');
            $table->index(['exam_id', 'student_id'], 'idx_exam_student');
            $table->index(['exam_id', 'score'], 'idx_exam_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};