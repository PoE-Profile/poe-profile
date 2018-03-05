<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class SkillTreeController extends CacheController
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
        return view('tree');
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
        $responseThree = $this->getTreeCache($acc, $char);
        return $responseThree;
    }
}
