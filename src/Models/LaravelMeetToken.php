<?php

namespace LaravelMeet\Models;



use Illuminate\Database\Eloquent\Model;

class LaravelMeetToken extends Model {
    
    protected $table = 'laravel_meet_tokens';
    
    protected $fillable = ['user_id', 'provider', 'access_token', 'refresh_token', 'expires_at'];
    
    public function user() {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
