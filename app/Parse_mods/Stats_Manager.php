<?php
namespace App\Parse_mods;

class Stats_Manager
{
    public $stats = [];
    public $currentItemStats = [];
    public $type = '';
    public $modValidator = '';

    public function __construct($items,$treeData)
    {
        $character = $items['character'];
        $this->stats = [
            'misc' => [
                new misc_stats\Totems,
                new misc_stats\Rarity,
                new misc_stats\Quantity,
                new misc_stats\Skill_Effect_Duration,
                new misc_stats\Projectile_Speed,
                new misc_stats\Flask_Charges_Gained,
                new misc_stats\Flask_Effect_Duration,
                new misc_stats\Reduced_Flask_Charges,
                new misc_stats\Effect_of_Curses,
                new misc_stats\Reduced_Mana_Reserved,
                new misc_stats\Effect_of_Auras,
                new misc_stats\Physical_Attack_Life_Leech,
                new misc_stats\Physical_Attack_Mana_Leech,
                new misc_stats\Totem_Damage,
                new misc_stats\Totem_Life,
                new misc_stats\Totem_Placement_Speed,
                new misc_stats\Totem_Duration,
                new misc_stats\Totem_Physical_Reduction,
                new misc_stats\Totem_Resistances,
                new misc_stats\Endurance_Charges,
                new misc_stats\Endurance_Charges_Duration,
                new misc_stats\Power_Charges,
                new misc_stats\Power_Charges_Duration,
                new misc_stats\Frenzy_Charges,
                new misc_stats\Frenzy_Charges_Duration,
            ],
            'defense' => [
                new defense_stats\Resist_Cold,
                new defense_stats\Resist_Cold,
                new defense_stats\Resist_Lightning,
                new defense_stats\Resist_Chaos,
                new defense_stats\Armor,
                new defense_stats\Percentage_Armor,
                new defense_stats\Evasion,
                new defense_stats\Percentage_Evasion,
                new defense_stats\Life,
                new defense_stats\Percentage_Life,
                new defense_stats\Mana,
                new defense_stats\Percentage_Mana,
                new defense_stats\Energy_Shield,
                new defense_stats\Percentage_ES,
                new base_stats\Strength,
                new base_stats\Intelligence,
                new base_stats\Dexterity,
                new defense_stats\Block,
                new defense_stats\Life_Regen,
                new defense_stats\Mana_Regen_Rate,
                new defense_stats\Movement_Speed
            ],
            'offense' => [
                new offence_stats\Attack_Speed,
                new offence_stats\Cast_Speed,
                new offence_stats\Wands_Damage,
                new offence_stats\Physical_Damage,
                new offence_stats\Melee_Phys_Damage,
                new offence_stats\Attack_Critical_Chance,
                new offence_stats\Spells_Critical_Chance,
                new offence_stats\Radius_of_Area_Skills,
                new offence_stats\Area_Damage,
                new offence_stats\Attack_Critical_Multiplier,
                new offence_stats\Spell_Critical_Multiplier,
                new offence_stats\Spell_Damage,
                new offence_stats\Elemental_Damage,
                new offence_stats\Weapons_Elemental_Damage,
                new offence_stats\Fire_Damage,
                new offence_stats\Cold_Damage,
                new offence_stats\Lightning_Damage,
                new offence_stats\Chaos_Damage,
                new offence_stats\Increased_Damage,
                new offence_stats\Projectile_Damage,
                new offence_stats\Accuracity,
                new offence_stats\Accuracy_Rating,
                new offence_stats\Damage_Over_Time,
            ]
        ];

        //items
        $banItems = ['Flask', 'Weapon2', 'Offhand2'];
        if (isset($_GET['offHand'])) {
            $banItems = ['Flask', 'Weapon', 'Offhand'];
        }

        $items = array_filter($items['items'], function ($item) use (&$banItems) {
            return !in_array($item['inventoryId'], $banItems);
        });

        $this->addItems($items);

        //tree
        $treeNodesJewels = new CharacterTreePoints;
        $nodes = $treeNodesJewels->getPoints($treeData);
        $this->addTreeNode($nodes);

        //base stats
        $baseStat = new Base_Stats;
        $baseStat = $baseStat->getStats($character['level'], $character['classId']);
        foreach ($baseStat as $stat) {
            $this->addBaseMods($stat);
        }

    }

    public function addItems($items)
    {
        foreach ($items as $item) {
            $this->type = 'item/' . $item['inventoryId'] . '/' . $this->getTypeFromIcon($item['icon']);

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
            $this->parseMod($mod);
        }
    }

    private function addMods($mods)
    {
        foreach ($mods as $mod) {
            $this->parseMod($mod);
        }
    }

    private function addProps($props)
    {
        foreach ($props as $prop) {
            if (count($prop['values']) == 0) {
                if (!isset($prop['type'])) {
                    $this->parseMod('Weapon type:'.$prop['name']);
                }
                continue;
            }

            $tempStr = $prop['values'][0][0] . ' ' . $prop['name']; // 800 Armour
            $this->parseMod($tempStr);
        }
    }

    private function parseMod($mod)
    {
        // if ($this->modValidator) {
        //     # code...
        // }

        foreach ($this->stats['defense'] as $m) {
            $m->parse($mod, $this->type);
        }

        foreach ($this->stats['offense'] as $m) {
            $m->parse($mod, $this->type);
        }

        foreach ($this->stats['misc'] as $m) {
            $m->parse($mod, $this->type);
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
