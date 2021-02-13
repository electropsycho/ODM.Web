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

class CreateAndAlterExamTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('exams', function (Blueprint $table) {
//            $table->integer('');
//        });




        Schema::table('exams', function (Blueprint $table) {
            $table->integer('purpose_id')->after("status_id");
            $table->string('code',50)->after("purpose_id");
            $table->dateTime('date_of_occurrence')->nullable();
            $table->renameColumn('status_id', 'status');
        });


        Schema::create('exam_purposes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purpose', 500);
            $table->string('code', 20)->nullable();
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
        Schema::dropIfExists('exam_purposes');
    }
}
