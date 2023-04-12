<?php

namespace App\Console\Commands;

use App\Http\Controllers\CustomWebsocketOneController;
use App\Http\Controllers\CustomWebsocketTwoController;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Http\OriginCheck;
use Ratchet\Http\Router;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class StartRatchetServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ratchet:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start ratchet websocket server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $port = 6001;
        echo "Ratchet server started on localhost:{$port} \n";
        $address = '0.0.0.0'; // Client address to accept. (0.0.0.0 means receive connections from any)
        $loop = LoopFactory::create();
        $socket = new Server("{$address}:{$port}", $loop);
        $routes = new RouteCollection();

        echo "App route-one websocket running on localhost:{$port}/route-one \n";
        $customWebsocketOneServer = new WsServer(new CustomWebsocketOneController($loop));
        $customWebsocketOneServer->enableKeepAlive($loop); // Enable message ping:pong
        $routes->add('route-one', new Route('/route-one', [
            '_controller' => $customWebsocketOneServer,
        ]));

        echo "App route-two websocket running on localhost:{$port}/route-two \n";
        $customWebsocketTwoServer = new WsServer(new CustomWebsocketTwoController($loop));
        $customWebsocketTwoServer->enableKeepAlive($loop); // Enable message ping:pong
        $routes->add('route-two', new Route('/route-two', [
            '_controller' => $customWebsocketTwoServer,
        ]));

        $urlMatcher = new UrlMatcher($routes, new RequestContext());
        $router = new Router($urlMatcher);
        $checkedApp = new OriginCheck($router, ['localhost']);
        $server = new IoServer(new HttpServer($router), $socket, $loop); // Pass $checkedApp to filter origin
        $server->run();
    }
}