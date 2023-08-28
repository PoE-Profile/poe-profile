export var poeHelpers  = function() {
    // for generating skill tree url
    var ByteEncoder=function() {
        this.init = function() {
            this.dataString = "", this.position = 0
        }, this.int16ToBytes = function(e) {
            return this.intToBytes(e, 2)
        }, this.intToBytes = function(e, t) {
            t = t || 4, e = parseInt(e);
            var n = [],
                r = t;
            do n[--r] = e & 255, e >>= 8; while (r > 0);
            return n
        }, this.appendInt8 = function(e) {
            this.appendInt(e, 1)
        }, this.appendInt16 = function(e) {
            this.appendInt(e, 2)
        }, this.appendInt = function(e, t) {
            // t = t || 4;
            // var n = this.intToBytes(e, t);
            // for (var r = 0; r < t; ++r) this.dataString += String.fromCharCode(n[r])
            t = t || 4;
            for (var n = this.intToBytes(e, t), i = 0; i < t; ++i)
                this.dataString += String.fromCharCode(n[i])
        }, this.getDataString = function() {
            return this.dataString
        }, this.init()
    }

    return {
        //docs from poe for tree https://www.pathofexile.com/developer/docs/reference#extra
        getTreeUrl : function (class_id,a_class_id,nodes,masteryEffects){
        	var u = new ByteEncoder();
        	var o=!0;
        	//r classId
        	var r = class_id,//n.activeCharacter.get("classId"),
        	//i ascendancyClass
        	i = a_class_id;//n.activeCharacter.get("ascendancyClass"),
        	//n PoE/PassiveSkillTree/Version
        	var n=6;
        	//tree nod ids
        	var s=nodes;
        	u.appendInt(n), u.appendInt8(r), u.appendInt8(i)//, u.appendInt8(o ? 1 : 0);
            
            u.appendInt8(s.length);
        	for (var a = 0, f = s.length; a < f; ++a) u.appendInt16(s[a]);

            //uint8 	count of extended (cluster jewel) node skill hashes (m) // skip
            // if (i.hashesEx) {
            //     u.appendInt8(i.hashesEx.length);
            //     for (r = 0, a = i.hashesEx.length; r < a; ++r)
            //         u.appendInt16(i.hashesEx[r])
            // } else
                u.appendInt8(0);
            //for mastery_effect_pairs -> https://poedb.tw/us/API%3APassive_Skill_Tree    
            u.appendInt8(6);
            Object.entries(masteryEffects).forEach(([k, v], i) => {
                u.appendInt16(v)
                u.appendInt16(k)
            });

        	var l=$.base64.encode( u.getDataString() );
        	l = l.replace(/\+/g, "-").replace(/\//g, "_"), (o ? "/fullscreen-passive-skill-tree/" : "/passive-skill-tree/") + l
        	return l;
        },
        getAtlasUrl : function (nodes){
        	var u = new ByteEncoder();
        	// var o=!0;
        	// //r classId
        	var r = 0,//n.activeCharacter.get("classId"),
        	// //i ascendancyClass
        	i = 0;//n.activeCharacter.get("ascendancyClass"),
        	// //n PoE/PassiveSkillTree/Version
        	var n=4;
        	// //tree nod ids
        	var s=nodes;
        	u.appendInt(n), u.appendInt8(r), u.appendInt8(i);
        	for (var a = 0, f = s.length; a < f; ++a) u.appendInt16(s[a]);
        	var l=$.base64.encode( u.getDataString() );
        	l = l.replace(/\+/g, "-").replace(/\//g, "_"), "/atlas-skill-tree/" + l
        	return l;
        },

    };
};
