<?php

namespace App\Http\Controllers;

use App\Models\Member;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function show()
    {
//        $client = new Client();
//        $membersParsed = Member::where('id_group', 120416132)->get();
//        $mem = Member::where('id_member', 35218464)->first();
//
//        $countLoops = 0;
//        $countWeight = 0;
//        for($i = 0; $i < 1; $i++) {
//            $friendsRes = $client->request('GET', 'https://api.vk.com/method/friends.get', ['query' => [
//                'v' => '5.9',
//                'user_id' => $mem->id_member,
//                'offset' => $i * 1000,
//                'count' => 1000,
//                'access_token' => 'a16db27311c9635bdb5a913ae312cdcd872833a2a1294ae5f1b8af32e41562a8726da40a8cd96f9472c67'
//            ], 'verify' => false]);
//            $VKResponse = (string)$friendsRes->getBody();
//            $VKResponse = json_decode($VKResponse);
//            if(property_exists($VKResponse, 'error'))
//            {
//                $this->info('ID second: '. $mem->id_member .' '. $VKResponse->error->error_msg);
//                continue;
//            }
//            // dd($VKResponse);
//            foreach ($VKResponse->response->items as $friend) {
//                foreach ($membersParsed as $item) {
//                    if ($item->id_member == $friend) {
//                        $countWeight++;
//                    }
//                }
//            }
//            sleep(1);
//        }
//        $mem->update(['weight' => $countWeight]);
//120416132
       $members = Member::where('id_group', 3183750)->where('weight', '>', 0)->orderBy('weight', 'DESC')->take(15)->get();
        return view('leaders', compact('members'));
    }
}
