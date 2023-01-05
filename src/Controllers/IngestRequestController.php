<?php

namespace Fathom\Controllers;

use Evenement\EventEmitter;
use Fathom\Logging\BulkLogger;
use Fathom\ProxyBrowser;
use Psr\Http\Message\ServerRequestInterface;
use React\Cache\ArrayCache;
use React\Http\Message\Response;
use React\Promise\Promise;

class IngestRequestController implements Controller
{

    public function __invoke(
        ServerRequestInterface $request,
        array $routeInfo,
        EventEmitter $body,
        BulkLogger $logger,
    ) : Promise {
        return new Promise(function ($resolve) use ($request, $body,$logger) {
            $logger->logRequestForBackend($request);

            $body->on('end', function () use ($resolve, $request, $logger) {

                $resolve(
                    new Response(
                        Response::STATUS_OK,
                        [
                            'Content-Type' => 'text/html',
                        ],
                        "GIF89a��!�,;%"
                    )

                );
            });
        });
    }
}
