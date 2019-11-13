<?php

namespace Amp\Http\Client\Interceptor;

use Amp\Http\Client\Internal\ForbidCloning;
use Amp\Http\Client\Internal\ForbidSerialization;
use Amp\Http\Client\Request;

final class SetRequestHeader extends ModifyRequest
{
    use ForbidCloning;
    use ForbidSerialization;

    public function __construct(string $headerName, string $headerValue, string ...$headerValues)
    {
        \array_unshift($headerValues, $headerValue);

        parent::__construct(static function (Request $request) use ($headerName, $headerValues) {
            $request->setHeader($headerName, $headerValues);

            return $request;
        });
    }
}
