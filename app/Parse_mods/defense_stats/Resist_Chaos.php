<?php

namespace App\Parse_mods\defense_stats;


use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;


class Resist_Chaos extends Stat
{
	private $parentMod = '% to Chaos Resistance';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Chaos Resistance';
        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);


        if ( $modName === '+#% to Chaos Resistance') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }

		if ( $modName === '-#% to Chaos Resistance') {
            $this->setVal($modValue);
        }

        $this->text = ($this->total - 60) . '% (' . $this->total .') Chaos Resistance';
    }

}