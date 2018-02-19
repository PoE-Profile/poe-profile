<template>
<div v-if="charData.length > 0">
    <table class="table table-hover homapage-table">
        <thead>
            <tr>
                <!-- <th>#</th> -->
                <th v-if="showRank">Rank</th>
                <th>
                    <drop-down  v-if="showRank"  v-on:selected="trigerFilterClass" :list="classes">
                        <span v-if="selectedClass.length>0">{{selectedClass}}</span>
                        <span v-else>Class</span>
                    </drop-down>
                    <span v-else>Class</span>
                </th>
                <th>Account</th>
                <th>
                    <span v-if="showRank">Character</span>
                    <span v-else>Last Character</span>
                </th>
                <th>
                    <drop-down v-if="showRank" v-on:selected="trigerFilterSkills"
                         style="width:190px; padding: 2px;" :list="skills">
                        <span v-if="selectedSkill.length>0">{{selectedSkill}}</span>
                        <span v-else>Skills</span>
                    </drop-down>
                    <span v-else>Skill</span>
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
                    <span v-if="showRank && !char.public">{{char.account.name}}</span>
                    <a :href="('profile/' + char.account.name + '/')" v-else>{{char.account.name}}</a>
                </td>
                <td>
                    <span v-if="char.name.length>0">
                    <span v-if="showRank && !char.public">{{char.name}}
                        <span style="color: gray;font-weight: bold;">(private)</span></span>
                    <a v-else :href="('profile/' + char.account.name + '/' + char.name)">{{char.name}}</a>
                    <span v-if="char.dead" style="color: red; font-weight: bold;">(dead)</span>
                    </span>
                    <span v-else>No Info</span>
                </td>
                <td class="skill-cell">
                    <ul class="home-list-skills">
                    <li v-for="(skill, index) in getActiveSkill(char.items_most_sockets)">
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

import dropDown from './DropDown.vue';
Vue.component('modal-twitch', require('./ModalTwitch.vue'));

export default {

    props: {
        charData: {
            type: Array,
            required: true,
            default: [],
        },
    },

    components: {
        'dropDown': dropDown,
    },

    watch : {
        charData: function(val){
            if(!this.trigerChangeFromFilter){
                this.clearFilters(false);
            }
            this.trigerChangeFromFilter=false;
        },
    },
    data: function(){
        return {
            isModalVisible: false,
            // showRank: false,
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
        noResults: function(){
            if(this.charData.length==0 &&
                (this.selectedClass.length>0 || this.selectedSkill.length>0))
            {
                return true;
            }else{
                return false;
            }
        }
    },

    mounted: function() {
        this.getSkillImages();
    },

    methods: {

        getActiveSkill: function(items){
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
