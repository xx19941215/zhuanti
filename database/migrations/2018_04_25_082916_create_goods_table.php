<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->bigInteger('id', false, true);
            $table->integer('shop_id', false, true);
            $table->string('img')->nullable();
            $table->tinyInteger('is_competition')->default(0);
            $table->integer('vote_count')->default(0);
            $table->string('title')->nullable();
            $table->string('video')->nullable();
            $table->text('desc')->nullable();
            $table->primary('id');
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
        Schema::dropIfExists('goods');
    }
}
