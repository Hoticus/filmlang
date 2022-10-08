<?php

namespace App\Controller;

use App\HttpFoundation\JsonApiSuccessfulResponse;
use App\Repository\FilmRepository;
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
    public function movieDiscover(
        TmdbApiClient $tmdbApiClient,
        FilmRepository $filmRepository,
        int $page = 1
    ): Response {
        $filmsWithLanguage = $filmRepository->findAllWithLanguage();
        $data = $tmdbApiClient->movieDiscover($page);
        foreach ($data['results'] as &$movie) {
            unset(
                $movie['adult'],
                $movie['backdrop_path'],
                $movie['genre_ids'],
                $movie['original_language'],
                $movie['original_title'],
                $movie['popularity'],
                $movie['video'],
                $movie['vote_count'],
            );

            $language = null;

            // binary search
            $low = 0;
            $high = count($filmsWithLanguage) - 1;
            while ($low <= $high) {
                $mid = intdiv($low + $high, 2);
                $guess = $filmsWithLanguage[$mid]->getTmdbId();
                if ($guess === $movie['id']) {
                    $language = $filmsWithLanguage[$mid]->getLanguage();
                    break;
                }
                if ($guess > $movie['id']) {
                    $high = $mid - 1;
                } else {
                    $low = $mid + 1;
                }
            }

            $movie['language'] = $language;
        }

        return new JsonApiSuccessfulResponse($data);
    }

    #[Route('/get-movie-details/{id<\d+>}', name: 'get_movie_details', methods: ['GET'])]
    public function getMovieDetails(
        int $id,
        TmdbApiClient $tmdbApiClient,
        FilmRepository $filmRepository,
    ): Response {
        $data = $tmdbApiClient->getMovieDetails($id);
        unset(
            $data['adult'],
            $data['backdrop_path'],
            $data['belongs_to_collection'],
            $data['budget'],
            $data['homepage'],
            $data['id'],
            $data['imdb_id'],
            $data['original_language'],
            $data['original_title'],
            $data['popularity'],
            $data['revenue'],
            $data['spoken_languages'],
            $data['status'],
            $data['tagline'],
            $data['video'],
            $data['vote_count'],
        );

        $film = $filmRepository->findOneBy(['tmdbId' => $id]);
        $data['language'] = $film?->getLanguage();

        $response = new JsonApiSuccessfulResponse($data);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setPublic();
        $response->setMaxAge(86400);
        return $response;
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
