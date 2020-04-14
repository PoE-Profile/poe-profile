<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Totem_Damage extends Stat
{
	private $parentMod = '% increased Totem Damage';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Totem Damage';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Totem Damage') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }
}