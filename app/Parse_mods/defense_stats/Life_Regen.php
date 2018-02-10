<?php

namespace App\Parse_mods\defense_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Life_Regen extends Stat
{
	private $parentMod = '% of Life Regenerated per second';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Life Regen p.s';
        $modValue = (float) filter_var( $mod, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% of Life Regenerated per second' || $modName === '#.#% of Life Regenerated per second') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}