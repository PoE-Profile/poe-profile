<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Physical_Attack_Mana_Leech extends Stat
{
	private $parentMod = '%Physical Attack Damage Leeched as Mana';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Physical Mana Leech';

        $modValue = (float) filter_var( $mod, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% of Physical Attack Damage Leeched as Mana' || $modName === '#.#% of Physical Attack Damage Leeched as Mana') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}