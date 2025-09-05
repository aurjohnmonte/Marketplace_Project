<?php

namespace App\Events;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $id;
    public $status;
    public function __construct($id, $status = 'follower')
    {
        $this->id = $id;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('id', ['id'=>$this->id]);
        Log::info('status', ['status' => $this->status]);
        
        if($this->status === 'follower'){
            $followers = User::select('users.name')
                            ->join('followers', 'users.id', '=', 'followers.follower_id')
                            ->where('followers.user_id', '=', $this->id)
                            ->get();

            Log::info('followers', ['followers'=>$followers]);

            $arr_user = [];

            if($followers->isEmpty()){
                return [];
            }

            foreach($followers as $follower){
                array_push($arr_user, new Channel("notify.{$follower->name}"));
            }
        }
        else{
            $follower = User::select('name')->where('id', $this->id)->first();
            Log::info('follower', ['follower' => $follower]);
            if(!$follower){
                return [];
            }

            $arr_user = [new Channel("notify.{$follower->name}")];
        }

        return $arr_user;
    }

    public function broadcastAs(): string
    {
        return "notify.sent";
    }
}
