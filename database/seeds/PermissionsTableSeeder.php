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

use Illuminate\Database\Seeder;
use Silber\Bouncer\Bouncer;

class PermissionsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $bouncer = Bouncer::create();

    $bouncer->ability()->firstOrCreate([
      'name' => 'manage-user',
      'title' => 'Kullanıcı yönetimi yetkisi',
    ]);

    $bouncer->ability()->firstOrCreate([
      'name' => 'manage-exams',
      'title' => 'Sınav yönetimi yetkisi',
    ]);

    $bouncer->ability()->firstOrCreate([
      'name' => 'manage-question',
      'title' => 'Soru yönetimi yetkisi',
    ]);

    $bouncer->ability()->firstOrCreate([
      'name' => 'manage-branch',
      'title' => 'Branş/Ders yönetim yetkisi',
    ]);

    $bouncer->ability()->firstOrCreate([
      'name' => 'manage-learning-outcome',
      'title' => 'Kazanım yönetim yetkisi',
    ]);

    $bouncer->ability()->firstOrCreate([
      'name' => 'add-question',
      'title' => 'Soru ekleme yetkisi',
    ]);

    $bouncer->ability()->firstOrCreate([
      'name' => 'delete-question',
      'title' => 'Soru silme yetkisi',
    ]);

    $bouncer->ability()->firstOrCreate([
      'name' => 'edit-question',
      'title' => 'Soru düzenleme yetkisi',
    ]);

    $bouncer->ability()->firstOrCreate([
      'name' => 'elect-question',
      'title' => 'Soru oylama yetkisi',
    ]);
  }
}
