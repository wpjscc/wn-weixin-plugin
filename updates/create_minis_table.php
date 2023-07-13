<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class CreateTestTable extends Migration
{
    public function up()
    {
        Schema::create('wechat_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('open_id')->nullable();
            $table->tinyInteger('wx_sex')->default(0);
            $table->string('wx_name')->nullable();
            $table->string('wx_avatar')->nullable();
            $table->string('miniprogram_session_key')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wechat_users');
    }
}
