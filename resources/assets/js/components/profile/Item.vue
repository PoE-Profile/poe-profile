<template>
<div :class="[item.inventoryId, itemRarity(item.frameType)]"
    @mouseover="hover = true" @mouseleave="hover = false"
    >

    <div :class="['icon', showInfo ? 'skillsFloatRight' : '']">
        <div :class="['sockets', ]" v-if="toggleInfo()">
            <div  v-if="item.sockets" class="socket-inner" style="position: relative; z-index: 9999;">
                <span v-for="(socket, index) in item.sockets">
                    <socket
                        v-if="item.sockets !== ''"
                        :socket="socket"
                        :current-socket="index"
                        :gem="findGem(index)"
                        :linked="findLinks()[index]"
                        :show-gem-tooltip="showGemTooltip"
                        :hide-gem-tooltip="hideGemTooltip">
                    </socket>
                </span>

            </div>
        </div>
        <img :src="item.icon" :class="showInfo ? 'skillsFloatRight' : ''">
    </div>

    <div :class="computedItemInfoClass" v-if="item.name !== 'null'">
        <gem-info  v-if="showGemInfo && !isAbyss" :gem-info="hoverGem" :show-wiki="false" style="float:left;width:420px;"></gem-info>
        <item-info v-if="showGemInfo && isAbyss" :item-info="hoverGem" style="float:left;width:410px;" :show-flask="showFlask" :show-wiki="showInfo"></item-info>
        <item-info v-if="toggleInfo()" :item-info="item" :show-Flask="showFlask" :show-wiki="showInfo"></item-info>
    </div>

</div>
</template>

<script type="text/javascript">

import ItemInfo from './ItemInfo.vue';
import GemInfo from './GemInfo.vue';
import Socket from './Socket.vue';

export default {

    // props: [
    //     'item', 'showInfo', 'showFlask'
    // ],
    props: {
        showInfo: {
            type: Boolean,
            default: false,
            required: false,
        },
        item: {
            type: Object,
            required: true
        },
        showFlask: {
            type: Boolean,
            default: false,
            required: false,
        },
    },
    computed: {
        'name': function() {
            return this.item.name.replace("<<set:MS>><<set:M>><<set:S>>", " ");
        },
        'isAbyss':function(){
            if(this.hoverGem.abyssJewel){
                return true;
            }
            return false;
        },
        'computedItemInfoClass':function(){
            // console.log(this.hoverGem);
            var classArray = ['item-info'];
            if(this.showFlask){
                classArray.push('item-info-flask');
            }
            if(this.hoverGem!==null && !this.showInfo){
                classArray.push('item-info-gem');
            }
            return classArray
        },
        'showItemInfoWiki': function(){
            return
        },
        'showGemInfo':function(){
            if(this.hoverGem!==null && !this.showInfo){
                return true;
            }
            return false;
        }
    },

    components: {
        'itemInfo': ItemInfo, 'socket': Socket, 'gemInfo': GemInfo
    },

    data: function() {
        return {
            parseSockets: [],
            lastGroup: 0,
            showItem: false,
            hover: false,
            hoverGem: null,
            infoClass: {
                items: 'item-info',
                gems: 'item-info-gems',
            }
        }
    },

    methods: {
        toggleInfo: function(){
            return this.showInfo ? true : this.hover;
        },

        showGemTooltip: function(index){
            this.hoverGem=this.findGem(index)
            if(!this.hoverGem){
                this.hoverGem=null;
            }
        },

        hideGemTooltip: function(){
            this.hoverGem = null
        },

        findLinks: function(){
            var tempGroup = 0;
            var links = [];
            this.item.sockets.forEach(function(socket, index){
                if (tempGroup !== socket.group) {
                    tempGroup = socket.group;
                    links.push(false);
                } else {
                    links.push(true);
                }
            })
            links.shift();
            // console.log(links);
            return links;
        },

        findGem: function(ind){
            var currentGem = this.item.socketedItems.filter(function(gem){
                return gem.socket === ind;
            })[0];
            return currentGem ? currentGem : null;
        },

        testGem: function(){
            return {gem:this.item.socketedItems[0]}
        },

        itemRarity: function(type){
            // console.log(this.item.inventoryId);
            if (this.showInfo) { return 'none'}
            if (type === 0) { return 'normalItemBg' }
            if (type === 1) { return 'magicItemBg' }
            if (type === 2) { return 'rareItemBg' }
            if (type === 3) { return 'uniqueItemBg' }
            if (type === 9) { return 'uniqueLegacyItemBg' }
            return 'noneItemBg';
        },

        hoverRarity: function(type){
            if (type === 2) { return '-rare' }
            if (type === 3) { return '-unique' }
            return '';
        },
    }

};

</script>
