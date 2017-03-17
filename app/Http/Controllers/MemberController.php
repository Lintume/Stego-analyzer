<?php

namespace App\Http\Controllers;

use App\Models\Member;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function show()
    {
       $members = Member::where('id_group', 120416132)->whereNotNull('weight')->orderBy('weight', 'DESC')->take(15)->get();
        return view('leaders', compact('members'));
    }
}
