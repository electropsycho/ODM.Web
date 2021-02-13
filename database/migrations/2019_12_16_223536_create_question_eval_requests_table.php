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

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionEvalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_eval_requests', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('elector_id')->nullable(false);
            $table->unsignedInteger('creator_id')->nullable(false);
            $table->unsignedInteger('question_id')->nullable(false);
            $table->string('code', 50)->nullable(true);
            $table->string('comment', 1000)->nullable(true);
            $table->unsignedInteger('point')->nullable(true);
            $table->boolean('is_open')->default(true);
            $table->timestamps();

            $table->foreign('elector_id')
                ->references('id')->on('users');

            $table->foreign('creator_id')
                ->references('id')->on('users');

            $table->foreign('question_id')
                ->references('id')->on('questions');
        });

        Schema::create('question_revision_requests', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('creator_id')->nullable(false);
            $table->unsignedInteger('question_id')->nullable(false);
            $table->string('title', 1000)->nullable(true);
            $table->string('comment', 1000)->nullable(true);
            $table->timestamps();

            $table->foreign('question_id')
                ->references('id')->on('questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_eval_requests');
        Schema::dropIfExists('question_revision_requests');
    }
}
