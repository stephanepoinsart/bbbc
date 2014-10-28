var maxping=0, minping=999, pingfail=0, pingprogress=0;

function display_flash_version() {
	if (!FlashDetect.installed) {
		$("#flashmessage").html("<span class=\"diagfail\">Echec</span><p class=\"diagdetail\">Attention, le plugin flash ne semble pas installé sur votre ordinateur, il est nécessaire pour la connexion à la webconf.</p>");
	} else if (!FlashDetect.versionAtLeast(11, 2, 202)) {
		$("#flashmessage").html("<span class=\"diagfail\">Echec</span><p class=\"diagdetail\">Votre plugin flash ne semble pas à jour. La webconf risque de ne pas fonctionner correctement ["+FlashDetect.raw+"].</p>");
	} else {
		$("#flashmessage").html("<span class=\"diagok\">OK</span><p class=\"diagdetail\">Votre plugin flash semble compatible ["+FlashDetect.raw+"].</p>");
	}
}

function display_browser_version() {
	var parser = new UAParser();
	var result = parser.getResult();
	var activate = "";
	if (parser.getBrowser().name=="Chrome") {
		activate="<p><span class=\"diagwarn\">Soyez attentif à la procéduire suivante...</span> Si vous voulez partager votre webcam, vous aurez à accepter la demande d'autorisation suivante qui s'affichera de manière très très discrète et mal traduite en version française : <br><img src=\"static/img/chrome-webcam1.png\" alt=\"bandeau d'autorisation d'accès à la webcam\"><br>Si vous Reffusez ou ignorez cette demande, il vous faudra réactiver votre webcam par la suite avec le petit boutton en haut à droite de la fenêtre :<img src=\"static/img/chrome-webcam2.png\" alt=\"bouton de réactivation webcam à la fin de la barre d'adresse\"></p>";
	}
	
	if (parser.getBrowser().name=="Firefox" || parser.getBrowser().name=="Chrome") {
		if (parser.getBrowser().major<30) {
			$("#browsermessage").html("<span class=\"diagwarn\">Limité</span><p class=\"diagdetail\">Vous utilisez une ancienne version de votre navigateur. Si vous rencontrez des problèmes, une mise à jour est recommandée ["+parser.getBrowser().name+ " "+parser.getBrowser().version+"].</p>"+activate);
		} else {
			$("#browsermessage").html("<span class=\"diagok\">OK</span><p class=\"diagdetail\">Votre navigateur semble compatible ["+parser.getBrowser().name+ " "+parser.getBrowser().version+"].</p>"+activate);
		}
	} else {
		$("#browsermessage").html("<span class=\"diagwarn\">Limité</span><p class=\"diagdetail\">Votre navigateur n'a pas pu être vérifié avec ce système de webconférence. Cela pourrait tout de même fonctionner mais si vous rencontrez des problèmes, essayez d'utiliser Chrome ou Firefox ["+parser.getBrowser().name+ " "+parser.getBrowser().version+"].</p>");
	}
}

/* the following function is based on ThinkingStiff code posted on stackoverflow
 * heavily modified...
 * http://stackoverflow.com/questions/15472251/estimate-users-upload-speed-without-direct-permission 
 */
var bwdiag="";
var bwstatus=0;
var bwprogress=0;

function checkspeed() {
	checkbw('down');

	function checkbw(direction) {
		var start;// = new Date;
		if (direction=='up')
			$.ajax({
				type: "POST",
				url: "static/void.html",
				data: getRandomString(1),
				processData: false,
				cache: false,
				timeout: 15000,
				beforeSend: function(output){ start = new Date; },
				success: function(output){ updatebw(true, start, direction); },
				error: function(output){ updatebw(false, start, direction); }
			});
		else if (direction=='down')
			$.ajax({
				type: "GET",
				url: "static/img/randomcat.jpg", // 1mb file
				processData: false,
				cache: false,
				timeout: 15000,
				beforeSend: function(output){ start = new Date; },
				success: function(output){ updatebw(true, start, direction); },
				error: function(output){ updatebw(false, start, direction); }
			});
	}
	
	function updatebw(success, start, direction) {
		if (success) {
			var kbps=Math.round( 1024 / ( ( new Date() - start ) / 1000 ));
			if (direction=='down') {
				if (kbps<200) {
					bwstatus=2;
					$("#"+direction+"loadspeed").html("<span class=\"diagfail\">"+kbps+" KBps</span>");
					bwdiag="La récéption de données est trop lente sur votre connexion. Demandez aux participants de désactiver leur webcam, et si des problèmes se produisent, essayez de faire votre webconf depuis une autre connexion ou vérifiez qu'elle n'est pas utilisée par d'autres programmes. ";
				} else if (kbps<800) {
					if (bwstatus==0)
						bwstatus=1;
					$("#"+direction+"loadspeed").html("<span class=\"diagwarn\">"+kbps+" KBps</span>");
					bwdiag+="La vitesse de réception de données sur votre connexion est juste suffisante pour participer à des réunion avec un petit nombre de webcam actives, en basse résolution. Si vous rencontrez des problèmes de son hashé, demandez aux participant de la conférence de régler leur webcam dans une résolution inférieure ou de la désactiver, et vérifiez que votre connexion n'est pas utilisée par d'autres programmes. ";
				} else {
					$("#"+direction+"loadspeed").html(""+kbps+" KBps");
				}
			} else {
				if (kbps<60) {
					bwstatus=2;
					$("#"+direction+"loadspeed").html("<span class=\"diagfail\">"+kbps+" KBps</span>");
					bwdiag+="L'envoie de données est trop lente sur votre connexion. Désactivez votre webcam et si des problèmes se produisent, essayez de faire votre webconf depuis une autre connexion ou vérifiez qu'elle n'est pas utilisée par d'autres programmes. ";				
				} else if (kbps<300) {
					if (bwstatus==0)
						bwstatus=1;
					$("#"+direction+"loadspeed").html("<span class=\"diagwarn\">"+kbps+" KBps</span>");
					bwdiag+="La vitesse d'envoie de données sur votre connexion est juste suffisante pour participer à des réunion en réglant votre webcam en basse résolution. Si vous rencontrez des problèmes de son hashé, désactivez votre webcam, et vérifiez que votre connexion n'est pas utilisée par d'autres programmes. ";
				} else {
					$("#"+direction+"loadspeed").html(""+kbps+" KBps");
				}
			}
		} else
			$("#"+direction+"loadspeed").html("<span class=\"diagfail\">Echec / perte de connexion lors du test ?</span>");
		if (direction=='down') {
			bwprogress++;
			checkbw('up');
		} else {
			bwprogress++;
			completeping();
		}
	}
	
	function getRandomString( sizeInMb ) {
		var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!@#$%^&*()_+`-=[]\{}|;':,./<>?", //random data prevents gzip effect
		iterations = sizeInMb * 1024 * 1024, //get byte count
		result = '';
		for (var index = 0; index < iterations; index++) {
			result += chars.charAt( Math.floor( Math.random() * chars.length ) );
		};
		return result;
	};
};

function completeping() {
	if (pingprogress>=10 && bwprogress==2) {
		if (bwstatus==2)
			$("#networkmessage").html("<span class=\"diagfail\">Echec</span><p class=\"diagdetail\">"+bwdiag+"</p>");
		else if (bwstatus==1)
			$("#networkmessage").html("<span class=\"diagwarn\">Limité</span><p class=\"diagdetail\">"+bwdiag+"</p>");
		else
			$("#networkmessage").html("<span class=\"diagok\">OK</span><p class=\"diagdetail\">"+bwdiag+"</p>");
	}
}


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



function updateping(success, start) {
	var ping = new Date - start;
	$("#pingnumeric").text(ping);
	$(".pingbar").first().remove();
	
	// keep up some global stats
	if (maxping<ping)
		maxping=ping;
	if (minping>ping)
		minping=ping;
	
	var height=Math.ceil(24*ping/1000);
	if (height>24)
		height=24;
	
	var color="#FF0000";
	if (!success) {
		color="#000000";
		height=24;
		pingfail+=3;
	} else if (ping<150) {
		color="#00EF00";
	} else if (ping<300) {
		color="#FF7F00";
		pingfail+=0.3;
	} else {
		pingfail++;
	}
	pingprogress++;
	if (pingprogress==10 && $("#pingdiag")) {
		if (pingfail>=3.0) {
			bwstatus=2;
			$("#pingdiag").html("<span class=\"diagfail\">"+maxping+"ms</span>");
			bwdiag+="Le temps de latence de votre connexion semble trop élevé, essayez de faire votre webconf depuis une autre connexion ou vérifiez qu'elle n'est pas utilisée par d'autres programmes.";				
		} else if (pingfail>=1.0) {
			if (bwstatus==0)
				bwstatus=1;
			$("#pingdiag").html("<span class=\"diagwarn\">"+maxping+"ms</span>");
			bwdiag+="Le temps de latence de votre connexion semble limite, si vous rencontrez des problèmes, désactivez la webcam, essayez de faire votre webconf depuis une autre connexion ou vérifiez qu'elle n'est pas utilisée par d'autres programmes, préférez vous connecter avec un cable réseau plutôt que par wifi.";
		} else {
			$("#pingdiag").html(""+maxping+"ms");
		}
		completeping();
	}
	$("#pinggraph").append("<span class=\"pingbar\" style=\"height:"+height+"px;background-color:"+color+"\">&nbsp;</span>");

	if (pingprogress<10)
		window.setTimeout(runping,500);
	else
		window.setTimeout(runping,3000);
}

function initping() {
	for (var i=0; i<30; i++) {
		$("#pinggraph").append("<span class=\"pingbar\" style=\"\">&nbsp;</span>");
	}
}

//display and update ping
function runping() {
	var start = new Date;

	$.ajax({
		type: "GET",
		url: "http://bbb.utc.fr/demo/ping.jsp",
		cache:false,
		success: function(output){ updateping(true, start); },
		error: function(output){ updateping(false, start); }
	});
}


initping();
window.setTimeout(runping,1000);