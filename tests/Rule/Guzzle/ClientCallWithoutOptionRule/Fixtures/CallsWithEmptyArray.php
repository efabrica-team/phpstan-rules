<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutOptionRule\Fixtures;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

final class CallsWithEmptyArray
{
    private Client $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    public function clientAsPrivateProperty(string $url, Request $request)
    {
        $this->guzzleClient->get($url, []);
        $this->guzzleClient->post($url, []);
        $this->guzzleClient->put($url, []);
        $this->guzzleClient->head($url, []);
        $this->guzzleClient->patch($url, []);
        $this->guzzleClient->delete($url, []);
        $this->guzzleClient->send($request, []);
        $this->guzzleClient->request('GET', $url, []);

        $optionsTimeout = [];
        $this->guzzleClient->getAsync($url, $optionsTimeout);
        $this->guzzleClient->postAsync($url, $optionsTimeout);
        $this->guzzleClient->putAsync($url, $optionsTimeout);
        $this->guzzleClient->headAsync($url, $optionsTimeout);
        $this->guzzleClient->patchAsync($url, $optionsTimeout);
        $this->guzzleClient->deleteAsync($url, $optionsTimeout);
        $this->guzzleClient->sendAsync($request, $optionsTimeout);
        $this->guzzleClient->requestAsync('GET', $url, $optionsTimeout);

        $this->guzzleClient->getConfig();
    }
}
