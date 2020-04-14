<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Frenzy_Charges extends Stat
{
	private $parentMod = 'to Frenzy Charges';
    public $baseVal = 3;
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Frenzy Charges';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '+# to Maximum Frenzy Charges') {
            $this->setVal($modValue);
        }

        $this->total = $this->itemVal + $this->treeVal + $this->jewVal + $this->baseVal;
    }

}