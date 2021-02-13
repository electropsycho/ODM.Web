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

namespace App\Events;

use App\Models\QuestionEvalRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class QuestionEvalCalculateRequired
{
//    use Dispatchable, InteractsWithSockets, SerializesModels;
    use Dispatchable, SerializesModels;
    /**
     * @var QuestionEvalRequest
     */
    private $request;

    /**
     * Create a new event instance.
     *
     * @param QuestionEvalRequest $request
     */
    public function __construct(QuestionEvalRequest $request)
    {
        $this->request = $request;
    }

    public function getRequest(): QuestionEvalRequest
    {
        return $this->request;
    }

//    /**
//     * Get the channels the event should broadcast on.
//     *
//     * @return \Illuminate\Broadcasting\Channel|array
//     */
//    public function broadcastOn()
//    {
//        return new PrivateChannel('channel-name');
//    }
}
