<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//  @TODO:  Optimize indices once all queries are known.
//  @TODO:  Add migration rollback once the schema is solidified.

class CreateVernacularTables extends Migration
{
    /**
     * Run the migrations.
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
            $table->integer('document_frequency')->unsigned();
            //  Indices
            $table->unique('word');
            $table->index(['soundex', 'word']);
            $table->index(['id', 'frequency']);
            $table->index(['id', 'document_frequency']);
        });

        //	Records which words follow each other.
        Schema::create('vernacular_bigram', function (Blueprint $table) {
            //  Columns
            $table->increments('id')->unsigned();
            $table->bigInteger('lookup_key')->unsigned();
            $table->integer('word_a_id')->unsigned();
            $table->integer('word_b_id')->unsigned();
            $table->tinyInteger('distance')->unsigned();
            $table->integer('frequency')->unsigned();
            $table->integer('document_frequency')->unsigned();
            //  Indices
            $table->unique(['word_a_id', 'word_b_id', 'distance']);
            $table->index(['lookup_key']);
        });

        //	Holds tags.
        Schema::create('vernacular_tag', function (Blueprint $table) {
            //  Columns
            $table->increments('id')->unsigned();
            $table->string('name', 64);
            $table->timestamps();
            //  Indices
            $table->unique(['name']);
        });

        //	Links bigrams to tags.
        Schema::create('vernacular_bigram_tag', function (Blueprint $table) {
            //  Columns
            $table->integer('bigram_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            //  Confidence level.
            //  Stored as a value between 0.000 and 1.000.
            $table->decimal('confidence', 4, 3);
            $table->timestamps();
            //  Indices
            $table->unique(['bigram_id', 'tag_id']);
        });
        
        

        //	Holds source identifiers.
        Schema::create('vernacular_source', function (Blueprint $table) {
            //  Columns
            $table->increments('id')->unsigned();
            $table->string('model_class', 128);
            $table->timestamps();
            //  Indices
            $table->unique(['model_class']);
        });

        //	Holds document identifiers.
        Schema::create('vernacular_document', function (Blueprint $table) {
            //  Columns
            $table->increments('id')->unsigned();
            $table->integer('source_id')->unsigned();
            $table->integer('source_model_id')->unsigned();
            $table->integer('word_count')->unsigned();
            $table->timestamps();
            //  Indices
            $table->unique(['source_id', 'source_model_id']);
        });

        //	Links document identifiers to bigrams.
        Schema::create('vernacular_document_bigram', function (Blueprint $table) {
            //  Columns
            $table->integer('document_id')->unsigned();
            $table->integer('bigram_id')->unsigned();
            $table->integer('frequency')->unsigned();
            //  The first instance of this bigram in this document.
            //  Stored as a value between 0.000 and 1.000.
            $table->decimal('first_instance', 4, 3);
            //  Indices
            $table->unique(['document_id', 'bigram_id']);
        });

        //	Links document identifiers to tags.
        Schema::create('vernacular_document_tag', function (Blueprint $table) {
            //  Columns
            $table->integer('document_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            //  Confidence level, if document was not explicitly tagged.
            //  Stored as a value between 0.000 and 1.000.
            $table->decimal('confidence', 4, 3);
            $table->timestamps();
            //  Indices
            $table->unique(['document_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
