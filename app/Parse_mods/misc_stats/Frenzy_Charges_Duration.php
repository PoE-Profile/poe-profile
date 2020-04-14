<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Frenzy_Charges_Duration extends Stat
{
	private $parentMod = '% Frenzy Charge Duration';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Frenzy Charge Duration';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Frenzy Charge Duration') {
            $this->setVal($modValue);
        }
    }
}