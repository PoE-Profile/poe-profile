<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Spell_Critical_Multiplier extends Stat
{
	private $parentMod = '% to Critical Strike Multiplier for spells';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Critical Multiplier for Spells';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '+#% to Global Critical Strike Multiplier') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        if ( $modName === '+#% to Critical Strike Multiplier') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        // From items // tree // jews
        if ( $modName === '+#% to Critical Strike Multiplier for Spells') {
            $this->setVal($modValue);
        }
    }
}