<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

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
            'group_id' => 35294456,
            'count' => 1
        ], 'verify' => false]);
        $VKResponse = (string) $res->getBody();
        $VKResponse = json_decode($VKResponse);
        $count = $VKResponse->response->count / 1000;
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
                //dd($member);
                Member::create(['id_member' => $member, 'id_group' => '35294456']);
                $bar->advance();
            }
        }
        $bar->finish();
        $this->info('\n Members add successfully');
    }
}
