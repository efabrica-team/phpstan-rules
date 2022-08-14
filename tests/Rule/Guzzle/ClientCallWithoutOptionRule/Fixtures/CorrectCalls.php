<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Guzzle\ClientCallWithoutOptionRule\Fixtures;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

final class CorrectCalls
{
    private Client $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    public function clientAsPrivateProperty(string $url, Request $request)
    {
        $this->guzzleClient->get($url, ['timeout' => 1, 'connect_timeout' => 1]);
        $this->guzzleClient->post($url, [RequestOptions::TIMEOUT => 2, RequestOptions::CONNECT_TIMEOUT => 2]);
        $this->guzzleClient->put($url, ['timeout' => 3, 'connect_timeout' => 3]);
        $this->guzzleClient->head($url, [RequestOptions::TIMEOUT => 4, RequestOptions::CONNECT_TIMEOUT => 4]);
        $this->guzzleClient->patch($url, ['timeout' => 5, 'connect_timeout' => 5]);
        $this->guzzleClient->delete($url, [RequestOptions::TIMEOUT => 6, RequestOptions::CONNECT_TIMEOUT => 6]);
        $this->guzzleClient->send($request, ['timeout' => 7, 'connect_timeout' => 7]);
        $this->guzzleClient->request('GET', $url, [RequestOptions::TIMEOUT => 8, RequestOptions::CONNECT_TIMEOUT => 8]);

        $optionsTimeout = ['timeout' => 1, 'connect_timeout' => 1];
        $optionsRequestOptionsTimeout = $this->getOptions();

        $this->guzzleClient->getAsync($url, $optionsTimeout);
        $this->guzzleClient->postAsync($url, $optionsTimeout);
        $this->guzzleClient->putAsync($url, $optionsRequestOptionsTimeout);
        $this->guzzleClient->headAsync($url, $optionsRequestOptionsTimeout);
        $this->guzzleClient->patchAsync($url, $optionsTimeout);
        $this->guzzleClient->deleteAsync($url, $optionsTimeout);
        $this->guzzleClient->sendAsync($request, $this->getOptions());
        $this->guzzleClient->requestAsync('GET', $url, $this->getOptions());

        $this->guzzleClient->getConfig();
    }

    private function getOptions(): array
    {
        return [RequestOptions::TIMEOUT => 2, RequestOptions::CONNECT_TIMEOUT => 2];
    }
}
