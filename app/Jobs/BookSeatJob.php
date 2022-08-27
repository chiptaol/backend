<?php

namespace App\Jobs;

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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public int $id)
    {
        //
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (SeanceSeat::find($this->id)->is_available) {
            $websocket = new Client(config('app.ws_url'));
            $websocket->send($this->id);
            $websocket->close();
        }


    }
}
