<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessDetailFieldsToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_source')->nullable();
            $table->decimal('collab_value', 10, 2)->nullable();
            $table->text('deliverables')->nullable();
            $table->date('booking_date')->nullable();
            $table->string('posted_url')->nullable();
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
                'phone',
                'address',
                'city',
                'state',
                'contact_source',
                'collab_value',
                'deliverables',
                'booking_date',
                'posted_url',
            ]);
        });
    }
}
