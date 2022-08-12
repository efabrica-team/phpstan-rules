<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutTimeoutOptionRule\Fixtures;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

final class NoOptionsWithClientAsPrivateProperty
{
    private Client $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    public function doCalls(string $url, Request $request)
    {
        $this->guzzleClient->get($url);
        $this->guzzleClient->post($url);
        $this->guzzleClient->put($url);
        $this->guzzleClient->head($url);
        $this->guzzleClient->patch($url);
        $this->guzzleClient->delete($url);
        $this->guzzleClient->send($request);
        $this->guzzleClient->request('GET', $url);

        $this->guzzleClient->getAsync($url);
        $this->guzzleClient->postAsync($url);
        $this->guzzleClient->putAsync($url);
        $this->guzzleClient->headAsync($url);
        $this->guzzleClient->patchAsync($url);
        $this->guzzleClient->deleteAsync($url);
        $this->guzzleClient->sendAsync($request);
        $this->guzzleClient->requestAsync('GET', $url);

        $this->guzzleClient->getConfig();
    }
}
