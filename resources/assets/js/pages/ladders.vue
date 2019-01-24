<template lang="html">
    <div id="ladders" class="ladder-bg">
        <div class="row filters pb-1 ">
            <div class="col-sm-1">
                <strong>Filter by</strong>
            </div>
            <drop-down class="col-sm-2" v-on:selected="trigerFilterClass" :list="classes">
                <span v-if="filterParms.hasOwnProperty('class')">{{filterParms.class}}</span>
                <span v-else>Class</span>
            </drop-down>
            <drop-down class="col-sm-2" v-on:selected="trigerFilterSkills" :list="skills">
                <span v-if="filterParms.hasOwnProperty('skill')">{{filterParms.skill}}</span>
                <span v-else>Skills</span>
            </drop-down>
            <div class="col-sm-4">
                <div class="input-group">
                    <input type="text" name="account" v-model="filterParms.search"
                    placeholder="Search for Character or Account name" class="form-control"
                    v-on:keyup.enter="filterListCharacters">
                    <span class="input-group-btn">
                        <button type="submit" class="btn poe-btn btn-secondary"
                            @click.prevent="filterListCharacters">Search</button>
                    </span>
                </div>
            </div>
            <div class="col-sm-2">
                <drop-down v-on:selected="trigerSort" :list="sortBy">
                    <span>Sort by</span>
                    <span v-if="filterParms.hasOwnProperty('sort')">{{filterParms.sort}}</span>
                    <span v-else>Rank</span>
                </drop-down>
            </div>
            <div class="col-sm-1">
                <button type="submit" class="btn btn-outline-warning"
                    @click.prevent="clearFilters">Clear</button>
            </div>
        </div>

        <list-characters @filter-skill="trigerFilterSkills" @filter-class="trigerFilterClass"
            :select="filterParms.rank" :delve="true" :compact="true" :load-profile="true"
            :char-data="(ladderPaginate.data !== 'Undefined') ? ladderPaginate.data : []" >
        </list-characters>

        <loader :loading="isLoading" style="margin-left:auto;margin-right:auto;width:150px;" ></loader>
        <nav class="text-sm-center">
            <ul class="pagination" >
                <li class="page-item"><a class="page-link poe-btn" href="#"
                    @click.prevent="changePage(1)">First</a></li>
                <li class="page-item"><a class="page-link poe-btn" href="#"
                    @click.prevent="changePage(ladderPaginate.current_page -1)"> < </a></li>
                <li class="page-item" v-for="n in pages">
                    <a class="page-link poe-btn" href="#" @click.prevent="changePage(n)"
                    :class="(ladderPaginate.current_page === n) ? 'active' : ''">{{n}}</a></li>
                <li class="page-item"><a class="page-link poe-btn" href="#"
                    @click.prevent="changePage(ladderPaginate.current_page+1)"> > </a></li>
                <li class="page-item"><a class="page-link poe-btn " href="#" @blur.prevent=""
                    @click.prevent="changePage(ladderPaginate.last_page)">Last</a></li>
            </ul>
        </nav>

    </div>
</template>

<script>
import Loader from '../components/Loader.vue';
import ListCharacters from '../components/ListCharacters.vue';
import {poeHelpers} from '../helpers/poeHelpers.js';
var skillsData = require('../helpers/SkillsData.js');

export default {
    components: {Loader, ListCharacters},
    props: {
        league: {
            type: Object,
            required: true,
        },
    },
    data: function(){
        return{
            filterParms:{},
            skills: '',
            ladderPaginate: [],
            sortBy: ['Rank', 'Team-Depth', 'Solo-Depth'],
            isLoading: false,
            listPages: 10,
            pages: 0,
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

    watch: {
        ladderPaginate: function (val) {
            var paginateStart = 0;
            var paginateEnd = 0;

            if (val.current_page - 3 < 1) {
                paginateStart = 1;
            } else {
                paginateStart = val.current_page - 3;
            }

            if (val.current_page + 3 < val.last_page) {
                paginateEnd = val.current_page + 3;
            } else {
                paginateEnd = val.last_page;
            }

            function range(start, end) {
                return Array(end - start + 1).fill().map((_, idx) => start + idx)
            }
            this.pages = range(paginateStart, paginateEnd);
        },
    },

    computed: {},

    created: function(){
        this.skills = Object.keys(skillsData);
        this.skills.unshift('All');
        var searchParams = new URL(window.location).searchParams;
        for(var pair of searchParams.entries()) {
           this.filterParms[pair[0]] = pair[1];
        }
        if(this.filterParms.hasOwnProperty('rank')){
            this.filterParms.rank=parseInt(this.filterParms.rank);
            this.filterParms.page = Math.ceil(this.filterParms.rank / 30);
        }
    },

    mounted: function () {
        this.filterListCharacters();
    },

    methods: {
        filterListCharacters() {
            this.isLoading = true;
            this.ladderPaginate = [];
            var base_url = '/api/ladders/'+this.league.name;
            if(!this.league.indexed){
                base_url = '/api/private-ladders/'+this.league.name;
            }

            axios.get(base_url,{ params: this.filterParms }).then((response) => {
                var responsUrl =  new URL(response.request.responseURL);
                var curentUrl = new URL(location);
                var stateUrl = curentUrl.pathname+responsUrl.search;
                window.history.pushState("", "", stateUrl);
                this.ladderPaginate = response.data;
                this.isLoading = false;
            });
        },

        trigerFilterClass: function(c){
            this.filterParms.class = c;
            if(c=='All'){
                delete this.filterParms.class
            }
            this.filterListCharacters();
        },

        trigerFilterSkills: function(s){
            this.filterParms.skill = s;
            if(s=='All'){
                delete this.filterParms.skill
            }
            this.filterListCharacters();
        },

        trigerSort: function(sort){
            this.filterParms.sort = sort;
            if(sort=='Rank'){
                delete this.filterParms.sort
            }
            this.filterListCharacters();
        },

        clearFilters: function(){
            this.filterParms={};
            this.filterListCharacters();
        },

        changePage: function (pageNum) {
            if (pageNum <= 0) {
                pageNum = this.ladderPaginate.last_page;
            }
            if (pageNum > this.ladderPaginate.last_page) {
                pageNum = 1;
            }
            this.filterParms.page = pageNum;
            this.filterListCharacters();
        },

    }

}
</script>

<style lang="css">
.page-link:focus{
    background-color: #494535;
    color: #ebb16c;
}
.page-link.active{ background-color: #494535; color: #ebb16c;}
.page-link{ width: 54px; }
#ladders .pagination{
    margin-left:auto;margin-right:auto;
}
.ladder-bg{
    background-color: #211F18;opacity: 0.85;min-height:800px;
}
</style>
