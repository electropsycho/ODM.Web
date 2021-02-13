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

use App\Events\NewEvalReqEvent;
use App\Models\Branch;
use App\Models\Setting;
use App\User;
use Illuminate\Support\Facades\Mail;

class NewEvalReqListener
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
     * @param NewEvalReqEvent $event
     * @return void
     */
    public function handle(NewEvalReqEvent $event)
    {
        $branch = Branch::find($event->getBranchId());
        $user = User::find($event->getUserId());
        $setting = Setting::first();
        Mail::send("mails.NewEvalRequestReceived", [
            'setting' => $setting,
            'user' => $user,
            'branch' => $branch
        ],
            static function ($m) use ($setting, $user) {
                $m->from($setting->email, $setting->inst_name);
                $m->to($user->email, $user->full_name)->subject('Size bir sorumuz var');
            });
    }
}
