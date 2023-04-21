export var SkillsHelper = function (images) {
    var skills = [];

    return {
        getImage: function(skillName) {
            return images[skillName];
        },

        checkTags: function(skill, support){
            var elementTags = ['Fire', 'Cold', 'Lightning', 'Chaos'];
            for (var i = 0; i < support.length; i++) {
                if (_.includes(elementTags,support[i])) {
                    return true;
                }
            }

            if (support.length === 1 ) {
                return true;
            }

            for (var k = 0; k < skill.length; k++) {
                for (var j = 0; j < support.length; j++) {
                    if (skill[k] !== support[j]) {
                        continue;
                    }
                    if (skill[k] === support[j]) {
                        return true;
                    }

                }
            }
            return false;
        },

        addSupportsToSkills: function(tempSkills, tempSupports){
            var self = this;
            tempSkills.forEach(function(skill){
                var tempMinionSupports = [];
                tempSupports.forEach(function(support){
                    var supportAll = [
                        'Greater Multiple Projectiles Support',
                        'Lesser Multiple Projectiles Support',
                        'Faster Projectiles Support',
                    ];
                    if (skill.socketGroup === support.socketGroup || support.socketGroup == 'unique') {
                        if (support.name === "Cast when Damage Taken Support" &&
                            (support.gem.requirements[0].values[0][0] <= skill.gem.requirements[0].values[0][0] ||
                            _.includes(skill.tags.split(', '),'Aura') )
                        ) {
                            return;
                        }


                        if (self.checkTags(skill.tags.split(', '), support.tags.split(', ')) || _.includes(supportAll,support.name)){
                            skill.supports.push(support);
                            if(support.name=='Blasphemy Support'){
                                skill.tags=skill.tags.concat(', Aura');
                            }
                            if (_.includes(['Spell Totem Support', 'Ranged Attack Totem Support'], support.name)) {
                                skill.tags = skill.tags.concat(', Totem');
                            }
                            //check if support has tag trigger
                            //call method getTrigerSKills(tempSkills, skill, support)
                            //return array with one or more skill that are linked
                            if (_.includes(support.tags.split(', '),'Trigger') &&
                                support.name !== "Cast when Damage Taken Support"
                            ) {
                                self.getTrigerSKills(tempSkills, skill, support)
                            }
                        } else {
                            if (self.checkTags(['Totem'], support.tags.split(', '))) {
                                tempMinionSupports.push(support);
                            }
                        }
                    }
                });

                if (self.checkTags(['Totem'], skill.tags.split(', '))) {
                    tempMinionSupports.forEach(function(gem){
                        skill.supports.push(gem);
                    })
                }
                //pushing all temp skill to main array
                skills.push(skill);
            });
        },

        getTrigerSKills: function(tempSkills, trigeredSkill, support){

            tempSkills.forEach(function(skill){
                if(skill.name === trigeredSkill.name) {
                    return
                }
                //For Curse on Hit add spell/attack skills that trigger Curses
                if (!_.includes( skill.tags.split(', '),'Curse') && _.includes(support.tags.split(', '),'Curse')) {
                    trigeredSkill.supports.push(skill);
                    return;
                }
                //For Cast on Crit add only attack skills that trigger Spell
                if (_.includes(skill.tags.split(', '),'Attack')) {
                    trigeredSkill.supports.push(skill);
                    return;
                }
                //For Cast on Chaneling add only Channelling skills that trigger Spell
                if (_.includes(skill.tags.split(', '),'Channelling')) {
                    trigeredSkill.supports.push(skill);
                    return;
                }
                return;
                //For Cast when Damage Taken dont need Skill to get triggered
            });
        },

        filterItems: function(item) {
            var types = ['Flask', 'Amulet', 'Belt'];
            if (item.name === 'null') {
                return true;
            }
            if (!_.includes(types,item.inventoryId)) {
                return false;
            }
            return true;
        },

        skillItems: function(item) {
            var self = this;
            var tempSkill = {};
            var itemName = item.name.replace("<<set:MS>><<set:M>><<set:S>>", "");
            var itemsWithSkills = {
                'Abberath\'s Hooves': 'Abberath\'s Fury',
                'Arakaali\'s Fang': 'Raise Spiders',
                'Ngamahu\'s Flame': 'Molten Burst',
                'The Dancing Dervish': 'Manifest Dancing Dervish',
                'The Scourge': 'Summon Spectral Wolf',
                'The Whispering Ice': 'Icestorm',
                'United in Dream': 'Envy',
                'Uul-Netol\'s Embrace': 'Bone Nova',
                'The Surrender': 'Reckoning'
            };

            if (!(itemName in itemsWithSkills)) {
                return;
            }

            tempSkill = itemSkills.filter(function(skill){
                return skill.name === itemsWithSkills[itemName]
            })[0];

            item.socketedItems.forEach(function(gem, index){
                if (gem.typeLine.search('Support') >= 0) {
                    tempSkill.supports.push({
                        'tags': self.getTags(gem.properties[0].name, gem.typeLine),
                        'socketGroup': item.sockets[gem.socket].group,
                        'itemType': item.inventoryId,
                        'name': gem.typeLine,
                        'gem': gem,
                    });
                }
            });
            tempSkill.itemType = item.inventoryId;
            tempSkill.imgUrl = self.getImage(itemsWithSkills[itemName]);
            if (tempSkill.gem.icon === '') {
                tempSkill.gem.icon = self.getImage(itemsWithSkills[itemName]);
            }

            skills.push(tempSkill);

        },

        itemWithSupports: function(item) {
            var self = this;
            var tempSupport = {};
            var itemName = item.name.replace("<<set:MS>><<set:M>><<set:S>>", "");
            var itemsWithSupports = {
                'Soul Mantle': ['Spell Totem Support'],
                'Doryani\'s Catalyst': ['Elemental Proliferation Support'],
                'Kitava\'s Feast': ['Melee Splash'],
                'Lioneye\'s Vision': ['Pierce Support'],
                'Pledge of Hands': ['Spell Echo Support (stave)'],
                'Heretic\'s Veil': ['Blasphemy Support'],
                'Prism Guardian': ['Blood Magic Support'],
                'Advancing Fortress': ['Fortify Support'],
                'Nycta\'s Lantern': ['Fire Penetration Support'],
                'Doon Cuebiyari': ['Iron Will Support'],
                'Marohi Erqi': ['Increased Area of Effect Support'],
                'Tidebreaker': ['Endurance Charge on Melee Stun Support'],
                'Reverberation Rod': ['Spell Echo Support (wand)'],
                'Ashrend': ['Added Fire Damage Support'],
                'Victario\'s Influence': ['Generosity Support'],
                'Deerstalker': ['Trap Support (boots)'],
                'Fencoil': ['Trap Support (stave)'],
                'Empire\'s Grasp': ['Knockback Support'],
                'Thunderfist': ['Added Lightning Damage Support'],
                'Rime Gaze': ['Concentrated Effect Support'],
                'The Bringer of Rain': ['Melee Physical Damage Support', 'Faster Attacks Support', 'Blind Support'],
            };

            if (!(itemName in itemsWithSupports)) {
                return;
            }

            tempSupport = itemSupports.filter(function (skill) {
                // return skill.name === itemsWithSupports[itemName]
                return _.includes(itemsWithSupports[itemName], skill.name)
            });

            //TempSupport = ItemSupports['itemName'] Changes At some Point

            return tempSupport;
        },

        getTags: function(tags, gemName){
            tags = tags.replace('Bow', 'Projectile');
            tags = tags.replace('Trap', 'Trap, Spell');
            tags = tags.replace('Mine', 'Mine, Spell, Attack');
            tags = tags.replace('Minion', 'Minion, Attack');

            if (gemName == 'Spell Totem Support') {
                tags = tags.concat(', Spell');
            }

            if (gemName == 'Cast while Channelling Support') {
                tags = tags.concat(', Trigger');
            }

            if (gemName == 'Minion and Totem Elemental Resistance Support') {
                tags = tags.concat(', Totem');
            }

            if (gemName == 'Rapid Decay Support') {
                tags = tags.concat(', Spell, Attack, Projectile');
            }

            if (gemName == 'Arctic Armour') {
                tags = tags.concat(', Aura');
            }

            if (gemName.toLowerCase().search("herald of")>=0) {
                tags = tags.concat(', Aura');
            }

            tags = tags.replace('Bow', 'Projectile');
            return tags;
        },

        getReducedMana: function(item, gem){
            var itemName = item.name.replace("<<set:MS>><<set:M>><<set:S>>", "");
            var tags = this.getTags(gem.properties[0].name, gem.typeLine)
            if (itemName === 'Heretic\'s Veil' && _.includes(tags.split(', '),'Curse')) {
                return 12;
            }
            if (itemName === 'Prism Guardian') {
                return 25;
            }
            return 0;
        },

        addSkills: function(item){

            var skillAdded = false;
            var tempSupportGems = [];
            var tempSkill = [];
            var self = this;

            if (!("socketedItems" in item)){
                return;
            }

            if (this.filterItems(item)) {
                return;
            }

            this.skillItems(item);

            item.socketedItems.forEach(function(gem, index){
                //skip if there is an abyss jewel
                if (gem.abyssJewel) {
                    return;
                }
                
                  
                if (gem.typeLine.search('Support') < 0) {
                    skillAdded = true;
                    var wordsToReplace = {
                        'Anomalous ': '',
                        'Divergent ': '',
                        'Phantasmal ': '',
                        'Vaal ': '',
                    }
                    var gemTypeLine=gem.typeLine.replace(/\b(?:Anomalous |Divergent |Vaal |Phantasmal )\b/gi, matched => wordsToReplace[matched]);
                    console.log(gemTypeLine);
                    tempSkill.push({
                        'tags': self.getTags(gem.properties[0].name, gem.typeLine),
                        'socketGroup': item.sockets[gem.socket].group,
                        'itemType': item.inventoryId,
                        'name': gem.typeLine,
                        'imgUrl': self.getImage(gemTypeLine),
                        'gem': gem,
                        'reducedManaItem': self.getReducedMana(item, gem),
                        'supports': [],
                    });
                } else {
                    tempSupportGems.push({
                        'tags': self.getTags(gem.properties[0].name, gem.typeLine),
                        'socketGroup': item.sockets[gem.socket].group,
                        'itemType': item.inventoryId,
                        'name': gem.typeLine,
                        'gem': gem,
                    });
                }
            });

            // If Unique Item has Support Gem as Explicid
            var itemSupportGem = this.itemWithSupports(item);
            if (itemSupportGem) {
                for (var index = 0; index < itemSupportGem.length; index++) {
                    tempSupportGems.push(itemSupportGem[index]);
                }
            }

            if (skillAdded) {
                this.addSupportsToSkills(tempSkill, tempSupportGems);
            }

        },

        result: function (){
            return skills;
        }

    };
};

var itemSupports = [

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/IncreasedPhysicalDamage.png?scale=1&w=1&h=1&v=217781293d9d65ecc9e283feacadd2323',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Melee, Support, Attack', values: [['']]  },
                { name: 'Level', values: [['18']] },
                { name: 'Mana Multiplier', values: [['140%']] },
            ],
            explicitMods: [
                'Supported Melee Skills deal 47% more Melee Physical Damage',
            ],
            typeLine: 'Melee Physical Damage Support',
        },
        socketGroup: 'unique',
        name: 'Melee Physical Damage Support',
        tags: 'Melee, Support, Attack',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/FasterAttacks.png?scale=1&w=1&h=1&v=c14203f7b19650861907a30a92e3b6fe3',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Attack, Support', values: [['']]  },
                { name: 'Level', values: [['12']] },
                { name: 'Mana Multiplier', values: [['115%']] },
            ],
            explicitMods: [
                '36% increased Attack Speed',
            ],
            typeLine: 'Faster Attacks Support',
        },
        socketGroup: 'unique',
        name: 'Faster Attacks Support',
        tags: 'Attack, Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Blind.png?scale=1&w=1&h=1&v=6d0203ad79f828033ba912d5f3e5e29d3',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Support', values: [['']]  },
                { name: 'Level', values: [['6']] },
            ],
            explicitMods: [
                'Supported Skills have 10% chance to Blind enemies on hit',
                'Supported Skills have 10% increased Blinding duration',
            ],
            typeLine: 'Blind Support',
        },
        socketGroup: 'unique',
        name: 'Blind Support',
        tags: 'Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/ConcentratedAOE.png?scale=1&w=1&h=1&v=e93b8c61b7fc1c64a3fd41a058d660a33',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Support, AoE', values: [['']]  },
                { name: 'Level', values: [['20']] },
                { name: 'Mana Multiplier', values: [['140%']] },
            ],
            explicitMods: [
                'Supported Skills have 30% less Area of Effect',
                'Supported Skills deal 54% more Area Damage',
            ],
            typeLine: 'Concentrated Effect Support',
        },
        socketGroup: 'unique',
        name: 'Concentrated Effect Support',
        tags: 'Support, AoE',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/AddedLightningDamage.png?scale=1&w=1&h=1&v=9228c011d886459c66e66caa1d3e6fb13',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Lightning, Support', values: [['']]  },
                { name: 'Level', values: [['18']] },
                { name: 'Mana Multiplier', values: [['130%']] },
            ],
            explicitMods: [
                'Adds 13 to 253 Lightning Damage',
            ],
            typeLine: 'Added Lightning Damage Support',
        },
        socketGroup: 'unique',
        name: 'Added Lightning Damage Support',
        tags: 'Lightning, Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Knockback.png?scale=1&w=1&h=1&v=f4c5ecc35abb51eb0c9254f635f11e2a3',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Support', values: [['']]  },
                { name: 'Level', values: [['10']] },
            ],
            explicitMods: [
                '34% chance to Knock Enemies Back on hit',
                '50% increased Knockback Distance',
            ],
            typeLine: 'Knockback Support',
        },
        socketGroup: 'unique',
        name: 'Knockback Support',
        tags: 'Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Trap.png?scale=1&w=1&h=1&v=f31015c2707ca14c04404a605d73c07a3',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Support, Trap, Duration', values: [['']]  },
                { name: 'Level', values: [['8']] },
                { name: 'Mana Multiplier', values: [['140%']] },
            ],
            explicitMods: [
                'Trap lasts 16 seconds Supported Skills deal 18% more Trap Damage with Hits',
                'Supported Attack Skills cannot be used with Melee Weapons',
            ],
            typeLine: 'Trap Support',
        },
        socketGroup: 'unique',
        name: 'Trap Support (stave)',
        tags: 'Support, Trap, Duration',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Trap.png?scale=1&w=1&h=1&v=f31015c2707ca14c04404a605d73c07a3',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Support, Trap, Duration', values: [['']]  },
                { name: 'Level', values: [['11']] },
                { name: 'Mana Multiplier', values: [['140%']] },
            ],
            explicitMods: [
                'Trap lasts 16 seconds Supported Skills deal 21% more Trap Damage with Hits',
                'Supported Attack Skills cannot be used with Melee Weapons',
            ],
            typeLine: 'Trap Support',
        },
        socketGroup: 'unique',
        name: 'Trap Support (boots)',
        tags: 'Support, Trap, Duration',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/GenerositySupport.png?scale=1&w=1&h=1&v=21d383fa0220142a71ad3d37af58c1533',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Support, Aura', values: [['']]  },
                { name: 'Level', values: [['30']] },
            ],
            explicitMods: [
                '49% increased effect of Non-Curse Auras you Cast',
                'Supported Auras do not affect You',
            ],
            typeLine: 'Generosity Support',
        },
        socketGroup: 'unique',
        name: 'Generosity Support',
        tags: 'Support, Aura',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/AddedFireDamage.png?scale=1&w=1&h=1&v=c5fbf989cfca77fcbbc4c9438fbbcf273',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Fire, Support', values: [['']]  },
                { name: 'Level', values: [['10']] },
                { name: 'Mana Multiplier', values: [['120%']] },
            ],
            explicitMods: [
                'Gain 34% of Physical Damage as Extra Fire Damage',
            ],
            typeLine: 'Added Fire Damage Support',
        },
        socketGroup: 'unique',
        name: 'Added Fire Damage Support',
        tags: 'Fire, Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/EnduranceChargeStun.png?scale=1&w=1&h=1&v=cfa0987c3df7eb55365015fb6faebfbc3',
            properties: [
                { name: 'From Item Mod' , values: [['']] },
                { name: 'Support, Melee, Attack', values: [['']]  },
                { name: 'Level', values: [['20']] },
                { name: 'Mana Multiplier', values: [['140%']] },
            ],
            explicitMods: [
                'Gain an Endurance Charge if you Stun an Enemy with Melee Damage',
                '19% reduced Enemy Stun Threshold',
            ],
            typeLine: 'Endurance Charge on Melee Stun Support',
        },
        socketGroup: 'unique',
        name: 'Endurance Charge on Melee Stun Support',
        tags: 'Support, Melee, Attack',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/IncreasedAOE.png?scale=1&w=1&h=1&v=f0accbe4733628f443cd691574b3f6043',
            properties: [
                { name: 'From Item Mod', values: [['']]  },
                { name: 'Support, AoE', values: [['']]  },
                { name: 'Level', values: [['15']] },
                { name: 'Mana Multiplier', values: [['140%']] },
            ],
            explicitMods: [
                'Supported Skills have 44% increased Area of Effect',
            ],
            typeLine: 'Increased Area of Effect Support',
        },
        socketGroup: 'unique',
        name: 'Increased Area of Effect Support',
        tags: 'Support, AoE',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/IronWill.png?scale=1&w=1&h=1&v=afb1f20cfad22f41efd4474e238b669a3',
            properties: [
                { name: 'From Item Mod', values: [['']]  },
                { name: 'Spell, Support', values: [['']]  },
                { name: 'Level', values: [['10']] },
            ],
            explicitMods: [
                'Strength\'s damage bonus applies to Spell Damage as well for Supported Skills',
                '58% increased Spell Damage',
            ],
            typeLine: 'Iron Will Support',
        },
        socketGroup: 'unique',
        name: 'Iron Will Support',
        tags: 'Spell, Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/FirePenetration.png?scale=1&w=1&h=1&v=71763309c9c93e038fdf49738e121e443',
            properties: [
                { name: 'From Item Mod', values: [['']]  },
                { name: 'Fire, Support', values: [['']]  },
                { name: 'Level', values: [['10']] },
                { name: 'Mana Multiplier', values: [['140%']] },
            ],
            explicitMods: [
                'Supported Skills Penetrate 27% Fire Resistance',
            ],
            typeLine: 'Fire Penetration Support',
        },
        socketGroup: 'unique',
        name: 'Fire Penetration Support',
        tags: 'Fire, Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/FortifyGem.png?scale=1&w=1&h=1&v=6a69853aaf7278667bde43531541ef7d3',
            properties: [
                { name: 'From Item Mod', values: [['']]  },
                { name: 'Attack, Support, Melee', values: [['']]  },
                { name: 'Level', values: [['12']] },
                { name: 'Mana Multiplier', values: [['110%']] },
            ],
            explicitMods: [
                'Grants Fortify on Melee hit',
                '36% increased Melee Physical Damage',
                '25% increased Fortify duration'
            ],
            typeLine: 'Fortify Support',
        },
        socketGroup: 'unique',
        name: 'Fortify Support',
        tags: 'Attack, Support, Melee',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Totem.png?scale=1&w=1&h=1&v=55108fd68db92b2f2c1ea638306754b43',
            properties: [
                { name: 'From Item Mod', values: [['']]  },
                { name: 'Support, Totem, Duration', values: [['']]  },
                { name: 'Level', values: [['20']] },
                { name: 'Mana Multiplier', values: [['200']] },
            ],
            explicitMods: [
                'Summons a Totem which uses this Skill Totem lasts 8 seconds',
                'Supported Skills deal 26% less Damage',
                'Spells Cast by Totem have 30% less Cast Speed',
            ],
            typeLine: 'Spell Totem Support',
        },
        socketGroup: 'unique',
        name: 'Spell Totem Support',
        tags: 'Support, Totem, Duration',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Aurify.png?scale=1&w=1&h=1&v=86f299cfe5955d46e447ac2bbf8785bd3',
            properties: [
                { name: 'From Item Mod', values: [['']]  },
                { name: 'Support, Curse, Aura', values: [['']]  },
                { name: 'Level', values: [['22']] },
                { name: 'Mana Reservation Override', values: [['35%']] },
            ],
            explicitMods: [
                'Supported Curse Spells are Cast as Auras',
                '84% increased Area of Effect of Curse Skills',
            ],
            typeLine: 'Blasphemy Support',
        },
        socketGroup: 'unique',
        name: 'Blasphemy Support',
        tags: 'Support, Curse, Aura',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Pierce.png?scale=1&w=1&h=1&v=3adf8484c5565e2f4b02dc1db98cd9db3',
            properties: [
                { name: 'From Item Mod', values: [['']]  },
                { name: 'Support, Projectile', values: [['']]  },
                { name: 'Level', values: [['15']] },
                { name: 'Mana Multiplier', values: [['130']] },
            ],
            explicitMods: [
                '50% chance of Projectiles Piercing',
                '17% more Projectile Damage',
            ],
            typeLine: 'Pierce Support',
        },
        socketGroup: 'unique',
        name: 'Pierce Support',
        tags: 'Support, Projectile',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Echo.png?scale=1&w=1&h=1&v=8c687a9247eff1393e4fe35d69838f723',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Spell, Support', values: [['']]  },
                { name: 'Level', values: [['30']] },
                { name: 'Mana Multiplier', values: [['140']] },
            ],
            explicitMods: [
                'Supported Spells Repeat an additional time',
                '80% more Cast Speed',
                'Supported Skills deal 10% less Damage',
            ],
            typeLine: 'Spell Echo Support',
        },
        socketGroup: 'unique',
        name: 'Spell Echo Support (stave)',
        tags: 'Spell, Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Echo.png?scale=1&w=1&h=1&v=8c687a9247eff1393e4fe35d69838f723',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Spell, Support', values: [['']]  },
                { name: 'Level', values: [['1']] },
                { name: 'Mana Multiplier', values: [['140']] },
            ],
            explicitMods: [
                'Supported Spells Repeat an additional time',
                '51% more Cast Speed',
                'Supported Skills deal 10% less Damage',
            ],
            typeLine: 'Spell Echo Support',
        },
        socketGroup: 'unique',
        name: 'Spell Echo Support (wand)',
        tags: 'Spell, Support',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/ElementalProliferation.png?scale=1&w=1&h=1&v=e7ee8a5ac2a22cbec33c7eb55ad82cf13',
            properties: [
                { name: 'From Item Mod', values: [['']] },
                { name: 'Cold, Fire, Lightning, Support, AoE', values: [['']]  },
                { name: 'Level', values: [['20']] },
                { name: 'Mana Multiplier', values: [['140%']] },
            ],
            explicitMods: [
                'Supported Skills Area of Effect: 16',
                'Elemental Status Effects caused by Supported Skills spread to other enemies',
                'Supported Skills deal 21% less Damage'
            ],
            typeLine: 'Elemental Proliferation Support',
        },
        socketGroup: 'unique',
        name: 'Elemental Proliferation Support',
        tags: 'Cold, Fire, Lightning, Support, AoE',
        itemType: '',
    },


    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/Splash.png?scale=1&w=1&h=1&v=afa90bab1663bcefcbabff20d7479a043',
            properties: [
                { name: 'From Item Mod', values: [['']]},
                { name: 'Support, Melee, Attack, AoE', values: [['']]  },
                { name: 'Level', values: [['25']] },
                { name: 'Mana Multiplier', values: [['160']] },
            ],
            explicitMods: [
                '23% less Damage to other targets',
                'Single-target Melee attacks deal Splash Damage to surrounding targets',
                '72% more Melee Splash Area of Effect'
            ],
            typeLine: 'Melee Splash Support',
        },
        socketGroup: 'unique',
        name: 'Melee Splash Support',
        tags: 'Support, Melee, Attack, AoE',
        itemType: '',
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Support/BloodMagic.png?scale=1&w=1&h=1&v=cddb11c579c3422bd85a7c989dc60cbc3',
            properties: [
                { name: 'From Item Mod', values: [['']]},
                { name: 'Support', values: [['']]  },
                { name: 'Mana Multiplier', values: [['100%']] },
            ],
            explicitMods: [
                'Supported Skills spend Life instead of Mana',
            ],
            typeLine: 'Blood Magic Support',
        },
        socketGroup: 'unique',
        name: 'Blood Magic Support',
        tags: 'Support',
        itemType: '',
    },
];

var itemSkills = [
    {
        gem: {
            icon: '',
            properties: [
                { name: 'Trigger, Projectile, Attack, AoE, Fire' },
                { name: 'Cooldown Time', values: [['0.15 sec']] },
            ],
            secDescrText: 'Launch molten projectiles from the point of impact, causing AoE attack damage to enemies where they land. Has a very short cooldown.',
            explicitMods: [
                'Deals 141% of Base Attack Damage',
                '60% of Physical Damage Converted to Fire Damage',
                '2 additional Projectiles',
                '40% less Projectile Damage',
                '20% chance to Attack with this Skill on Melee Hit'
            ],
            typeLine: 'Molten Burst',
        },
        name: 'Molten Burst',
        tags: 'Trigger, Projectile, Attack, AoE, Fire',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    },

    {
        gem: {
            icon: '',
            properties: [
                {name: 'Trigger, Spell, AoE, Fire'},
                {name: 'Critical Strike Chance', values: [['5.00%']]},
                {name: 'Damage effectiveness', values: [['100%']]},
            ],
            secDescrText: 'While you run, this skill creates a small explosion with each step, dealing fire damage in an area around you.',
            explicitMods:[
                '50 to 70 fire damage',
                '10% chance to Ignite enemies',
                'You cannot Cast this Spell directly',
                'This Skill cannot Knock Enemies Back',
                'This Spell is Cast while Equipped'
            ],
            typeLine: 'Abberath\'s Fury',
        },
        name: 'Abberath\'s Fury',
        tags: 'Trigger, Spell, AoE, Fire',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    },

    {
        gem: {
            icon: '',
            properties: [
                {name: 'Spell, Minion, Duration'},
            ],
            secDescrText: 'Summon cute doggies',
            explicitMods:[],
            typeLine: 'Summon Spectral Wolf',
        },
        name: 'Summon Spectral Wolf',
        tags: 'Spell, Minion, Duration',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    },

    {
        gem: {
            icon: '',
            properties: [
                {name: 'Spell, Minion, Duration'},
            ],
            secDescrText: 'Summon Spiders',
            explicitMods:[],
            typeLine: 'Raise Spiders',
        },
        name: 'Raise Spiders',
        tags: 'Spell, Minion, Duration',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    },

    {
        gem: {
            icon: '',
            properties: [
                {name: 'Spell'},
                {name: 'Cooldown Time', values: [['0.5']]},
            ],
            secDescrText: 'Releases Dancing Dervish to fight by your side. While Dancing Dervish is manifested, you have Onslaught and cannot use Weapons.',
            explicitMods:[
                '25% increased Attack Speed',
                '110% increased Attack Damage',
                '100% chance to Cast this Spell when you Rampage',
                'Minions have 30% increased Movement Speed'
            ],
            typeLine: 'Manifest Dancing Dervish',
        },
        name: 'Manifest Dancing Dervish',
        tags: 'Spell',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    },

    {
        gem: {
            icon: '',
            properties: [
                {name: 'Cold, Spell, AoE, Duration'},
                {name: 'Cast time', values: [[' 1sec']]},
                {name: 'Radius', values: [['25 for storm, 10 for individual icicles']]},
                {name: 'Critical Strike Chance', values: [['5%']]},
                {name: 'Damage effectiveness', values: [['30%']]},
            ],
            secDescrText: 'Icy bolts rain down over the targeted area. They explode when landing, dealing damage to nearby enemies and chilling them, as well as causing patches of chilled ground. Skill damage is based on Intelligence.',
            explicitMods:[
                'Deals 1–3 base cold Damage per 10 Intelligence',
                'One impact every 0.10 seconds',
                'Chilled Ground lasts 0.50 seconds',
                'Skill Lasts 1.5 seconds',
                'Skill 0.15 seconds increased skill effect base duration per 100 intelligence'
            ],
            typeLine: 'Icestorm',
        },
        name: 'Icestorm',
        tags: 'Cold, Spell, AoE, Duration',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    },

    {
        gem: {
            icon: '',
            properties: [
                {name: 'Aura, Spell, AoE, Chaos'},
                {name: 'Level', values: [['60']]},
                {name: 'Mana Reserved', values: [['50%']]},
                {name: 'Cooldown Time', values: [['1.2 sec']]},
                {name: 'Radius', values: [['36']]},
            ],
            secDescrText: 'Casts an aura that adds chaos damage to the attacks and spells of you and your allies.',
            explicitMods:[
                'Adds 58 to 81 Chaos Damage to Attacks',
                'Adds 52 to 69 Chaos Damage to Spells',
            ],
            typeLine: 'Envy',
        },
        name: 'Envy',
        tags: 'Aura, Spell, AoE, Chaos',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    },

    {
        gem: {
            icon: '',
            properties: [
                {name: 'Trigger, Attack, Projectile, Physical'},
                {name: 'Level', values: [['64']]},
                {name: 'Cooldown Time', values: [['0,5 sec']]},
            ],
            secDescrText: 'A spiral of bones erupts around you, dealing physical damage.',
            explicitMods:[
                'Deals 180 to 300 Physical Damage',
                '8 additional Projectiles',
                'Cannot cause Bleeding',
                'Attack with this Skill when you kill a Bleeding Enemy',
                'You cannot use this Attack directly',
            ],
            typeLine: 'Bone Nova',
        },
        name: 'Bone Nova',
        tags: 'Trigger, Attack, Projectile, Physical',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    },

    {
        gem: {
            icon: 'https://web.poecdn.com/image/Art/2DItems/Gems/Repulse.png?scale=1&w=1&h=1&v=cfe165c3e1592b8b7f1e7152416963d73',
            properties: [
                {name: 'Trigger, Attack, AoE, Melee'},
                {name: 'Can store 1 use(s)'},
                {name: 'Level', values: [['30']]},
                {name: 'Cooldown Time', values: [['0,40 sec']]},
            ],
            secDescrText: 'Perform a swift counter-attack against enemies in a cone shape when you block with your shield.',
            explicitMods:[
                '0.5% increased Area of Effect radius',
                'Deals 128% of Base Attack Damage',
                'Counterattack with this Skill when you Block',
                'You cannot use this Attack directly',
            ],
            typeLine: 'Reckoning',
        },
        name: 'Reckoning',
        tags: 'Trigger, Attack, AoE, Melee',
        itemType: '',
        reducedManaItem: 0,
        supports: []
    }
];
