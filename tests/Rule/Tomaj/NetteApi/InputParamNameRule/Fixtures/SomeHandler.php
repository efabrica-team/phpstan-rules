<?php

declare(strict_types=1);

namespace Efabrica\PHPStanRules\Tests\Rule\Tomaj\NetteApi\InputParamNameRule\Fixtures;

use Nette\Http\IResponse;
use Tomaj\NetteApi\Handlers\BaseHandler;
use Tomaj\NetteApi\Params\GetInputParam;
use Tomaj\NetteApi\Params\JsonInputParam;
use Tomaj\NetteApi\Params\PostInputParam;
use Tomaj\NetteApi\Params\PutInputParam;
use Tomaj\NetteApi\Response\JsonApiResponse;
use Tomaj\NetteApi\Response\ResponseInterface;

final class SomeHandler extends BaseHandler
{
    public function params(): array
    {
        return [
            (new GetInputParam('correct_get_name'))->setRequired(),
            new GetInputParam('incorrect-get-name'),
            (new PostInputParam('correct_post_name'))->setDescription('description')
            (new PostInputParam('incorrect-post-name'))->setRequired(),
            (new JsonInputParam('incorrect-json-input-param-name', '{}')),
            (new PutInputParam('incorrect-PUT-name')),
        ];
    }

    public function handle(array $params): ResponseInterface
    {
        return new JsonApiResponse(IResponse::S200_OK, []);
    }
}
