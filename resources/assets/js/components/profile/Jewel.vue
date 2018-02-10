<style media="screen">

</style>

<template>
    <div class="" style="color: white !important; min-height: 30px;">
        <span :class="['item-box', itemRarity]" style="min-width: 370px!important;height: 10px;">
            <span :class="'header ' + typeHeader">
                <div class="wiki-link" v-if="jewel.frameType === 3">
                    <small> <a :href="wikiLink" target="_blank" style="color: white;">[ wiki ]</a></small>
                </div>
                <img :src="jewel.icon" alt="" style="float:left;" height="35" width="35">
                <span v-if="name.length > 1">
                    {{name}}
                    <br v-if="typeLine.length > 1">
                </span> 
                {{typeLine}}
            </span>
            <span class="item-stats">
                <span class="-textwrap tc -mod" style="white-space: normal;">
                    <span v-for="mod in jewel.explicitMods">{{mod}}<br></span>
                </span>
            </span>
        </span>
    </div>
</template>

<script type="text/javascript">

export default {

    props: [
        'jewel',
    ],

    computed: {
        'name': function() {
            return this.jewel.name.replace("<<set:MS>><<set:M>><<set:S>>", "");
        },

        'typeLine': function() {
            return this.jewel.typeLine.replace("<<set:MS>><<set:M>><<set:S>>", "");
        },

        'itemRarity': function() {
            if (this.jewel.frameType === 0) { return '-normal' }
            if (this.jewel.frameType === 1) { return '-magic' }
            if (this.jewel.frameType === 2) { return '-rare' }
            if (this.jewel.frameType === 3) { return '-unique' }
            return '';
        },
        'typeHeader': function() {
            if (this.jewel.frameType === 0) { return '-single' }
            if (this.jewel.frameType === 1) { return '-single' }
            if (this.jewel.frameType === 2) { return '-double' }
            if (this.jewel.frameType === 3) { return '-double' }
            return '';
        },
        'wikiLink': function() {
            return 'http://pathofexile.gamepedia.com/' + this.name.replace(' ', '_');
        },
    },

    components: {

    },

    data: function() {
        return {

        }
    },

    created: function(){
        
    },

    methods: {

    }

};

</script>
