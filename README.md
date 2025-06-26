# laravelmeet
A Laravel package to manage Zoom/Google Meet etc.


## Publish Config

```php artisan vendor:publish --tag="laravelmeet-config"```

## Get authorize url
```
LaravelMeet::getAuthorizationUrl();
```


## Connect zoom using oauth
```
LaravelMeet::connect($authorizationCode, $userId);
```

$authorizationCode will be returned in callback function from zoom. Callback url must be set ```laravelmeet.zoom.redirect_uri```

## Create Meeting
```
         $m = LaravelMeet::createMeeting([
                    'user_id' => $user_id,
                    'start_time' => $time,
                    'duration' => $duration,
                    'topic' => $topic,
                ]);
```

