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

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sınav bilgilerini tutacak sınıfımız
 * Class Exam
 * @package App\Models
 */
class Exam extends Model
{
    //Sınava ait durmumsallık bilgileri
    const CREATED = 5000;       //Sınav oluşturuldu
    const PLANNED = 5001;       //Sınava ait dersler ve sorular seçildi
    const CANCELED = 5002;      //İptal edildi
    const COMPLETED = 5003;     //Sınav tamamlandı

    protected $fillable = [
        "creator_id",
        'purpose_id',
        "status",
        'code',
        "title",
        "class_level",
        "planned_date",
        'date_of_occurrence',
        "description"
    ];
}
