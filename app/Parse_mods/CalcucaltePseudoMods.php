<?php
namespace App\Parse_mods;

use App\Parse_mods\defense_stats\Resist_Fire;
use App\Parse_mods\defense_stats\Resist_Cold;
use App\Parse_mods\defense_stats\Resist_Lightning;
use App\Parse_mods\defense_stats\Energy_Shield;
use App\Parse_mods\defense_stats\Armor;
use App\Parse_mods\defense_stats\Evasion;
use App\Parse_mods\defense_stats\Life;
use App\Parse_mods\defense_stats\Mana;
//base
use App\Parse_mods\base_stats\Strength;
use App\Parse_mods\base_stats\Intelligence;
use App\Parse_mods\base_stats\Dexterity;

class CalcucaltePseudoMods
{
    public $stats = [];
    public $currentItemStats = [];
    public $type = '';

    public function __construct()
    {

        $this->stats = [
            'total +# to Fire Resistance' => new Resist_Fire,
            'total +# to Cold Resist' => new Resist_Cold,
            'total +# to Lightning Resist' => new Resist_Lightning,
            'total +# to Energy shield' => new Energy_Shield,
            'total +# to Armor' => new Armor,
            'total +# to Evasion' => new Evasion,
            'total +# to maximum Life' => new Life,
            'total +# to maximum Mana' => new Mana,
            'total +# to Strength' => new Strength,
            'total +# to Intelligence' => new Intelligence,
            'total +# to Dexterity' => new Dexterity,
            ];
    }

    public function addMods($mods)
    {
        foreach ($mods as $mod) {
            $this->check($mod);
        }
    }

    private function check($stat)
    {
        foreach ($this->stats as $m) {
            $m->parse($stat, 'item');
        }
    }

    public function getMods()
    {
        return $this->stats;
    }
}
