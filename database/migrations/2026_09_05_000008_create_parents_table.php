<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null');
            $table->string('phone')->nullable();
            $table->string('occupation')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->default('active'); // active, inactive, suspended, archived
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parents');
    }
};
