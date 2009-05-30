// language class

function LanguageOperator() {
	LAN_OPERATOR = this;
	this.lang = "tr";
		
	this.load = function(lang) {			
			this.lang = lang
			this.url = location.href.substring(0, location.href.lastIndexOf('/'));		
			document.writeln("<script language='text/javascript' src='"+this.url+"/js/langs/"+this.lang+".js'></script>");
			
	};
	
	this.addIndexes= function() {
		alert("in add indexes");
		for (var n in arguments[0]) { 
			this[n] = arguments[0][n]; 
		}
	};	
}