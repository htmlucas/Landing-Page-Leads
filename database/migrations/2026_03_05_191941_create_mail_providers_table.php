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
        Schema::create('mail_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // mailchimp, activecampaign
            $table->string('api_key');
            $table->string('server_prefix')->nullable(); // Para Mailchimp, ex: us6
            $table->string('list_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_providers');
    }
};
