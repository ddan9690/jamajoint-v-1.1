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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->enum('role', ['super_admin', 'exam_admin', 'teacher'])->default('teacher');
            $table->string('phone')->nullable();
            $table->string('password');
            $table->foreignId('county_id')->nullable()->constrained('counties');
            $table->foreignId('school_id')->nullable()->constrained('schools');
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_active')->default(1);
            $table->string('last_login_ip')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
