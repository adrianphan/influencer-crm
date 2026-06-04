<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneratedEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::create('generated_emails', function (Blueprint $table) {
    $table->id();
    $table->foreignId('business_id')->constrained()->cascadeOnDelete();
    $table->string('type')->default('outreach');
    $table->string('subject');
    $table->text('body');
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
        Schema::dropIfExists('generated_emails');
    }
}
