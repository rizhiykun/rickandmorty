<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

/** @psalm-suppress UnusedClass */
class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected SerializerInterface $_serializer,
        protected LoggerInterface     $logger,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Если исключение является HTTP-исключением и код равен 404, пропускаем логирование
        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() === 404) {
            // Можно оставить вывод ответа, если он нужен, но без логирования
            $data = [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
            $event->setResponse(new JsonResponse($data, 404));
            return;
        }

        $data = [
            'success' => false,
            'message' => $exception->getMessage(),
            'trace' => $exception->getTrace(),
        ];

        $env = $_ENV['APP_ENV'] ?? 'prod';
        $status = 400;

        if (method_exists($exception, 'getStatusCode')) {
            $status = (int) $exception->getStatusCode();
        } elseif (is_numeric($exception->getCode()) && (int) $exception->getCode() > 99) {
            $status = (int) $exception->getCode();
        }

        if ($status < 100 || $status > 599) {
            $status = 500; // По умолчанию 500 (Internal Server Error)
        }

        // Логируем только нужные исключения
        $this->logger->error(
            sprintf(
                'Error from ExceptionSubscriber: %s, in %s, on line %s with code %s',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $status
            ),
            [
                'environment' => $env,
                'status' => $status,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]
        );

        $event->setResponse(new JsonResponse($data, $status));
    }

    #[\Override]
    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
