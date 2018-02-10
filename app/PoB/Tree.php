<?php

namespace App\PoB;

use App\PoB\ByteEncoder;
use App\PoB\Items;

class Tree
{
    public $xml = '';
    public $tree = '';
    public $treeJewels = [];

    public function __construct($xml, $tree, $char, $treeJewels)
    {
        $this->tree = $tree;
        $this->xml = $xml;
        $this->treeJewels = $treeJewels;
        $this->addTree($char);
    }

    public function addTree($char)
    {
        // Tree
        $tree = $this->xml->addChild('Tree');
        $tree->addAttribute('activeSpec', '1');
        $spec = $tree->addChild('Spec');
        $spec->addChild('URL', 'https://www.pathofexile.com/passive-skill-tree/'.$this->getTreeUrl($char['classId'], $char['ascendancyClass'], $this->tree['hashes']));
        $sockets = $spec->addChild('Sockets');
        $socketsArr=['7960', '61834', '28475', '26196', '36634', '33989', '21984', '46882', '26725', '41263', '60735', '55190', '48768', '32763', '6230', '54127', '34483', '33631', '31683', '61419', '2491'];
        foreach ($socketsArr as $k => $coord) {
            $socket = $sockets->addChild('Socket');
            $socket->addAttribute('nodeId', $coord);

            if (array_key_exists($coord, $this->treeJewels)) {
                $socket->addAttribute('itemId', $this->treeJewels[$coord]);
            } else {
                $socket->addAttribute('itemId', '0');
            }
        }
    }

    public function getXML()
    {
        return $this->xml;
    }

    public function getTreeUrl($class_id, $a_class_id, $nodes)
    {
        $u = new ByteEncoder();
        // $o=!0;
        //r classId
        $r = $class_id;//n.activeCharacter.get("classId"),
        //i ascendancyClass
        $i = $a_class_id;//n.activeCharacter.get("ascendancyClass"),
        //n PoE/PassiveSkillTree/Version
        $n=4;
        //tree nod ids
        $s=$nodes;
        $u->appendInt($n);
        $u->appendInt8($r);
        $u->appendInt8($i);
        $u->appendInt8(1);
        for ($a = 0; $a < count($s); ++$a) {
            $u->appendInt16($s[$a]);
        }
        // var l=$.base64.encode( u.getDataString() );
        $l=base64_encode($u->getDataString());
        //$l = l.replace(/\+/g, "-").replace(/\//g, "_"), (o ? "/fullscreen-passive-skill-tree/" : "/passive-skill-tree/") + l
        $l = str_replace("+", "-", $l);
        $l = str_replace("/", "_", $l);
        return $l;
    }
}
