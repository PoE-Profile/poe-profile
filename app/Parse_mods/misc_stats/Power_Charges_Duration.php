<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Power_Charges_Duration extends Stat
{
	private $parentMod = '% Power Charge Duration';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Power Charge Duration';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Power Charge Duration') {
            $this->setVal($modValue);
        }
    }
}