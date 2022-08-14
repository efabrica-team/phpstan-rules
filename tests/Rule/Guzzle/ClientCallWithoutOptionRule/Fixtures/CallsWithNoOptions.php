<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutOptionRule\Fixtures;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

final class CallsWithNoOptions
{
    private Client $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    public function clientAsPrivateProperty(string $url, Request $request)
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

    public function clientAsLocalVariable(string $url, Request $request)
    {
        $client = new Client();
        $client->get($url);
        $client->post($url);
        $client->put($url);
        $client->head($url);
        $client->patch($url);
        $client->delete($url);
        $client->send($request);
        $client->request('GET', $url);

        $client->getAsync($url);
        $client->postAsync($url);
        $client->putAsync($url);
        $client->headAsync($url);
        $client->patchAsync($url);
        $client->deleteAsync($url);
        $client->sendAsync($request);
        $client->requestAsync('GET', $url);

        $client->getConfig();
    }
}
