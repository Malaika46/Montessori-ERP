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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'tardy', 'excused'])->default('present');
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'date']);
        });

        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
            $table->string('title');
            $table->string('avenue')->default('Practical Life');
            $table->text('notes');
            $table->enum('mastery_level', ['Introduced', 'Working', 'Mastered'])->default('Working');
            $table->date('observed_at');
            $table->boolean('is_family_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->string('title');
            $table->string('avenue')->default('Practical Life');
            $table->text('description')->nullable();
            $table->text('materials_needed')->nullable();
            $table->date('scheduled_date');
            $table->enum('status', ['planned', 'presented', 'completed'])->default('planned');
            $table->timestamps();
        });

        Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('avenue')->default('Practical Life');
            $table->string('age_group')->nullable();
            $table->text('description')->nullable();
            $table->text('learning_objectives')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_items');
        Schema::dropIfExists('lesson_plans');
        Schema::dropIfExists('observations');
        Schema::dropIfExists('attendances');
    }
};
