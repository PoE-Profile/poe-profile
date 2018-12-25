<template>
<div>

    <div v-if="showRank && !archive" class="input-group " style="margin-left:auto;margin-right:auto;background:#202624;">

        <input type="text" name="account" v-model="searchBig" class="form-control"
        style="border-color: #CCCCCC;"
        placeholder="Search for Character or Account name"
        v-on:keyup.enter="search()">

        <span class="input-group-btn">
        <button type="submit" class="btn btn-outline-warning" @click.prevent="search()">Search</button>
        </span>
    </div>

    <table class="table table-hover homapage-table" style="color:white">
        <thead>
            <tr>
                <!-- <th>#</th> -->
                <th v-if="showRank">Rank</th>
                <th v-if="!archive">
                    <drop-down  v-if="showRank"  v-on:selected="trigerFilterClass" :list="classes">
                        <span v-if="selectedClass.length>0">{{selectedClass}}</span>
                        <span v-else>Class</span>
                    </drop-down>
                    <span v-else>Class</span>
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
                    <drop-down v-if="showRank" v-on:selected="trigerFilterSkills"
                         style="width:190px; padding: 2px;" :list="skills">
                        <span v-if="selectedSkill.length>0">{{selectedSkill}}</span>
                        <span v-else>Skills</span>
                    </drop-down>
                    <span v-else>Skill</span>
                </th>
                <th v-if="archive||league">
                    <span>League</span>
                </th>
                <th v-if="depth" style="text-align:center;">
                    Solo Depth
                </th>
                <th v-if="depth" style="text-align:center;">
                    Team Depth
                </th>
                <th>Level</th>
                <th v-if="showTwitch">Twitch</th>

            </tr>
        </thead>
        <tbody>
            <tr v-for="char in charData">
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
                    <a :href="ranksUrl(char, true)" >{{char.account.name}}</a>
                </td>
                <td>
                    <span v-if="char.name.length>0">
                    <a :href="ranksUrl(char)">{{char.name}}</a>
                    <span v-if="showRank && !char.public" style="color: gray;font-weight: bold;">(private)</span>
                    <span v-if="char.dead" style="color: red; font-weight: bold;">(dead)</span>
                    </span>
                    <span v-else>No Info</span>
                </td>
                <td class="skill-cell" v-if="!archive">
                    <ul class="home-list-skills">
                        <li v-for="skill in getActiveSkill(char.items_most_sockets)">
                            <a href="#" @click.prevent="trigerFilterSkills(withEllipsis(skill.name,18))">
                            <div class="skill-card card card-inverse">
                                <img v-bind:src="skill.imgUrl">
                                <div class="caption-overlay">
                                    <p class="card-text">
                                        {{withEllipsis(skill.name,18)}}
                                    </p>
                                </div>
                                <div class="card-backdrop"></div>
                            </div>
                            </a>
                        </li>
                    </ul>
                </td>
                <td class="skill-cell" v-if="archive||league">
                    <span>
                        {{char.league}}
                    </span>
                </td>
                <td v-if="depth" style="text-align:center;">
                    {{char.delve_solo}}
                </td>
                <td v-if="depth" style="text-align:center;">
                    {{char.delve_default}}
                </td>
                <td>{{char.level}}</td>
                <td class="twitch-cell" v-if="showTwitch">
                    <a href="#" class="show-tooltip" @click.prevent="openTwitch(char.twitch)"
                    data-placement="top" title="" :data-original-title="char.twitch.status">
                        <div class="card card-inverse twitch-card">
                            <!-- <span class="play">&#9658;</span> -->
                            <div class="hover">
                                <span class="fa fa-play fa-5x play"></span>
                            </div>
                            <img v-bind:src="char.twitch.img_preview">
                            <div class="caption-overlay">
                                <h4 class="card-title"></h4>
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

import { SkillsHelper } from '../../helpers/SkillsHelper.js';
import {poeHelpers} from '../../helpers/poeHelpers.js';
import dropDown from './DropDown.vue';
Vue.component('modal-twitch', require('./ModalTwitch.vue'));

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
        depth:{
            type: Boolean,
            default: false,
        },
        league:{
            type: Boolean,
            default: false,
        }
    },

    components: {
        'dropDown': dropDown,
    },

    watch : {
        charData: function(val){
            if(!this.trigerChangeFromFilter){
                this.clearFilters(false);
            }
            this.noResults = val.length==0;

            this.trigerChangeFromFilter=false;
        },
    },
    data: function(){
        return {
            searchBig: '',
            isModalVisible: false,
            noResults: false,
            // showTwitch: false,
            trigerChangeFromFilter:false,
            skillImages: '',
            selectedSkill: '',
            selectedClass: '',
            skills: '',
            stream: null,
            classes: [
                'All',
                'Slayer',
                'Gladiator',
                'Champion',
                'Assassin',
                'Saboteur',
                'Trickster',
                'Juggernaut',
                'Berserker',
                'Chieftain',
                'Necromancer',
                'Ocultist',
                'Elemntalist',
                'Deadeye',
                'Raider',
                'Pathfinder',
                'Inquisitor',
                'Hierophant',
                'Guardian',
                'Ascendant'
            ]
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
            if(this.charData.length==0 &&(this.selectedClass.length>0 || this.selectedSkill.length>0)){
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
        // noResults: function(){
        //     if(this.charData.length==0)
        //     {
        //         return true;
        //     }else{
        //         return false;
        //     }
        // }
    },

    mounted: function() {
        this.getSkillImages();
    },

    methods: {

        ranksUrl: function(char, acc=false){
            if (acc) {
                return (new poeHelpers).getBaseDomain() + '/profile/' + char.account.name
            }
            return (new poeHelpers).getBaseDomain() + '/profile/' + char.account.name + '/'+ char.name

        },

        search: function() {
            this.$emit('filter-list', {'search': this.searchBig})
        },

        getActiveSkill: function(items){
            // console.log(items);
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
            this.selectedSkill="";
            this.selectedClass="";
            // location.hash="";
            if(reload){
                this.$emit('filter-list', null)
            }
        },

        trigerFilterSkills: function(skill){
            this.selectedSkill = '';
            if (skill !== 'All') {
                this.selectedSkill = skill;
                this.trigerChangeFromFilter=true;
            }
            this.$emit('filter-list', {'skill': skill})
        },

        trigerFilterClass: function(c){
            this.selectedClass = '';
            if (c !== 'All') {
                this.selectedClass = c;
                this.trigerChangeFromFilter=true;
            }
            this.$emit('filter-list', {'class': c})
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

        getSkillImages: function () {
            axios.get('/imgs/skills/skill_images.json').then((response) => {
                this.skillImages = response.data;
                this.skills = Object.keys(response.data);
                this.skills.unshift('All');
            });
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
