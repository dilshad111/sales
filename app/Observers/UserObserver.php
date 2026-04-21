<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        if (in_array($user->role, ['Agent', 'Principal'])) {
            Account::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'type' => strtolower($user->role),
                    'status' => 'active',
                ]
            );
        }
    }

    public function updated(User $user): void
    {
        if (in_array($user->role, ['Agent', 'Principal'])) {
            $account = Account::where('user_id', $user->id)->first();
            if ($account) {
                $account->update([
                    'name' => $user->name,
                    'type' => strtolower($user->role)
                ]);
            } else {
                $this->created($user);
            }
        }
    }

    public function deleted(User $user): void
    {
        $account = Account::where('user_id', $user->id)->first();
        if ($account) {
            $account->update(['status' => 'inactive']);
        }
    }
}
