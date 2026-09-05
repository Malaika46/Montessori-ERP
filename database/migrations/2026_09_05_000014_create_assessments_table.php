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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->string('title');
            $table->string('evaluation_period')->default('Jan 2026 - Jun 2026');
            $table->string('term_name')->default('Term 1');
            $table->integer('practical_life_score')->default(90);
            $table->enum('practical_life_status', ['Introduced', 'Working', 'Mastered'])->default('Mastered');
            $table->integer('sensorial_score')->default(90);
            $table->enum('sensorial_status', ['Introduced', 'Working', 'Mastered'])->default('Mastered');
            $table->integer('mathematics_score')->default(85);
            $table->enum('mathematics_status', ['Introduced', 'Working', 'Mastered'])->default('Working');
            $table->integer('language_score')->default(85);
            $table->enum('language_status', ['Introduced', 'Working', 'Mastered'])->default('Introduced');
            $table->integer('cultural_score')->default(88);
            $table->enum('cultural_status', ['Introduced', 'Working', 'Mastered'])->default('Mastered');
            $table->integer('overall_score')->default(88);
            $table->text('overall_summary')->nullable();
            $table->enum('status', ['draft', 'review', 'released'])->default('released');
            $table->date('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
