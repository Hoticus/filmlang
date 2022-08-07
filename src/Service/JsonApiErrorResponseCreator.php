<?php

namespace App\Service;

use App\Serializer\ProblemNormalizerContextBuilder;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Debug\TraceableNormalizer;

class JsonApiErrorResponseCreator
{
    public function __construct(private TraceableNormalizer $problemNormalizer)
    {
    }

    public function create(
        \Throwable $throwable,
        ?string $error = null,
        ?string $errorMessage = null,
        ?int $statusCode = null
    ): JsonResponse {
        $exception = FlattenException::createFromThrowable($throwable);
        $context = new ProblemNormalizerContextBuilder();
        if ($error !== null) {
            $context = $context->withError($error);
        }
        if ($errorMessage !== null) {
            $context = $context->withErrorMessage($errorMessage);
        }
        if ($statusCode !== null) {
            $context = $context->withStatusCode($statusCode);
        }
        $data = $this->problemNormalizer->normalize($exception, context: $context->toArray());
        return new JsonResponse($data, $statusCode ?: $exception->getStatusCode(), $exception->getHeaders());
    }
}
