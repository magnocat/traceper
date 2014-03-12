// language class

function LanguageOperator() {
	LAN_OPERATOR = this;
	this.lang = "en";
		
	this.load = function(lang, url) {
			this.lang = lang ? lang : "en";
			//this.url = url;
			//this.url = location.href.substring(0, location.href.lastIndexOf('/'));
			//this.url = "http://localhost/traceper/branches/DevWebInterface";
			//document.writeln("<script type='text/javascript' src='"+this.url+"/js/langs/"+this.lang+".js'></script>");
			document.writeln("<script type='text/javascript' src='"+url+"/js/langs/"+this.lang+".js'></script>");
	};
	
	this.addIndexes= function() {
		for (var n in arguments[0]) { 
			this[n] = arguments[0][n]; 
		}
	};	
}