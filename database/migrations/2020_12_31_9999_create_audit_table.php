<?php

use \Illuminate\Database\Migrations\Migration;

class CreateAuditTable extends Migration
{
    public function up()
    {
        Schema::create('audit', function (Blueprint $table) {
            $table->char('id', 32);
            $table->string('model', 255);
            $table->string('model_id', 255);
            $table->integer('user_id')->nullable();
            $table->text('new_value')->nullable();
            $table->text('old_value')->nullable();
            $table->string('action');

            $table->primary('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('audit');
    }
}