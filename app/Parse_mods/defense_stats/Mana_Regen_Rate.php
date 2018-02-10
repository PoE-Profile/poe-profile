<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Mana_Regen_Rate extends Stat
{
	private $parentMod = '% increased Mana Regeneration Rate';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Mana Regen Rate';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);


        if ( $modName === '#% increased Mana Regeneration Rate') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}