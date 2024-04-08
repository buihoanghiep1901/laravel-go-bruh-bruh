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
        Schema::table('job_stages', function (Blueprint $table) {
            $table->unsignedBigInteger('email_template_id')->nullable()->after('id');
            $table->foreign('email_template_id')->references('id')->on('email_templates');
            $table->unsignedBigInteger('interview_template_id')->nullable()->after('id');
            $table->foreign('interview_template_id')->references('id')->on('interview_templates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_stages', function (Blueprint $table) {
            $table->dropColumn(['email_template_id', 'interview_template_id']);
        });
    }
};
