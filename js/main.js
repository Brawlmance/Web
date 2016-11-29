function defer(tocheck, method) {
	if (typeof method == "undefined") {
		switch (typeof tocheck) {
			case "string":
				var elm = window;
				var done = true;
				tocheck.split(".").forEach(function(element) {
					if (typeof elm[element] != "undefined") elm = elm[element];
					else {
						done = false;
						return;
					}
				});
				return done;
				break;
			case "object":
				var done = true;
				tocheck.forEach(function(element) {
					if (!defer(element)) done = false;
				});
				return done;
				break;
		}
		return false;
	}
	if (defer(tocheck)) {
		method();
		return;
	}
	setTimeout(function() {
		defer(tocheck, method)
	}, 50);
}
defer(["$"], function() {
	$.getScript("https://ttv-api.s3.amazonaws.com/twitch.min.js", function(){
	  if($('.streams').length>0) {
		  Twitch.init({clientId: 'jnbefsfmq6ms8838022rdxn30duav2u'}, function(error, status) {
			Twitch.api({method: 'streams', params: {game:'Brawlhalla', limit:3, client_id: 'jnbefsfmq6ms8838022rdxn30duav2u'} }, function(error, list) {
				for(key in list.streams) {
					var stream=list.streams[key];
					$('.streams').append('<div><a href="'+stream.channel.url+'"><img src="'+stream.preview.medium+'"/> <span class="name">'+stream.channel.display_name+'</span><span class="viewers"><i class="fa fa-user"></i> '+stream.viewers+'</span></a></div>');
				}
			});
		  });
	  }
	});
	var onhashchangefn=function(e) {
		$('.card').removeClass('hash');
		$(window.location.hash).addClass('hash');
	}
	$(window).on( 'hashchange', onhashchangefn);
	onhashchangefn();
	
  $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top-100
        }, 350);
      }
    }
  });



});