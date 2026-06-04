<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('category')->nullable();
    $table->string('website')->nullable();
    $table->string('instagram')->nullable();
    $table->string('email')->nullable();
    $table->string('contact_name')->nullable();
    $table->string('status')->default('Lead Found');
    $table->integer('fit_score')->nullable();
    $table->text('notes')->nullable();
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
        Schema::dropIfExists('businesses');
    }
}
