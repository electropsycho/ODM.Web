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

namespace App\Traits;


trait ValidationTrait
{
    public $MESSAGES = [
            'password.regex' => 'Şifre en az: 1 büyük harf, 1 küçük harf, 1 rakam, ve bir özel karakter içermelidir(. , + @ # % vs.)',
        ];

    public $ATTRIBUTES = [
        'learning_outcome_id' => 'Kazanım',
        'difficulty' => 'Zorluk',
        'question_file' => 'Soru dosyası',
        'password' => 'Şifre',
        'token' => 'Jeton',
        'email' => 'E-Posta',
        'full_name' => 'Ad soyad',
        'phone' => 'Telefon numarası',
        'inst_id' => 'Okul/Kurum kodu',
        'branch_id' => 'Alan/Branş',
        'minRequiredElection'=> 'Manuel hesaplama için gerekli en az puanlama sayısı',
        'electors' => 'Değerlendiriciler'
    ];
}