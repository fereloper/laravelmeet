<?php

namespace LaravelMeet;

use Illuminate\Support\Carbon;
use LaravelMeet\Contracts\OAuthTokenStore;
use LaravelMeet\Models\LaravelMeetToken;

class DatabaseOAuthTokenStore implements OAuthTokenStore
{
    /**
     * @param string $provider
     * @param string $userId
     * @param array $tokens
     * @return void
     */
    public function saveTokens(string $provider, string $userId, array $tokens): void
    {
        LaravelMeetToken::updateOrCreate(
            ['provider' => $provider, 'user_id' => $userId],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'] ?? null,
                'expires_at' =>  $tokens['expires_at'] ?? null,
            ]
        );
    }
    
    /**
     * Retrieve tokens for a given provider and user ID.
     *
     * @param string $provider The name of the provider (e.g., Google, Facebook).
     * @param string $userId The unique identifier of the user.
     * @return array|null An array containing access token, refresh token, and expiration time, or null if no record is found.
     */
    public function getTokens(string $provider, string $userId): ?array
    {
        $record = LaravelMeetToken::where('provider', $provider)
            ->where('user_id', $userId)
            ->first();
        
        return $record ? [
            'access_token' => $record->access_token,
            'refresh_token' => $record->refresh_token,
            'expires_at' => $record->expires_at,
        ] : null;
    }
    
    /**
     * @param string $provider
     * @param string $userId
     * @return array
     */
    public function refreshToken(string $provider, string $userId): array
    {
        // Placeholder logic. In real usage, you'd make an API call to refresh the token.
        $tokens = $this->getTokens($provider, $userId);
        
        if (!$tokens) {
            throw new \RuntimeException("No tokens found for $provider and user $userId.");
        }
        
        return $tokens;
    }
    
    public function deleteTokens(string $provider, string $userId): void
    {
        LaravelMeetToken::where('provider', $provider)
            ->where('user_id', $userId)
            ->delete();
    }
}
