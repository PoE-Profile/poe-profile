<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Effect_of_Auras extends Stat
{
	private $parentMod = '% increased effect of Non-Curse Auras you Cast';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% effect of Auras';

        $mod=str_replace("Non-Curse", "Non Curse", $mod);

        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased effect of Non Curse Auras you Cast') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}