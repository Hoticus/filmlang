<?php

namespace App\Serializer;

use InvalidArgumentException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProblemNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public const STATUS = 'status';
    public const ERROR = 'error';
    public const ERROR_MESSAGE = 'error_message';

    private $defaultContext = [
        self::ERROR => 'https://tools.ietf.org/html/rfc2616#section-10',
        self::ERROR_MESSAGE => 'An error occurred.',
    ];

    public function __construct(private bool $debug = false)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof FlattenException) {
            throw new InvalidArgumentException(sprintf('The object must implement "%s".', FlattenException::class));
        }

        $context += $this->defaultContext;
        $debug = $this->debug && ($context['debug'] ?? true);

        $data = [
            'success' => false,
            self::STATUS => $context['status'] ?? $object->getStatusCode(),
            self::ERROR => $context['error'],
            self::ERROR_MESSAGE => $context['error_message'],
            'detail' => $debug ? $object->getMessage() : $object->getStatusText(),
        ];
        if ($debug) {
            $data['class'] = $object->getClass();
            $data['trace'] = $object->getTrace();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof FlattenException;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
