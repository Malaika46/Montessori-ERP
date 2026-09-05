<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null');
            $table->string('audience_type')->default('all_parents'); // all_parents, all_students, specific_classroom, specific_students, specific_parents, direct_user
            $table->foreignId('target_classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('sent'); // draft, sent, archived
            $table->timestamps();
        });

        Schema::create('communication_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('communication_recipients');
        Schema::dropIfExists('communications');
    }
};
