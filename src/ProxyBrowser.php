<?php

namespace Fathom;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Aws\Signature\S3SignatureV4;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Http\Io\Sender;
use React\Http\Io\Transaction;
use React\Http\Message\ServerRequest;

class ProxyBrowser
{
    private Browser $browser;
    private string $baseUrl;

    public function __construct(LoopInterface $loop)
    {
        //$this->browser = new Browser(null,$loop);

        $this->baseUrl = 'https://fathom.vapor.cloud';
    }

    public function reportBulk(array $data)
    {
        /**
        $this->browser->post(
            $this->baseUrl . 'report',
            [
                'Content-Type' => 'application/json',
            ],
            json_encode($data)
        )
                      ->then(function (ResponseInterface $response) {
                          echo $response->getBody()->getContents();
                      }, function (\Exception $ex) {
                          echo PHP_EOL . 'ERROR WITH API: ' . PHP_EOL . $ex->getMessage(
                              ) . PHP_EOL . 'URL: ' . $this->baseUrl . 'report' . PHP_EOL;
                      });
         */
        //Cant send so delay by 1 second
        //usleep(100);
    }
}
