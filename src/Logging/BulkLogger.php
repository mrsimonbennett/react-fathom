<?php

namespace Fathom\Logging;

use Carbon\Carbon;
use Fathom\ProxyBrowser;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Http\Client\Request;

class BulkLogger
{
    private array $buffer = [];
    private ?TimerInterface $timer = null;
    private int $maxTimeoutDelay = 1;

    /**
     * @param ProxyBrowser $browser
     * @param LoopInterface $loop
     */
    public function __construct(private ProxyBrowser $browser, private LoopInterface $loop)
    {
        // When the process is killed, flush the logs
        $this->loop->addSignal(
            SIGTERM,
            $func = function () use (&$func) {
                $this->sentBulk();
                $this->loop->removeSignal(SIGTERM, $func);
                echo 'Sent Any Logs' . PHP_EOL;
                exit();
            }
        );
        $this->loop->addSignal(
            SIGINT,
            $func = function () use (&$func) {
                $this->sentBulk();
                $this->loop->removeSignal(SIGINT, $func);
                echo 'Sent Any Logs' . PHP_EOL;
                exit();
            }
        );
    }

    public function ensureTimer() : void
    {
        if(count($this->buffer) > 10000){
            $this->sentBulk();
            return;
        }
        if ($this->timer instanceof TimerInterface) {
            return;
        }
        $this->timer = $this->loop->addPeriodicTimer(
            $this->maxTimeoutDelay,
            function () {
                $this->timer = null;
                $this->sentBulk();
            }
        );
    }

    public function sentBulk() : void
    {
        if ($this->timer instanceof TimerInterface) {
            $this->loop->cancelTimer($this->timer);
            $this->timer = null;
        }

        $data = $this->buffer;
        $this->buffer = [];
        if (count($data) === 0) {
            return;
        }

        $this->browser->reportBulk($data);
    }

    public function logRequestForBackend(
        ServerRequestInterface $request
    ) : void {
            $hash = hash('sha256', $request->getUri()); //Plus your unique daily key, site id, ip address what ever.

            $this->buffer[] = array_merge($request->getQueryParams(),['hash' => $hash] );
            $this->ensureTimer();

    }
}
