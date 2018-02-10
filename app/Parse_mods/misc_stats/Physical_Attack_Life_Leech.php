<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Physical_Attack_Life_Leech extends Stat
{
	private $parentMod = '%Physical Attack Damage Leeched as Life';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Physical Life Leech';

        $modValue = (float) filter_var( $mod, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% of Physical Attack Damage Leeched as Life' || $modName === '#.#% of Physical Attack Damage Leeched as Life') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}