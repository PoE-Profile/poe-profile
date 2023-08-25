<?php

namespace App\Parse_mods;

class Base_Stats
{
    public $level;
    public $class=[
        "Marauder"=>1, "Juggernaut"=>1, "Berserker"=>1, "Chieftain"=>1, 
        "Ranger"=>2, "Deadeye"=>2, "Raider"=>2, "Pathfinder"=>2,
        "Witch"=>3, "Necromancer"=>3, "Elementalist"=>3, "Occultist"=>3,
        "Duelist"=>4, "Gladiator"=>4, "Slayer"=>4, "Champion"=>4, 
        "Templar"=>5, "Inquisitor"=>5, "Hierophant"=>5, "Guardian"=>5,
        "Shadow"=>6, "Assassin"=>6, "Saboteur"=>6, "Trickster"=>6,
        "Scion"=>0, "Ascendant"=>0,
    ];
    private $class_base_stats = [
        1 => [
            // Marauder
            '+32 to Strength',
            '+14 to Dexterity',
            '+14 to Intelligence',
            '1 additional Totem',
            '3 additional Trap',
            '3 additional Mine'
        ],
        2 => [
            // Ranger
            '+14 to Strength',
            '+32 to Dexterity',
            '+14 to Intelligence',
            '1 additional Totem',
            '3 additional Trap',
            '3 additional Mine'
        ],
        3 => [
            // Witch
            '+14 to Strength',
            '+14 to Dexterity',
            '+32 to Intelligence',
            '1 additional Totem',
            '3 additional Trap',
            '3 additional Mine'
        ],
        4 => [
            // Duelist
            '+23 to Strength',
            '+23 to Dexterity',
            '+14 to Intelligence',
            '1 additional Totem',
            '3 additional Trap',
            '3 additional Mine'
        ],
        5 => [
            // Templar
            '+23 to Strength',
            '+14 to Dexterity',
            '+23 to Intelligence',
            '1 additional Totem',
            '3 additional Trap',
            '3 additional Mine'
        ],
        6 => [
            // Shadow
            '+14 to Strength',
            '+23 to Dexterity',
            '+23 to Intelligence',
            '1 additional Totem',
            '3 additional Trap',
            '3 additional Mine'
        ],
        0 => [
            // Scion
            '+20 to Strength',
            '+20 to Dexterity',
            '+20 to Intelligence',
            '1 additional Totem',
            '3 additional Trap',
            '3 additional Mine'
        ]
    ];

    private function base_stats_level($level)
    {
        $life = 38 + 12*$level;
        $mana = 34 + 6*$level;
        $evasion = 53 + 3*$level;
        $accuracity = 2*$level;

        return [
            '+'. $life .' to maximum Life',
            '+'. $mana .' to maximum Mana',
            '+'. $evasion .' to Evasion Rating',
            '+'. $accuracity .' to Accuracy Rating'
        ];
    }


    public function getStats($level, $class)
    {
        $levelStats = $this->base_stats_level($level);
        $classStats = $this->class_base_stats[$this->class[$class]];

        return array_merge($levelStats, $classStats);
    }

}