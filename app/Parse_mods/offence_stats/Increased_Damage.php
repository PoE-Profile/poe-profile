<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Increased_Damage extends Stat
{
	private $parentMod = '% increased Damage';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% increased Damage';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Damage') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }
}