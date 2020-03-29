
require('./bootstrap');

Vue.component('loader', require('./components/Loader.vue').default);
Vue.component('ladders-page', require('./pages/ladders.vue').default);
Vue.component('race-page', require('./pages/race.vue').default);
Vue.component('twitch-page', require('./pages/twitch.vue').default);
Vue.component('list-characters', require('./components/ListCharacters.vue').default);
Vue.component('drop-down', require('./components/DropDown.vue').default);
Vue.component('ladder-select', require('./components/LadderSelect.vue').default);
Vue.component('modal-twitch', require('./components/ModalTwitch.vue').default);

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
