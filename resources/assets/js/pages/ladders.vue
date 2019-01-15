<template lang="html">
    <div class="tab-pane" id="ladders" role="tabpanel" style="background-color: #211F18;opacity: 0.85;min-height:800px;">

        <list-characters v-on:filter-list="filterListCharacters"
            :char-data="(ladderPaginate.data !== 'Undefined') ? ladderPaginate.data : []"
            :delve="true" >
            <template slot="thead">
                <th>Rank</th>
                <th>
                    <drop-down v-on:selected="trigerFilterClass" :list="classes">
                        <span v-if="selectedClass.length>0">{{selectedClass}}</span>
                        <span v-else>Class</span>
                    </drop-down>
                </th>
                <th>Account</th>
                <th>Character</th>
                <th>
                    <drop-down v-on:selected="trigerFilterSkills"
                         style="width:190px; padding: 2px;" :list="skills">
                        <span>Skills</span>
                    </drop-down>
                </th>
                <th><a href="#" v-on:click="">Solo Depth</a></th>
                <th><a href="#">Team Depth</a></th>
                <th class="text-center">Level</th>
            </template>
        </list-characters>
        <loader :loading="isLoading" style="margin-left:auto;margin-right:auto;width:150px;"></loader>
        <div class="prevNext text-xs-center" v-if="ladderPaginate.total > 0">

            <a class="page-link poe-btn" href="#" @click.prevent="changePage(1)">First</a>
            <a class="page-link poe-btn" href="#" @click.prevent="changePage(ladderPaginate.current_page -1)">Previous</a>

            <div style="
                  left: 0;
                  right: 0;
                  margin-left: auto;
                  margin-right: auto;
                  width: 360px; ">
                <span v-for="n in pages" >
                    <a class="page-link poe-btn"  :class="(ladderPaginate.current_page === n) ? 'active' : ''" href="#" @click.prevent="changePage(n)">{{n}}</a>
                </span>
            </div>

            <a class="page-link poe-btn pull-right" href="#" @click.prevent="changePage(ladderPaginate.last_page)">Last</a>
            <a class="page-link poe-btn pull-right" href="#" @click.prevent="changePage(ladderPaginate.current_page+1)">Next</a>

        </div>
    </div>
</template>

<script>
import Loader from '../components/Loader.vue';
import ListCharacters from '../components/ListCharacters.vue';

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
            // accName: '',
            // characters: '',
            classFilter: '',
            skillFilter: '',
            leagueFilter: '',
            searchFilter: '',
            skillImages: '',
            ladderPaginate: [],
            isLoading: false,
            listPages: 10,
            pages: 0,
            selectedClass: '',
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

    computed: {
        filters: function () {
            var filter = this.leagueFilter + this.skillFilter + this.classFilter + this.searchFilter;
            if (filter.charAt(filter.length - 1) === '&') {
                filter = filter.substring(0, filter.length - 1);
            }
            return filter;
        },

    },

    mounted: function () {
        this.leagueFilter = 'leagueFilter=' + this.league.name + '&';
        this.filterListCharacters(null);

        if (location.pathname === '/ladders') {
            // this.watchHashUrl()
        }
    },

    methods: {

        filterListCharacters(filter) {
            if (filter === null) {
                this.skillFilter = '';
                this.classFilter = '';
                this.searchFilter = "";
            } else {

                if (filter.hasOwnProperty('class')) {
                    this.classFilter = (filter.class == 'All') ? '' : 'classFilter=' + filter.class + '&';
                }

                if (filter.hasOwnProperty('skill')) {
                    this.skillFilter = (filter.skill == 'All') ? '' : 'skillFilter=' + filter.skill + '&';
                }

                if (filter.hasOwnProperty('search')) {
                    // this.leagueFilter = ''
                    this.searchFilter = (filter.search == '') ? '' : 'searchFilter=' + filter.search + '&';
                }
            }

            this.isLoading = true;
            this.ladderPaginate = [];
            var base_url = '/api/ladders/'+this.league.name;
            if(!this.league.indexed){
                base_url = '/api/private-ladders/'+this.league.name;
            }
            var url = base_url +'?' + this.filters;

            axios.get(url).then((response) => {
                this.ladderPaginate = response.data;
                this.isLoading = false;
            });
            if (location.pathname === '/ladders') {
                // this.buildHashUrl();
            }
        },

        goToAcc: function (acc) {
            window.location = "/profile/" + acc;
        },

        changePage: function (pageNum) {

            if (pageNum <= 0) {
                pageNum = this.ladderPaginate.last_page;
            }
            if (pageNum > this.ladderPaginate.last_page) {
                pageNum = 1;
            }
            var base_url = '/api/ladders/'+this.league.name;
            if(!this.league.indexed){
                base_url = '/api/private-ladders/'+this.league.name;
            }
            var url = base_url +'?' + this.filters + '&page=' + pageNum;
            axios.get(url).then((response) => {
                this.ladderPaginate = response.data;
            });
            this.buildHashUrl(true);
        },

        withEllipsis: function (text, after) {
            if (text.length <= after) {
                return text;
            }
            return text.substring(0, after) + ".."
        },

        trigerFilterClass: function(c){
            this.classFilter = c;
            this.filterListCharacters({'class': c});
        },

        watchHashUrl: function () {
            if (location.hash === "") {
                return;
            }
            let url = location.hash.split("/");
            let filters = '';
            let page = ''
            url.forEach((el, index) => {
                if (index == 1) {
                    filters += 'leagueFilter=' + el + '&';
                }
                if (index == 2) {
                    page = 'page=' + el;
                }
                if (el.includes("class-")) {
                    filters += 'classFilter=' + el.replace('class-', '').replace('-', ' ') + '&';
                }
                if (el.includes("skill-")) {
                    filters += "skillFilter=" + el.replace("skill-", "").replace('-', ' ') + "&";
                }
                if (el.includes("?search-")) {
                    filters += "searchFilter=" + el.replace("?search-", "") + "&";
                }

            });
            filters += page;
            this.isLoading = true;
            this.ladderPaginate = [];
            axios.get('/api/ladderData?' + filters).then((response) => {
                this.ladderPaginate = response.data;
                this.isLoading = false;
                this.setFilters(url)
            });

        },

        setFilters: function (url) {
            url.forEach((el, index) => {
                if (index == 1) {
                    this.leagueFilter = 'leagueFilter=' + el + '&';
                }

                if (index == 2) {
                    this.changePage(el);
                }

                if (el.includes("class-")) {
                    this.classFilter = 'classFilter=' + el.replace('class-', '').replace('-', ' ') + '&';
                }
                if (el.includes("skill-")) {
                    this.skillFilter = "skillFilter=" + el.replace("skill-", "").replace('-', ' ') + "&";
                }
                if (el.includes("?search-")) {
                    this.searchFilter = "searchFilter=" + el.replace("?search-", "") + "&";
                }
            });
        },

        buildHashUrl: function (page_changed = false) {
            //on Load
            if (location.hash === "") {
                setTimeout(() => {
                    location.hash = '#/' + this.selectedLeague + '/' + this.ladderPaginate.current_page;
                }, 1000);
                return
            }

            // page changed
            if (page_changed) {
                setTimeout(() => {
                    let url = location.hash.split("/");
                    url[2] = this.ladderPaginate.current_page;
                    location.hash = url.join('/');
                }, 400);
                return
            }

            // filter change
            let filterArr = this.filters.split('&');
            let url = '';
            filterArr.forEach(el => {
                if (el.includes("classFilter")) {
                    url += '/class-' + el.replace('classFilter=', '').replace(' ', '-');
                }
                if (el.includes("skillFilter")) {
                    url += '/skill-' + el.replace('skillFilter=', '').replace(' ', '-');
                }
                if (el.includes("searchFilter")) {
                    url += '/?search-' + el.replace('searchFilter=', '');
                }
            });
            setTimeout(() => {
                location.hash = '#/' + this.selectedLeague + '/' + this.ladderPaginate.current_page + url;
            }, 700);

        }
    }

}
</script>

<style lang="css">
</style>
