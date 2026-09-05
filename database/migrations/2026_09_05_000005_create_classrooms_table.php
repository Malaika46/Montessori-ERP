<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('age_group'); // e.g., Toddler (1.5-3), Primary (3-6), Lower Elementary (6-9), Upper Elementary (9-12)
            $table->integer('capacity')->default(25);
            $table->foreignId('lead_teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, inactive, archived
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('classrooms');
    }
};
