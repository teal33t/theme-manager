<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = 500;
        $data = [
            'type' => 'Error',
            'title' => 'Internal Server Error',
            'detail' => 'An unexpected error occurred',
        ];

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $data['detail'] = $exception->getMessage();
        } elseif ($exception instanceof ValidationFailedException) {
            $statusCode = 422;
            $data['type'] = 'Validation Error';
            $data['title'] = 'Validation Failed';
            $data['violations'] = [];
            
            foreach ($exception->getViolations() as $violation) {
                $data['violations'][] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }
        }

        $data['status'] = $statusCode;
        $event->setResponse(new JsonResponse($data, $statusCode));
    }
}