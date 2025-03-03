<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\Traits;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    use Traits;
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {   if($user->email !=null){
            $this->sendWelcomeMail($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
