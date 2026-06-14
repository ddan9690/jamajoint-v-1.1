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
        Schema::create('exam_result_summaries', function (Blueprint $table) {
            $table->id();

            // 🔗 Snapshot link
            $table->foreignId('exam_id')->unique()->constrained('exams')->onDelete('cascade');

            // 🏫 Participation stats
            $table->unsignedInteger('total_schools')->default(0);
            $table->unsignedInteger('total_students')->default(0);
            $table->unsignedInteger('eligible_students')->default(0);

            // 📊 Core performance metrics
            $table->decimal('mean_score', 8, 4)->default(0);
            $table->string('mean_grade')->nullable();

            // ✅ Pass metrics
            $table->decimal('pass_percentage', 5, 2)->default(0);

            // 🏆 Extremes (VERY useful for dashboard)
            $table->unsignedBigInteger('top_student_id')->nullable();
            $table->unsignedBigInteger('top_school_id')->nullable();

            // 📈 Optional but powerful
            $table->decimal('highest_score', 8, 4)->nullable();
            $table->decimal('lowest_score', 8, 4)->nullable();

            // ⚙️ Snapshot control (important for your system design)
            $table->enum('status', ['processing', 'ready', 'archived'])
                ->default('processing');

            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            // 🚀 Indexes for fast lookup
            $table->index(['exam_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_result_summaries');
    }
};
