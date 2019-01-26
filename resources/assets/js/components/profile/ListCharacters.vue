<template>
<div class="row" v-if="characters.length > 0">
    <div :class="['', showAllChars ? 'more-characters' : 'characters']">

        <div class="panel-heading" style="padding:2px;padding-top:7px;height:50px;overflow:hidden;">

            <div class="form-inline" style="margin-bottom: 0;">
                <input type="text" class="form-control pull-right" style="width:190px;"
                v-model="searchChar" name="" :placeholder="isBuild ? 'Search Build' : 'Search Name or Class'">
                <ul class="nav nav-pills char-nav">
                  <li class="nav-item" v-for="league in leagues">
                      <a href="#" @click.prevent="setLeague(league.name)"
                      :class="['nav-link', league.name===currentLeague ? 'active' : '']">
                      {{league.name}}({{league.count}})
                      </a>
                  </li>
                </ul>
            </div>

        </div>
        <ul class="list-characters" style="margin-bottom: 0;">

            <li>
                <a :href="characterUrl(currentCharacter)">
                  <div :class="['character current', hideCurentChar ? 'hide-current' : '']">
                      <div :class="'icon2 ' + currentCharacter.class" style=""></div>
                      <span :class="'level ' + currentCharacter.league">L{{currentCharacter.level}}</span>
                      <span class="name">{{withEllipsis(currentCharacter.name,17)}}</span>
                  </div>
                </a>
            </li>

            <li v-for="char in computetChars">
                <a :href="characterUrl(char)">
                  <div :class="['character', char.name === currentCharacter.name ? 'hide-current' : '']">
                      <div :class="'icon2 ' + char.class" style=""></div>
                      <span :class="'level ' +char.league">L{{char.level}}</span>
                      <span class="name">{{withEllipsis(char.name,17)}}</span>
                  </div>
                </a>
            </li>

        </ul>


    </div>
    <div v-if="computetChars.length>11" style="background:#190a09;">
        <a href="#" @click.prevent="showAllChars=!showAllChars" class="toggle-show">
            <span v-if="!showAllChars">
                Show More Characters <i class="fa fa-caret-down" aria-hidden="true"></i>
            </span>
            <span v-else>
                Show Less Characters <i class="fa fa-caret-up" aria-hidden="true"></i>
            </span>
        </a>
    </div>
</div>
</template>

<script type="text/javascript">

import {poeHelpers} from '../../helpers/poeHelpers.js';

export default {
    props: {
        characters: {
            type: Array,
            required: true,
            default: [],
        },
        currentCharacter: {
            type: Object,
            required: true,
        },

        isBuild: {
            type: Boolean,
            default: false,
        },
        account: { type:String, required: true},
    },

    data: function() {
        return {
            currentLeague: 'All',
            searchChar: '',
            showAllChars:false,

        }
    },
    created: function () {
        this.currentLeague=this.currentCharacter.league;
    },
    computed: {
        'computetChars': function () {
            // `this` points to the vm instance
            var vm=this;
            var temp=this.characters.filter(function(char){
                if(vm.searchChar.length>0){
                    return JSON.stringify(char).toLowerCase().search(vm.searchChar)>=0;
                }
                if(vm.currentLeague=='All'){
                    return true;
                }
                return char.league===vm.currentLeague;//.inventoryId === self.hoveredItem;
            });
            return temp;
        },
        'leagues': function () {
            var tempArr=['Hardcore','Standard'];
            var resultArr=[
                {name:'Hardcore',count:0},
                {name:'Standard',count:0},
            ];
            if(this.isBuild) {
                return [{name:'Builds', count:this.characters.length}]
            }

            var count_all=0;
            this.characters.forEach(function(char) {
                 if(tempArr.indexOf(char.league)==-1){
                     tempArr.push(char.league);
                     resultArr.push({name:char.league,count:1});
                }else{
                    var index=tempArr.indexOf(char.league);
                    var temp=resultArr[index];
                    temp.count=temp.count+1;
                }
                count_all++;
            });
            resultArr.push({name:'All',count:count_all});
            return resultArr.reverse();
        },
        //check if char is from curent league
        hideCurentChar: function(){
            if(this.searchChar.length>0 &&
                JSON.stringify(this.currentCharacter).toLowerCase().search(this.searchChar)<0){
                return true;
            }
            if(this.currentLeague==='All')
                return false;
            return this.currentLeague != this.currentCharacter.league
        },
    },

    methods: {
        setLeague:function(l){
            this.currentLeague=l;
        },
        characterUrl: function(char){
            if (this.isBuild) {
                return route('build.show', char.buildId);
            }
            return route('profile.char', {acc: this.account, char: char.name});
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
