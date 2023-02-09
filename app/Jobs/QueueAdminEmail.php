<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Mail;

use App\Mail\SendMail;
use App\Models\User;

class QueueAdminEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        // QueueGenericEmail::dispatch($data);

        $admins = User::where('type','=','admin')->orWhere('type','=','super admin')->get();
        
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new SendMail($data)); 
        }

        Mail::to('signupknoxville@gmail.com')->send(new SendMail($data)); 
        Mail::to('codes.kenjimagto@gmail.com')->send(new SendMail($data)); 
        
    }
}
