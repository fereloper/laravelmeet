<?php

namespace LaravelMeet;

use LaravelMeet\Contracts\MeetingProvider;
use LaravelMeet\Providers\ZoomProvider;

class LaravelMeetManager implements MeetingProvider {
    protected $provider;
    
    public function __construct() {
        $driver = config('laravelmeet.driver');
        
        switch ($driver) {
            case 'zoom':
                $this->provider = new ZoomProvider();
                break;
            default:
                throw new \Exception("Unsupported meeting provider: $driver");
        }
    }
    
    public function createMeeting(array $data) {
        return $this->provider->createMeeting($data);
    }
    
    public function deleteMeeting(int $userId, string $meetingId) {
        return $this->provider->deleteMeeting($userId, $meetingId);
    }
    
    public function getAuthorizationUrl(): string
    {
        return $this->provider->getAuthorizationUrl();
    }
    
    public function connect(string $authorizationCode, int $userId): array
    {
        return $this->provider->connect($authorizationCode, $userId);
    }
    
    
    public function disconnect(int $userId): bool
    {
        return $this->provider->disconnect($userId);
    }
}
