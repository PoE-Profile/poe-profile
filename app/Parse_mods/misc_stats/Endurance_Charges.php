<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Endurance_Charges extends Stat
{
	private $parentMod = 'to Endurance Charges';
    public $baseVal = 3;
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Endurance Charges';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '+# to Maximum Endurance Charges') {
            $this->setVal($modValue);
        }

        $this->total = $this->itemVal + $this->treeVal + $this->jewVal + $this->baseVal;
    }

}