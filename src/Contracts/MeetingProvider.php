<?php

namespace LaravelMeet\Contracts;

interface MeetingProvider {
    public function createMeeting(array $data);
    public function deleteMeeting(int $userId, string $meetingId);
    
    public function getAuthorizationUrl();
    public function connect(string $authorizationCode, int $userId);
    public function disconnect(int $userId);
}
