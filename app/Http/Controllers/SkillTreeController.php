<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\PoeApi;
use Illuminate\Http\Request;

class SkillTreeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function showSkillTree(){
        // dd(\Input::all());
        $version = 'tree.'.str_replace(".", "_", config('app.poe_version'));
        if (\Input::has('version')) {
            $version = 'tree.' . str_replace(".", "_", \Input::get('version'));
        }
        return view($version);
    }

    public function showSkillTreeTwo()
    {
        // dd('two');
        
        return view('tree.3_3');
    }

    public function getPassiveSkills()
    {
        $b = explode('::', $_GET['accountName']);
        if($b[0] == 'build'){
            $snapshot=\App\Snapshot::where('hash','=',$b[1])->first();
            return $snapshot->tree_data;
        }
        $dbAcc = \App\Account::where('name', $_GET['accountName'])->first();
        $acc=$dbAcc->name;
        $char=$_GET['character'];
        $responseThree = PoeApi::getTreeData($acc, $char);
        return $responseThree;
    }
}
