<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('name', 50);
            $table->char('school_name', 50);
            $table->string('email');
            $table->boolean('is_email_verified')->default(false);
            $table->string('email_verify_token')->nullable();
            $table->date('email_verify_date')->nullable();
            $table->string('password');
            $table->string('change_password_token')->nullable();
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
        Schema::dropIfExists('users');
    }
}
