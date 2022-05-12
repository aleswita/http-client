<?php

namespace Amp\Http\Client\Connection\Internal;

use Amp\Cancellation;
use Amp\DeferredFuture;
use Amp\ForbidCloning;
use Amp\ForbidSerialization;
use Amp\Future;
use Amp\Http\Client\Connection\Stream;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Amp\Pipeline\Queue;
use Revolt\EventLoop;

/**
 * Used in Http2ConnectionProcessor.
 *
 * @internal
 */
final class Http2Stream
{
    use ForbidSerialization;
    use ForbidCloning;

    public ?Response $response = null;

    public ?DeferredFuture $pendingResponse;

    public ?Future $preResponseResolution = null;

    public bool $responsePending = true;

    public ?Queue $body = null;

    public ?DeferredFuture $trailers = null;

    /** @var int Bytes received on the stream. */
    public int $received = 0;

    public int $bufferSize;

    public string $requestBodyBuffer = '';

    public readonly DeferredFuture $requestBodyCompletion;

    /** @var int Integer between 1 and 256 */
    public int $weight = 16;

    public int $dependency = 0;

    public ?int $expectedLength = null;

    public ?DeferredFuture $windowSizeIncrease = null;

    public function __construct(
        public readonly int $id,
        public readonly Request $request,
        public readonly Stream $stream,
        public readonly Cancellation $cancellation,
        public readonly ?string $watcher,
        public int $serverWindow,
        public int $clientWindow,
    ) {
        $this->pendingResponse = new DeferredFuture;
        $this->requestBodyCompletion = new DeferredFuture;
        $this->bufferSize = 0;
    }

    public function __destruct()
    {
        if ($this->watcher !== null) {
            EventLoop::cancel($this->watcher);
        }
    }

    public function disableInactivityWatcher(): void
    {
        if ($this->watcher === null) {
            return;
        }

        EventLoop::disable($this->watcher);
    }

    public function enableInactivityWatcher(): void
    {
        if ($this->watcher === null) {
            return;
        }

        $watcher = $this->watcher;

        EventLoop::disable($watcher);
        EventLoop::enable($watcher);
    }
}
