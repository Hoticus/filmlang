<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Context\ContextBuilderInterface;
use Symfony\Component\Serializer\Context\ContextBuilderTrait;

final class ProblemNormalizerContextBuilder implements ContextBuilderInterface
{
    use ContextBuilderTrait;

    public function withError(?string $error): static
    {
        return $this->with(ProblemNormalizer::ERROR, $error);
    }


    public function withErrorMessage(?string $errorMessage): static
    {
        return $this->with(ProblemNormalizer::ERROR_MESSAGE, $errorMessage);
    }


    public function withStatusCode(int|string|null $statusCode): static
    {
        return $this->with(ProblemNormalizer::STATUS, $statusCode);
    }
}
