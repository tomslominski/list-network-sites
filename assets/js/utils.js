/* ============================================================ *\
	List Network Sites by Tom Slominski
	-----------------------------------
	Scripts for general use as utilities/helpers etc.

	https://github.com/tomslominski/wp-list-network-sites/
\* ============================================================ */


	/* ============================================
	  Cookie scripts from QuirksMode
	  http://www.quirksmode.org/js/cookies.html#script
	============================================ */
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}

	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}

	function eraseCookie(name) {
		createCookie(name,"",-1);
	}

	/* ============================================
	  Delay function from StackOverflow
	  http://stackoverflow.com/a/1909508
	============================================ */
	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();
