<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>skill-tree</title>
        <link href="css/tree_style.css" media="screen" rel="stylesheet" type="text/css" >
        <link href="https://web.poecdn.com/css/screen.css?v=6f03cfe2997c9f753abdf5b41e56a1ac" media="screen" rel="stylesheet" type="text/css" >
    </head>
<body>

    <button class="btn poe-btn" >
        Show Tree Stats
    </button>

<div id="passiveSkillTree" >
</div>


<div id="passiveSkillTreeControlsContainer" style="display: none">
    <div id="passiveSkillTreeControls" style="display: block;">
        <div class="poeForm" id="passiveControlsForm">
            <div id="skillTreeInfo" class="skillTreeInfo FontinRegular">
                <span class="pointsUsed"></span> of <span class="totalPoints">123</span> points used            </div>

            <div class="clear"></div>

            <div class="controlsTop">
                <div class="characterClassContainer flContainer">
                    <span class="label FontinBold">Character Class</span>
                    <div class="input-row">
                        <div id="element-classStartPoint">
                            <select name="classStartPoint" id="classStartPoint">
                                <option value="1">Marauder</option>
                                <option value="2">Ranger</option>
                                <option value="3" selected="selected">Witch</option>
                                <option value="4">Duelist</option>
                                <option value="5">Templar</option>
                                <option value="6">Shadow</option>
                                <option value="0">Scion</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="ascendancyClassContainer" class="flContainer">
                    <span class="label FontinBold">Ascendancy</span>
                    <div class="input-row">
                        <div id="element-ascendancyClass">
                            <select name="ascendancyClass" id="ascendancyClass"><option value="0">None</option><option value="1">Juggernaut</option><option value="2">Berserker</option><option value="3" selected="selected">Chieftain</option></select>
                        </div>
                    </div>
                </div>

                <div id="passiveSearchBoxContainer" class="passiveSearchBoxContainer flContainer">
                    <span class="label FontinBold">Search</span>
                    <div id="element-permaLink" class="text formTextInline">
<input name="permaLink" id="passiveSearchBox" value="" class="textInput" type="text"></div>                </div>

                <div id="permaLinkContainer" class="permaLinkContainer flContainer">
                    <div class="label FontinBold">Link to this build</div>
                    <div class="bbcodePermaLinkCont">
                        <input value="1" name="bbcodePermaLink" id="bbcodePermaLink" type="checkbox">BBCode                    </div>
                    <div id="element-permaLink" class="text formTextInline">
<input name="permaLink" id="permaLink" value="" class="textInput" type="text"></div>                </div>

                <div id="buildNameContainer" class="buildNameContainer flContainer">
                    <span class="label FontinBold">Build name</span>
                    <div id="element-buildName" class="text formTextInline">
<input name="buildName" id="buildName" value="" maxlength="100" class="textInput" type="text"></div>                </div>
            </div>

            <div class="controlsBottom">
                <div class="highlightSimilarNodesContainer">
                    <input value="1" name="highlightSimilarNodes" id="highlightSimilarNodes" type="checkbox">Highlight similar skills                </div>

                <div class="highlightShortestPathsContainer">
                    <input value="1" name="highlightShortestPaths" id="highlightShortestPaths" type="checkbox">Highlight shortest paths                </div>

                <div class="buttonContainer">
                    <input value="Reset Tree" class="button1 important" name="resetSkillTree" id="resetSkillTree" type="button">
                </div>

                <div class="buttonContainer">
                    <input value="Full Screen (f)" class="button1 important" name="toggleFullScreen" id="toggleFullScreen" type="button">
                </div>

                <div class="buttonContainer">
                    <input value="Open in new window" style="display: none;" class="button1 important tree-link" type="button">
                </div>

            </div>

            <div class="clear"></div>
        </div>
    </div>
</div>

<style>
    body{
        background: #000;
    }
    .btn {
        display: none;
        font-weight: 500;
        width: 320px;
        line-height: 1.25;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid transparent;
        padding: .5rem 1rem;
        font-size: 1.5rem;
        border-radius: .25rem;
    }
    .btn-group-sm > .btn, .btn-sm {
        padding: .25rem .5rem;
        font-size: .875rem;
        border-radius: .2rem;
    }
    .poe-btn {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #332F24;
        color: #FFF;
        border: 1px solid #ddd;
        z-index: 99101;
    }
    .poe-btn:hover {
        background-color: #494535;
    }
    #sidebar {
    	display: block;
    	position: absolute;
    	top: 0px;
        padding: 5px;
        padding-top: 45px;
    	left: 0px;
    	bottom: 0px;
    	background: #111;
    	opacity: 0.8;
    	color: #ccc;
    	width: 350px;
    	z-index: 99100;
    	overflow-y: auto;
        display: none
    }
    .attrCat {
    	text-decoration: underline;
    	line-height: 32px;
    	text-align: center;
    }
    #sidebar-dock {
    	display: block;
    	position: absolute;
    	top: 0px;
    	left: 350px;
    	bottom: 0px;
    	background-color: #222;
    	opacity: 0.8;
    	color: #ccc;
    	height: 100%;
    	z-index: 99000;
    	width: 20px;
    }
    .nav {
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
        width: 97%;
        border-bottom:
    }
    .nav-tabs .nav-item {
        float: left;
        margin-bottom: -1px;
        background:none;

    }
    .nav-tabs .nav-link.active, .nav-tabs .nav-link.active:focus, .nav-tabs .nav-link.active:hover {
        color: #ebb16c;
        background-color: rgba(0, 0, 0, 0);
        border-color: transparent transparent transparent;
        border-bottom: 5px solid #ebb16c;
        font-weight: bold;
    }
    .nav-tabs .nav-link {
        display: block;
        padding: .5em 1em;
        border: 1px solid transparent;
        border-top-right-radius: .25rem;
        border-top-left-radius: .25rem;
        text-decoration:none;
    }
    .nav-tabs {
        margin-top: 15px;
        height: 35px;
        border-bottom: 1px solid #ebb16c;
    }
</style>
<div id="sidebar-dock1"></div>
<div id="sidebar" name="sidebar" >
    <h1>V{{$version}}</h1>
    <!-- <ul class="nav nav-tabs"  style="padding-left:10px;">
        <li class="nav-item" style="">
            <a class="nav-link active" href="#" data-toggle="more-info">
                Tree Stats
            </a>
        </li>
        <li class="nav-item" style="">
            <a class="nav-link" href="#" data-toggle="skill-tree" target="_blank">
                Jewels
            </a>
        </li>
    </ul> -->

	<div id="summary" name="summary"></div>
</div>
        <script type="text/javascript" src="https://web.poecdn.com/js/lib/require-2.3.2.js?v=53f6b5a112b20a13c435d7b4630b8320"></script>    <!-- <script type="text/javascript" src="https://web.poecdn.com/js/lib/require-2.3.2.js?v=fb734febccaaa258d2fe03b3dca5e0a6"></script> -->
<script type="text/javascript">
    //<!--
                document.body.className = document.body.className.replace( /(?:^|\s)noJS(?!\S)/ , '');
            window.momentLocale = 'en_US';
            window.momentTimezone = 'Europe/Sofia';
            window.PoELocale = 'en_US';

            BASEURL = '';
/*
            require.config({
                baseUrl: "http://127.0.0.1:8080/js",
                paths : {"main":"main.poe-tree","skilltree":"skilltree.poe-tree"},
                //"plugins":"plugins.poe-tree",
                // shim: {"main":{"deps":["config","plugins"]},"plugins":{"deps":["config"]}}
            });

  */
            require.config({
                baseUrl: "https://web.poecdn.com/js",
                paths : {"main":"main.ec6ba113020f467ba890095e38b0d0247c2e7522","plugins":"plugins.3969f39d17a09125aa97f240ec53337f489db098","skilltree":"skilltree.31429c9be558995934071c0d089e4158d331c164"},
            });
            require(["main"], function(){});    //-->


</script>
<script type="text/javascript" src="/js/st_us{{$version}}.js"></script>

<script type="text/javascript">

    //<!--
    var temp='';
    var oldPoints=[];

    require(['main'], function() {
        require(['skilltree'], function (PassiveSkillTree) {
            var opts = {
                passiveSkillTreeData: passiveSkillTreeData,
                ascClasses:                 {"1":{"name":"Marauder","classes":{"1":{"name":"Juggernaut","displayName":"Juggernaut","flavourText":"     What divides the conqueror \r\n from the conquered? Perseverance.","flavourTextRect":"215,165,1063,436","flavourTextColour":"175,90,50"},"2":{"name":"Berserker","displayName":"Berserker","flavourText":"The savage path is \r\nalways swift and sure.","flavourTextRect":"760,345,976,429","flavourTextColour":"175,90,50"},"3":{"name":"Chieftain","displayName":"Chieftain","flavourText":"   The Ancestors speak \r\nthrough your clenched fists.","flavourTextRect":"185,245,1076,449","flavourTextColour":"175,90,50"}}},"2":{"name":"Ranger","classes":{"1":{"name":"Raider","displayName":"Raider","flavourText":"No hunt is complete without\r\nthe gutting and the skinning.","flavourTextRect":"365,965,900,1250","flavourTextColour":"124,179,118"},"2":{"name":"Deadeye","displayName":"Deadeye","flavourText":"A woman can change the world \r\nwith a single, well-placed arrow.","flavourTextRect":"365,965,900,1250","flavourTextColour":"124,179,118"},"3":{"name":"Pathfinder","displayName":"Pathfinder","flavourText":"There are venoms and virtues aplenty in \r\n the wilds, if you know where to look.","flavourTextRect":"265,975,900,1250","flavourTextColour":"124,179,118"}}},"3":{"name":"Witch","classes":{"1":{"name":"Occultist","displayName":"Occultist","flavourText":" Throw off the chains\r\nof fear and embrace that\r\n which was forbidden.","flavourTextRect":"665,385,906,389","flavourTextColour":"154,195,201"},"2":{"name":"Elementalist","displayName":"Elementalist","flavourText":"Feed a storm with savage intent \r\nand not even the strongest walls\r\nwill hold it back.","flavourTextRect":"125,475,510,768","flavourTextColour":"154,195,201"},"3":{"name":"Necromancer","displayName":"Necromancer","flavourText":"Embrace the serene\r\npower that is undeath.","flavourTextRect":"745,450,1000,1000","flavourTextColour":"154,195,201"}}},"4":{"name":"Duelist","classes":{"1":{"name":"Slayer","displayName":"Slayer","flavourText":" No judge. No jury.\r\nJust the executioner.","flavourTextRect":"470,310,976,429","flavourTextColour":"150,175,200"},"2":{"name":"Gladiator","displayName":"Gladiator","flavourText":"Raise your hand to the \r\nroaring crowd and pledge \r\nyour allegiance to glory.","flavourTextRect":"670,395,976,429","flavourTextColour":"150,175,200"},"3":{"name":"Champion","displayName":"Champion","flavourText":"Champion that which \r\n you love. He who fights\r\n for nothing, dies\r\n for nothing.","flavourTextRect":"735,625,976,429","flavourTextColour":"150,175,200"}}},"5":{"name":"Templar","classes":{"1":{"name":"Inquisitor","displayName":"Inquisitor","flavourText":" Truth is elusive, yet God has\r\nprovided us with all the tools \r\n necessary to find it.","flavourTextRect":"335,940,976,429","flavourTextColour":"207,189,138"},"2":{"name":"Hierophant","displayName":"Hierophant","flavourText":"Drink deeply from God's\r\n chalice, for the faithful\r\n will never find it empty.","flavourTextRect":"100,720,936,399","flavourTextColour":"207,189,138"},"3":{"name":"Guardian","displayName":"Guardian","flavourText":"When bound by faith\r\n and respect, the flock\r\n will overwhelm the wolf.","flavourTextRect":"170,780,976,429","flavourTextColour":"207,189,138"}}},"6":{"name":"Shadow","classes":{"1":{"name":"Assassin","displayName":"Assassin","flavourText":"Death is a banquet. \r\n It's up to the murderer \r\n to write the menu.","flavourTextRect":"650,845,976,429","flavourTextColour":"114,129,141"},"2":{"name":"Trickster","displayName":"Trickster","flavourText":"  Everyone knows how to die. \r\n Some just need a little nudge \r\nto get them started.","flavourTextRect":"315,150,976,429","flavourTextColour":"114,129,141"},"3":{"name":"Saboteur","displayName":"Saboteur","flavourText":"The artist need not be present \r\n to make a lasting impression.","flavourTextRect":"355,970,976,429","flavourTextColour":"114,129,141"}}},"0":{"name":"Scion","classes":{"1":{"name":"Ascendant","displayName":"Ascendant","flavourText":"","flavourTextRect":"305,925,976,429","flavourTextColour":"90,90,90"}}}},
                zoomLevels: [0.1246,0.2109,0.2972,0.3835],
                height:    967,
                width: 1300,
                startClass: 6,
                version: '{{$version}}',
                fullScreen: true,
                circles: {"Small":[{"level":0.1246,"width":199},{"level":0.2109,"width":337},{"level":0.2972,"width":476},{"level":0.3835,"width":614}],"Medium":[{"level":0.1246,"width":299},{"level":0.2109,"width":506},{"level":0.2972,"width":713},{"level":0.3835,"width":920}],"Large":[{"level":0.1246,"width":374},{"level":0.2109,"width":633},{"level":0.2972,"width":892},{"level":0.3835,"width":1151}]} 
            };
            var treeControls = new PassiveSkillTree.controls(opts);
            // console.log(this);
            temp=treeControls;
            var showStats=false;

            $( document ).ready(function() {
                // console.log( "ready!" );

                $( ".poe-btn" ).hide();
                $( "#sidebar" ).hide();


                $('#passiveSkillTreeControlsContainer .pointsUsed').bind('DOMSubtreeModified', function(e) {
                    // loadStats(temp.skillTree);
                    $( ".poe-btn" ).show();
                    // temp.skillTree.readonly=true;
                    // temp.skillTree.settings.highlightShortestPaths=true;
                    // parent.stopIframeLoad();
                    if(temp.skillTree.ascendancyClass>0){
                        temp.skillTree.ascendancyClassPopupHidden=false;
                    }
                    //console.log(temp.skillTree.passiveAllocation.allocatedSkills[885].skill.skillDescription);
                    // console.log(temp.skillTree.passiveAllocation.allocatedSkills);

                    for (var t in temp.skillTree.passiveAllocation.allocatedSkills){
                        if(!oldPoints.includes(t)){
                            //console.log('new point:'+t);
                            //console.log(temp.skillTree.passiveAllocation.allocatedSkills[t].skill.skillDescription);
                            //console.log(temp.skillTree.passiveAllocation.allocatedSkills[t]);
                        }
                    }
                    // console.log(JSON.stringify(temp.skillTree.passiveAllocation.allocatedSkills));
                    oldPoints=Object.keys(temp.skillTree.passiveAllocation.allocatedSkills)
                });
                $( ".poe-btn" ).click(function() {
                  loadStats(temp.skillTree);
                  showStats=!showStats;
                  if(showStats){
                      $(".poe-btn").html('Hide Tree Stats');
                      $( "#sidebar" ).show();
                  }else{
                      $(".poe-btn").html('Show Tree Stats');
                      $( "#sidebar" ).hide();
                  }
                   //console.log(temp);


                });


                function loadStats(skillTree){
                    $("#summary").html("");
                	var pp = []
                		var sdp = {};
                	for (var t in skillTree.passiveAllocation.allocatedSkills) {
                		var n = skillTree.passiveAllocation.allocatedSkills[t];
                        if(n.isJewel){
                            // console.log("Jewel");
                            // console.log(n);
                        }

                		for(var s = 0, o = n.skill.skillDescription.length; s < o; ++s) {
                			var p = n.skill.skillDescription[s];
                			if (n.keyStone) {
                				if (sdp["Keystone"] == null)
                					sdp["Keystone"] = [];
                				sdp["Keystone"].push(p);
                			} else {
                				pp.push(p);
                			}
                		}
                	}
                	var sdd = [];
                	var sdn = {};
                	for (var n in pp) {
                		var p = pp[n];
                		var reg = /-?(\d+\.?\d*)$|(\d*\.?\d+)/;
                		var m = p.match(reg);
                        if (m == null) {
                			sdd.push(p);
                		} else {
                			var r = p.replace(m[0], "XXX");
                			if (sdn[r] == null) {
                				sdn[r] = parseFloat(m[0]);
                			} else {
                				sdn[r] += parseFloat(m[0]);
                			}
                		}
                	}
                	for (var n in sdn) {
                		var p = sdn[n];
                		var r = n.replace("XXX", p);
                		sdd.push(r);
                	}
                	var cats = {};
                	var pcats = [];
            		cats = {
                        "and Endurance Charges on Hit with Claws": "Weapon",
                        "Endurance Charge": "Charges",
                        "Frenzy Charge": "Charges",
                        "Power Charge": "Charges",
                        "Maximum number of Spectres": "Minion",
                        "Maximum number of Zombies": "Minion",
                        "Maximum number of Skeletons": "Minion",
                        "Minions deal": "Minion",
                        "Minions have": "Minion",
                        "Minions Leech": "Minion",
                        "Minions Regenerate": "Minion",
                        "Mine Damage": "Trap",
                        "Trap Damage": "Trap",
                        "Trap Duration": "Trap",
                        "Trap Trigger Radius": "Trap",
                        "Mine Duration": "Trap",
                        "Mine Laying Speed": "Trap",
                        "Trap Throwing Speed": "Trap",
                        "Can set up to": "Trap",
                        "Detonating Mines is Instant": "Trap",
                        "Mine Damage Penetrates": "Trap",
                        "Mines cannot be Damaged": "Trap",
                        "Trap Damage Penetrates": "Trap",
                        "Traps cannot be Damaged": "Trap",
                        "Totem Duration": "Totem",
                        "Casting Speed for Summoning Totems": "Totem",
                        "Totem Life": "Totem",
                        "Totem Damage": "Totem",
                        "Attacks used by Totems": "Totem",
                        "Spells Cast by Totems": "Totem",
                        "Totems gain": "Totem",
                        "Totems have": "Totem",
                        "Curse Duration": "Curse",
                        "Effect of your Curses": "Curse",
                        "Radius of Curses": "Curse",
                        "Cast Speed for Curses": "Curse",
                        "Enemies can have 1 additional Curse": "Curse",
                        "Mana Reserved": "Aura",
                        "effect of Auras": "Aura",
                        "Radius of Auras": "Aura",
                        "Effect of Buffs on You": "Aura",
                        "Weapon Critical Strike Chance": "Crit",
                        "increased Critical Strike Chance": "Crit",
                        "increased Critical Strike Multiplier": "Crit",
                        "Global Critical Strike": "Crit",
                        "Critical Strikes with Daggers Poison the enemy": "Crit",
                        "Knocks Back enemies if you get a Critical Strike": "Crit",
                        "increased Melee Critical Strike Multiplier": "Crit",
                        "increased Melee Critical Strike Chance": "Crit",
                        "Elemental Resistances while holding a Shield": "Shield",
                        "Chance to Block Spells with Shields": "Block",
                        "Armour from equipped Shield": "Shield",
                        "additional Block Chance while Dual Wielding or Holding a shield": "Block",
                        "Chance to Block with Shields": "Block",
                        "Block and Stun Recovery": "Block",
                        "Energy Shield from equipped Shield": "Shield",
                        "Block Recovery": "Block",
                        "Defences from equipped Shield": "Shield",
                        "Damage Penetrates": "General", //needs to be here to pull into the correct tab.
                        "reduced Extra Damage from Critical Strikes": "Defense",
                        "Armour": "Defense",
                        "all Elemental Resistances": "Defense",
                        "Chaos Resistance": "Defense",
                        "Evasion Rating": "Defense",
                        "Cold Resistance": "Defense",
                        "Lightning Resistance": "Defense",
                        "maximum Mana": "Defense",
                        "maximum Energy Shield": "Defense",
                        "Fire Resistance": "Defense",
                        "maximum Life": "Defense",
                        "Light Radius": "Defense",
                        "Evasion Rating and Armour": "Defense",
                        "Energy Shield Recharge": "Defense",
                        "Life Regenerated": "Defense",
                        "Melee Physical Damage taken reflected to Attacker": "Defense",
                        "Flask Recovery Speed": "Defense",
                        "Avoid Elemental Status Ailments": "Defense",
                        "Damage taken Gained as Mana when Hit": "Defense",
                        "Avoid being Chilled": "Defense",
                        "Avoid being Frozen": "Defense",
                        "Avoid being Ignited": "Defense",
                        "Avoid being Shocked": "Defense",
                        "Avoid being Stunned": "Defense",
                        "increased Stun Recovery": "Defense",
                        "Flasks": "Defense",
                        "Flask effect duration": "Defense",
                        "Mana Regeneration Rate": "Defense",
                        "maximum Mana": "Defense",
                        "Armour": "Defense",
                        "Avoid interruption from Stuns while Casting": "Defense",
                        "Movement Speed": "Defense",
                        "Mana Recovery from Flasks": "Defense",
                        "Life Recovery from Flasks": "Defense",
                        "Enemies Cannot Leech Life From You": "Defense",
                        "Enemies Cannot Leech Mana From You": "Defense",
                        "Ignore all Movement Penalties": "Defense",
                        "Physical Damage Reduction": "Defense",
                        "Hits that Stun Enemies have Culling Strike": "General",
                        "increased Damage against Frozen, Shocked or Ignited Enemies": "General",
                        "Shock Duration on enemies": "General",
                        "Radius of Area Skills": "General",
                        "chance to Ignite": "General",
                        "chance to Shock": "General",
                        "Mana Gained on Kill": "General",
                        "Life gained on General": "General",
                        "Burning Damage": "General",
                        "Projectile Damage": "General",
                        "Knock enemies Back on hit": "General",
                        "chance to Freeze": "General",
                        "Projectile Speed": "General",
                        "Projectiles Piercing": "General",
                        "Ignite Duration on enemies": "General",
                        "Knockback Distance": "General",
                        "Mana Cost of Skills": "General",
                        "Chill Duration on enemies": "General",
                        "Freeze Duration on enemies": "General",
                        "Damage over Time": "General",
                        "Chaos Damage": "General",
                        "Enemies Become Chilled as they Unfreeze": "General",
                        "Skill Effect Duration": "General",
                        "Life Gained on Kill": "General",
                        "Area Damage": "General",
                        "Enemy Stun Threshold": "General",
                        "Stun Duration": "General",
                        "increased Damage against Enemies on Low Life": "General",
                        "chance to gain Onslaught": "General",
                        "Spell Damage": "Spell",
                        "Elemental Damage with Spells": "Spell",
                        "Accuracy Rating": "Weapon",
                        "Mana gained for each enemy hit by your Attacks": "Weapon",
                        "Melee Weapon and Unarmed range": "Weapon",
                        "Life gained for each enemy hit by your Attacks": "Weapon",
                        "chance to cause Bleeding": "Weapon",
                        "Wand Physical Damage": "Weapon",
                        "Attack Speed": "Weapon",
                        "Melee Attack Speed": "Weapon",
                        "Melee Damage": "Weapon",
                        "Physical Damage with Claws": "Weapon",
                        "Block Chance With Staves": "Block",
                        "Physical Damage with Daggers": "Weapon",
                        "Physical Attack Damage Leeched as Mana": "Weapon",
                        "Physical Damage Dealt with Claws Leeched as Mana": "Weapon",
                        "Arrow Speed": "Weapon",
                        "Cast Speed while Dual Wielding": "Weapon",
                        "Physical Damage with Staves": "Weapon",
                        "Attack Damage with Main Hand": "Weapon",
                        "Attack Damage against Bleeding Enemies": "Weapon",
                        "Physical Damage with Axes": "Weapon",
                        "Physical Weapon Damage while Dual Wielding": "Weapon",
                        "Physical Damage with One Handed Melee Weapons": "Weapon",
                        "Physical Damage with Two Handed Melee Weapons": "Weapon",
                        "Physical Damage with Maces": "Weapon",
                        "Physical Damage with Bows": "Weapon",
                        "enemy chance to Block Sword Attacks": "Block",
                        "additional Block Chance while Dual Wielding": "Block",
                        "mana gained when you Block": "Block",
                        "Melee Physical Damage": "Weapon",
                        "Physical Damage with Swords": "Weapon",
                        "Elemental Damage with Wands": "Weapon",
                        "Elemental Damage with Maces": "Weapon",
                        "Physical Attack Damage Leeched as Life": "Weapon",
                        "Cold Damage with Weapons": "Weapon",
                        "Fire Damage with Weapons": "Weapon",
                        "Lightning Damage with Weapons": "Weapon",
                        "Physical Damage Dealt with Claws Leeched as Life": "Weapon",
                        "Elemental Damage with Weapons": "Weapon",
                        "Physical Damage with Wands": "Weapon",
                        "Damage with Wands": "Weapon",
                        "increased Physical Damage": "General",
                        "Elemental Damage": "General",
                        "Cast Speed": "Spell",
                        "Cold Damage": "General",
                        "Fire Damage": "General",
                        "Lightning Damage": "General",
                        "Strength": "BaseStat",
                        "Intelligence": "BaseStat",
                        "Dexterity": "BaseStat"
            		};
            		pcats = ["Keystone", "BaseStat", "General", "Defense", "Shield", "Charges", "Weapon", "Aura", "Spell"];

            		for (var n in sdd) {
            			var p = sdd[n];
            			var found = false;
            			for (var o in cats) {
            				if (p.indexOf(o) > 0) {
            					found = true;
            					if (sdp[cats[o]] == null) {
            						sdp[cats[o]] = [];
            					}
            					sdp[cats[o]].push(p);
            					break;
            				}
            			}
            			if (found == false) {
            				if (sdp["Others"] == null)
            					sdp["Others"] = [];
            				sdp["Others"].push(p);
            			}
            		}
            		for (var p in pcats) {
            			n = pcats[p];
            			if (sdp[n] != null) {
            				$("#summary").append("<div class='attrCat'>"+n+"</div>");
            				for(var o in sdp[n]) {
            					$("#summary").append("<li>"+sdp[n][o]+"</li>");
            				}
            				delete sdp[n];
            			}
            		}
            		for (var n in sdp) {
            			$("#summary").append("<div class='attrCat'>"+n+"</div>");
            			for(var o in sdp[n]) {
            				$("#summary").append("<li>"+sdp[n][o]+"</li>");
            			}
            		}
                }
            });

        });
    });

</script>


</body>
</html>
