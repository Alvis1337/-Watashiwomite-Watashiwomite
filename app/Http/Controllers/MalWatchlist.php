<?php

namespace App\Http\Controllers;

use App\Models\MalUser;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class MalWatchlist extends Controller
{

    public function codeGrant()
    {

        $provider = new GenericProvider([
            'clientId' => env('MAL_CLIENT_ID'),
            'clientSecret' => env('MAL_CLIENT_SECRET'),
            'response_type' => 'code',
            'urlAuthorize' => 'https://myanimelist.net/v1/oauth2/authorize',
            'urlAccessToken' => 'https://myanimelist.net/v1/oauth2/token',
            'urlResourceOwnerDetails' => 'what',
            'code_challenge' => 'NDBa4efKCShkEc2jtoAckbQxUmvh2DByNtnPo3TI6Eu6mff6Hx84p11tI-eu1hcFRr1EC9EralZ9PywSagEZ6t4qa54SKc72w3gppKJ5qzkAXX3jmE-g_c0qI3dJBa9O'
        ]);

        if (!isset($_GET['code'])) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $provider->getAuthorizationUrl();

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $provider->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

// Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }

            exit('Invalid state');

        } else {

            try {

                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code'],
                ]);

                $getUserInfo = $provider->getAuthenticatedRequest('GET', 'https://api.myanimelist.net/v2/users/@me', $accessToken);

                $userInfo = $provider->getParsedResponse($getUserInfo);

                $user = MalUser::firstOrNew([
                    'user_id' => $userInfo['id'],
                    'user_name' => $userInfo['name'],
                    'picture_url' => $userInfo['picture'],
                    'access_token' => $accessToken->getToken(),
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'expires_in' => $accessToken->getExpires(),
                    'has_expired' => $accessToken->hasExpired()
                ]);

                $existing_user = MalUser::where('user_id', $userInfo['id']);

                if ($existing_user) {
                    $existing_user->update([
                        'access_token' => $accessToken->getToken(),
                        'refresh_token' => $accessToken->getRefreshToken(),
                        'expires_in' => $accessToken->getExpires(),
                        'has_expired' => $accessToken->hasExpired()
                    ]);
                    return response($userInfo['name'] . ' already exists, new token gathered', 200)->header('Content-type', 'text/plain');
                } else {
                    $user->save();
                    return response($userInfo['name'] . ' added to database successfully', 200)->header('Content-type', 'text/plain');
                }

            } catch (IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit(response($e, 400)->header('Content-type', 'text/plain'));

            }
        }
    }

    public function refreshToken()
    {
        $provider = new GenericProvider([
            'clientId' => env('MAL_CLIENT_ID'),
            'clientSecret' => env('MAL_CLIENT_SECRET'),
            'redirectUri' => 'https://camhelp.ngrok.io/auth/oauth',
            'urlAuthorize' => 'https://myanimelist.net/v1/oauth2/authorize',
            'urlAccessToken' => 'https://myanimelist.net/v1/oauth2/token',
            'urlResourceOwnerDetails' => 'what',
        ]);

        $existingAccessTokens = MalUser::where('has_expired', 1)->get();

        if (!$existingAccessTokens->isEmpty()) {
            foreach ($existingAccessTokens as $expiredTokens) {
                $newAccessToken = $provider->getAccessToken('refresh_token', [
                    'refresh_token' => $expiredTokens->refresh_token
                ]);

                $existing_user = MalUser::where('user_id', $expiredTokens['user_id']);

                $existing_user->update([
                    'access_token' => $newAccessToken->getToken(),
                    'refresh_token' => $newAccessToken->getRefreshToken(),
                    'expires_in' => $newAccessToken->getExpires(),
                    'has_expired' => $newAccessToken->hasExpired()
                ]);
                echo 'Token was refreshed!';
            }
        } else {
            echo 'No tokens are expired.';
        }
    }

    /**
     * @throws IdentityProviderException
     */
    public function getWatchlist($user_id) {
        $provider = new GenericProvider([
            'clientId' => env('MAL_CLIENT_ID'),
            'clientSecret' => env('MAL_CLIENT_SECRET'),
            'response_type' => 'code',
            'urlAuthorize' => 'https://myanimelist.net/v1/oauth2/authorize',
            'urlAccessToken' => 'https://myanimelist.net/v1/oauth2/token',
            'urlResourceOwnerDetails' => 'what',
            'code_challenge' => 'NDBa4efKCShkEc2jtoAckbQxUmvh2DByNtnPo3TI6Eu6mff6Hx84p11tI-eu1hcFRr1EC9EralZ9PywSagEZ6t4qa54SKc72w3gppKJ5qzkAXX3jmE-g_c0qI3dJBa9O'
        ]);

        $accessToken = MalUser::where('user_id', $user_id);

        $getUserWatchlist = $provider->getAuthenticatedRequest('GET', 'https://api.myanimelist.net/v2/users/@me/animelist?status=plan_to_watch&limit=1000', $accessToken->pluck('access_token')[0]);

        return response()->json($provider->getParsedResponse($getUserWatchlist));

    }
}
