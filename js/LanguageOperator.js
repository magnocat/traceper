// language class

function LanguageOperator() {
	this.lang = "tr";
		
	this.load = function(lang) {
		if (lang != "en")
		{
			this.lang = lang
			this.url = location.href.substring(0, location.href.lastIndexOf('/'));		
			document.write("<script language='text/javascript' src='"+this.url+"/js/langs/"+this.lang+".js'></script>");
		}
	};
	
	this.addIndexes= function() {
		for (var n in arguments[0]) { 
			this[n] = arguments[0][n]; 
		}
	};	
}