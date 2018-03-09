<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Resist_Lightning extends Stat
{
    private $parentMod = '% to Lightning Resistance';
    // public $note = 'Resistances from Bandits Quest not included<br>';
    private $associatedMods= [
        '+#% to Lightning Resistance',
        '+#% to Fire and Lightning Resistances',
        '+#% to Cold and Lightning Resistances',
        '+#% to all Elemental Resistances'
    ];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Lightning Resistance';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);


        if ( in_array($modName, $this->associatedMods) ) {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        if ( $modName === '-#% to Lightning Resistance' || $modName === '-#% to all Elemental Resistances') {
            $this->setVal($modValue);
        }
        $this->text = ($this->total - 60) . '% (' . $this->total . ') Lightning Resistance';
    }
}