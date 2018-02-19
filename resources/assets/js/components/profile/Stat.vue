<style type="text/css">
    .stick {
        background-color: #494535 !important;
        color: #ebb16c !important;
    }
</style>

<template>
<a :class="['list-group-item list-group-item-action flexing', checkSticked ? 'stick' : '']" href="#">
  {{stat.total}} {{stat.name }}
  <i :class="[store.checkStatIsFav(stat.name) ? favIcon.is : favIcon.not]" aria-hidden="true" @click.prevent="makeFavorite"></i>
</a>
</template>

<script type="text/javascript">
var favStore = require('../../helpers/FavStore.js');
export default {

    props: [
        'stat', 'sticked', 'index'
    ],

    data: function() {
        return {
            favIcon: {
                is:'fa fa-star',
                not:'fa fa-star-o'
            },
            store: favStore,
        }
    },

    computed: {
        'checkSticked': function() {
            if (this.stat.name !== this.sticked.name) {
                return false
            }
            return true
        },
    },

    methods: {
        makeFavorite: function() {
            if (this.store.checkStatIsFav(this.stat.name)) {
                this.store.removeStat(this.stat.name);
                return;
            }
            this.store.addStat(this.stat.name);
        },
    }
};

</script>
