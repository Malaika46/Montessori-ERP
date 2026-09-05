<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. LMS Learning Paths
        Schema::create('lms_learning_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('montessori_domain')->default('Practical Life'); // Practical Life, Sensorial, Language, Mathematics, Cultural
            $table->boolean('is_published')->default(true);
            $table->string('status')->default('active'); // active, inactive, archived
            $table->timestamps();
        });

        // 2. LMS Activities
        Schema::create('lms_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained('lms_learning_paths')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('type')->default('exercise'); // quiz, game, exercise, skill_node, challenge
            $table->integer('xp_points')->default(50);
            $table->json('config_json')->nullable();
            $table->boolean('is_published')->default(true);
            $table->string('status')->default('active'); // active, inactive, archived
            $table->timestamps();
        });

        // 3. LMS Student Progress
        Schema::create('lms_student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('lms_activities')->onDelete('cascade');
            $table->string('status')->default('in_progress'); // in_progress, completed, mastered
            $table->integer('xp_earned')->default(0);
            $table->integer('score')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 4. LMS Student Rewards & Badges
        Schema::create('lms_student_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('badge_name');
            $table->string('badge_icon')->default('bi-trophy-fill');
            $table->integer('streak_count')->default(1);
            $table->integer('total_xp')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lms_student_rewards');
        Schema::dropIfExists('lms_student_progress');
        Schema::dropIfExists('lms_activities');
        Schema::dropIfExists('lms_learning_paths');
    }
};
