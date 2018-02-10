<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Damage_Over_Time extends Stat
{
	private $parentMod = '% increased Damage over Time';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Damage over Time';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Damage over Time') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}