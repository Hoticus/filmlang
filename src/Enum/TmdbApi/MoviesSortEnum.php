<?php

namespace App\Enum\TmdbApi;

/**
 * @see https://developers.themoviedb.org/3/discover/movie-discover#EpDef-def sort_by
 */
enum MoviesSortEnum: string
{
    case PopularityAsc = 'popularity.asc';
    case PopularityDesc = 'popularity.desc';
    case ReleaseDateAsc = 'release_date.asc';
    case ReleaseDateDesc = 'release_date.desc';
    case RevenueAsc = 'revenue.asc';
    case RevenueDesc = 'revenue.desc';
    case PrimaryReleaseDateAsc = 'primary_release_date.asc';
    case PrimaryReleaseDateDesc = 'primary_release_date.desc';
    case OriginalTitleAsc = 'original_title.asc';
    case OriginalTitleDesc = 'original_title.desc';
    case VoteAverageAsc = 'vote_average.asc';
    case VoteAverageDesc = 'vote_average.desc';
    case VoteCountAsc = 'vote_count.asc';
    case VoteCountDesc = 'vote_count.desc';
}
