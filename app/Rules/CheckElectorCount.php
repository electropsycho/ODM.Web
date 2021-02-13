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

namespace App\Rules;

use App\Models\QuestionEvalRequest;
use App\Models\Setting;
use Illuminate\Contracts\Validation\Rule;

class CheckElectorCount implements Rule
{
    private $count = 0;
    private $max = 0;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($questionId, $code)
    {
        $this->count = QuestionEvalRequest::where('question_id', $questionId)
            ->where('code', $code)
            ->count();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // TODO Toplam değerlendirici sayısı Settings tablosu içine atılıp parametrik hale getirilebilir.
        // Toplam değerlendirici sayısı 5 i geçemez
        $this->max = Setting::select('max_elector_count')->first()->max_elector_count;
        return $this->max >= $this->count + count($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Toplam değerlendirici sayısı azami '.$this->max.' olmalıdır';
    }
}
