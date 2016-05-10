<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVernacularTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //	Holds single words.
        Schema::create('vernacular_word', function (Blueprint $table) {
            //  Columns
            $table->increments('id')->unsigned();
            $table->char('soundex', 4);
            $table->string('word', 64);
            $table->integer('frequency')->unsigned();
            //  Indices
            $table->unique('word');
            $table->index(['soundex', 'word']);
            $table->index(['id', 'frequency']);
        });
        
        //	Records which words follow each other.
        Schema::create('vernacular_bigram', function (Blueprint $table) {
            //  Columns
            $table->increments('id')->unsigned();
            $table->integer('word_a_id')->unsigned();
            $table->integer('word_b_id')->unsigned();
            $table->integer('distance')->unsigned();
            $table->integer('frequency')->unsigned();
            //  Indices
            $table->unique(['word_a_id', 'word_b_id', 'distance']);
        });
        
        //	Holds tags.
        Schema::create('vernacular_tag', function (Blueprint $table) {
            //  Columns
            $table->increments('id')->unsigned();
            $table->string('name', 64);
            //  Indices
            $table->unique(['name']);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //  @TODO
    }
}
