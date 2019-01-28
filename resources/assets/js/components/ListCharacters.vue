<template>
<div class="ladder-table h-100" :class="{'compact': compact }">
    <table class="table h-100 table-hover homapage-table">
        <thead>
            <slot name="thead">
                <tr>
                    <th v-if="showRank">Rank</th>
                    <th v-if="!archive">
                        Class
                    </th>
                    <th v-else>
                        <span>Class</span>
                    </th>
                    <th>Account</th>
                    <th>
                        <span v-if="showRank">Character</span>
                        <span v-else>Last Character</span>
                    </th>
                    <th v-if="showSkills">
                        Skills
                    </th>
                    <th v-if="archive||league">
                        <span>League</span>
                    </th>
                    <th v-if="delve">
                        Depth
                    </th>
                    <th class="text-center" style="max-width:50px;" v-if="showRank">XPH</th>
                    <th class="text-center">Level</th>
                    <th v-if="showTwitch"><i aria-hidden="true" class="fa fa-twitch"></i></th>
                </tr>
            </slot>
        </thead>
        <tbody>
            <tr v-for="(char, index) in charData" :class="{ 'active': (char.rank==select), 'dead': char.dead , 'online': char.online }">
                <th scope="row" v-if="showRank">
                    {{char.rank}}<br>
                    <small v-if="char.stats!=null && char.stats.ranks.length>0"
                        :class="classRankStats(char.stats.ranks)">
                        {{char.stats.ranks}}</small>
                </th>
                <td class="class-cell">
                    <a href="#" v-if="char.class.length>0" title=""
                        @click.prevent="trigerFilterClass(char.class)" >
                    <div class="card card-inverse twitch-card">
                        <img v-bind:src="'/imgs/classes/' + char.class + '.png'"  >
                        <div class="caption-overlay">
                            <p class="card-text">{{char.class}}</p>
                        </div>
                        <div class="card-backdrop"></div>
                    </div>
                    </a>
                </td>
                <td>
                    <i aria-hidden="true" v-if="char.online" class="fa fa-circle show-tooltip"
                        style="color: #02a600;font-size: 14px;"
                        data-toggle="tooltip" data-placement="top" title="online"></i>
                    <a :href="ranksUrl(char, true)" @click="selectChar(char,$event)">{{char.account.name}}</a>
                </td>
                <td>
                    <span v-if="char.name.length>0">
                        <a v-if="!char.dead" :href="ranksUrl(char)"
                            @click="selectChar(char,$event)">{{char.name}}</a>
                        <span v-else>{{char.name}} <small style="color: red;">DEAD</small></span>
                    </span>
                    <span v-else>No Info</span>
                </td>
                <td class="skill-cell text-center" v-if="showSkills">
                    <span v-if="showRank && !char.public">
                        <span style="color: gray;">private/removed</span>
                    </span>
                    <ul class="home-list-skills" v-else>
                        <li v-for="skill in getActiveSkill(char.items_most_sockets)"
                        class="show-tooltip" data-toggle="tooltip" data-placement="top" data-html="true"
                        :title="'<strong>'+skill.name+'</strong><br>'+skill.supports.map(x => x.name).join('<br>')">
                            <a href="#" @click.prevent="trigerFilterSkills(skill.name)">
                                <div class="skill-card card card-inverse">
                                    <img v-bind:src="skill.imgUrl">
                                    <div class="card-backdrop"></div>
                                </div>
                            </a>
                        </li>
                        <li style="width: 25px!important;" v-if="!char.dead && showRank">
                            <button class="btn btn-sm poe-btn show-tooltip"
                            data-toggle="tooltip" data-placement="top" style="padding:6px;"
                            title="Update skill" @click.prevent="updateActiveSkills(index)">
                                <span v-if="updatingIndex==index">loading..</span>
                                <i class="fa fa-refresh" v-else ></i>
                            </button>
                        </li>
                    </ul>
                </td>
                <td class="skill-cell" v-if="archive||league">
                    <a :href="route('ladders.show',char.league)">{{char.league}}</a>
                </td>
                <td v-if="delve">
                    <span v-if="char.delve_default!=char.delve_solo&&char.delve_solo>0">
                        {{char.delve_default}}
                        <br>Solo: {{char.delve_solo}}
                    </span>
                    <span v-else>{{char.delve_default}}</span>
                </td>
                <td v-if="showRank" class="stats-xph">
                    <small v-if="char.stats!=null && parseInt(char.stats.xph)>0">
                        {{char.stats.xph}}</small>
                </td>
                <td>
                    <b>{{char.level}}</b>
                    <div class="html5-progress-bar">
                        <progress v-if="!league&&char.levelProgress<100" max="100"
                        :value="char.levelProgress"></progress>
                    </div>
                </td>
                <td class="twitch-cell" v-if="showTwitch">
                    <span v-if="char.account.streamer!=null">
                        <button class="btn btn-sm poe-btn show-tooltip" style="padding:4px;"
                        data-toggle="tooltip" data-placement="top" v-if="compact"
                        title="Load Twitch Stream" @click.prevent="openTwitch(char.account.streamer)">
                            <span v-if="char.account.streamer.online">
                                <i class="fa fa-circle" aria-hidden="true" style="color:red;"></i>
                                <strong><i class="fa fa-twitch" aria-hidden="true"></i></strong>
                            </span>
                            <span v-else style="color:gray;">
                                <strong><i class="fa fa-twitch" aria-hidden="true"></i></strong>
                            </span>
                        </button>
                        <a href="#" class="show-tooltip" @click.prevent="openTwitch(char.account.streamer)"
                        data-placement="top" title="" v-else :data-original-title="char.account.streamer.status">
                            <div class="card card-inverse twitch-card">
                                <div class="hover">
                                    <span class="fa fa-play fa-5x play"></span>
                                </div>
                                <img v-bind:src="char.account.streamer.img_preview">
                                <div class="caption-overlay">
                                    <p class="card-text">
                                        {{char.account.streamer.name}}<span class="pull-right">
                                            {{char.account.streamer.viewers}} viewers</span>
                                    </p>
                                </div>
                                <div class="card-backdrop"></div>
                            </div>
                        </a>
                    </span>
                </td>
            </tr>
            <tr v-if="noResults">
                <td colspan="6" align="center">
                    <br><br><br><br><br><br>
                    No Results <a href="#" @click.prevent="clearFilters(true)">Clear filter</a>
                    <br><br><br><br><br><br><br><br>
                </td>
            </tr>
        </tbody>
    </table>

</div>
</template>

<script type="text/javascript">

import { SkillsHelper } from '../helpers/SkillsHelper.js';
import {poeHelpers} from '../helpers/poeHelpers.js';
var skillsData = require('../helpers/SkillsData.js');

export default {

    props: {
        charData: {
            type: Array,
            required: true,
            default: [],
        },
        archive: {
            type: Boolean,
            default: false,
        },
        delve:{
            type: Boolean,
            default: false,
        },
        league:{
            type: Boolean,
            default: false,
        },
        select:{
            type: Number,
            required: false,
            default:-1
        },
        compact:{
            type: Boolean,
            default: false,
        },
        loadProfile:{
            type: Boolean,
            default: true,
        },
        showTwitch:{
            type: Boolean,
            default: false,
        },
        showSkills:{
            type: Boolean,
            default: true,
        }
    },

    components: {},

    watch : {
        charData: function(val){
            this.noResults = val.length==0;
        },
    },
    data: function(){
        return {
            noResults: false,
            skillImages: '',
            updatingIndex: -1,
        }
    },

    computed: {
        showRank: function(){
            // return false;
            if(this.charData.length==0){
                return true;
            }

            if (this.charData.length <= 0) {
                return false;
            }
            var char = this.charData[0];
            if('rank' in char){
                return true;
            }
        },
    },

    mounted: function() {
        this.skillImages = skillsData;
    },

    methods: {
        ranksUrl: function(char, acc=false){
            if (acc) {
                return route('profile.acc',char.account.name);
            }
            return route('profile.char', {acc: char.account.name, char: char.name});
        },

        selectChar: function(char,event){
            if(this.loadProfile){
                return;
            }
            event.preventDefault();
            this.$emit('selected-char', char)
        },

        getActiveSkill: function(items){
            if(items==null || items.length==0){
                return;
            }
            var skills = new SkillsHelper(this.skillImages);
            if (items === null) {
                return;
            }

            items.forEach(function(item){
                skills.addSkills(item);
            });
            return this.filterSkills(skills.result());
        },

        updateActiveSkills: function(charIndex){
            this.updatingIndex=charIndex;
            var char = this.charData[charIndex];
            var formData = new FormData();
            formData.append('charId', char.id);
            axios.post(route('api.ladders.skill'), formData).then((response) => {
                this.charData[charIndex] = response.data;
                this.updatingIndex=-1;
                this.loadTooltips();
            });
        },
        loadTooltips(){
            Vue.nextTick(function () {
                $('.show-tooltip').tooltip('dispose');
                $('.show-tooltip').tooltip();
            });
        },

        clearFilters: function(reload){
            this.trigerFilterSkills('All')
            this.trigerFilterClass('All')
        },

        trigerFilterSkills: function(s){
            this.loadTooltips();
            this.$emit('filter-skill', s)
        },

        trigerFilterClass: function(c){
            this.$emit('filter-class', c)
        },

        filterSkills: function (arr) {
            var activeSkills = [];
            var filterNoneRepeat = ['Portal','Immortal Call','Blood Rage'];

            arr.forEach(function(skill){
                if (!(skill.tags.includes('Curse,') || skill.tags.includes('Aura,') || skill.tags.includes('Warcry,'))
                    && !_.includes(filterNoneRepeat, skill.name)) {
                    activeSkills.push(skill);
                    filterNoneRepeat.push(skill.name);
                }
            })
            return activeSkills;
        },

        openTwitch: function(stream){
            this.$emit('selected-twitch', stream)
        },
        classRankStats: function(ranks){
            return {
                'rank-plus': (parseInt(ranks)>0),
                'rank-minus': (parseInt(ranks)<0)
            }
        }
    },
};
</script>

<style>
    .rank-plus{color:lightgreen}
    .rank-minus{color:red}
    .table td, .table th{
        padding: 0;
    }
    .homapage-table{
        /*background: #202624;opacity:0.8;*/
        background-color: #211F18;margin-bottom: 0rem;
    }
    .homapage-table a{
        color: #ebb16c;
    }
    .homapage-table tbody tr {
        border-bottom: 1px solid #000;
    }
    .homapage-table tbody tr:hover{
        /*background-color: rgba(0,0,0,1);*/
        background-color: #494535;
        color: #CCCCCC;
    }
    .homapage-table .twitch-cell{
        width:240px;
        height: 100px;
        padding: 0px;
        font-size: 0.75rem;
    }
    .homapage-table .class-cell{
        width:90px;
        height: 70px;
        padding: 0px;
        font-size: 0.8rem;
    }
    .class-cell img{
        width: 90px;
    }
    .twitch-cell img{
        width:240px;
    }
    .homapage-table .row{
        width: 40px;
    }
    .homapage-table td,.homapage-table th{
    border-top: 0px solid #eceeef;
    vertical-align: middle;
    }
    .homapage-table thead th{
    border-bottom: 1px solid #ebb16c;
    }

    .table {
        color: #fff;
    }
    .table th , td{
        text-align: center;
        line-height: 1;
    }
    /* .ladder-table    { overflow-y: auto;  }
    .ladder-table th { position: sticky; top: 0; } */
    td {
        overflow: hidden;
        text-overflow: ellipsis;
    }
    tr.active{background-color:rgb(25, 10, 9)!important;border-left: 10px solid #ebb16c!important;;}
    tr.dead{background-color: #14130E;color: #626262!important;}


    /* progres bar css */
    .html5-progress-bar {
    	text-align: center;
        margin: 0;
    }

    .html5-progress-bar progress {
    	background-color: #f3f3f3;
        width: 75px;
    	border: 0;
    	height: 7px;
    	border-radius: 12px;
        margin: 0;
    }
    .html5-progress-bar progress::-webkit-progress-bar {
    	background-color: #f3f3f3;
    	border-radius: 9px;
    }

    .html5-progress-bar progress::-webkit-progress-value {
    	background: #3f3d3d;
    	background: -moz-linear-gradient(top,  #bdbdbd 0%, #3f3d3d 100%);
    	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#cdeb8e), color-stop(100%,#3f3d3d));
    	background: -webkit-linear-gradient(top,  #bdbdbd 0%,#3f3d3d 100%);
    	background: -o-linear-gradient(top,  #bdbdbd 0%,#3f3d3d 100%);
    	background: -ms-linear-gradient(top,  #bdbdbd 0%,#3f3d3d 100%);
    	background: linear-gradient(to bottom,  #bdbdbd 0%,#3f3d3d 100%);
    	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bdbdbd', endColorstr='#3f3d3d',GradientType=0 );
    	border-radius: 9px;
    }
    .html5-progress-bar progress::-moz-progress-bar {
    	background: #cdeb8e;
    	background: -moz-linear-gradient(top,  #bdbdbd 0%, #3f3d3d 100%);
    	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#bdbdbd), color-stop(100%,#3f3d3d));
    	background: -webkit-linear-gradient(top,  #bdbdbd 0%,#3f3d3d 100%);
    	background: -o-linear-gradient(top,  #bdbdbd 0%,#3f3d3d 100%);
    	background: -ms-linear-gradient(top,  #bdbdbd 0%,#3f3d3d 100%);
    	background: linear-gradient(to bottom,  #bdbdbd 0%,#3f3d3d 100%);
    	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bdbdbd', endColorstr='#3f3d3d',GradientType=0 );
    	border-radius: 9px;
    }

    /* compact view css  */
    .compact .table td{
        padding: 0;
    }
    .table th{padding-bottom: 10px;padding-top: 10px;}
    .compact .home-list-skills,
    .compact .class-cell{
        padding: 0;
        height: 40px!important;
        overflow: hidden;
        text-align:center;
    }
    .compact .class-cell .card-text{
        position: absolute;
        bottom: 0;
    }
    .compact .skill-cell {
        width: 120px;
        padding: 0rem !important;
    }
    .compact .class-cell .card{width: 60px!important;margin: 0px;}
    .compact .home-list-skills li{
        height: 35px!important;
        width: 35px!important;
        margin: 1px;
    }
    .compact .home-list-skills{
        max-width: 146px!important;
    }
    .compact .home-list-skills .card-text{display: none;}
    .compact .skill-card img ,
    .compact .class-cell img{height: 35px!important;width: 35px;}

    .compact .twitch-cell img {
        height: 35px!important;
        width: 55px!important;
    }
    .compact .twitch-cell{
        height: 35px!important;
        width: 100px!important;
    }

    .twitch-card{
    border: 0px;
    margin-bottom: 0rem;
    background-color: #000;
    }

    .twitch-card:hover,
    .skill-card:hover{
    box-shadow: 0 2px 2px 0 #ebb16c, 0 1px 5px 0 #ebb16c, 0 3px 1px -2px #ebb16c;
    }
    .twitch-card .card-text{
        color: #FFFFFF;
    }
    .twitch-card .caption-overlay ,
    .skill-card .caption-overlay {
        position: absolute;
        right: 0;
        top: ;
        bottom: 0;
        left: 0;
        z-index: 2;
        padding: 4px;
    }
    .card-backdrop {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
        /*border-radius: .35rem;*/
        background: transparent linear-gradient(to bottom, transparent 10%, rgba(0, 0, 0, 0.35) 50%, rgba(0, 0, 0, 1) 100%) repeat scroll 0% 0%;
        /*background: transparent linear-gradient(to bottom, transparent 30%, rgba(0, 0, 0, 0.35) 56%, #002d5b 100%) repeat scroll 0% 0%;*/
    }
    .twitch-card .card {
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .twitch-card:hover .hover .play {
        top: 50%;
        position: absolute;
        left: 0;
        right: 0;
        transform: translateY(-50%);
    }
    .twitch-card:hover .hover {
        opacity: 0.7; /* added */
        top: 0; /* added */
        z-index: 100;
        background: black;
    }
    .twitch-card .hover{
        display: block;
        position: absolute;
        right: -100%;
        opacity: 0;
        left: 0;
        bottom: 0;
        right: 0;
        text-align: center;
        color: inherit;
    }

    .home-list-skills{
        width: 210px;
        padding: 0;
        height: 66px;
        overflow: hidden;
    }
    .home-list-skills li{
        width: 65px;
        list-style: none;
        display: inline-block;
        height: 65px;
        margin: 2px;
        font-size: 0.8rem;
    }
    .skill-cell ul{
        margin: 0;
    }
    .skill-card{
        /*filter: brightness(50%);*/
        border: 0px;
        margin-bottom: 0px;
    }
    .skill-card .card-text{
        color: #ebb16c;
        line-height: 0.9;
        text-align: center;
    }
    .skill-card img{
        width: 65px;
        /*border-radius: .35rem;*/
    }

    .skill-cell{
        width:280px;
        padding: 0rem!important;
    }

    .stats-xph{color: lightgreen;}
</style>
