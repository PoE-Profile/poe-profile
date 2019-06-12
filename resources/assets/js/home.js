
require('./bootstrap');

Vue.component('loader', require('./components/Loader.vue'));
Vue.component('ladders-page', require('./pages/ladders.vue'));
Vue.component('race-page', require('./pages/race.vue'));
Vue.component('twitch-page', require('./pages/twitch.vue'));
Vue.component('list-characters', require('./components/ListCharacters.vue'));
Vue.component('drop-down', require('./components/DropDown.vue'));
Vue.component('ladder-select', require('./components/LadderSelect.vue'));

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
        favAccChars: [],
        isLoading:false,
        listCharsError:'',
        realm:'pc',
    },

    watch : {},

    computed: {},

    mounted: function () {
        if (location.pathname === '/favorites') {
            this.getFavs()
        }
    },

    methods: {

        goToAcc: function(acc) {
            window.location = "/profile/"+acc;
        },

        getFavs: function () {
            if (favStore.favAcc.length == 0) {
                vm.listCharsError = 'No Favorites'
                return;
            }
            this.isLoading = true;
            axios.get('/api/favorites/chars?accs=' + favStore.favAcc.join()).then((response) => {
                this.favAccChars = response.data;
                this.isLoading = false;
                Vue.nextTick(function () {
                    $('.show-tooltip').tooltip();
                });
            });
        },


    }
});
