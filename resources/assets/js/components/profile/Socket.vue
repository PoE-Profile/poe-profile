<template>
<div>
    <div v-if="linked" :class="'socketLink socketLink' + currentSocket"></div>

    <div v-if="gem !== null" data-toggle="tooltip" data-placement="right" :title="gem.typeLine"
        @mouseover="showGemTooltip(currentSocket)" @mouseleave="hideGemTooltip"
        :class="['show-tooltip socket '+typeClass()+' '+abyssClass(), (currentSocket === 2 || currentSocket === 3) ? 'socketRight' : '']"
    >
    </div>

    <div v-else :class="['socket socket'+socket.attr+' '+abyssClass(), (currentSocket === 2 || currentSocket === 3) ? 'socketRight' : '']">
    </div>
</div>
</template>

<script type="text/javascript">

export default {
    props: {
        socket: Object,
        currentSocket: Number,
        linked:{
            type: Boolean,
            default: false
        },
        gem: Object,
        showGemTooltip: Function,
        hideGemTooltip: Function,
    },

    created: function(){
        this.$nextTick(function () {
            $('.show-tooltip').tooltip();
        })
    },

    methods: {
        abyssClass:function(){
            if(this.socket.sColour=="A"){
                if(this.gem){
                    return "socketAbyss abyssJewel"
                }else{
                    return "socketAbyss"
                }
            }
        },

        typeClass: function(){
            var gemType = (this.gem.typeLine.indexOf('Support') !== -1) ? '-Support' : '-Skill';

            if(this.socket.attr === 'G'){
                //White socket with Gem
                return 'socket'+this.socket.attr+'-full-'+this.gem.colour+gemType;
            }
            //Normal socket with Gem
            return 'socket'+this.socket.attr+'-full'+gemType;
        }
    }
};

</script>
