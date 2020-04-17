<?php

namespace App\Parse_mods\offence_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Spells_Critical_Chance extends Stat
{
    private $weapons = ['Weapon', 'Weapon2', 'Offhand', 'Offhand2'];
	private $parentMod = '% increased Critical Strike Chance for Spells';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Critical Chance for Spells';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        // From items // tree // jews
        if ( $modName === '#% increased Global Critical Strike Chance') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        // From items // tree // jews
        if ( $modName === '#% increased Critical Strike Chance for Spells') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        if ($this->currentType === 'item' && !in_array( $this->currentItemType , $this->weapons)) {
            if ( $modName === '#% increased Critical Strike Chance') {
                $this->setVal($modValue);
                $this->setItemVal($modValue, $mod);
            }
        }

        // From items // tree // jews
        if ($this->currentType !== 'item' && $modName === '#% increased Critical Strike Chance') {
            $this->setVal($modValue);
        }

    }
}