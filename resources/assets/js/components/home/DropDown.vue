<style media="screen">

</style>
<template>
<div class="dropdown">
    <a class="dropdown-toggle" href="#" :class="[lclass]" data-toggle="dropdown" aria-haspopup="true"
    aria-expanded="false" style="padding: 5px 0.3rem;margin-left:0px;"
    id="dropdownMenuButton"
    @click.prevent="toggleDropdown()"
    >
        <slot></slot>

    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
        style="max-height:400px;overflow:auto;padding:0px;width: 100%;"
        v-bind:class="{'open':openSuggestion}"
        v-if="showDropdown"
    >
        <div class="my-sticky-top" style="background-color: #373A3C;" v-if="search">
            <input type="text" v-model="selection" id="dropdown-search1"
                style="margin:0px;width:100%" placeholder="Search"
                @keydown.enter = 'enter'
                @keydown.down = 'down'
                @keydown.up = 'up'
                @input = 'change'
            />

        </div>
        <a class="dropdown-item" href="#" v-for="(suggestion, index) in matches"
            v-bind:class="{'active': isActive(index)}"
            @click="suggestionClick(index)"
        >
            {{ withEllipsis(suggestion,22) }}
        </a>
    </div>
</div>
</template>

<script type="text/javascript">

import { event } from '../../helpers/eventHub.js';

export default {
    props: {
        search: {
            type: Boolean,
            default: true,
        },
        list: {
            type: Array,
            required: true
        },
        lclass: {
            type: String,
            default: 'btn btn-secondary poe-btn',
        },
    },

    data: function(){
        return {
            selection: '',
            open: false,
            current: 0,
            lastSelection: '',
            showDropdown: false,
        }
    },
    watch : {},

    computed: {
        matches() {
            return this.list.filter((str) => {
                return str.toLowerCase().indexOf(this.selection.toLowerCase()) >= 0;
            });
        },
        openSuggestion() {
            return this.selection !== "" &&
                   this.matches.length != 0 &&
                   this.open === true;
        }
    },

    mounted: function() {
    },

    methods: {
        toggleDropdown: function(){
            this.selection = '';
            this.showDropdown = true;
            this.open = true;
            setTimeout(function(){
                $("#dropdown-search1").focus();},1);
        },

        selected: function(data){
            this.showDropdown = false;
            this.$emit('selected', data);
        },

        enter() {
            this.selection = this.matches[this.current];
            this.showDropdown = false,
            this.open = false;
            this.$emit('selected', this.selection)
        },
        up() {
            if(this.current > 0)
                this.current--;
        },
        down() {
            if(this.current < this.matches.length - 1)
                this.current++;
        },
        isActive(index) {
            if(!this.search){
                return false;
            }
            return index === this.current;
        },
        change() {
            if (this.open == false) {
                this.open = true;
                this.current = 0;
            }
        },
        suggestionClick(index) {
            this.selection = this.matches[index];
            this.showDropdown = false;
            this.open = false;
            this.$emit('selected', this.selection)
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
