<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderMatched implements ShouldBroadcast
{
    use SerializesModels;

    public $data; // ye hi frontend me milega

    private $userId;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param array $data
     */
    public function __construct(int $userId, array $data)
    {
        $this->userId = $userId;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    /**
     * Custom event name for frontend
     */
    public function broadcastAs(): string
    {
        return 'order.matched';
    }
}
