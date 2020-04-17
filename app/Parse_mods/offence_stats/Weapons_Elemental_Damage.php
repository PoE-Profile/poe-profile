<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Weapons_Elemental_Damage extends Stat
{

	private $parentMod = '% increased Elemental Damage with Weapons';

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Weapons Ele Damage';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Elemental Damage with Weapons') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

    }

}