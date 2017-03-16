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
                'group_id' => 35294456,
                'count' => 1000
            ], 'verify' => false]);
            $VKResponse1000 = (string) $res->getBody();
            $VKResponse1000 = json_decode($VKResponse1000);
            foreach ($VKResponse1000->response->users as $member) {
                Member::firstOrCreate(['id_member' => $member, 'id_group' => '120416132']);
                $bar->advance();
            }
        }
        $bar->finish();
        $this->info(sprintf('Members add successfully'));

        $this->info(sprintf('Start searching leaders...'));
        $membersParsed = Member::where('id_group', 120416132)->get();
        foreach ($membersParsed as $mem) {
            $res = $client->request('GET', 'http://api.vk.com/method/groups.getMembers', ['query' =>[
            'v' => '5.9',
            'group_id' => 120416132,
            'count' => 1
            ], 'verify' => false]);
            $VKResponse = (string) $res->getBody();
            $VKResponse = json_decode($VKResponse);
            $countLoops = intdiv($VKResponse->response->count, 1000);
            $bar = $this->output->createProgressBar($VKResponse->response->count);
            for($i = 0; $i < $countLoops; $i++) {

                $countWeight = 0;
                $friendsRes = $client->request('GET', 'http://api.vk.com/method/friend.get', ['query' => [
                    'v' => '5.9',
                    'user_id' => $mem->id_member,
                    'offset' => $i * 1000,
                    'count' => 1000
                ], 'verify' => false]);
                $VKResponse = (string)$friendsRes->getBody();
                $VKResponse = json_decode($VKResponse);
                foreach ($VKResponse->response->items as $friend) {
                    if ($mem->id_member == $friend->id) {
                        $countWeight++;
                    }
                    $bar->advance();
                }
                $mem->update(['weight' => $countWeight]);
            }
        }
        $bar->finish();
        $this->info(sprintf('Leaders find!'));
    }
}
