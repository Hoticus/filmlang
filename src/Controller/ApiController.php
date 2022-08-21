<?php

namespace App\Controller;

use App\HttpFoundation\JsonApiSuccessfulResponse;
use App\Service\TmdbApi\TmdbApiClient;
use App\Service\TmdbApi\TmdbApiConfiguration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'app_', format: 'json')]
class ApiController extends AbstractController
{
    #[Route('/movie-discover/{page<\d+>}', name: 'movie_discover', methods: ['GET'])]
    public function movieDiscover(TmdbApiClient $tmdbApiClient, int $page = 1): Response
    {
        $data = $tmdbApiClient->movieDiscover($page);
        foreach ($data['results'] as &$movie) {
            unset(
                $movie['adult'],
                $movie['backdrop_path'],
                $movie['genre_ids'],
                $movie['id'],
                $movie['original_language'],
                $movie['original_title'],
                $movie['popularity'],
                $movie['video'],
                $movie['vote_count'],
            );
        }

        return new JsonApiSuccessfulResponse($data);
    }

    #[Route('/get-tmdb-api-configuration', name: 'get_tmdb_api_configuration', methods: ['GET'])]
    public function getTmdbApiConfiguration(TmdbApiConfiguration $tmdbApiConfiguration): Response
    {
        $response = new JsonApiSuccessfulResponse($tmdbApiConfiguration->configuration);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setPublic();
        $response->setExpires(new \DateTime(
            '@' . $tmdbApiConfiguration->refreshedAt + TmdbApiConfiguration::CACHED_FOR
        ));
        return $response;
    }
}
