<?php

namespace App\Jobs;

use App\Func\Pusher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $text;
    private $users;

    /**
     * Create a new job instance.
     * @param $text
     * @param mixed $users
     */
    public function __construct($text, $users)
    {
        $this->users = is_array($users) ? $users : [$users];
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pusher = new Pusher();
        $pusher->setText($this->text);

        foreach ($this->users as $user) {
            foreach ($user->devices as $device) {
                if ($device->isValid()) {
                    $pusher->addDevice($device);
                }
            }
        }

        $pusher->send(function ($e) {
            print $e;
        });
    }
}
