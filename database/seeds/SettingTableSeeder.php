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

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::firstOrCreate(
          [
            "inst_name" => "Nevşehir Ölçme Değerlendirme Merkezi",
            "governor" => "Nevşehir Valiliği",
            "directorate" => "Nevşehir Milli Eğitim Müdürlüğü",
            "twitter_address" => "https://twitter.com/nevsehirodm",
            "web_address" => "https://nevsehirodm.meb.gov.tr/",
            "phone" => "0384 213 79 33 Dahili:178",
            "email" => "odm50@meb.gov.tr",
            "address" => "350 Evler Mah. Yeni Kayseri Cad. Hükümet Konağı Kat:2"
          ]);
    }
}
