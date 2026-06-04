<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrmFieldsToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('businesses', function (Blueprint $table) {
        $table->date('last_contacted_at')->nullable();
        $table->date('follow_up_at')->nullable();
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
            'last_contacted_at',
            'follow_up_at'
        ]);
    });
    }
}
