module.exports = {
    favAcc: JSON.parse(localStorage.getItem('favAcc')) ? JSON.parse(localStorage.getItem('favAcc')) : [],
    favBuilds: JSON.parse(localStorage.getItem('favBuilds')) ? JSON.parse(localStorage.getItem('favBuilds')) : [],
    favStats: JSON.parse(localStorage.getItem('favStats')) ? JSON.parse(localStorage.getItem('favStats')) : [],
    
    addStat (stat) {
        this.favStats.push(stat)
        this.save('favStats',this.favStats);
    },
    removeStat (name) {
        for(var i = 0; i < this.favStats.length; i++) {
            if(this.favStats[i] === name) {
                this.favStats.splice(i, 1);
            }
        }
        this.save('favStats',this.favStats);
    },
    checkStatIsFav (name){
        for(var i = 0; i < this.favStats.length; i++) {
            if(this.favStats[i] === name) {
                return true;
            }
        }
        return false;
    },

    //acc getar setars
    addAcc (acc) {
        this.favAcc.push(acc);
        this.save('favAcc',this.favAcc);
    },

    removeAcc (name) {
        for(var i = 0; i < this.favAcc.length; i++) {
            if(this.favAcc[i] === name) {
                this.favAcc.splice(i, 1);
            }
        }
        this.save('favAcc',this.favAcc);
    },

    checkAccIsFav (name){
        for(var i = 0; i < this.favAcc.length; i++) {
            if(this.favAcc[i].toLowerCase() === name.toLowerCase()) {
                return true;
            }
        }
        return false;
    },

    //Builds
    addBuild(build) {
        this.favBuilds.push(build);
        this.save('favBuilds', this.favBuilds);
    },

    removeBuild(name) {
        for (var i = 0; i < this.favBuilds.length; i++) {
            if (this.favBuilds[i] === name) {
                this.favBuilds.splice(i, 1);
            }
        }
        this.save('favBuilds', this.favBuilds);
    },

    getMainAcc (){
        match = document.cookie.match(new RegExp('default-acc=([^;]+)'));
        if (match) return match[1];
    },
    isMainAcc (acc){
        var match = document.cookie.match(new RegExp('default-acc=([^;]+)'));
         if (!match) return false;
        if(acc.toLowerCase()==match[1].toLowerCase()){
            return true
        }else {
            return false
        }
    },

    save (key,data){
        localStorage.setItem(key, JSON.stringify(data));
    },
}
