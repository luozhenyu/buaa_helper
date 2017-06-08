<?php

namespace App\Jobs;

use App\Func\Pusher;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $notification;
    private $users;

    /**
     * Create a new job instance.
     * @param Notification $notification
     * @param mixed $users
     */
    public function __construct(Notification $notification, $users)
    {
        $this->users = (array)$users;
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pusher = new Pusher();
        $pusher->setText($this->notification->excerpt);

        foreach ($this->users as $user) {
            foreach ($user->devices as $device) {
                if ($device->isValid()) {
                    $pusher->addDevice($device);
                }
            }
        }

        $pusher->send();
    }
}
