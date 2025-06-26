<?php

namespace LaravelMeet\Contracts;

interface OAuthTokenStore {
    public function saveTokens(string $provider, string $userId, array $tokens): void;
    public function getTokens(string $provider, string $userId): ?array;
    public function refreshToken(string $provider, string $userId): array;
    
    public function deleteTokens(string $provider, string $userId): void;
}
