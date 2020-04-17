<?php

namespace App\Parse_mods\misc_stats;

use App\Parse_mods\ModParse;
use App\Parse_mods\Stat;

class Skill_Effect_Duration extends Stat
{
	private $parentMod = '% increased Skill Effect Duration';
    public function parse($mod, $type)
    {
        $this->setType($type);
        $this->name ='% Skill Effect Duration';

        $modValue = (int) filter_var($mod, FILTER_SANITIZE_NUMBER_INT);
        $modName = preg_replace('/\d+/u', '#', $mod);

        if ( $modName === '#% increased Skill Effect Duration') {
            $this->setVal($modValue);
            $this->setItemVal($modValue, $mod);
        }
    }

}