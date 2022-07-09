<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watch', function (Blueprint $table) {
            $table->string('user_id');
            $table->string('title');
            $table->string('tvdb_id');
            $table->string('profile_id');
            $table->string('title_slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('watch', function (Blueprint $table) {
            //
        });
    }
};
