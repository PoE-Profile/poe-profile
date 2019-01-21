<template>
<div class="ladder-table" :class="{'compact': compact }">
    <table class="table table-hover homapage-table">
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
                    <th v-if="!archive">
                        Skills
                    </th>
                    <th v-if="archive||league">
                        <span>League</span>
                    </th>
                    <th v-if="delve">
                        Depth
                    </th>
                    <th class="text-center">Level</th>
                    <th v-if="showTwitch">Twitch</th>
                </tr>
            </slot>
        </thead>
        <tbody>
            <tr v-for="char in charData" :class="{ 'active': (char.rank==select), 'dead': char.dead , 'online': char.online }">
                <th scope="row" v-if="showRank">{{char.rank}}</th>
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
                    <a :href="ranksUrl(char, true)" >{{char.account.name}}</a>
                </td>
                <td>
                    <span v-if="char.name.length>0">
                        <a v-if="!char.dead" :href="ranksUrl(char)">{{char.name}}</a>
                        <span v-else>{{char.name}} <i style="color: red;">DEAD</i></span>
                        <span v-if="showRank && !char.public" style="color: gray;font-weight: bold;">(private)</span>
                    </span>
                    <span v-else>No Info</span>
                </td>
                <td class="skill-cell" v-if="!archive">
                    <ul class="home-list-skills">
                        <li v-for="skill in getActiveSkill(char.items_most_sockets)"
                            class="show-tooltip" data-toggle="tooltip" data-placement="top" :title="skill.name">
                            <a href="#" @click.prevent="trigerFilterSkills(withEllipsis(skill.name,18))">
                            <div class="skill-card card card-inverse">
                                <img v-bind:src="skill.imgUrl">
                                <div class="card-backdrop"></div>
                            </div>
                            </a>
                        </li>
                    </ul>
                </td>
                <td class="skill-cell" v-if="archive||league">
                    {{char.league}}
                </td>
                <td v-if="delve">
                    <span v-if="char.delve_default!=char.delve_solo&&char.delve_solo>0">
                        Team: {{char.delve_default}}
                        <br>Solo: {{char.delve_solo}}
                    </span>
                    <span v-else>{{char.delve_default}}</span>
                </td>
                <td>
                    <b>{{char.level}}</b>
                    <div class="html5-progress-bar">
                        <progress v-if="!league&&char.levelProgress<100"  max="100"
                        :value="char.levelProgress"></progress>
                    </div>
                </td>
                <td class="twitch-cell" v-if="showTwitch">
                    <a href="#" class="show-tooltip" @click.prevent="openTwitch(char.twitch)"
                    data-placement="top" title="" :data-original-title="char.twitch.status">
                        <div class="card card-inverse twitch-card">
                            <div class="hover">
                                <span class="fa fa-play fa-5x play"></span>
                            </div>
                            <img v-bind:src="char.twitch.img_preview">
                            <div class="caption-overlay">
                                <p class="card-text">
                                    {{char.twitch.name}}<span class="pull-right">
                                        {{char.twitch.viewers}} viewers</span>
                                </p>
                            </div>
                            <div class="card-backdrop"></div>
                        </div>
                    </a>
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
    <div style="background:#000;">
        <modal-twitch :stream="stream" v-show="isModalVisible" @close="closeModal" ></modal-twitch>
    </div>
</div>
</template>

<script type="text/javascript">

import { SkillsHelper } from '../helpers/SkillsHelper.js';
import {poeHelpers} from '../helpers/poeHelpers.js';
Vue.component('modal-twitch', require('./ModalTwitch.vue'));
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
            isModalVisible: false,
            noResults: false,
            skillImages: '',
            stream: null,
        }
    },

    computed: {
        showTwitch: function(){
            // return false;
            if (this.charData.length <= 0) {
                return false;
            }
            var char = this.charData[0];
            if('twitch' in char){
                return true;
            }
        },

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
                return (new poeHelpers).getBaseDomain() + '/profile/' + char.account.name
            }
            return (new poeHelpers).getBaseDomain() + '/profile/' + char.account.name + '/'+ char.name
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

        clearFilters: function(reload){
            this.trigerFilterSkills('All')
            this.trigerFilterClass('All')
        },

        trigerFilterSkills: function(s){
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

        withEllipsis: function(text,after){
            if(text.length<=after){
                return text;
            }
            return text.substring(0, after)+".."
        },

        openTwitch: function(stream){
            this.stream = stream;
            this.isModalVisible=true;
        },
        closeModal: function() {
            this.stream = null;
            this.isModalVisible = false;
        }
    },
};
</script>

<style>
.table {
    color: #fff;
}
.table th , td{
    text-align: center;
    line-height: 1;
}
.ladder-table    { overflow-y: auto;  height: 100%; }
.ladder-table th { position: sticky; top: 0; }
td {
    overflow: hidden;
    text-overflow: ellipsis;
}
tr.active{background-color:rgb(25, 10, 9)!important;border-left: 10px solid #ebb16c!important;;}
/* tr.online{border-left: 10px solid green!important;} */
tr.dead{background-color: #14130E;color: #626262!important;}


/* progres bar css */
.html5-progress-bar {
	text-align: center;
    margin: 0;
}

.html5-progress-bar progress {
	background-color: #f3f3f3;
    width: 100px;
	border: 0;
	height: 7px;
	border-radius: 12px;
    margin: 0;
}
.html5-progress-bar progress::-webkit-progress-bar {
	background-color: #f3f3f3;
	border-radius: 9px;
}

/* .online .html5-progress-bar progress::-webkit-progress-value {
	background: #cdeb8e;
	background: -moz-linear-gradient(top,  #cdeb8e 0%, #47682c 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#cdeb8e), color-stop(100%,#47682c));
	background: -webkit-linear-gradient(top,  #cdeb8e 0%,#47682c 100%);
	background: -o-linear-gradient(top,  #cdeb8e 0%,#47682c 100%);
	background: -ms-linear-gradient(top,  #cdeb8e 0%,#47682c 100%);
	background: linear-gradient(to bottom,  #cdeb8e 0%,#47682c 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cdeb8e', endColorstr='#47682c',GradientType=0 );
	border-radius: 9px;
}
.online .html5-progress-bar progress::-moz-progress-bar {
	background: #02a600;
	background: -moz-linear-gradient(top,  #cdeb8e 0%, #02a600 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#cdeb8e), color-stop(100%,#02a600));
	background: -webkit-linear-gradient(top,  #cdeb8e 0%,#02a600 100%);
	background: -o-linear-gradient(top,  #cdeb8e 0%,#02a600 100%);
	background: -ms-linear-gradient(top,  #cdeb8e 0%,#02a600 100%);
	background: linear-gradient(to bottom,  #cdeb8e 0%,#02a600 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cdeb8e', endColorstr='#02a600',GradientType=0 );
	border-radius: 9px;
} */

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
.compact .table td, .table th{
    padding: 0;
}
.compact .home-list-skills,
.compact .class-cell{
    padding: 0;
    height: 40px!important;
    overflow: hidden;
}
.compact .home-list-skills li,
.compact .class-cell .card{
    height: 35px!important;
    width: 35px!important;
    margin: 2px;
    font-size: 0.8rem;
}
.compact .home-list-skills .card-text{display: none;}
.compact .skill-card img ,
.compact .class-cell img{height: 35px!important;width: 35px;}

</style>
