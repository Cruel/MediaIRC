function loadHistoryJS(){
	var $content = $('#content'),
	    $body = $(document.body),
	    rootUrl = History.getRootUrl();
	
	// Internal Helper
	$.expr[':'].internal = function(obj, index, meta, stack){
		var $this = $(obj),
		    url = $this.attr('href')||'',
		    isInternalLink;
		// Check link
		isInternalLink = url.substring(0,rootUrl.length) === rootUrl || url.indexOf(':') === -1;
		// Ignore or Keep
		return isInternalLink;
	};
	
	// Ajaxify Helper
	$.fn.ajaxify = function(){
		// Prepare
		var $this = $(this);
		// Ajaxify
		$this.find('a:internal:not(.no-ajaxy)').click(function(event){
			var $this = $(this),
			    url = $this.attr('href'),
			    title = $this.attr('title')||null;
			// Continue as normal for cmd clicks etc
			if ( event.which == 2 || event.metaKey ) { return true; }
			// Change ball gravity (random)
			gravity = { x: Math.floor(Math.random()*3)-1, y: Math.floor(Math.random()*3)-1};
			// Ajaxify this link
			History.pushState(null,title,url);
			event.preventDefault();
			return false;
		});
		return $this;
	};
	
	$body.ajaxify();
	
	// Hook into State Changes
	$(window).bind('statechange',function(){
		// Prepare Variables
		var State = History.getState(),
		    url = State.url;
		
		// Set Loading
		$body.addClass('loading');
		
		// Start Fade Out
		// Animating to opacity to 0 still keeps the element's height intact
		// Which prevents that annoying pop bang issue when loading in new content
		$content.css('opacity',1).animate({opacity:0},500);
		
		// Ajax Request the Traditional Page
		$.ajax({
			url: url,
			success: function(data, textStatus, jqXHR){
				var $dataContent = $(data).find('#content');
				
				// Update the menu
				setNavActive();
				// Update the content
				$content.stop(true,false);
				$content.html($dataContent.html()).css('opacity',0).ajaxify().animate({opacity:1},500); /* you could fade in here if you'd like */
				
				// Update the title
				document.title = $(data).filter('title').text();
				try {
					document.getElementsByTagName('title')[0].innerHTML = document.title.replace('<','&lt;').replace('>','&gt;').replace(' & ',' &amp; ');
				}
				catch ( Exception ) { }
				
				// Complete the change
				$body.removeClass('loading');
			},
			error: function(jqXHR, textStatus, errorThrown){
				document.location.href = url;
				return false;
			}
		}); // end ajax
		
	}); // end onStateChange
}

//Make appropriate nav links active
function setNavActive(){
	$('.nav > li').removeClass('active');
	$(".nav a[href='"+document.location.pathname+"']").parent().addClass('active');
	$(".nav a[href='"+document.location.href+"']").parent().addClass('active');
}

$(function(){
	setNavActive();
	// Init tooltips
	$("*[data-toggle='tooltip']").tooltip();
	
	loadHistoryJS();
	
	$(document).on("click", ".testy", function(){
		alert('test');
	});
	$(document).on("click", "#gravup", function(){
//		world.gravity.set(0,-gravity,0);
		gravity = { x: 0, y: -1 };
	});
	$(document).on("click", "#gravdown", function(){
//		world.gravity.set(0,gravity,0);
		gravity = { x: 0, y: 1 };
	});
	$(document).on("click", "#gravleft", function(){
//		world.gravity.set(-gravity,0,0);
		gravity = { x: -1, y: 0 };
	});
	$(document).on("click", "#gravright", function(){
//		world.gravity.set(gravity,0,0);
		gravity = { x: 1, y: 0 };
	});
});
