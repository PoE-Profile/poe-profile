<template>

<span :class="['item-box', itemRarity]">
    <span :class="'header ' + typeHeader">
        <div class="wiki-link" v-if="itemInfo.frameType === 3 && showWiki">
            <small> <a :href="wikiLink" target="_blank" style="color: white;">[ wiki ]</a></small>
        </div>
         {{name}}
        <br v-if="name.length>0">{{withEllipsis(typeLine,35)}}
    </span>

    <span class="item-stats">

        <span v-if="showFlask" class="group -textwrap tc -mod">
            <span :style="{listStyleType: 'none', color: 'grey'}" v-for="prop in flaskProperties">
                {{ prop}}<br>
            </span>
        </span>
        <span class="group -textwrap tc -mod" v-if="itemInfo.properties">
            <span :style="{listStyleType: 'none', color: 'grey'}" v-for="prop in itemInfo.properties">
                {{ prop.name }}: <span v-if="prop.values.length > 0" style="color: white;"> {{prop.values[0][0]}}</span><br>
            </span>
        </span>

        <span class="group -textwrap tc -mod" v-if="itemInfo.requirements">
            <span class="group -textwrap tc -mod" v-if="itemInfo.talismanTier">
                <p :style="{listStyleType: 'none', color: 'grey', display: 'inline'}">Talisman Tier: </p> <span style="color: white;">{{ itemInfo.talismanTier }} </span>
                <br>
            </span>

            <p :style="{listStyleType: 'none', color: 'grey', display: 'inline'}">Requires </p>
            <p
                :style="{listStyleType: 'none', color: 'grey', display: 'inline'}"
                v-for="(req, index) in itemInfo.requirements"
            >
                {{ req.name }}
                <span v-if="req.values.length > 0" style="color: white;"> {{req.values[0][0]}} {{ index+1 < itemInfo.requirements.length ? ',' : ''}} </span>
            </p>
        </span>

        <span class="group -textwrap tc -mod" v-if="itemInfo.implicitMods">
            <span :style="{listStyleType: 'none'}" v-for="mod in itemInfo.implicitMods"> {{ mod }} <br></span>
        </span>

        <span class="group -textwrap tc -mod" v-if="itemInfo.enchantMods" style="color:lightblue;">
            {{itemInfo.enchantMods[0]}}
        </span>

        <span class="group -textwrap tc -mod" v-if="itemInfo.utilityMods">
            <span :style="{listStyleType: 'none'}" v-for="mod in itemInfo.utilityMods"> {{ mod }} <br></span>
        </span>

        <span class="group -textwrap tc -mod" v-if="itemInfo.explicitMods">
            <span :style="{listStyleType: 'none'}" v-for="mod in itemInfo.explicitMods"> {{ mod }} <br></span>
            <span :style="{listStyleType: 'none', color: 'lightblue'}"
                v-if="itemInfo.craftedMods"
                v-for="craftedMod in itemInfo.craftedMods">
                {{craftedMod}} <br>
            </span>
        </span>

        <span class="group -textwrap tc -mtx" v-if="cosmeticMods">
            <span v-for="mod in cosmeticMods"> {{ mod }} <br></span>
        </span>

        <span class="group -textwrap tc -mod" v-if="itemInfo.corrupted">
            <span class="corrupted"> CORRUPTED </span>
        </span>
    </span>
</span>

</template>

<script type="text/javascript">

export default {

    props: {
        itemInfo: Object,
        showFlask: Boolean,
        showWiki:{
            type: Boolean,
            default: false
        },
    },

    data: function() {
        return {
        }
    },

    computed: {
        'flaskProperties': function(){
            if (this.showFlask !== true) {
                return [];
            }
            var props = [];
            this.itemInfo.properties.forEach(function(prop){
                var tempProp = '';
                if (prop.name.indexOf('%0') !== -1) {
                    tempProp = prop.name.replace('%0', prop.values[0][0]);
                }
                if (prop.name.indexOf('%1') !== -1) {
                    tempProp = tempProp.replace('%1', prop.values[1][0]);
                }
                props.push(tempProp);
            });
            props.shift();
            return props;
        },

        'wikiLink': function() {
            var name = this.itemInfo.name.replace("<<set:MS>><<set:M>><<set:S>>", "")
            return 'http://pathofexile.gamepedia.com/' + name.replace(' ', '_');
        },

        'name': function() {
            return this.itemInfo.name.replace("<<set:MS>><<set:M>><<set:S>>", "");
        },

        'typeLine': function() {
            return this.itemInfo.typeLine.replace("<<set:MS>><<set:M>><<set:S>>", "");
        },

        'cosmeticMods': function() {
            var tempMods = [];
            if (this.itemInfo.cosmeticMods) {
                this.itemInfo.cosmeticMods.forEach(function(mod){
                    tempMods.push(mod.replace("<<set:MS>><<set:M>><<set:S>>", ""));
                })
                return tempMods;
            }
            return false;
        },

        'typeHeader': function() {
            if (this.itemInfo.frameType === 0) { return '-single' }
            if (this.itemInfo.frameType === 1) { return '-single' }
            if (this.itemInfo.frameType === 2) { return '-double' }
            if (this.itemInfo.frameType === 3) { return '-double' }
            if (this.itemInfo.frameType === 9) { return '-double' }
            return '';
        },

        'itemRarity': function() {
            if (this.itemInfo.frameType === 0) { return '-normal' }
            if (this.itemInfo.frameType === 1) { return '-magic' }
            if (this.itemInfo.frameType === 2) { return '-rare' }
            if (this.itemInfo.frameType === 3) { return '-unique' }
            if (this.itemInfo.frameType === 9) { return '-uniqueLegacy' }
            return '';
        },

    },

    watch: {
        'itemInfo': function(val, oldVal){
            if (val.inventoryId === 'Flask') {
                this.name = this.itemInfo.typeLine.replace("<<set:MS>><<set:M>><<set:S>>", "");
            }
        },
    },
    methods: {
        withEllipsis: function(text,after){
            if(text.length<=after){
                return text;
            }
            return text.substring(0, after)+".."
        },
    },

};

</script>
