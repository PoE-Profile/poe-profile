<style media="screen">

</style>
<template>
<div class="p-1" style=""> 
    <h5>
        <div class="pull-right">
            <ul class="nav nav-pills char-nav">
                <li class="nav-item" v-for="l in leagues">
                    <a :href="route('ladders.show',l)+'?realm='+realm" class="nav-link m-0" :class="{'active':(l==active)}">
                        <span :class="'profile-icon platform-'+realm"></span>{{l}}
                    </a>
                </li>
            </ul>
        </div>

        Ladders Realm
        <drop-down class="d-inline-block" style="width: 90px" v-on:selected="selectRealm" :list="realms" >
            <span>{{realm}}</span>
        </drop-down> 
        :
    </h5>
</div>
</template>

<script type="text/javascript">


export default {
    props: {
        leagues: {
            type: Array,
            required: true
        },
        realm: {
            type: String,
            default: 'pc',
        },
        active:{
            type: String,
            default: ''
        }
    },

    data: function(){
        return {
            realms: ['pc','xbox','sony'],
            open: false,
            current: -1,
            lastSelection: '',
            showDropdown: false,
        }
    },
    watch : {},

    computed: {},

    mounted: function() {
    },

    methods: {
        selectRealm: function(realm){
            if(this.active.length>0){
                var url=route('ladders.show',this.active)+'?realm='+realm;
                console.log(url);
                window.location = url;
            }
            this.realm = realm;
        },
        withEllipsis: function(text,after){
            if(text.length<=after){
                return text;
            }
            return text.substring(0, after)+".."
        },
    }

};
</script>
