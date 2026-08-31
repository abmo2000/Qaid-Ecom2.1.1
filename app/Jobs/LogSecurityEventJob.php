<?php

namespace App\Jobs;

use App\Models\SecurityEvent;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogSecurityEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $payload)
    {
        // Payload should contain all SecurityEvent fields.
    }

    public function handle(): void
    {
        SecurityEvent::create($this->payload);
    }
}
