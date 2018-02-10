module.exports = {
    skillGems: [],
    allStats: [],

    setAllStats(data) {
        this.allStats = data;
    },

    getAllStats() {
        return this.allStats;
    },

    //get Specific Stats
    getReducedManaStat() {
        return this.allStats.misc[8];
    },

    findAura(aura) {
        var auraLevel;
        var auraValue;
        var tempAura = this.skillGems.filter(function (skill) {
            return skill.name === aura;
        })[0];

        if (typeof tempAura == 'undefined') {
            return false;
        }

        tempAura.gem.properties.forEach(function (prop) {
            if (prop.type === 5) {
                auraLevel = prop.values[0][0];
            }
        });

        tempAura.gem.explicitMods.forEach(function (mod) {
            if (mod.includes('additional Energy Shield')) {
                auraValue = mod.replace(/[^\d.]/g, '');
            }
            if (mod.includes('additional Evasion Rating')) {
                auraValue = mod.replace(/[^\d.]/g, '');
            }
            if (mod.includes('more Armour')) {
                auraValue = mod.replace(/[^\d.]/g, '');
            }
        });

        return { lvl: auraLevel, val: auraValue };
    },

    getAuras() {
        var tempAuras = this.skillGems.filter(function (skill) {
            var tags = skill.tags.split(', ');
            if (inArray('Vaal', tags)) {
                return false;
            }

            return inArray('Aura', tags);
        });
        return tempAuras;
    },

    setGems(gems) {
        this.skillGems = gems;
    },

    save(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    },
}
