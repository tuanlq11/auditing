<?php

use \Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuditTable extends Migration
{
    protected $table_name = 'audits';

    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
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
        Schema::drop($this->table_name);
    }
}