<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlignCreatorProfilesTableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('creator_profiles')) {
            return;
        }

        Schema::table('creator_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('creator_profiles', 'display_name')) {
                $table->string('display_name')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'instagram_handle')) {
                $table->string('instagram_handle')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'instagram_followers')) {
                $table->unsignedInteger('instagram_followers')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'tiktok_handle')) {
                $table->string('tiktok_handle')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'tiktok_followers')) {
                $table->unsignedInteger('tiktok_followers')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'youtube_handle')) {
                $table->string('youtube_handle')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'youtube_subscribers')) {
                $table->unsignedInteger('youtube_subscribers')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'bio')) {
                $table->text('bio')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'media_kit_url')) {
                $table->string('media_kit_url')->nullable();
            }

            if (!Schema::hasColumn('creator_profiles', 'email_signature')) {
                $table->text('email_signature')->nullable();
            }
        });

        if (Schema::hasColumn('creator_profiles', 'short_name') && Schema::hasColumn('creator_profiles', 'display_name')) {
            DB::table('creator_profiles')
                ->whereNull('display_name')
                ->update(['display_name' => DB::raw('short_name')]);
        }

        if (Schema::hasColumn('creator_profiles', 'follower_count') && Schema::hasColumn('creator_profiles', 'instagram_followers')) {
            DB::table('creator_profiles')
                ->whereNull('instagram_followers')
                ->update(['instagram_followers' => DB::raw('follower_count')]);
        }

        DB::table('creator_profiles')
            ->whereNull('display_name')
            ->update(['display_name' => DB::raw('name')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally left empty to avoid destructive drops in SQLite.
    }
}
