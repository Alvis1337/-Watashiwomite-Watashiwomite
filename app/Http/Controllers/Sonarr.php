<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class Sonarr extends Controller
{
    /**
     * @throws IdentityProviderException
     */
    public function getSystemStatus(): JsonResponse
    {
        $provider = new GenericProvider([
            'urlAuthorize' => 'https://myanimelist.net/v1/oauth2/authorize',
            'urlAccessToken' => 'https://myanimelist.net/v1/oauth2/token',
            'urlResourceOwnerDetails' => 'what',
        ]);

        $sonarrURL = env('SONARR_URL');
        $sonarrAPIKey = env('SONARR_API_KEY');

        $apiCall = $provider->getRequest('GET', $sonarrURL . '/api/v3/system/status?apikey=' . $sonarrAPIKey);

        $response = $provider->getParsedResponse($apiCall);

        if ($response['appName']) {
            return response()->json($response);
        } else {
            //TODO it doesnt corrrectly handle if the response fails
            return response()->json(['error' => 'Something could be wrong with your SONARR_URL env variable']);
        }
    }

    public function seriesLookup($anime) {
        $provider = new GenericProvider([
            'urlAuthorize' => 'https://myanimelist.net/v1/oauth2/authorize',
            'urlAccessToken' => 'https://myanimelist.net/v1/oauth2/token',
            'urlResourceOwnerDetails' => 'what',
        ]);

        $sonarrURL = env('SONARR_URL');
        $sonarrAPIKey = env('SONARR_API_KEY');

        $apiCall = $provider->getRequest('GET', $sonarrURL . '/api/series/lookup?term=' . urlencode($anime) . '&apikey=' . $sonarrAPIKey);

        $response = $provider->getParsedResponse($apiCall);

        return response()->json($response);
    }

    public function seriesAdd(Int $tvdbId, String $title, Int $profileId, String $titleSlug, Array $images, Array $seasons): JsonResponse
    {
        $provider = new GenericProvider([
            'urlAuthorize' => 'https://myanimelist.net/v1/oauth2/authorize',
            'urlAccessToken' => 'https://myanimelist.net/v1/oauth2/token',
            'urlResourceOwnerDetails' => 'what',
        ]);

        $sonarrURL = env('SONARR_URL');
        $sonarrAPIKey = env('SONARR_API_KEY');

        $params = [
            'apiKey' => $sonarrAPIKey,
            'tvdbId' => $tvdbId,
            'title' => $title,
            'profileId' => $profileId,
            'titleSlug' => $titleSlug,
            'images' => $images,
            'season' => $seasons
        ];

        $apiCall = $provider->getRequest('POST', $sonarrURL . '/api/series', $params);

        $response = $provider->getParsedResponse($apiCall);

        return response()->json($response);
    }

    public function grabAndAdd() {
        $provider = new GenericProvider([
            'urlAuthorize' => 'https://myanimelist.net/v1/oauth2/authorize',
            'urlAccessToken' => 'https://myanimelist.net/v1/oauth2/token',
            'urlResourceOwnerDetails' => 'what',
        ]);

        $apiCall = $provider->getRequest('GET', 'http://127.0.0.1:8000/series-lookup?anime=Gleipner');
        $response = $provider->getParsedResponse($apiCall);

        dd($response);

    }

}
