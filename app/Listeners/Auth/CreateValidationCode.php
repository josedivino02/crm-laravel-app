<?php

namespace App\Listeners\Auth;

use App\Events\SendNewCode;
use App\Models\User;
use App\Notifications\Auth\ValidationCodeNotification;
use Illuminate\Auth\Events\Registered;

class CreateValidationCode
{
    public function handle(Registered | SendNewCode $event): void
    {
        $user = $event->user;

        if ($user instanceof User) {
            $user->validation_code = random_int(100000, 999999);

            $user->save();

            $user->notify(new ValidationCodeNotification());
        }
    }
}
