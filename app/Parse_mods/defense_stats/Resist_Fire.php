<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Resist_Fire extends Stat
{
    private $parentMod = '% to Fire Resistance';
    // public $note = 'Resistances from Bandits Quest not included<br>';
    public $fireResist_Mods = [
        '+#% to Fire Resistance',
        '+#% to Fire and Lightning Resistances',
        '+#% to Fire and Cold Resistances',
        '+#% to all Elemental Resistances'
    ];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Fire Resistance';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( in_array($modName, $this->fireResist_Mods) ) {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        if ( $modName === '-#% to Fire Resistance' || $modName === '-#% to all Elemental Resistances') {
            $this->setVal($modValue);
        }

        $this->text = ($this->total - 60) . '% (' . $this->total . ') Fire Resistance';
    }
}