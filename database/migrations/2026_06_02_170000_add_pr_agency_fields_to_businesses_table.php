<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrAgencyFieldsToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->text('agency_specialties')->nullable();
            $table->string('pr_contact_role')->nullable();
            $table->text('client_types')->nullable();
            $table->string('roster_status')->nullable();
            $table->date('media_kit_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'agency_specialties',
                'pr_contact_role',
                'client_types',
                'roster_status',
                'media_kit_sent_at',
            ]);
        });
    }
}
