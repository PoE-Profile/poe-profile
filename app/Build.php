<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Parse_mods\Stats_Manager;
use App\Parse_mods\Base_Stats;
use App\Parse_mods\CharacterTreePoints;

class Build extends Model
{
    protected $fillable = ['name', 'tree_data', 'item_data', 'stats', 'poe_version'];

    // Tree_Data Mutator & Accessor
    public function setTreeDataAttribute($value)
    {
        $tree_data = gzcompress($value);
        $tree_data = base64_encode($tree_data);
        $this->attributes['tree_data'] = $tree_data;
    }

    public function getTreeDataAttribute($value)
    {
        // dd($value);
        $tree_data = base64_decode($value);
        $tree_data = gzuncompress($tree_data);
        return json_decode($tree_data, true);
    }

    // Item_Data Mutator & Accessor
    public function setItemDataAttribute($value)
    {
        $item_data = gzcompress($value);
        $item_data = base64_encode($item_data);
        $this->attributes['item_data'] = $item_data;
    }

    public function getItemDataAttribute($value)
    {
        $item_data = base64_decode($value);
        $item_data = gzuncompress($item_data);
        return json_decode($item_data, true);
    }

    // Stats Mutator & Accessor
    public function setStatsAttribute($value)
    {
        $stats = gzcompress(json_encode($value, true));
        $stats = base64_encode($stats);
        $this->attributes['stats'] = $stats;
    }

    public function getStatsAttribute($value)
    {
        $stats = base64_decode($value);
        $stats = gzuncompress($stats);
        return json_decode($stats, true);
    }

    public function getStats()
    {
        $stManager = new Stats_Manager;

        //items
        $banItems = ['Flask', 'Weapon2', 'Offhand2'];
        if (isset($_GET['offHand'])) {
            $banItems = ['Flask', 'Weapon', 'Offhand'];
        }

        $items = array_filter($this->item_data['items'], function ($item) use (&$banItems) {
            return in_array($item['inventoryId'], $banItems);
        });

        $stManager->addItems($items);

        //tree
        $treeNodesJewels = new CharacterTreePoints;
        $nodes = $treeNodesJewels->getPoints($this->tree_data);
        $stManager->addTreeNode($nodes);

        //base stats
        $character = $this->item_data['character'];
        $baseStat = new Base_Stats;
        $baseStat = $baseStat->getStats($character['level'], $character['classId']);
        foreach ($baseStat as $stat) {
            $stManager->addBaseMods($stat);
        }

        return $stManager->getStats();
    }
}
