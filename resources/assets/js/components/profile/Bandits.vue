<template>
<div class=" " :class="" id="">
<a href="#" class="pull-right" @click.prevent="closePopOver">
    <i class="fa fa-times-circle" style="font-size: 1.5em;" aria-hidden="true"></i>
</a>
    <h3 @click.prevent="popoverTest" style="text-align: center;">Deal with the Bandits quest reward:</h3>
    <table class="bandits-table" style="max-width: 100%;" align="center">
    <tbody>

    <tr class="reward" :class="{ active: selectReward.oak }"
        @click.prevent="setReward('oak')">
        <td>
            <a href="#" title="Normal" class="mw-redirect">The Bandit Lord Oak</a>
        </td>
        <td>
            <span class="text-color -mod"> 1% of Life Regenerated per second </span>
        </td>
        <td>
            <span class="text-color -mod">2% additional Physical Damage Reduction </span>
        </td>
        <td>
            <span class="text-color -mod">20% increased Physical Damage</span>
        </td>
    </tr>
    <tr class="reward" :class="{ active: selectReward.kraityn }"
        @click.prevent="setReward('kraityn')">
        <td>
            <a href="#" title="Cruel" class="mw-redirect">The Bandit Lord Kraityn</a>
        </td>
        <td>
             <span class="text-color -mod"> 6% increased Attack and Cast Speed </span>
        </td>
        <td>
             <span class="text-color -mod">3% chance to Dodge Attacks </span>
        </td>
        <td>
             <span class="text-color -mod">6% increased Movement Speed </span>
        </td>
    </tr>
    <tr class="reward" :class="{ active: selectReward.alira }"
        @click.prevent="setReward('alira')">
        <td>
            <a href="#" title="Merciless" class="mw-redirect">The Bandit Lord Alira</a>
        </td>
        <td>
            <span class="text-color -mod">5 Mana Regenerated per second</span>
        </td>
        <td>
            <span class="text-color -mod">+20% to Global Critical Strike Multiplier </span>
        </td>
        <td>
            <span class="text-color -mod">+15% to all Elemental Resistances </span>
        </td>
    </tr>
    </tbody>
    </table>
</div>

</template>

<script type="text/javascript">
var profileStore = require('./../../helpers/profileStore.js');

export default {
    //type life ot mana
    props: {
        show: {
            type: Boolean,
            default: false,
        },
    },
    watch : {
        // show : function (value) {},
    },
    data: function() {
        return {
            profileStore: profileStore,
            account: '',
            char: '',
            loaded: false,
            selectReward: {
                oak: false,
                kraityn: false,
                alira: false,
            }
        }
    },

    created: function(){
        this.account = window.PHP.account;
        this.char=window.PHP.char;
    },

    computed: {
        // 'isEmpty': function(){},
    },
    events: {
        'stats-loaded': function () {
            var key='bandits3/'+this.account+"/"+this.char;
            var tempSelect={};
            if( JSON.parse(localStorage.getItem(key)) ){
                tempSelect = JSON.parse(localStorage.getItem(key));
            }

            for(bandit in tempSelect){
                if(tempSelect[bandit]){
                    this.setReward(bandit)
                    console.log(" bandit:"+bandit+ "true");
                }
            }

         },
    },
    methods: {
        closePopOver: function(){
            Vue.nextTick(function () {
                 $( ".po-bandits-link" ).trigger( "click" );
            })
        },
        saveSelection: function(){
            var key='bandits3/'+this.account+"/"+this.char;
            localStorage.setItem(key, JSON.stringify(this.selectReward));
        },
        reset: function(){
            //console.log(this.selectReward[difficulty]);
            if(this.selectReward["oak"]){
                this.setOakReward(false);
            }else if(this.selectReward["kraityn"]){
                this.setKraitynReward(false)
            }else if(this.selectReward["alira"]){
                this.setAliraReward(false)
            }
        },
        setReward: function(bandit){
            var selected=this.selectReward[bandit]
            // console.log('difficulty:'+difficulty+' bandit:'+bandit+' selected:'+selected);
            if(selected){
                this.reset()
                this.saveSelection();
                return;
            }else{
                this.reset()
            }

            switch(bandit){
                case 'oak':
                    this.setOakReward(true);
                    break;
                case 'kraityn':
                    this.setKraitynReward(true);
                    break;
                case 'alira':
                    this.setAliraReward(true);
                    break;
                default:
            }
            this.saveSelection();
        },
        setOakReward: function(setTo){
            var stats=this.profileStore.getAllStats()

            //if setTo true add reward
            if(setTo){
                // 1% of Life Regenerated per second
                stats.defense[18].total+=1;
                // 20% increased Physical Damage
                stats.offense[3].total+=20;
            }else{
                //else remove reward
                stats.defense[18].total-=1;
                stats.offense[3].total-=20;
            }
            this.selectReward['oak']=setTo;
        },
        setKraitynReward: function(setTo){
            var stats=this.profileStore.getAllStats()

            if(setTo){
                // 6% increased Attack and Cast Speed
                stats.offense[0].total+=6;
                stats.offense[1].total+=6;
                //6% increased Movement Speed
                stats.offense[23].total+=6;
            }else{
                //else remove reward
                stats.offense[0].total-=6;
                stats.offense[1].total-=6;
                stats.offense[23].total-=6;
            }

            this.selectReward['kraityn']=setTo;
        },
        setAliraReward: function(setTo){
            var stats=this.profileStore.getAllStats()

            if(setTo){
                //+15% to all Elemental Resistances
                stats.defense[0].total+=15;
                stats.defense[1].total+=15;
                stats.defense[2].total+=15;
                //+20% to Global Critical Strike Multiplier
                stats.offense[9].total+=20;
                stats.offense[10].total+=20;
            }else{
                //else remove reward
                stats.defense[0].total-=15;
                stats.defense[1].total-=15;
                stats.defense[2].total-=15;
                stats.offense[9].total-=20;
                stats.offense[10].total-=20;
            }

            this.selectReward['alira']=setTo;

        },

    }

};

</script>


<style type="text/css">
.popover{
    background-color: #332F24!important;
    color: white!important;
}
.popover.bs-tether-element-attached-bottom::after,
.popover.popover-top::after{
    border-top-color: #332F24!important;
}
table {
   border-collapse: separate;
   border-spacing: 2px;
}
.popover .popover-title{
    display: none;
}
.bandits-table td{
    padding: 10px;
}
.bandits-table .reward:hover{
    background-color: #494535;
    color: #ebb16c;
}
.reward{
    cursor: pointer; cursor: hand;
}
.reward.active{
    border: solid 1px #ebb16c;
    background-color: #494535;
}

.mw-redirect{
    color: #ebb16c;
}

</style>
