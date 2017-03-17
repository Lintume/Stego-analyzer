<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class parseGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        $res = $client->request('GET', 'http://api.vk.com/method/groups.getMembers', ['query' =>[
            'v' => '5.9',
            'group_id' => 120416132,
            'count' => 1
        ], 'verify' => false]);
        $VKResponse = (string) $res->getBody();
        $VKResponse = json_decode($VKResponse);
        $count = intdiv($VKResponse->response->count, 1000);
        $bar = $this->output->createProgressBar($VKResponse->response->count);
        for($i = 0; $i < $count; $i++) {
            $res = $client->request('GET', 'http://api.vk.com/method/groups.getMembers', ['query' =>[
                'v' => '5.5',
                'offset' => $i * 1000,
                'group_id' => 120416132,
                'count' => 1000,
                'fields' => 'name'
            ], 'verify' => false]);
            $VKResponse1000 = (string) $res->getBody();
            $VKResponse1000 = json_decode($VKResponse1000);
            foreach ($VKResponse1000->response->users as $member) {
                Member::firstOrCreate(['id_member' => $member->id, 'first_name'=>$member->first_name, 'last_name' => $member->last_name, 'id_group' => '120416132']);
                $bar->advance();
            }
        }
        $bar->finish();
        $this->info(sprintf('Members add successfully'));

        $this->info(sprintf('Start searching leaders...'));
        $membersParsed = Member::where('id_group', 120416132)->get();
        $bar = $this->output->createProgressBar($membersParsed->count());
        foreach ($membersParsed as $mem) {
            $bar->advance();
            $res = $client->request('GET', 'https://api.vk.com/method/friends.get', ['query' => [
                'v' => '5.9',
                'user_id' => $mem->id_member,
                'count' => 1,
                'access_token' => 'a16db27311c9635bdb5a913ae312cdcd872833a2a1294ae5f1b8af32e41562a8726da40a8cd96f9472c67'
            ], 'verify' => false]);
            $VKResponse = (string) $res->getBody();
            $VKResponse = json_decode($VKResponse);
            if(property_exists($VKResponse, 'error'))
            {
                $this->info('ID first: '. $mem->id_member .' '. $VKResponse->error->error_msg);
                if($VKResponse->error->error_code == 18)
                {
                    $mem->delete();
                }
                continue;
            }
            $countLoops = intdiv($VKResponse->response->count, 1000) + 1;
            $countWeight = 0;
            sleep(1);
            for($i = 0; $i < $countLoops; $i++) {
                $friendsRes = $client->request('GET', 'https://api.vk.com/method/friends.get', ['query' => [
                    'v' => '5.9',
                    'user_id' => $mem->id_member,
                    'offset' => $i * 1000,
                    'count' => 1000,
                    'access_token' => 'a16db27311c9635bdb5a913ae312cdcd872833a2a1294ae5f1b8af32e41562a8726da40a8cd96f9472c67'
                ], 'verify' => false]);
                $VKResponse = (string)$friendsRes->getBody();
                $VKResponse = json_decode($VKResponse);
                if(property_exists($VKResponse, 'error'))
                {
                    $this->info('ID second: '. $mem->id_member .' '. $VKResponse->error->error_msg);
                    continue;
                }
               // dd($VKResponse);
                foreach ($VKResponse->response->items as $friend) {
                    foreach ($membersParsed as $item) {
                        if ($item->id_member == $friend) {
                            $countWeight++;
                        }
                    }
                }
                sleep(1);
            }
            $mem->update(['weight' => $countWeight]);
        }
        $bar->finish();
        $this->info(sprintf('Leaders find!'));
    }
}
