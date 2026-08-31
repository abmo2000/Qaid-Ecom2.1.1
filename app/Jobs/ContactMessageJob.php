<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMessageJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
                    $email = config('mail.to.address')
                            ?? getBuisnessSettings('buisness-info')?->email
                            ?? config('mail.from.address');
        Mail::send('web.emails.contact', $this->data, function ($message) use ( $email) {
            $message->to($email) 
                    ->subject('New Contact Form Submission from ' . $this->data['name'])
                    ->replyTo($this->data['email'], $this->data['name']);
        });


    } catch (\Throwable $th) {
   
        Log::info('mai error' , ['message' => $th->getMessage()]);
    }
    }
}
