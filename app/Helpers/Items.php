<?php
namespace App\Helpers;

class Items
{

    public function __construct()
    {
    }

    /// $items_data is items from api PoeApi::getItemsData

    public static function withMostSockets($items_data)
    {
        $items_most_sockets = [];
        foreach ($items_data['items'] as $item) {
            if (!array_key_exists('sockets', $item)) {
                continue;
            }
            $grouped = collect($item['sockets'])->groupBy('group');
            $item_most_links=count($grouped[0]);
            $supports = collect($item['socketedItems'])
                        ->where('support',true)
                        ->filter(function ($item) use($item_most_links){
                            return $item['socket'] < $item_most_links;
                        });
            $level=$items_data['character']['level'];
            $requiredSupports = $level<30 ? 2 : 3 ;
            $requiredSupports = $level<10 ? 1 : $requiredSupports ;

            if ($supports->count()>=$requiredSupports) {
                $items_most_sockets[] = $item;
            }
        }
        return $items_most_sockets;
    }

    public static function getSkillsFrom($items){
        $skills = collect();
        foreach($items as $item){
            $skills[]= collect($item['socketedItems'])->where('support',false)->map(function($gem){
                return $gem['typeLine'];
            });
        }
        return implode(',',$skills->collapse()->toArray());
    }
}