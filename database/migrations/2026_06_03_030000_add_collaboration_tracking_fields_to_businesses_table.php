<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollaborationTrackingFieldsToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->decimal('compensation', 10, 2)->nullable()->after('deliverables');
            $table->date('posting_date')->nullable()->after('booking_date');
            $table->string('payment_status')->nullable()->after('posted_url');
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
                'compensation',
                'posting_date',
                'payment_status',
            ]);
        });
    }
}
