<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Endurance_Charges_Duration extends Stat
{
	private $parentMod = '% Endurance Charge Duration';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Endurance Charge Duration';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Endurance Charge Duration') {
            $this->setVal($modValue);
        }
    }
}