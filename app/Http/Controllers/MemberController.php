<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function show()
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'http://api.vk.com/method/groups.getMembers', ['query' =>[
            'v' => '5.9',
            'group_id' => 35294456,
            'count' => 1
        ], 'verify' => false ]);
        $VKResponse = (string) $res->getBody();
        $VKResponse = json_decode($VKResponse);
        $count = $VKResponse->response->count / 1000;
        for($i = 0; $i < $count; $i++) {
            $res = $client->request('GET', 'http://api.vk.com/method/groups.getMembers', ['query' =>[
                'v' => '5.5',
                'offset' => $i * 1000,
                'group_id' => 35294456,
                'count' => 1000
            ]]);
            $VKResponse1000 = (string) $res->getBody();
            $VKResponse1000 = json_decode($VKResponse1000);
            foreach ($VKResponse1000->response->items as $member) {
                Member::create(['id_member' => $member, 'id_group' => 35294456]);
              
            }
        }
      return view('welcome');
    }
}
