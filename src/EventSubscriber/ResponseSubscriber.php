<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\AppException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

/** @psalm-suppress UnusedClass */
class ResponseSubscriber implements EventSubscriberInterface
{
    protected SerializerInterface $serializer;
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if ($response->isOk() && $this->shouldSerialize($response)) {
            try {
                $content = $response->getContent();

                $result = $content !== false ? json_decode($content, true, 512, JSON_THROW_ON_ERROR) : null;
                $response->setContent($this->serializer->serialize([
                    'success' => true,
                    'result' => $result,
                ], 'json'));
            } catch (\JsonException $e) {
                throw new AppException('Ошибка JSON: ' . $e->getMessage());
            }
        }
    }

    private function shouldSerialize(Response $response): bool
    {
        $contentType = $response->headers->get('content-type');
        return !$response->headers->has('content-type') || ($contentType && strpos(
            $contentType,
            'application/json'
        ) !== false);
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
