<?php

namespace Fathom\Controllers;

use Evenement\EventEmitter;
use Fathom\Logging\BulkLogger;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Promise;

class NotFound implements Controller
{

    public function __invoke(
        ServerRequestInterface $request,
        array $routeInfo,
        EventEmitter $body,
        BulkLogger $logger,
    ) : Promise {
        return new Promise(function ($resolve) use ($request, $body) {
            $body->on('end', function () use ($resolve, $request) {

                echo $request->getUri() . " not found'";
                $resolve(
                    new Response(Response::STATUS_NOT_FOUND, [], 'Not Found')
                );
            });
        });
    }
}
