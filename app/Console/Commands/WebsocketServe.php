<?php

namespace App\Console\Commands;

use App\Websockets\Core;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class WebsocketServe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ws:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $wsServer = new WsServer(new Core());

        $server = IoServer::factory(
            new HttpServer(
                $wsServer
            ),
            8080
        );

        $wsServer->enableKeepAlive($server->loop, 20);

        $server->run();;

        return true;
    }
}
