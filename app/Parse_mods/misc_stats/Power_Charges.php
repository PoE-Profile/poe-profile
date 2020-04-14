<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Power_Charges extends Stat
{
	private $parentMod = 'to Power Charges';
    public $baseVal = 3;
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='Power Charges';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '+# to Maximum Power Charges') {
            $this->setVal($modValue);
        }

        $this->total = $this->itemVal + $this->treeVal + $this->jewVal + $this->baseVal;
    }

}