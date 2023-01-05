<?php

use Fathom\Controllers\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Middleware\StreamingRequestMiddleware;
use React\Socket\SocketServer;

require __DIR__ . '/../vendor/autoload.php';


$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $routes) {
        $routes->addRoute('GET', '/', \Fathom\Controllers\IngestRequestController::class);

        /**
         * Clearly this is a shit way to return the script, but it works for now, replace with caddy or nginx
         */
        $routes->addRoute('GET', '/script.js', \Fathom\Controllers\ScriptController::class);
    }
);
$loop = Loop::get();
$proxyBrowser = new \Fathom\ProxyBrowser($loop);

$logger = new \Fathom\Logging\BulkLogger($proxyBrowser, $loop);

$http = new HttpServer(
    $loop,
    new StreamingRequestMiddleware(),
    function (ServerRequestInterface $request) use ($dispatcher, $proxyBrowser, $logger) {
        $body = $request->getBody();

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        echo $request->getMethod() . ' ' . $request->getUri()->getPath() . ' ' . number_format(
                memory_get_usage() / 1024 / 1024,
                2
            ). '/' . number_format(
                memory_get_peak_usage() / 1024 / 1024,
                2
            ) . 'M' . PHP_EOL;

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
               echo  'Not Found method:' . $request->getMethod() . ' url:' . $request->getUri()->getPath(). PHP_EOL;
                return (new NotFound())(
                    $request,
                    $routeInfo,
                    $body,
                    $logger,
                );
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                echo  'Method Not Allowed:' . $request->getMethod() . ' url:' . $request->getUri()->getPath(). PHP_EOL;

                return (new NotFound())($request, $routeInfo, $body, $logger);

            case FastRoute\Dispatcher::FOUND:
                $controller = new $routeInfo[1];

                return $controller($request, $routeInfo[2], $body, $logger);
        }
    }
);

$http->on('error', function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL . $e->getFile() . PHP_EOL;

    if ($e->getPrevious() !== null) {
        echo 'Previous: ' . $e->getPrevious()->getMessage() . PHP_EOL;
    }
    echo $e->getTraceAsString() . PHP_EOL;
    dd('dead');
});

$socket = new SocketServer('127.0.0.1:8000', []);

$http->listen($socket);
echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
