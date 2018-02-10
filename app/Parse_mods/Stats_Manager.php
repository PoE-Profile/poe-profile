<?php
namespace App\Parse_mods;

//offence
use App\Parse_mods\offence_stats\Attack_Critical_Chance;
use App\Parse_mods\offence_stats\Spells_Critical_Chance;
use App\Parse_mods\offence_stats\Attack_Critical_Multiplier;
use App\Parse_mods\offence_stats\Spell_Critical_Multiplier;
use App\Parse_mods\offence_stats\Spell_Damage;
use App\Parse_mods\offence_stats\Elemental_Damage;
use App\Parse_mods\offence_stats\Weapons_Elemental_Damage;
use App\Parse_mods\offence_stats\Fire_Damage;
use App\Parse_mods\offence_stats\Cold_Damage;
use App\Parse_mods\offence_stats\Lightning_Damage;
use App\Parse_mods\offence_stats\Chaos_Damage;
use App\Parse_mods\offence_stats\Increased_Damage;
use App\Parse_mods\offence_stats\Projectile_Damage;
use App\Parse_mods\offence_stats\Damage_Over_Time;
use App\Parse_mods\offence_stats\Accuracity;
use App\Parse_mods\offence_stats\Accuracy_Rating;
use App\Parse_mods\offence_stats\Radius_of_Area_Skills;
use App\Parse_mods\offence_stats\Area_Damage;
use App\Parse_mods\offence_stats\Cast_Speed;
use App\Parse_mods\offence_stats\Melee_Phys_Damage;
use App\Parse_mods\offence_stats\Wands_Damage;
use App\Parse_mods\offence_stats\Physical_Damage;
use App\Parse_mods\offence_stats\Attack_Speed;

//defense
use App\Parse_mods\defense_stats\Resist_Fire;
use App\Parse_mods\defense_stats\Resist_Cold;
use App\Parse_mods\defense_stats\Resist_Lightning;
use App\Parse_mods\defense_stats\Resist_Chaos;
use App\Parse_mods\defense_stats\Energy_Shield;
use App\Parse_mods\defense_stats\Block;
use App\Parse_mods\defense_stats\Armor;
use App\Parse_mods\defense_stats\Evasion;
use App\Parse_mods\defense_stats\Life;
use App\Parse_mods\defense_stats\Mana;
use App\Parse_mods\defense_stats\Life_Regen;
use App\Parse_mods\defense_stats\Mana_Regen_Rate;
use App\Parse_mods\defense_stats\Percentage_Life;
use App\Parse_mods\defense_stats\Percentage_ES;
use App\Parse_mods\defense_stats\Percentage_Mana;
use App\Parse_mods\defense_stats\Percentage_Armor;
use App\Parse_mods\defense_stats\Percentage_Evasion;
use App\Parse_mods\defense_stats\Movement_Speed;
//base
use App\Parse_mods\base_stats\Strength;
use App\Parse_mods\base_stats\Intelligence;
use App\Parse_mods\base_stats\Dexterity;
//misc
use App\Parse_mods\misc_stats\Rarity;
use App\Parse_mods\misc_stats\Quantity;
use App\Parse_mods\misc_stats\Skill_Effect_Duration;
use App\Parse_mods\misc_stats\Projectile_Speed;
use App\Parse_mods\misc_stats\Flask_Charges_Gained;
use App\Parse_mods\misc_stats\Flask_Effect_Duration;
use App\Parse_mods\misc_stats\Reduced_Flask_Charges;
use App\Parse_mods\misc_stats\Effect_of_Curses;
use App\Parse_mods\misc_stats\Reduced_Mana_Reserved;
use App\Parse_mods\misc_stats\Effect_of_Auras;
use App\Parse_mods\misc_stats\Physical_Attack_Life_Leech;
use App\Parse_mods\misc_stats\Physical_Attack_Mana_Leech;
use App\Parse_mods\misc_stats\Totem_Damage;
use App\Parse_mods\misc_stats\Totem_Life;
use App\Parse_mods\misc_stats\Totem_Placement_Speed;
use App\Parse_mods\misc_stats\Totem_Duration;
use App\Parse_mods\misc_stats\Totem_Physical_Reduction;
use App\Parse_mods\misc_stats\Totem_Resistances;
use App\Parse_mods\misc_stats\Endurance_Charges;
use App\Parse_mods\misc_stats\Endurance_Charges_Duration;
use App\Parse_mods\misc_stats\Power_Charges;
use App\Parse_mods\misc_stats\Power_Charges_Duration;
use App\Parse_mods\misc_stats\Frenzy_Charges;
use App\Parse_mods\misc_stats\Frenzy_Charges_Duration;

class Stats_Manager
{
    public $stats = [];
    public $currentItemStats = [];
    public $type = '';

    public function __construct()
    {
        $this->stats = [
            'misc' => [
                new Rarity,
                new Quantity,
                new Skill_Effect_Duration,
                new Projectile_Speed,
                new Flask_Charges_Gained,
                new Flask_Effect_Duration,
                new Reduced_Flask_Charges,
                new Effect_of_Curses,
                new Reduced_Mana_Reserved,
                new Effect_of_Auras,
                new Physical_Attack_Life_Leech,
                new Physical_Attack_Mana_Leech,
                new Totem_Damage,
                new Totem_Life,
                new Totem_Placement_Speed,
                new Totem_Duration,
                new Totem_Physical_Reduction,
                new Totem_Resistances,
                new Endurance_Charges,
                new Endurance_Charges_Duration,
                new Power_Charges,
                new Power_Charges_Duration,
                new Frenzy_Charges,
                new Frenzy_Charges_Duration,
            ],
            'defense' => [
                new Resist_Fire,
                new Resist_Cold,
                new Resist_Lightning,
                new Resist_Chaos,
                new Armor,
                new Percentage_Armor,
                new Evasion,
                new Percentage_Evasion,
                new Life,
                new Percentage_Life,
                new Mana,
                new Percentage_Mana,
                new Energy_Shield,
                new Percentage_ES,
                new Strength,
                new Intelligence,
                new Dexterity,
                new Block,
                new Life_Regen,
                new Mana_Regen_Rate,
            ],
            'offense' => [
                new Attack_Speed,
                new Cast_Speed,
                new Wands_Damage,
                new Physical_Damage,
                new Melee_Phys_Damage,
                new Attack_Critical_Chance,
                new Spells_Critical_Chance,
                new Radius_of_Area_Skills,
                new Area_Damage,
                new Attack_Critical_Multiplier,
                new Spell_Critical_Multiplier,
                new Spell_Damage,
                new Elemental_Damage,
                new Weapons_Elemental_Damage,
                new Fire_Damage,
                new Cold_Damage,
                new Lightning_Damage,
                new Chaos_Damage,
                new Increased_Damage,
                new Projectile_Damage,
                new Accuracity,
                new Accuracy_Rating,
                new Damage_Over_Time,
                new Movement_Speed
            ]
        ];
    }

    public function addItem($item)
    {
        $this->type = 'item/'.$item['inventoryId'] . '/'. $this->getTypeFromIcon($item['icon']);
       

        if (isset($item["explicitMods"])) {
            $this->addMods($item['explicitMods']);
        }

        if (isset($item['implicitMods'][0])) {
            $this->addMods($item['implicitMods']);
        }

        if (isset($item['craftedMods'][0])) {
            $this->addMods($item['craftedMods']);
        }

        if (isset($item['properties'])) {
            $this->addProps($item['properties']);
        }
        $this->addAbyssJewels($item);
    }

    public function addTreeNode($nodes)
    {
        foreach ($nodes as $node) {
            $this->type = $node['type'];

            if (count($node['mods']) > 0) {
                $this->addMods($node['mods']);
            }
        }
    }

    public function addBaseMods($mod)
    {
        $this->type = 'base';
        if ($mod) {
            $this->check($mod);
        }
    }

    private function addMods($mods)
    {
        foreach ($mods as $mod) {
            $this->check($mod);
        }
    }

    private function addProps($props)
    {
        foreach ($props as $prop) {
            if (count($prop['values']) == 0) {
                if (!isset($prop['type'])) {
                    $this->check('Weapon type:'.$prop['name']);
                }
                continue;
            }

            $tempStr = $prop['values'][0][0] . ' ' . $prop['name']; // 800 Armour
            $this->check($tempStr);
        }
    }

    private function check($stat)
    {
        foreach ($this->stats['defense'] as $m) {
            $m->parse($stat, $this->type);
        }

        foreach ($this->stats['offense'] as $m) {
            $m->parse($stat, $this->type);
        }

        foreach ($this->stats['misc'] as $m) {
            $m->parse($stat, $this->type);
        }
    }

    private function finalCalculations()
    {
        foreach ($this->stats['defense'] as $m) {
            $m->finalCalcualtion($this->stats);
        }

        foreach ($this->stats['offense'] as $m) {
            $m->finalCalcualtion($this->stats);
        }

        foreach ($this->stats['misc'] as $m) {
            $m->finalCalcualtion($this->stats);
        }
    }

    public function getStats()
    {
        $this->finalCalculations();
        return $this->stats;
    }

    private function getTypeFromIcon($icon)
    {
        $pieces = explode("/", $icon);
        $type="unknown";
        if (count($pieces)<=10) {
            $type=$pieces[count($pieces)-2];
        }

        return $type;
    }

    private function addAbyssJewels($item)
    {   
        //To Do get all Abyss jewels and creaate TYPE and count them LIKE "Belt Abyssal Socket 1"
        $countAbys = 0;
        if (array_key_exists('socketedItems', $item)) {
            foreach ($item['socketedItems'] as $k => $i) {
                if ($i['properties'][0]['name'] === 'Abyss') {
                    $abyss = $i;
                    $countAbys++;

                    $type = $item['inventoryId'];
                    if (isset($abyss["explicitMods"])) {
                        // $this->type = 'item/'.$type . ' Abys' . $countAbys . '/';
                        $this->type = 'jewel';
                        $this->addMods($abyss['explicitMods']);
                    }
                }
            }
        }

    }
}
