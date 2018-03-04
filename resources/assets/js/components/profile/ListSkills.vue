<style type="text/css">

</style>

<template>
<div class="skills">

    <ul class="list-skills">
        <li v-for="(skill, index) in skills">
            <a class="darken" @click.prevent="showSkill(skill, index)">
            <div :class="['skill', currentIndex === index ? 'current' : '']">
                <div class="icon2" style="">
                    <img :src="skill.imgUrl">
                </div>
                <span class="links" v-if="skill.supports.length>0">{{skill.supports.length+1}}</span>
                <span class="name">{{withEllipsis(skill.name,18)}}</span>
            </div>
            </a>

        </li>
    </ul>

    <div class="row">
        <div
            class="timeline timeline-line-dotted"
            style="overflow: auto;color: white !important;padding: 5px;"
            v-if="current"
        >
            <!--  Selected Skill Item  -->
            <div class="timeline-item" v-if='getItem'>
                <div class="timeline-point timeline-point-blank" ref="lastitem">
                </div>
                <div class="timeline-event"
                style="background: #190a09;border: none !important; min-height:225px;">
                    <div style="margin-left: 440px; width: 0px;">
                        <item :item="getItem" :show-info="true"></item>
                    </div>
                </div>
            </div>

            <!--  SELECTED SKILL GEM  -->
            <div class="timeline-item" style="padding-top: 50px;">
                <div class="timeline-point timeline-point-danger" style="padding-top: 50px;z-index: 0;">
                    <img class="gem-icon" ref="lastgem" :src="current.gem.icon">
                </div>
                <div class="timeline-event">
                    <gem-info :gem-info="current.gem"></gem-info>
                </div>
            </div>


            <!--  SUPPORT GEMS  -->
            <div v-for="(suport, index) in current.supports"
                style="padding-bottom: 5px;"
                :class="'timeline-item timeline-suport-' + index"
                >
                <div class="timeline-point timeline-point-danger" style="z-index: 0;">
                    <img class="gem-icon" :src="suport.gem.icon" >
                </div>
                <div class="timeline-event">
                    <gem-info :gem-info="suport.gem"></gem-info>
                </div>
            </div>
        </div>
    </div>

</div>

</template>

<script type="text/javascript">
var profileStore = require('../../helpers/profileStore.js');
import {SkillsHelper} from '../../helpers/SkillsHelper.js';
import Item from './Item.vue';
import GemInfo from './GemInfo.vue';

export default {

    props: {
        items: {
            type: Array,
            required: true,
            default: [],
        },
    },

    components: {
        'item': Item, 'gemInfo': GemInfo,
    },

    data: function() {
        return {
            skillImages: '',
            supportGems: [],
            current:'',
            currentIndex: 0,
            skills:'',
            profileStore: profileStore
        }
    },

    computed: {
        'checkSticked': function() {
            if (this.stat.name !== this.sticked.name) {
                return false
            }
            return true
        },

        'getItem': function() {
            var self = this;
            return this.items.filter(function(item){
                return item.inventoryId === self.current.itemType;
            })[0];
        }
    },

    mounted: function (){
        if(this.items.length>0){
            this.getSkillImages();
        }
    },

    watch: {
        'items': function (val, oldVal) {
            this.getSkillImages();
        }
    },

    methods: {
        skillOnLoad: function(){
            var mostLinksSkill = {links: 0, index: 0};
            this.skills.forEach(function(skill, index){
                if (skill.supports.length > mostLinksSkill.links) {
                    mostLinksSkill.links = skill.supports.length;
                    mostLinksSkill.index = index;
                }

            });
            this.current = this.skills[mostLinksSkill.index];
            this.currentIndex = mostLinksSkill.index;
            if (this.current) {
                this.fixOverlapping();
            }
        },

        fixOverlapping: function(){
            var vm= this;
            this.$nextTick(function() {
                var lastOffset=0;
                this.current.supports.forEach(function(skill, index){
                    var el = vm.$el.querySelector('.timeline-suport-'+index);
                    var curentOffset=el.offsetTop;
                    var test=curentOffset-lastOffset;
                    if(test<=50){
                        el.style.marginTop = (50-test)+"px";
                    }
                    lastOffset=curentOffset;
                });
            });
        },

        showSkill: function(skill, index) {
            this.current = skill;
            this.currentIndex = index;
            // this.fixOverlapping();
        },

        getSkillImages: function () {
            axios.get('/imgs/skills/skill_images.json').then((response) => {
                this.skillImages = response.data;
                var skills = new SkillsHelper(this.skillImages);
                this.items.forEach(function(item){
                    skills.addSkills(item);
                });
                this.skills = skills.result();
                this.profileStore.setGems(this.skills);
                this.skillOnLoad();
            });
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
