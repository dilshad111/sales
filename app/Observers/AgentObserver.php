<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Agent;

class AgentObserver
{
    public function created(Agent $agent): void
    {
        Account::create([
            'name' => $agent->name,
            'type' => 'agent',
            'agent_id' => $agent->id,
            'phone' => $agent->phone,
            'email' => $agent->email,
            'address' => $agent->address,
            'status' => $agent->status ?? 'active',
        ]);
    }

    public function updated(Agent $agent): void
    {
        $account = Account::where('agent_id', $agent->id)->first();
        if ($account) {
            $account->update([
                'name' => $agent->name,
                'phone' => $agent->phone,
                'email' => $agent->email,
                'address' => $agent->address,
                'status' => $agent->status ?? 'active',
            ]);
        }
    }
}
