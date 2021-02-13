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

use App\Events\NewUserReqReceived;
use App\Models\Setting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class  NewUserReqListener
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
   * @param NewUserReqReceived $event
   * @return void
   */
  public function handle(NewUserReqReceived $event)
  {
      $user = $event->getUserReq();
      $setting = Setting::first();
      Mail::send("mails.NewUserRequestReceived", ['setting' => $setting, "user" => $user],
          function ($m) use ($setting, $user) {
              $m->from($setting->email, $setting->inst_name);
              $m->to($user->email, $user->full_name)->subject('Talebinizi aldık ;-)');
          });
  }
}
