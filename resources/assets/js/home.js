
require('./bootstrap');

Vue.component('loader', require('./components/Loader.vue'));
Vue.component('list-characters', require('./components/home/ListCharacters.vue'));
Vue.component('drop-down', require('./components/home/DropDown.vue'));

import { SkillsHelper } from './helpers/SkillsHelper.js';
var favStore = require('./helpers/FavStore.js');
var localStore = require('./helpers/LocalStore.js');


const app = new Vue({
    el: '#app',
    data: {
        accName: '',
        characters: '',
        progress: 0,
        classFilter: '',
        skillFilter: '',
        leagueFilter: '',
        searchFilter: '',
        favStore: favStore,
        skillImages: '',
        favStore: favStore,
        localStore: localStore,
        favAccChars: [],
        twitchAccChars: [],
        ladderPaginate: [],
        selectedTab: '',
        isLoading:false,
        selectedLeague:'',
        poe_leagues:[],
        listCharsError:'',
        listPages: 10,
        pages: 0,
    },

    watch : {
        ladderPaginate: function(val){
            var paginateStart = 0;
            var paginateEnd = 0;

            if (val.current_page - 3 < 1) {
                paginateStart = 1;
            } else {
                paginateStart = val.current_page -3;
            }

            if (val.current_page + 3 < val.last_page ) {
                paginateEnd = val.current_page + 3;
            } else {
                paginateEnd =val.last_page;
            }
            
            function range(start, end) {
              return Array(end - start + 1).fill().map((_, idx) => start + idx)
            }
            this.pages = range(paginateStart, paginateEnd);
        },
    },


    computed: {

        filters: function(){
            var filter = this.leagueFilter + this.skillFilter + this.classFilter + this.searchFilter;
            if (filter.charAt(filter.length - 1) === '&') {
                filter = filter.substring(0, filter.length - 1);
            }
            return filter;
        },
        /**/
        listChars: function(){
            // console.log(this.twitchAccChars);
            // console.log(this.selectedTab);
            switch (this.selectedTab) {
                case 'favorites':
                    return this.favAccChars;
                case 'twitch':
                    return this.twitchAccChars;
                case 'ladder':
                    return this.ladderPaginate.data;
                default:
                return [];
            }
        }
    },

    mounted: function () {

        this.poe_leagues=window.PHP.poe_leagues.split(",");
        //load data if ladder URL

        this.selectedLeague=this.poe_leagues[0];
        if(this.isLeagueDropDown(this.selectedLeague)){
            this.selectedLeague=this.selectedLeague.split("::")[1].split("|")[0];
        }

        var urlArr = window.location.href.replace('#', '').split('/');
        if (urlArr[urlArr.length - 1] === 'ladders') {
            this.filterLeague(this.selectedLeague);
        }
        if (urlArr[urlArr.length - 1] === 'favorites') {
            this.getFavs();
        }
        this.getTwitch();

    },

    methods: {

        // getArchive: function() {
        //     var filter = 'archiveFilter=' + window.PHP.account
        //     axios.get('api/ladderData?' + filter).then((response) => {
        //         this.archive = response.data;
        //     });
        // },

        filterListCharacters (filter){
            if (filter === null) {
                this.skillFilter = '';
                this.classFilter = '';
            } else {
                if (filter.hasOwnProperty('skill')) {
                this.skillFilter = (filter.skill == 'All') ? '' : 'skillFilter='+filter.skill+'&';
                }

                if (filter.hasOwnProperty('class')) {
                    this.classFilter = (filter.class == 'All') ? '' : 'classFilter='+filter.class+'&';
                }

                // if (filter.hasOwnProperty('search')) {
                //     this.skillFilter = (filter.search == '') ? '' : 'searchFilter=' + filter.search + '&';
                //     // this.leagueFilter = ''
                // }
            }

            this.isLoading=true;
            this.selectedTab='ladder';
            axios.get('api/ladderData?' + this.filters).then((response) => {
                this.ladderPaginate = response.data;
                this.isLoading=false;
                this.selectedTab='ladder';
            });
        },

        goToAcc: function(acc) {
            window.location = "/profile/"+acc;
        },

        getFavs: function(){
            var vm = this;
            if(favStore.favAcc.length==0){
                vm.selectedTab='favorites';
                vm.listCharsError='No Favorites'
                return;
            }
            this.isLoading=true;
            vm.selectedTab='favorites';
            if(vm.favAccChars.length>0){
                vm.selectedTab='favorites';
                return;
            }

            // console.log(favStore.favAcc.join());
            axios.get('api/favorites/chars?accs='+favStore.favAcc.join()).then((response) => {
                vm.favAccChars = response.data;
                vm.selectedTab='favorites';
                vm.isLoading=false;

            });
        },

        getTwitch: function(){
            this.isLoading=true;
            axios.get('api/twitch').then((response) => {
                this.twitchAccChars = response.data;
                // this.listChars=response.data;
                this.isLoading=false;
                this.selectedTab='twitch';
                Vue.nextTick(function () {
                    $('.show-tooltip').tooltip();
                });
            });
        },

        getLadder:function () {
            this.filterLeague(this.selectedLeague);
        },

        filterLeague: function (filterLeague) {
            this.selectedLeague=filterLeague;
            this.leagueFilter = 'leagueFilter='+filterLeague+'&';
            this.filterListCharacters(null);
        },

        changePage: function(pageNum){
            var vm = this;

            if (pageNum <= 0) {
                pageNum = vm.ladderPaginate.last_page;
            }
            if (pageNum > vm.ladderPaginate.last_page) {
                pageNum = 1;
            }
            
            axios.get('api/ladderData?'+vm.filters+'&page='+ pageNum).then((response) => {
                vm.ladderPaginate = response.data;
            });
        },

        withEllipsis: function(text,after){
            if(text.length<=after){
                return text;
            }
            return text.substring(0, after)+".."
        },

        removeFavAcc: function (name) {
            this.favStore.removeAcc(name);
        },

        //suport for leaguesDropDown for small multi day events
        leaguesDropDown: function(l){
            return l.split("::")[1].split("|");
        },

        isLeagueDropDown: function(l){
            if(l.split("::").length>1)
                return true;
            else
                return false;
        },

        isLeagueDropDownSelected: function(l){
            if(!this.isLeagueDropDown(l)){
                return false;
            }
            var tempLNames=l.split("::")[1].split("|");
            var sel=false;
            for (var i = 0; i <= tempLNames.length; i++) {
                if(tempLNames[i]==this.selectedLeague){
                    sel=true;
                }
            }
            return sel;
        },
    }
});
