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
        Schema::create('job_application_mentions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['user', 'group']);
            $table->unsignedBigInteger('mention_id');

            $table->unsignedBigInteger('job_application_id');
            $table->foreign('job_application_id')->references('id')->on('job_applications')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_application_mentions');
    }
};
