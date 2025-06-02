<?php

namespace App\Services;

use Pusher\PushNotifications\PushNotifications;

class PusherBeamsService
{
    protected $beamsClient;

    public function __construct()
    {
        $instanceId = config('services.beams.instance_id');
        $secretKey = config('services.beams.secret_key');

        if (empty($instanceId) || empty($secretKey)) {
            return redirect()->back()->with('failed', trans('Pusher Beams instance ID or secret key is missing.'));
        }

        $this->beamsClient = new PushNotifications([
            'instanceId' => $instanceId,
            'secretKey'  => $secretKey,
        ]);
    }

    /**
     * Send a notification to all devices subscribed to an interest.
     *
     * @param string $interest
     * @param array $notification
     * @return mixed
     */
    public function broadcastToInterest(string $interest, array $notification)
    {
        // Ensure that the beamsClient is initialized before trying to use it
        if (!$this->beamsClient) {
            return redirect()->back()->with('failed', trans('Pusher Beams instance ID or secret key is missing.'));
        }

        return $this->beamsClient->publishToInterests(
            [$interest],
            [
                'web' => [
                    'notification' => $notification,
                ],
            ]
        );
    }
}
