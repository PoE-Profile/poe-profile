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

    public function showSkillTree(Request $request){
        if(!\Storage::exists('passive-skill-tree.html')){
            \Artisan::call('poe:tree');
        }
        return \Storage::get('passive-skill-tree.html');
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
        $realm=$_GET['realm'];
        $responseThree = PoeApi::getTreeData($acc, $char, $realm);
        return $responseThree;
    }
}
