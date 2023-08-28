<style type="text/css">
.mod-info a {
    color: #ebb16c!important;
    font-weight: bold;
}

</style>

<template>
<div>
    <div>
        <div class="panel-heading" style="padding: 5px 0px;">
            <input type="text" class="form-control" v-model="searchStat" name=""
            style="width:95%;margin:6px 6px 0px;" placeholder="Search Stat ...">
            <div style="padding:8px;" v-show="searchStat.length>0">
                Search result "{{searchStat}}":
                <a href="#" @click.prevent="searchStat=''" class="pull-right">
                    <i class="fa fa-times-circle" style="font-size: 1.5em;" aria-hidden="true"></i></a>

            </div>
            <span v-if="loading" class="">
                <loader :loading="loading" style="margin-left:auto;margin-right:auto;"></loader>
            </span>
            <ul v-else class="nav nav-tabs" v-show="searchStat.length==0" style="padding-left: 0px; margin-bottom:3px;">
                <li class="nav-item" style="width:20%;">
                <a :class="['nav-link', selectedStatType === 'main' ? 'active' : '']"
                    href="#" @click.prevent="selectedStatType='main'" style="padding: .5em 0em;"> Main </a>
                </li>
                <li class="nav-item" style="width:22%;">
                <a :class="['nav-link', selectedStatType === 'offense' ? 'active' : '']"
                    href="#" @click.prevent="selectedStatType='offense'" style="padding: .5em 0em;"> Offense </a>
                </li>
                <li class="nav-item" style="width:22%;">
                <a :class="['nav-link', selectedStatType === 'defense' ? 'active' : '']"
                    href="#" @click.prevent="selectedStatType='defense'" style="padding: .5em 0em;">Defense</a>
                </li>
                <li class="nav-item" style="width:20%;">
                <a :class="['nav-link', selectedStatType === 'misc' ? 'active' : '']"
                    href="#" @click.prevent="selectedStatType='misc'" style="padding: .5em 0em;"> Misc </a>
                </li>
                <li class="nav-item" style="width:12%;">
                <a :class="['nav-link', selectedStatType === 'favs' ? 'active' : '']"
                    href="#" @click.prevent="selectedStatType='favs'" style="padding: .5em 0em;">
                <i class="fa fa-star" aria-hidden="true"></i> </a>
                </li>
            </ul>
        </div>

        <div class="list-group showless">
            <span v-for="(stat, index) in computedStats"
                @mouseover="toggleStat(stat, index)"
                @mouseleave="untoggleStat"
                @click.prevent="stickStat(stat)">
                <stat
                    :stat="stat"
                    :index="index"
                    :key="index"
                    :sticked="stickedStat">
                </stat>
            </span>
        </div>

    </div>

    <div class="mod-info stat-info" v-show="showStatInfo" :style="{position:'absolute', left:'332px', top: hoveredStatPos+'px'}">
        <span class="mod-header">{{hoveredStat.text !== '' ? hoveredStat.text : hoveredStat.total+' '+hoveredStat.name}}</span>
        <span class="item-stats">
            <span class="group -textwrap tc -stat" v-if="hoveredStat.itemVal > 0">
                <span class="title">From Items: </span>{{hoveredStat.itemVal}}
                <br>
            </span>

            <span class="group -textwrap tc -stat" v-if="hoveredStat.strVal">
                <span class="title">From Strength: </span>{{hoveredStat.strVal}}
            </span>
            <span class="group -textwrap tc -stat" v-if="hoveredStat.intVal">
                <span class="title">From Intellingence: </span>{{hoveredStat.intVal}}
            </span>
            <span class="group -textwrap tc -stat" v-if="hoveredStat.dexVal">
                <span class="title">From Dexterity: </span>{{hoveredStat.dexVal}}
            </span>
            <span class="group -textwrap tc -stat" v-if="hoveredStat.jewVal > 0">
                <span class="title">From jewels: </span>{{hoveredStat.jewVal}}
            </span>
            <span class="group -textwrap tc -stat" v-if="hoveredStat.treeVal > 0">
                <span class="title">From Tree: </span>{{hoveredStat.treeVal}}
            </span>
            <span class="group -textwrap tc -stat" v-if="hoveredStat.baseVal">
                <span class="title">From Base: </span>{{hoveredStat.baseVal}}
            </span>
            <span class="group gem-textwrap tc -mod" v-if="hoveredStat.itemVal > 0 || hoveredStat.note">
                Click For More Info
            </span>
        </span>
    </div>
</div>
</template>

<script type="text/javascript">
var favStore = require('../../helpers/FavStore.js');
var profileStore = require('../../helpers/profileStore.js');
import Stat from './Stat.vue';
import Loader from '../Loader.vue';

export default {

    props: [
        'character', 'account', 'offHand'
    ],
    components: {
        'stat': Stat,'loader':Loader,
    },
    data: function() {
        return {
            allStats: [],
            searchStat: '',
            selectedStatType: 'main',
            favStore: favStore,
            profileStore: profileStore,
            stickedStat: '',
            hoveredStat: '',
            hoveredStatPos:0,
            showStatInfo:false,
            loading:false,
        }
    },

    created: function () {
    },

    mounted: function () {
        this.getCharacterStats();
    },

    computed: {

        'computedStats': function(){
            var allStats = this.profileStore.getAllStats();
            if (!Object.keys(allStats).length > 0) {
                return;
            }
            var baseStats = [];
            var vm = this;

            if(vm.searchStat.length>0){
                var tempStats = allStats.defense.concat(allStats.offense).concat(allStats.misc);
                return tempStats.filter(function(stat){
                    let statName = stat.name.toLowerCase();
                    return stat.total > 0 && statName.indexOf(vm.searchStat.toLowerCase()) !== -1
                });;
            }

            if (this.selectedStatType === 'main') {
                baseStats = allStats.defense.concat(allStats.offense);
                var baseStatsIndexes = [14,15,16,8,9,10,11,12,13,4,5,6,7];
                var tempStats=[];
                baseStatsIndexes.forEach(function(index){
                    if(baseStats[index].total>0){
                        tempStats.push(baseStats[index]);
                    }
                });
                return tempStats;
            } else if(this.selectedStatType==='favs'){
                return allStats.defense.concat(allStats.offense).filter(
                    function(stat, index){
                        return vm.favStore.checkStatIsFav(stat.name)
                    }
                );
            } else {
                return allStats[this.selectedStatType].filter(function(s){
                    return s.total>0 || s.text.includes("Resist");
                });
            }

        },
    },

    watch: {
        'offHand': function(val,oldVal){
            this.getCharacterStats();
        }
    },

    methods: {
        getCharacterStats: function () {
            this.loading=true;
            if(this.account === ''){
                var build = favStore.favBuilds[0];
                this.account = 'build::'+build.buildId,
                this.character = build
            }
            var formData = new FormData();
            formData.append('account', this.account);
            formData.append('character', this.character.name);
            formData.append('realm', window.PHP.realm);

            //start loading bar for items and stats
            var url="/api/char/stats"
            if (this.offHand) {
                var url="/api/char/stats?offHand=yes"
            }
            axios.post(url, formData).then((response) => {
                //stop loading bar for stats
                this.profileStore.setAllStats(response.data);
                this.$emit('stat-loaded');
                this.loading=false;
            }).catch(e => {
                if (e.response.status === 404) {
                    alert(e.response.data.message);
                }
            });;
        },

        toggleStat: function (stat,index) {
            this.hoveredStatPos=60+(index*47)
            this.hoveredStat=stat;
            this.showStatInfo=true;
        },

        untoggleStat: function () {
            this.showStatInfo=false;
        },

        stickStat: function (stat) {
            if(this.stickedStat==stat){
                this.stickedStat=''
                this.showStatInfo=true;
            }else{
                this.stickedStat=stat;
                this.showStatInfo=false;

            }
            this.$emit('toggle-stick', stat)
        },
    }

};

</script>
