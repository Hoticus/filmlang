<?php

namespace App\Service\TmdbApi;

use Symfony\Component\HttpKernel\KernelInterface;

class TmdbApiConfiguration
{
    public const CACHED_FOR = 24 * 60 * 60;

    public readonly array $configuration;
    public readonly int $refreshedAt;

    public function __construct(KernelInterface $kernel, TmdbApiClient $tmdbApiClient)
    {
        $filePath = $kernel->getProjectDir() . '/var/tmdb-configuration.json';

        if (
            !file_exists($filePath)
            || json_decode(file_get_contents($filePath), true)['refreshed_at'] + self::CACHED_FOR < time()
        ) {
            $this->configuration = $configuration = $tmdbApiClient->getApiConfiguration();
            $configuration['refreshed_at'] = $this->refreshedAt = time();
            file_put_contents($filePath, json_encode($configuration));
        } else {
            $configuration = json_decode(file_get_contents($filePath), true);
            $this->refreshedAt = $configuration['refreshed_at'];
            unset($configuration['refreshed_at']);
            $this->configuration = $configuration;
        }
    }
}
