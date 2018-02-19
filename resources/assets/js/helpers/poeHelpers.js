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
            t = t || 4;
            var n = this.intToBytes(e, t);
            for (var r = 0; r < t; ++r) this.dataString += String.fromCharCode(n[r])
        }, this.getDataString = function() {
            return this.dataString
        }, this.init()
    }

    return {
        getTreeUrl : function (class_id,a_class_id,nodes){
        	var u = new ByteEncoder();
        	var o=!0;
        	//r classId
        	var r = class_id,//n.activeCharacter.get("classId"),
        	//i ascendancyClass
        	i = a_class_id;//n.activeCharacter.get("ascendancyClass"),
        	//n PoE/PassiveSkillTree/Version
        	var n=4;
        	//tree nod ids
        	var s=nodes;
        	u.appendInt(n), u.appendInt8(r), u.appendInt8(i), u.appendInt8(o ? 1 : 0);
        	for (var a = 0, f = s.length; a < f; ++a) u.appendInt16(s[a]);
        	var l=$.base64.encode( u.getDataString() );
        	l = l.replace(/\+/g, "-").replace(/\//g, "_"), (o ? "/fullscreen-passive-skill-tree/" : "/passive-skill-tree/") + l
        	return l;
        },
    };
};
