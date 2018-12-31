<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LadderController extends Controller
{
    public function privateLadder($ladder_name)
    {
        return view('private_ladder', compact('ladder_name'));
    }
}
