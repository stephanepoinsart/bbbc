// button behavior to toggle HTML 5 fullscreen of the conference view
function toggleFullScreen() {
	if (!document.fullscreenElement &&    // alternative standard method
			!document.mozFullScreenElement && !document.webkitFullscreenElement) {  // current working methods
		if (document.documentElement.requestFullscreen) {
			document.documentElement.requestFullscreen();
		} else if (document.documentElement.mozRequestFullScreen) {
			document.documentElement.mozRequestFullScreen();
		} else if (document.documentElement.webkitRequestFullscreen) {
			document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
		}
	} else {
		if (document.cancelFullScreen) {
			document.cancelFullScreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.webkitCancelFullScreen) {
			document.webkitCancelFullScreen();
		}
	}
}


function updateping(success, date) {
	var ping = new Date - date;
	$("#numericping").text(ping);
	window.setTimeout(runping,3000);
	$(".pingbar").first().remove();
	
	var height=Math.round(24*ping/40);
	if (height>24)
		height=24;
	
	var color="#FF0000";
	if (!success)
		color="#000000";
	else if (ping<15)
		color="#00EF00";
	else if (ping<19)
		color="#FF7F00";
	$("#pinggraph").append("<span class=\"pingbar\" style=\"height:"+height+"px;background-color:"+color+"\">&nbsp;</span>");
}

function initping() {
	for (var i=0; i<30; i++) {
		$("#pinggraph").append("<span class=\"pingbar\" style=\"\">&nbsp;</span>");
	}
}

//display and update ping
function runping() {
	var date = new Date;

	$.ajax({
		type: "GET",
		url: "http://bbb.utc.fr/demo/ping.jsp",
		cache:false,
		success: function(output){ updateping(true, date); },
		error: function(output){ updateping(false, date); }
	});
}

initping();
window.setTimeout(runping,1000);