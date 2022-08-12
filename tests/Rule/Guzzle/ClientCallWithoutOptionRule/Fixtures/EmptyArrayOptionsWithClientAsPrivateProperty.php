<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutOptionRule\Fixtures;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

final class EmptyArrayOptionsWithClientAsPrivateProperty
{
    private Client $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    public function doCalls(string $url, Request $request)
    {
        $this->guzzleClient->get($url, []);
        $this->guzzleClient->post($url, []);
        $this->guzzleClient->put($url, []);
        $this->guzzleClient->head($url, []);
        $this->guzzleClient->patch($url, []);
        $this->guzzleClient->delete($url, []);
        $this->guzzleClient->send($request, []);
        $this->guzzleClient->request('GET', $url, []);

        $options = [];
        $this->guzzleClient->getAsync($url, $options);
        $this->guzzleClient->postAsync($url, $options);
        $this->guzzleClient->putAsync($url, $options);
        $this->guzzleClient->headAsync($url, $options);
        $this->guzzleClient->patchAsync($url, $options);
        $this->guzzleClient->deleteAsync($url, $options);
        $this->guzzleClient->sendAsync($request, $options);
        $this->guzzleClient->requestAsync('GET', $url, $options);

        $this->guzzleClient->getConfig();
    }
}
