<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Resist_Cold extends Stat
{
    private $parentMod = '% to Cold Resistance';
    // public $note = 'Resistances from Bandits Quest not included<br>';
    public $coldResist_Mods = [
        '+#% to Cold Resistance',
        '+#% to Cold and Lightning Resistances',
        '+#% to Fire and Cold Resistances',
        '+#% to all Elemental Resistances'
    ];

    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Cold Resistance';
        $modValue = filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);


        if ( in_array($modName, $this->coldResist_Mods) ) {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

        if ( $modName === '-#% to Cold Resistance' || $modName === '-#% to all Elemental Resistances') {
            $this->setVal($modValue);
        }
    }

}