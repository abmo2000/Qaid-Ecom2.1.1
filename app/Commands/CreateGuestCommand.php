<?php

namespace App\Commands;

use App\Models\User;
use App\Models\Guest;
use Illuminate\Support\Facades\Auth;


class CreateGuestCommand{


    public function handle(array $data, \Closure $next)
    {
        if (!Auth::check()) {
            $guest = Guest::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'] ?? null,
                    'phone' => $data['phone'] ?? null,
                ]
            );
            
            $data['customer_id'] = $guest->id;
            $data['customer_type'] = "guest";
        } else {
            $data['customer_id'] = Auth::user()->id;
            $data['customer_type'] = "user";
        }

        return $next($data);
    }
}