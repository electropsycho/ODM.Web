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

namespace App\Listeners;

use App\Events\QuestionEvalCalculateRequired;
use App\Logic\Question\EngineConstants;
use App\Logic\Question\QuestionEvalEngine;
use App\Models\Question;
use App\Models\QuestionEvalRequest;
use App\Traits\QuestionEvalTraits;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionEvaulationListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param QuestionEvalCalculateRequired $event
     * @return void
     * @throws Exception
     */
    public function handle(QuestionEvalCalculateRequired $event): void
    {
        try {
            $qer = $event->getRequest();
            QuestionEvalEngine::getInstance()
                ->setQuestionId($qer->question_id)
                ->setCode($qer->code)
                ->setCalculationType(EngineConstants::AUTOMATIC)
                ->calculate();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            // throw $exception;
        }
    }
}
