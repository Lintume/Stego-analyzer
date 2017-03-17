<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('social:setup', function () {
    \App\SocialGroup::firstOrCreate(['social_id' => 120416132]);
})->describe('Add groups');

Artisan::command('social:members', function () {
    $groups = \App\SocialGroup::orderBy('id', 'DESC')->get();

    foreach ($groups as $group) {
        $count = 100;
        $offset = 0;
        $client = new GuzzleHttp\Client();
        do {
            $chunk = $client->request('POST', 'https://api.vk.com/method/groups.getMembers', [
                'query' => [
                    'v' => '5.69',
                    'group_id' => $group->social_id,
                    'count' => $count,
                    'offset' => $offset,
                    'access_token'  => env('ACCESS_TOKEN_ZOGXRAY'),
                ],
                'verify' => false
            ])->getBody();
            $chunk = json_decode($chunk);
            if (isset($chunk->error)) {
                $this->error("Members: ".$chunk->error->error_msg);
                die();
            }

            if(isset($chunk->response)) {
                foreach ($chunk->response->items as $item) {
                    $this->info('User:'.$item);
                    $status = $client->request('POST', 'https://api.vk.com/method/status.get', [
                        'query' => [
                            'v' => '5.69',
                            'user_id' => $item,
                            'access_token'  => env('ACCESS_TOKEN_ZOGXRAY'),
                        ],
                        'verify' => false
                    ])->getBody();
                    $status = json_decode($status);
                    if (isset($status->error)) {
                        $this->error("Status: ".$status->error->error_msg);
                    } else {
                        $user = App\SocialUser::firstOrCreate(['social_id' => $item]);
                        $group->users()->attach($user->id);
                    }
                    usleep(1000000/3);
                }
            }
            $offset = $offset+100;
            $this->info('Chunk:'.$offset);
            usleep(1000000/3);
        } while ($offset < $chunk->response->count);
    }

})->describe('Parse group members and remove banned users');

Artisan::command('social:relations', function () {
    $groups = \App\SocialGroup::orderBy('id', 'DESC')->get();
    foreach ($groups as $group) {
        $users = $group->users()->get();
        $client = new GuzzleHttp\Client();
        foreach ($users as $user) {
            $this->info('Process user:'.$user->social_id);
            $friends = $client->request('POST', 'https://api.vk.com/method/friends.get', [
                'query' => [
                    'v' => '5.69',
                    'user_id' => $user->social_id,
                    'order' => 'random',
                    'access_token'  => env('ACCESS_TOKEN_ZOGXRAY'),
                ],
                'verify' => false
            ])->getBody();
            $friends = json_decode($friends);
            if (isset($friends->error)) {
                $this->error("Status: ".$friends->error->error_msg);
            } else {
                if(count($friends->response->items)) {
                    foreach ($friends->response->items as $item) {
                        $this->info('Process friend:'.$item);
                        $status = $client->request('POST', 'https://api.vk.com/method/status.get', [
                            'query' => [
                                'v' => '5.69',
                                'user_id' => $item,
                                'access_token' => env('ACCESS_TOKEN_ZOGXRAY'),
                            ],
                            'verify' => false
                        ])->getBody();
                        $status = json_decode($status);
                        if (isset($status->error)) {
                            $this->error("Status: " . $status->error->error_msg);
                        } else {
                            $friend = App\SocialUser::firstOrCreate(['social_id' => $item]);
                            $user->friends()->attach($friend->id);
                        }
                        usleep(1000000/3);
                    }
                }
            }
            usleep(1000000/3);
        }
    }
})->describe('Parse group members and add members friends');

Artisan::command('social:table', function () {
    $headers = ['group_id', 'user_id', 'weight'];
    $groups = \App\SocialGroup::orderBy('id', 'DESC')->get();
    $data = collect([]);
    foreach ($groups as $group) {
        $users = $group->users()->get();
        foreach ($users as $user) {
            $weight = count($users->intersect($user->friends()->get()));
            $row = ['group_id' => $group->social_id, 'user_id' => $user->social_id, 'weight' => $weight];
            $data->push($row);
        }
    }

    $pony_data = $data->filter(function ($data) {
        return $data['user_id'] == 101942629 || $data['user_id'] == 35218464;
    });

    $data = $data->filter(function ($data) {
        return $data['weight'] > 0;
    });
    $data = $data->sortByDesc('weight');



//    $data = $data->take(10);
    $this->table($headers, $data);

    $this->table($headers, $pony_data);
})->describe('Print leaders');

