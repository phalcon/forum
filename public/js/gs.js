
$(window).on("load", function(){

	if (document.location.hostname != 'localhost'){
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount',    'UA-29332509-1']);
		_gaq.push(['_setDomainName', 'phalconphp.com']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	}

  	(function() {
	    var cx = '009733439235723428699:lh9ltjgvdz8';
	    var gcse = document.createElement('script');
	    gcse.type = 'text/javascript';
	    gcse.async = true;
	    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
	        '//www.google.com/cse/cse.js?cx=' + cx;
	    var s = document.getElementsByTagName('script')[0];
	    s.parentNode.insertBefore(gcse, s);
  	})();
});
