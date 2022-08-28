<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_shop');
            $table->string('name',255);
            $table->string('thumb',255);
            $table->string('subject',255);
            $table->text('email_content');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return 
     */
    public function down()
    {
        
        Schema::dropIfExists('campaigns');
    }
}
