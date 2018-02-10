<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Totem_Resistances extends Stat
{
	private $parentMod = '% Totems all Elemental Resistances';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Totem Elemental Resistances';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === 'Totems gains #% to all Elemental Resistances') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }
}