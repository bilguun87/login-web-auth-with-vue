<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupmatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupmatrix', function (Blueprint $table) {
            $table->id();
            $table->string('br_id')->nullable();
            $table->string('br_name')->nullable();
            $table->string('pos_id')->nullable();
            $table->string('pos_name')->nullable();
            $table->string('objectguid');
            $table->string('cn');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groupmatrix');
    }
}
