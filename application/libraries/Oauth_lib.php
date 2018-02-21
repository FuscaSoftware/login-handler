<?php
/**
 * User: sbraun
 * Date: 15.02.18
 * Time: 19:42
 */

class Oauth_lib
{
    public $config = [
        'clientId' => 'demoapp',    // The client ID assigned to you by the provider
        'clientSecret' => 'demopass',   // The client password assigned to you by the provider
        'redirectUri' => 'http://example.com/your-redirect-url/',
        'urlAuthorize' => 'http://brentertainment.com/oauth2/lockdin/authorize',
        'urlAccessToken' => 'http://brentertainment.com/oauth2/lockdin/token',
        'urlResourceOwnerDetails' => 'http://brentertainment.com/oauth2/lockdin/resource',
    ];
    # config for google
//    [
//    'clientId' => '{google-app-id}',
//    'clientSecret' => '{google-app-secret}',
//    'redirectUri' => 'https://example.com/callback-url',
//    'hostedDomain' => 'https://example.com',
//    ]


    public function __construct() {
        $lib_path = dirname(APPPATH) . "/oauth2-client/vendor/autoload.php";
        require $lib_path;
    }

    public function basic() {
        $provider = new \League\OAuth2\Client\Provider\GenericProvider($this->config);

// If we don't have an authorization code then get one
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
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else {

            try {

                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                echo $accessToken->getToken() . "\n";
                echo $accessToken->getRefreshToken() . "\n";
                echo $accessToken->getExpires() . "\n";
                echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";

                // Using the access token, we may look up details about the
                // resource owner.
                $resourceOwner = $provider->getResourceOwner($accessToken);

                var_export($resourceOwner->toArray());

                // The provider provides a way to get an authenticated API request for
                // the service, using the access token; it returns an object conforming
                // to Psr\Http\Message\RequestInterface.
                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    'http://brentertainment.com/oauth2/lockdin/resource',
                    $accessToken
                );

            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit($e->getMessage());

            }

        }
    }

    public function refresh_token() {
        $provider = new \League\OAuth2\Client\Provider\GenericProvider($this->config);

        $existingAccessToken = getAccessTokenFromYourDataStore();

        if ($existingAccessToken->hasExpired()) {
            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $existingAccessToken->getRefreshToken()
            ]);

            // Purge old access token and store new access token to your data store.
        }
    }

    public function google_auth() {
        $this->config = ci()->config->item('oauth')['google'];
        $provider = new League\OAuth2\Client\Provider\Google($this->config);

        if (!empty($_GET['error'])) {

            // Got an error, probably user denied access
            exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));

        } elseif (empty($_GET['code'])) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: ' . $authUrl);
            exit;

        } elseif (empty($_GET['state']) || ($_GET['state'] !== @$_SESSION['oauth2state'])) {

            // State is invalid, possible CSRF attack in progress
            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else {
//            var_dump($_GET['code']);die;
            // Try to get an access token (using the authorization code grant)
            try {
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => trim($_GET['code'])
                ]);
            } catch (Exception $e) {
                echo "Cannot get token! :( " . $e->getMessage();
                echo "\nCode was: " . $_GET['code'];
                // persist the token in a database
                if (isset($token)) {
                    $refreshToken = $token->getRefreshToken();
                    $grant = new League\OAuth2\Client\Grant\RefreshToken();
                    $token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);
                }
            }

            // Optional: Now you have a token you can look up a users profile data
            try {
                if (@$token) {
                    // We got an access token, let's now get the owner details
                    $ownerDetails = $provider->getResourceOwner($token);

                    // Use these details to create a new profile
                    printf('Hello %s!', $ownerDetails->getFirstName());
                    echo "\n";
                }

            } catch (Exception $e) {

                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());

            }
            if (@$token) {
                // Use this to interact with an API on the users behalf
                echo "\nToken:";
                echo $token->getToken();

                // Use this to get a new access token if the old one expires
                echo "\nRefreshToken:";
                echo $token->getRefreshToken();

                // Number of seconds until the access token will expire, and need refreshing
                echo "\nExpires:";
                echo $token->getExpires();
            }

            if (@$token) {
//                $_SESSION['qlu']['google']['token_obj'] = $token;
                $_SESSION['qlu']['google']['token'] = $token->getToken();
                $_SESSION['qlu']['google']['token_val'] = $token->getValues();
            }
            if (@$ownerDetails)
                $_SESSION['qlu']['google']['ownerDetails'] = $ownerDetails->toArray();
        }
    }

    public function google_refresh_token() {
        $this->config = ci()->config->item('oauth')['google'];
        $this->config['accessType'] = 'offline';

        $code = ($_GET['code']) ?: '';

//        $provider = new League\OAuth2\Client\Provider\Google([
//            'clientId' => '{google-app-id}',
//            'clientSecret' => '{google-app-secret}',
//            'redirectUri' => 'https://example.com/callback-url',
//            'accessType' => 'offline',
//        ]);
        $provider = new League\OAuth2\Client\Provider\Google($this->config);
        # It is important to note that the refresh token is only returned on the first request after this it will be null. You should securely store the refresh token when it is returned:
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        // persist the token in a database
        $refreshToken = $token->getRefreshToken();
        # If you ever need to get a new refresh token you can request one by forcing the approval prompt:
        $authUrl = $provider->getAuthorizationUrl(['approval_prompt' => 'force']);

        $provider = new League\OAuth2\Client\Provider\Google([
            'clientId'     => '{google-app-id}',
            'clientSecret' => '{google-app-secret}',
            'redirectUri'  => 'https://example.com/callback-url',
        ]);

        $grant = new League\OAuth2\Client\Grant\RefreshToken();
        $token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);

    }

    public function google_scope() {
        $authorizationUrl = $provider->getAuthorizationUrl([
            'scope' => [
                'https://www.googleapis.com/auth/drive',
            ]
        ]);
        header('Location: ' . $authorizationUrl);
        exit;
    }
}