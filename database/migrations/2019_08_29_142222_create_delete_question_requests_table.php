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

class CreateDeleteQuestionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_delete_requests', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger("question_id")->nullable(true);
            $table->unsignedInteger("creator_id");
            $table->unsignedInteger("lo_id");
            $table->string("comment", 500);
            $table->string("keywords", 500);
            $table->boolean("is_accepted")->nullable(true);
            $table->boolean("acceptor_id")->nullable(true);
            $table->timestamps();
//            $table->foreign('creator_id')
//                ->references('id')->on('questions')
//            $table->foreign('acceptor_id')
//                ->references('id')->on('questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delete_question_requests');
    }
}
