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

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   * @return void
   */
  public function up()
  {
    Schema::create("units", function (Blueprint $table) {
      $table->increments("id");
      $table->string("name", 200);
      $table->string("code", 10);
    });

    Schema::create("institutions", function (Blueprint $table) {
        $table->unsignedInteger("id")->primary();
        $table->unsignedInteger("unit_id");
        $table->string("province")->nullable();
        $table->string("name");
        $table->string("phone")->nullable();

        $table->foreign('unit_id')
            ->references('id')->on('units');
    });

    //Dersler/Alanlar tablosu
    Schema::create("branches", function (Blueprint $table) {
      $table->increments('id');
      $table->string("name", 50);
      $table->string("code", 10)->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('users', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger("branch_id")->nullable(); //Alan id
      $table->unsignedInteger("inst_id")->nullable(); //Kurum id
      $table->unsignedInteger("activator_id")->nullable(); //Kurum id
      $table->string('full_name');
      $table->string('phone');
      $table->string('email')->unique();
      $table->string('password')->nullable();
      $table->date('activation_date')->nullable();
      $table->rememberToken();
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('branch_id')
        ->references('id')->on('branches');

      $table->foreign('inst_id')
        ->references('id')->on('institutions');

      $table->foreign('activator_id')
        ->references('id')->on('users');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('users');
    Schema::drop('branches');
    Schema::drop('institutions');
    Schema::drop('units');

  }
}
