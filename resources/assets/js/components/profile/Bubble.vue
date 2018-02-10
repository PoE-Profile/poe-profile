

<template>
<div class="bubble " :class="{ blood: isEmpty }" :id="type">
    <span v-if="usedAuras.length>0" class="label " :class="labelPosition">
        <a href="#" class="show-tooltip" style="text-decoration: underline;" data-toggle="tooltip"
            data-placement="top" :title="getTooltip()">
            {{usedAuras.length}} Auras
        </a>
        <br>
        Reserved: <span class="reserved">{{percentReserved}}</span>%<br />
        {{title}}: <span class="total">{{realMana}} / {{total}}</span>
    </span>
    <span v-else class="label -none" :class="labelPosition">
        {{title}}: <span class="total">{{total}}</span>
    </span>
    <div v-bind:style="heightReserved"></div>
</div>

</template>

<script type="text/javascript">

export default {
    //type life ot mana
    props: {
        total: {
            type: Number,
            default: 0,
            // required: true
        },
        reduced: {
            type: Object,
            default: {}
        },
        title: {
            type: String,
            required: true
        },
        labelPosition: {
            type: String,
            default: 'right'
        },
        auras: {
            type: Array,
            default: []
        },
        bloodMagic: {
            type: Boolean,
            default: false
        }
    },
    data: function() {
        return {
            percentReserved:0,
        }
    },

    mounted: function(){
        this.calcReserved();
    },

    created: function(){
    },

    computed: {
        'type': function(){
            if(this.title.toLowerCase()=='life'){
                return 'life';
            }
            return 'mana';
        },
        'isEmpty': function(){
            return this.bloodMagic && this.type=='mana'
        },
        'heightReserved': function(){
            var result=0;
            if(this.percentReserved>0){
                result=200*(this.percentReserved*0.01);
            }

            if(result>=200){
                return {height: '200px'};
            }
            return {height: result+'px'};
        },
        'realMana': function(){
            var reserved=Math.floor(this.total*(this.percentReserved*0.01));
            return this.total-reserved;
        },
        'usedAuras': function(){
            var self = this;
            var temp = this.auras.filter(function(skill){
                //life auras
                if(self.hasBloodMagic(skill) && self.type=='life'){
                    return true;
                }
                //mana auras
                if(!self.hasBloodMagic(skill) && self.type=='mana'){
                    return true;
                }
                return false;
            });

            return temp;
        }
    },

    methods: {
        calcReserved: function(){
            if(this.isEmpty){
                return;
            }
            this.percentReserved=0;
            var self=this;
            this.usedAuras.forEach(function(skill){
                self.setReserved(skill);
            })
            var reserved=Math.floor(this.total*(this.percentReserved*0.01));

            if(reserved>this.total){
                reserved=this.total;
            }

            if(reserved > 0 && this.type == 'mana'){

                this.$emit('set-reserved-mana', reserved);
            }

        },
        setReserved: function(skill) {
            // console.log(skill.name)
            // if skill Curse then is Blasphemy
            var tags=skill.tags.split(', ');
            if(inArray('Curse', tags)){
                var percent=this.getReducedFor(35, skill.reducedManaItem);
                this.percentReserved+=percent;
                return ;
            }

            // console.log(JSON.stringify(skill))
            //manaReserved
            var manaReserved=this.getManaReserved(skill.gem);

            var isFlat = true;
            if (manaReserved.search("%")>=0){
                manaReserved=manaReserved.replace('%','');
                isFlat = false;
            }
            manaReserved=parseInt(manaReserved);
            manaReserved=this.getReservedWithSupprts(manaReserved,skill.supports);
            manaReserved=this.getReducedFor(manaReserved, skill.reducedManaItem);

            //if flat val not percent convert to percent
            if(isFlat){
                manaReserved=Math.floor((manaReserved/this.total)*100);
            }
            if(this.reduced.mortalConviction){
                manaReserved = manaReserved - Math.floor(manaReserved*0.5);
            }

            //manaReserved has to by in percent
            this.percentReserved+=manaReserved;
            //console.log('percentReserved :'+this.percentReserved);
        },
        getReservedWithSupprts: function(reserved,supports){
            self=this;
            var tempMulti=0;
            //check for Blood Magic Support gem
            supports.forEach(function(support){
                if(support.name=="Enlighten Support"||
                    support.name=="Blood Magic Support"||
                    support.name=="Empower Support")
                {
                    tempMulti=self.getManaMultiplier(support.gem);
                    reserved=Math.floor(reserved*(tempMulti*0.01))
                }
            });
            // if(this.bloodMagicQuality > 0){
            //     console.log(reserved);
            //     reserved -= this.bloodMagicQuality;
            //     console.log(reserved);
            //     // console.log(this.bloodMagicQuality);
            // }
            return reserved;
        },
        getManaReserved: function(gem){
            var returnVal='';
            gem.properties.forEach(function(prop){
                if(prop.name=='Mana Reserved'){
                    returnVal=prop.values[0][0];
                }
            })
            return returnVal;
        },
        getManaMultiplier: function(gem){
            var returnVal='';
            gem.properties.forEach(function(prop){
                if(prop.name=='Mana Multiplier'){
                    returnVal=prop.values[0][0];
                }
            });
            return parseInt(returnVal.replace('%',''));
        },
        getReducedFor: function(percent, reducedManaItem){
            var reduced = this.reduced.total + reducedManaItem;
            return percent-(Math.floor(percent*(reduced*0.01)));
        },
        hasBloodMagic: function(skill){
            //if prop bloodMagic set true always return true
            if(this.bloodMagic){
                return true;
            }
            var result=false
            //check for Blood Magic Support gem
            skill.supports.forEach(function(gem){
                if(gem.name=="Blood Magic Support"){
                    result=true;
                }
            });
            return result;
        },
        getTooltip: function(){
            var tooltip = ''
            this.usedAuras.forEach((aura) => {
                tooltip =tooltip.concat(aura.name + ', ');
            });

            return tooltip
        },

    }

};

</script>

<style type="text/css">
.bubble {
   width: 200px;   height: 200px;   background-repeat: no-repeat;   background-position: 0 0;   position: relative;   margin: 10px;   text-align: center;   font-weight: bold;
   }
.bubble div {
      width: 200px;   position: absolute;   z-index: 1;   top: 0px;   height: 0px;
}
.error {
   color: red;
}
#life {
   background-image: url("/imgs/globe_hp.png");   float: left;
}
#life div {
   background-image: url("/imgs/globe_r_hp.png");
}
#mana {
   background-image: url("/imgs/globe_mana.png");   float: right;
}
#mana.blood {
   background-image: url("/imgs/globe_empty.png");
}
#mana div {
   background-image: url("/imgs/globe_r_mana.png");
}
#footer {
   text-align: center;   width: 100%;   font-size: 16px;   line-height: 30px;
}
#footer a i {   font-size: 18px; } #footer a:hover i {   color: #56DCF2; }


.bubble .label{
  position: absolute;
  width: 100%;
  bottom: 0;

  color: white;
}
.bubble .label.right { text-align: right;right: -95px;}
.bubble .label.left { text-align: left;left: -95px;}
.label.-none.right{right: -45px!important;}
.label.-none.left{left: -45px!important;}
</style>
