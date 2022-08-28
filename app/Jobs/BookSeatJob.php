<?php

namespace App\Jobs;

use App\Enums\SeanceSeatStatus;
use App\Models\SeanceSeat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use WebSocket\Client;

class BookSeatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $seat;
    public $websocket;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SeanceSeat $seat, Client $websocket)
    {
        $this->seat = $seat;
        $this->websocket = $websocket;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->seat->refresh();

        if ($this->seat->status === SeanceSeatStatus::PENDING) {
            $this->seat->update([
                'status' => SeanceSeatStatus::AVAILABLE
            ]);

            $data = [
                'id' => $this->seat->seat_id,
                'status' => $this->status
            ];
            $this->websocket->send(json_encode($data));
        }
    }
}
