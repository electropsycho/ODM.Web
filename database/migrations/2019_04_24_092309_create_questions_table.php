<?php
/*
 * ODM.Web  https://github.com/electropsycho/ODM.Web
 * Copyright 2019-2020 Hakan GÜLEN
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Silber\Bouncer\Database\Models;

class CreateQuestionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {

    //Kazanımlar tablosu
    Schema::create("learning_outcomes", function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger("branch_id")->nullable(false);
      $table->unsignedInteger("class_level")->nullable(false);
      $table->string("code", 50)->nullable(false);
      $table->string("content", 4000)->nullable(false);
      $table->string("description", 6000)->nullable(true);
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('branch_id')
        ->references('id')->on('branches');
    });

    Schema::create("blooms_taxonomy", function (Blueprint $table) {
      $table->increments('id');
      $table->string("code", 50)->nullable(true);
      $table->string("content", 200)->nullable(false);
      $table->string("description", 6000)->nullable(true);
      $table->timestamps();
      $table->softDeletes();
    });

    //Sorular tablosu
    Schema::create('questions', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger("creator_id"); //Soru yazarı id
      $table->unsignedInteger("lesson_id"); //Ders branş id
      $table->unsignedInteger("learning_outcome_id"); //Kazanım id
      $table->unsignedInteger("taxonomy_id")->nullable(); //Kazanım id
      $table->unsignedInteger("difficulty_point")->nullable(); // 1 - 5 arası
      $table->integer("status")->default(0); // 0 - 3 arası
      $table->string("item_root", 5000);
      $table->string("content_url", 2000)->nullable();
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('creator_id')
        ->references('id')->on('users');

      $table->foreign('lesson_id')
        ->references('id')->on('branches');

      $table->foreign('learning_outcome_id')
        ->references('id')->on('learning_outcomes');

      $table->foreign('taxonomy_id')
        ->references('id')->on('blooms_taxonomy');
    });

  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('questions');
    Schema::drop('learning_outcomes');
    Schema::drop('blooms_taxonomy');
  }
}
