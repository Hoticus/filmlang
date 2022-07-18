<?php

namespace App\Service\TmdbApi;

use App\Service\TmdbApi\Enum\MoviesSortEnum;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbApiClient
{
    protected const API_BASE_URL = 'https://api.themoviedb.org/3/';

    public function __construct(
        private readonly HttpClientInterface $tmdbClient,
        private readonly KernelInterface $kernel,
    ) {
    }

    // TODO: handle exceptions
    protected function request(string $apiMethodUrl, array $options = [], string $requestMethod = 'GET'): array
    {
        return $this->tmdbClient->request(
            $requestMethod,
            self::API_BASE_URL . $apiMethodUrl,
            [
                'query' => $requestMethod === 'GET' ? $options : [],
                'body' => $requestMethod !== 'GET' ? $options : []
            ],
        )->toArray();
    }

    /**
     * @see https://developers.themoviedb.org/3/configuration/get-api-configuration
     */
    public function getApiConfiguration(): array
    {
        return $this->request('configuration');
    }

    /**
     * @see https://developers.themoviedb.org/3/discover/movie-discover
     */
    public function movieDiscover(int $page = 1, MoviesSortEnum $sortBy = MoviesSortEnum::PopularityDesc): array
    {
        return $this->request('discover/movie', [
            'page' => $page,
            'sort_by' => $sortBy->value
        ]);
    }

    /**
     * @see https://developers.themoviedb.org/3/search/search-movies
     */
    public function searchMovies(
        string $query,
        int $page = 1
    ): array {
        return $this->request('search/movie', [
            'query' => $query,
            'page' => $page,
        ]);
    }
}
