require('./bootstrap');

Vue.component('loader', require('./components/Loader.vue'));
Vue.component('list-characters', require('./components/home/ListCharacters.vue'));
Vue.component('drop-down', require('./components/home/DropDown.vue'));

var favStore = require('./helpers/FavStore.js');

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
        skillImages: '',
        favStore: favStore,
        ladderPaginate: [],
        selectedTab: '',
        isLoading: false,
        selectedLeague: '',
        poe_leagues: [],
        listCharsError: '',
        listPages: 10,
        pages: 0,
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

        this.poe_leagues = window.PHP.poe_leagues.split(", ");
        //load data if ladder URL

        this.selectedLeague = this.poe_leagues[0];
        if (this.isLeagueDropDown(this.selectedLeague)) {
            this.selectedLeague = this.selectedLeague.split("::")[1].split("|")[0];
        }

        if (location.pathname === '/ladders') {
            this.watchHashUrl()
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
            this.selectedTab = 'ladder';
            axios.get('/api/ladderData?' + this.filters).then((response) => {
                this.ladderPaginate = response.data;
                this.isLoading = false;
                this.selectedTab = 'ladder';
            });
            if (location.pathname === '/ladders') {
                this.buildHashUrl();
            }
        },

        goToAcc: function (acc) {
            window.location = "/profile/" + acc;
        },

        getLadder: function () {
            this.filterLeague(this.selectedLeague);
        },

        filterLeague: function (filterLeague) {
            this.selectedLeague = filterLeague;
            this.leagueFilter = 'leagueFilter=' + filterLeague + '&';
            this.filterListCharacters(null);
        },

        changePage: function (pageNum) {
            var vm = this;

            if (pageNum <= 0) {
                pageNum = vm.ladderPaginate.last_page;
            }
            if (pageNum > vm.ladderPaginate.last_page) {
                pageNum = 1;
            }

            axios.get('/api/ladderData?' + vm.filters + '&page=' + pageNum).then((response) => {
                vm.ladderPaginate = response.data;
            });
            this.buildHashUrl(true);
        },

        withEllipsis: function (text, after) {
            if (text.length <= after) {
                return text;
            }
            return text.substring(0, after) + ".."
        },

        //suport for leaguesDropDown for small multi day events
        leaguesDropDown: function (l) {
            return l.split("::")[1].split("|");
        },

        isLeagueDropDown: function (l) {
            if (l.split("::").length > 1)
                return true;
            else
                return false;
        },

        isLeagueDropDownSelected: function (l) {
            if (!this.isLeagueDropDown(l)) {
                return false;
            }
            var tempLNames = l.split("::")[1].split("|");
            var sel = false;
            for (var i = 0; i <= tempLNames.length; i++) {
                if (tempLNames[i] == this.selectedLeague) {
                    sel = true;
                }
            }
            return sel;
        },

        watchHashUrl: function () {
            if (location.hash === "") {
                this.filterLeague(this.selectedLeague);
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
            this.selectedTab = 'ladder';
            axios.get('/api/ladderData?' + filters).then((response) => {
                this.ladderPaginate = response.data;
                this.isLoading = false;
                this.selectedTab = 'ladder';
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
});
