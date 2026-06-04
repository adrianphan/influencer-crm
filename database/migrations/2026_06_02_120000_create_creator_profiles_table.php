<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreatorProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name');
            $table->string('instagram_handle')->nullable();
            $table->unsignedInteger('instagram_followers')->nullable();
            $table->string('tiktok_handle')->nullable();
            $table->unsignedInteger('tiktok_followers')->nullable();
            $table->string('youtube_handle')->nullable();
            $table->unsignedInteger('youtube_subscribers')->nullable();
            $table->string('location');
            $table->string('niche');
            $table->text('bio')->nullable();
            $table->string('media_kit_url')->nullable();
            $table->text('email_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creator_profiles');
    }
}
