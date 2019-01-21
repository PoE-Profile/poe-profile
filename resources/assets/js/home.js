
require('./bootstrap');

Vue.component('loader', require('./components/Loader.vue'));
Vue.component('ladders-page', require('./pages/ladders.vue'));
Vue.component('list-characters', require('./components/ListCharacters.vue'));
Vue.component('drop-down', require('./components/DropDown.vue'));

var favStore = require('./helpers/FavStore.js');

Vue.mixin({
    methods: {
        route: route
    }
});

const app = new Vue({
    el: '#app',
    data: {
        favStore: favStore,
        twitchAccChars: [],
        favAccChars: [],
        isLoading:false,
        listCharsError:'',
    },

    watch : {},

    computed: {},

    mounted: function () {
        if (location.pathname === '/favorites') {
            this.getFavs()
        } else {
            this.getTwitch();
        }
    },

    methods: {

        goToAcc: function(acc) {
            window.location = "/profile/"+acc;
        },

        getFavs: function () {
            var vm = this;
            if (favStore.favAcc.length == 0) {
                vm.selectedTab = 'favorites';
                vm.listCharsError = 'No Favorites'
                return;
            }
            this.isLoading = true;
            vm.selectedTab = 'favorites';
            if (vm.favAccChars.length > 0) {
                vm.selectedTab = 'favorites';
                return;
            }

            // console.log(favStore.favAcc.join());
            axios.get('/api/favorites/chars?accs=' + favStore.favAcc.join()).then((response) => {
                vm.favAccChars = response.data;
                vm.selectedTab = 'favorites';
                vm.isLoading = false;

            });
        },

        getTwitch: function(){
            this.isLoading=true;
            axios.get('/api/twitch').then((response) => {
                this.twitchAccChars = response.data;
                // this.listChars=response.data;
                this.isLoading=false;
                this.selectedTab='twitch';
                Vue.nextTick(function () {
                    $('.show-tooltip').tooltip();
                });
            });
        },

    }
});
