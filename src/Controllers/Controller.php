<?php

namespace Fathom\Controllers;

use Evenement\EventEmitter;
use Fathom\Logging\BulkLogger;
use Fathom\ProxyBrowser;
use Psr\Http\Message\ServerRequestInterface;
use React\Cache\ArrayCache;
use React\Promise\Promise;

interface Controller
{
    public function __invoke(
        ServerRequestInterface $request,
        array $routeInfo,
        EventEmitter $body,
        BulkLogger $logger,
    ) : Promise;
}
