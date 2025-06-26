<?php
namespace LaravelMeet\Providers;

use Carbon\Carbon;
use LaravelMeet\Contracts\MeetingProvider;
use Illuminate\Support\Facades\Http;
use LaravelMeet\Contracts\OAuthTokenStore;

class ZoomProvider implements MeetingProvider {
    protected $apiKey;
    protected $apiSecret;
    protected $jwt;
    protected $baseUrl = 'https://api.zoom.us/v2';
    
    public function __construct() {
        $this->jwt = config('laravelmeet.zoom.jwt');
    }
    
    /**
     * @param array $data
     * @return array|mixed
     */
    public function createMeeting(array $data) {
        $userId = $data['user_id'] ?? 'me';
        
        // Fetch the consultant's OAuth token
        $tokenData = app(OAuthTokenStore::class)->getTokens('zoom', $userId);
        $accessToken = $tokenData['access_token'];
        
        // Create meeting using consultant's token
        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/users/me/meetings", [
                'topic' => $data['topic'] ?? 'Zoom Meeting',
                'type' => $data['type'] ?? 2,
                'start_time' => $data['start_time'],
                'duration' => $data['duration'] ?? 30,
                'timezone' => $data['timezone'] ?? 'UTC',
                'settings' => [
                    'use_pmi' => false,
                ]
            ]);
        
        if ($response->unauthorized()) {
            $this->refreshToken($tokenData['refresh_token'], $userId);
            return $this->createMeeting($data); // Retry after refresh
        }
        
        return json_decode($response->body());
    }
    
    /**
     * @param int $userId
     * @param string $meetingId
     * @return bool
     */
    public function deleteMeeting(int $userId, string $meetingId) {
        // Fetch the consultant's OAuth token
        $tokenData = app(OAuthTokenStore::class)->getTokens('zoom', $userId);
        $accessToken = $tokenData['access_token'];
        
        $response = Http::withToken($accessToken)
            ->delete("{$this->baseUrl}/meetings/{$meetingId}");
        
        return $response->status() === 204;
    }

    /**
     * Handles the OAuth callback by exchanging the authorization code for Zoom access tokens.
     *
     * @param string $authorizationCode
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function connect(string $authorizationCode, int $userId): array
    {
        $clientId = config('laravelmeet.zoom.client_id');
        $clientSecret = config('laravelmeet.zoom.client_secret');
        
        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $authorizationCode,
                'redirect_uri' => config('laravelmeet.zoom.redirect_uri'),
            ]);
        
        if ($response->failed()) {
            throw new \Exception('Failed to exchange Zoom authorization code for tokens.');
        }
        
        $data = $response->json();
        
        app(OAuthTokenStore::class)->saveTokens('zoom', $userId, [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at' => Carbon::now()->addSeconds($data['expires_in']),
        ]);
        
        return $data;
    }
    
    /**
     * @param int $userId
     * @return bool
     */
    public function disconnect(int $userId): bool
    {
        $tokenData = app(OAuthTokenStore::class)->getTokens('zoom', $userId);
        $accessToken = $tokenData['access_token'];
        
        $clientId = config('laravelmeet.zoom.client_id');
        $clientSecret = config('laravelmeet.zoom.client_secret');
        
        Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/revoke', [
                'token' => $accessToken,
            ]);
        
        app(OAuthTokenStore::class)->deleteTokens('zoom', $userId);
        
        return true;
    }
    
    /**
     * Generates the Zoom OAuth authorization URL.
     *
     * @param string|null $state Optional state parameter to include in the authorization request.
     * @return string The fully constructed authorization URL.
     */
    public function getAuthorizationUrl(string $state = null): string
    {
        $clientId = config('laravelmeet.zoom.client_id');
        $baseAuthUrl = 'https://zoom.us/oauth/authorize';
        
        $query = [
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => config('laravelmeet.zoom.redirect_uri'),
        ];
        
        if ($state) {
            $query['state'] = $state;
        }
        
        return $baseAuthUrl . '?' . http_build_query($query);
    }

    /**
     * Refreshes the Zoom OAuth token using a refresh token.
     *
     * @param string $refreshToken
     * @param string $userId
     * @return array
     * @throws \Exception
     */
    public function refreshToken(string $refreshToken, string $userId): array
    {
        $clientId = config('laravelmeet.zoom.client_id');
        $clientSecret = config('laravelmeet.zoom.client_secret');

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);
        
        if ($response->failed()) {
            throw new \Exception('Failed to refresh Zoom token.');
        }

        $data = $response->json();

        app(OAuthTokenStore::class)->saveTokens('zoom', $userId, [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at' => Carbon::now()->addSeconds($data['expires_in']),
        ]);

        return $data;
    }
}
