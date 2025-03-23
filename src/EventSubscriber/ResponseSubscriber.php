<?php

declare(strict_types=1);

namespace App\EventSubscriber;

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

        // Check response content type and status
        if ($response->isOk() && $this->shouldSerialize($response)) {
            try {
                $response->setContent($this->serializer->serialize([
                    'success' => true,
                    'result' => json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR),
                ], 'json'));
            } catch (\JsonException $e) {
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

    /** @psalm-suppress MissingOverrideAttribute */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
