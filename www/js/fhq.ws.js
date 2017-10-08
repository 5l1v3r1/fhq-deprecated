if(!window.fhq) window.fhq = {};
if(!window.fhq.ws) window.fhq.ws = {};
fhq.ws.lastm = 0;

// WebSocket protocol

window.fhq.ws.handlerReceivedChatMessage = function(response){
	fhq.handlerReceivedChatMessage(response);
};
window.fhq.ws.listeners = {}
window.fhq.ws.addListener = function(m, d){
	fhq.ws.listeners[m] = d;
}

fhq.ws.handleCommand = function(response){
	if(fhq.ws.listeners[response.m]){
		if(response['error']){
			setTimeout(function(){
				fhq.ws.listeners[response.m].reject(response);
				delete fhq.ws.listeners[response.m];
			},1);
		} else {
			setTimeout(function(){
				fhq.ws.listeners[response.m].resolve(response);
				delete fhq.ws.listeners[response.m];
			},1);
		}
	}else if(response.cmd == "chat"){
		fhq.ws.handlerReceivedChatMessage(response);
	}else{
		console.error("Not found handler for '" + response.cmd + "/" + response.m + "'");
	}
};

window.fhq.ws.WSState = "?";

window.fhq.ws.getWSState = function(){
	return fhq.ws.WSState;
}

window.fhq.ws.setWSState = function(s){
	fhq.ws.WSState = s;
	var el = document.getElementById('websocket_state');
	if(el){
		document.getElementById('websocket_state').innerHTML = s;
	}
}
window.fhq.ws.onconnect = function(){
	
};

window.fhq.ws.initWebsocket = function(){

	fhq.ws.socket = new WebSocket(fhq.ws.protocol + "//" + fhq.ws.hostname + ":" + fhq.ws.port + "/");
	// fhq.ws.socket = new WebSocket(protocol + "//freehackquest.com:" + port + "/");
	window.fhq.ws.socket.onopen = function() {
		console.log('WS Opened');
		setTimeout(window.fhq.ws.onconnect,1);
		fhq.ws.setWSState("OK");
		fhq.ws.token();
	};

	window.fhq.ws.socket.onclose = function(event) {
		console.log('Closed');
		
		if(fhq.ui && fhq.ui.onwsclose){
			fhq.ui.onwsclose();
		}
		
		if (event.wasClean) {
			fhq.ws.setWSState("CLOSED");
		} else {
			fhq.ws.setWSState("BROKEN");
			setTimeout(function(){
				fhq.ws.setWSState("RECONN");
				fhq.ws.initWebsocket();
			}, 10000);
		  // Try reconnect after 5 sec
		}
		console.log('Code: ' + event.code + ' Reason: ' + event.reason);
	};
	fhq.ws.socket.onmessage = function(event) {
		// console.log('Received: ' + event.data);
		try{
			var response = JSON.parse(event.data);
			fhq.ws.handleCommand(response);
		}catch(e){
			console.error(e);
		}
		
	};
	fhq.ws.socket.onerror = function(error) {
		console.log('Error: ' + error.message);
	};
}

fhq.ws.initWebsocket();

window.fhq.ws.send = function(obj, def){
	var d = def || $.Deferred();
	fhq.ws.lastm++;
	obj.m = "m" + fhq.ws.lastm;
	fhq.ws.listeners[obj.m] = d;
	try{
		if(fhq.ws.socket.readyState == 0){
			setTimeout(function(){
				fhq.ws.send(obj, d);
			},1000);
		}else{
			// console.log("ReadyState " + fhq.ws.socket.readyState);
			// console.log("Send " + JSON.stringify(obj));
			fhq.ws.socket.send(JSON.stringify(obj));
		}
	}catch(e){
		console.error(e);
	}
	return d;
}

window.fhq.ws.getPublicInfo = function(){
	return fhq.ws.send({
		'cmd': 'getpublicinfo'
	});
}

window.fhq.ws.getmap = function(params){
	params = params || {};
	params.cmd = 'getmap';
	return fhq.ws.send(params);
}

window.fhq.ws.sendChatMessage = function(params){
	params = params || {};
	params.cmd = 'sendchatmessage';
	return fhq.ws.send(params);
}

window.fhq.ws.sendMessageToAll = function(type, message){
	return fhq.ws.send({
		'cmd': 'sendmessagetoall',
		'type': type,
		'message': message
	});
}

window.fhq.ws.sendLettersToSubscribers = function(message){
	return fhq.ws.send({
		'cmd': 'send_letters_to_subscribers',
		'message': message
	});
}

fhq.ws.updateUserProfileAsync = function(){
	setTimeout(function(){
		fhq.ws.user().done(function(r){
			fhq.profile.bInitUserProfile == true;
			fhq.profile.university = r.profile.university;
			fhq.profile.country = r.profile.country;
			fhq.profile.city = r.profile.city;
			fhq.userinfo = {};
			fhq.userinfo.id = r.data.id;
			fhq.userinfo.nick = r.data.nick;
			fhq.userinfo.email = r.data.email;
			fhq.userinfo.role = r.data.role;
			fhq.userinfo.logo = r.data.logo;
			$(document).ready(function(){
				fhq.ui.processParams();
			});
		}).fail(function(){
			fhq.api.cleanuptoken();
			$(document).ready(function(){
				fhq.ui.processParams();
			});
		});
	},10);
}

fhq.ws.token = function(){
	var d = $.Deferred();
	fhq.ws.send({
		'cmd': 'token',
		'token': fhq.getTokenFromCookie()
	}).done(function(r){
		$(document).ready(function(){
			fhq.ui.processParams();
		});
		fhq.ws.updateUserProfileAsync();
	}).fail(function(r){
		fhq.api.cleanuptoken();
		$(document).ready(function(){
			fhq.ui.processParams();
		});
	});
	return d;
}

fhq.ws.login = function(params){
	var d = $.Deferred();
	params = params || {};
	params.cmd = 'login';
	fhq.ws.send(params).done(function(r){
		fhq.token = r.token;
		console.log(fhq.token);
		fhq.userinfo = r.user;
		localStorage.setItem('userinfo', JSON.stringify(fhq.userinfo));
		fhq.setTokenToCookie(r.token);
		$(document).ready(function(){
			fhq.ui.processParams();
		});
		fhq.ws.updateUserProfileAsync();	
		d.resolve(r);
		// try{fhq.ws.socket.close();fhq.ws.initWebsocket()}catch(e){console.error(e)};
	}).fail(function(err){
		fhq.api.cleanuptoken();
		localStorage.removeItem('userinfo');
		fhq.userinfo = {};
		d.reject(err);
	})
	return d;
}

fhq.ws.users = function(params){
	params = params || {};
	params.cmd = 'users';
	return fhq.ws.send(params);
}

fhq.ws.classbook = function(params){
	params = params || {};
	params.cmd = 'classbook';
	return fhq.ws.send(params);
}

fhq.ws.addhint = function(params){
	params = params || {};
	params.cmd = 'addhint';
	return fhq.ws.send(params);
}

fhq.ws.deletehint = function(params){
	params = params || {};
	params.cmd = 'deletehint';
	return fhq.ws.send(params);
}

fhq.ws.hints = function(params){
	params = params || {};
	params.cmd = 'hints';
	return fhq.ws.send(params);
}

fhq.ws.writeups = function(params){
	params = params || {};
	params.cmd = 'writeups';
	return fhq.ws.send(params);
}

fhq.ws.answerlist = function(params){
	params = params || {};
	params.cmd = 'answerlist';
	return fhq.ws.send(params);
}

fhq.ws.scoreboard = function(params){
	params = params || {};
	params.cmd = 'scoreboard';
	return fhq.ws.send(params);
}

fhq.ws.serverinfo = function(params){
	params = params || {};
	params.cmd = 'serverinfo';
	return fhq.ws.send(params);
}

fhq.ws.serversettings = function(params){
	params = params || {};
	params.cmd = 'serversettings';
	return fhq.ws.send(params);
}

fhq.ws.update_server_settings = function(params){
	params = params || {};
	params.cmd = 'update_server_settings';
	return fhq.ws.send(params);
}

fhq.ws.publiceventslist = function(params){
	params = params || {};
	params.cmd = 'publiceventslist';
	return fhq.ws.send(params);
}

fhq.ws.createpublicevent = function(params){
	params = params || {};
	params.cmd = 'createpublicevent';
	return fhq.ws.send(params);
}

fhq.ws.deletepublicevent = function(params){
	params = params || {};
	params.cmd = 'deletepublicevent';
	return fhq.ws.send(params);
}

fhq.ws.api = function(params){
	params = params || {};
	params.cmd = 'api';
	return fhq.ws.send(params);
}

fhq.ws.games = function(params){
	params = params || {};
	params.cmd = 'games';
	return fhq.ws.send(params);
}

fhq.ws.createquest = function(params){
	params = params || {};
	params.cmd = 'createquest';
	return fhq.ws.send(params);
}

fhq.ws.deletequest = function(params){
	params = params || {};
	params.cmd = 'deletequest';
	return fhq.ws.send(params);
}

fhq.ws.updatequest = function(params){
	params = params || {};
	params.cmd = 'updatequest';
	return fhq.ws.send(params);
}

fhq.ws.quest = function(params){
	params = params || {};
	params.cmd = 'quest';
	return fhq.ws.send(params);
}

fhq.ws.user = function(params){
	params = params || {};
	params.cmd = 'user';
	return fhq.ws.send(params);
}

fhq.ws.registration = function(params){
	params = params || {};
	params.cmd = 'registration';
	return fhq.ws.send(params);
}

fhq.ws.user_answers = function(params){
	params = params || {};
	params.cmd = 'user_answers';
	return fhq.ws.send(params);
}

fhq.ws.user_skills = function(params){
	params = params || {};
	params.cmd = 'user_skills';
	return fhq.ws.send(params);
}

fhq.ws.game_create = function(data){
	data = data || {};
	data.cmd = 'game_create';
	return fhq.ws.send(data);
}

fhq.ws.quests_subjects = function(data){
	data = data || {};
	data.cmd = 'quests_subjects';
	return fhq.ws.send(data);
}

fhq.ws.quests = function(data){
	data = data || {};
	data.cmd = 'quests';
	return fhq.ws.send(data);
}

fhq.ws.user_change_password = function(data){
	data = data || {};
	data.cmd = 'user_change_password';
	return fhq.ws.send(data);
}
