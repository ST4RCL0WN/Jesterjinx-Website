<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCharacterWarningToCharacterImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_images', function (Blueprint $table) {
            //
            $table->json('content_warnings')->nullable()->default(null);
        });

        Schema::table('user_settings', function(Blueprint $table) {
            $table->tinyInteger('content_warning_visibility')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_images', function (Blueprint $table) {
            //
            $table->dropColumn('content_warnings');
        });

        Schema::table('user_settings', function(Blueprint $table) {
            $table->dropcolumn('content_warning_visibility');
        });
    }
}