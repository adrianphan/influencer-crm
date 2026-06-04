<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGmailDraftFieldsToGeneratedEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('generated_emails', function (Blueprint $table) {
            $table->string('draft_id')->nullable()->after('body');
            $table->timestamp('draft_created_at')->nullable()->after('draft_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('generated_emails', function (Blueprint $table) {
            $table->dropColumn(['draft_id', 'draft_created_at']);
        });
    }
}
