
function load_content_html(idelem, url) {
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	};  
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById(idelem).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET", url ,true);
	xmlhttp.send();
}

function show_index_element(idelem) {
	var index_elems = [
		'indexcontent_sign_in',
		'indexcontent_about',
		'indexcontent_registration',
		'indexcontent_contacts',
		'indexcontent_restore'
	];

	for (var i = 0; i < index_elems.length; i++) {
		document.getElementById(index_elems[i]).style.display = 'none';
	}
	document.getElementById(idelem).style.display = 'block';
} 

function send_request(url, callbackf) {
	if (window.XMLHttpRequest)
	{
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	};  
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			var obj = JSON.parse(xmlhttp.responseText);
			callbackf(obj);
		}
	}
	xmlhttp.open("GET",url,true);
	xmlhttp.send();	
}

function sign_in()
{
	document.getElementById("error_message").innerHTML = "";
	document.getElementById("info_message").innerHTML = "Please wait...";
	
	var email = document.getElementById('email').value;
	var password = document.getElementById('password').value;
	
	send_request(
		"api/index/sign_in.php?email="+email + "&password=" + password,
		function(obj) {
			if (obj.result == "fail") {
				document.getElementById("error_message").innerHTML = "<b>" + obj.error.message + "</b>";
			} else {
				window.location.href = "main.php";
			}
		}
	);
}

function restore()
{
	document.getElementById("error_message").innerHTML = "";
	document.getElementById("info_message").innerHTML = "Please wait...";
	
	var email = document.getElementById('email').value;
	var captcha = document.getElementById('captcha_reg').value;
	
	send_request(
		"api/index/restore.php?email="+email + "&captcha=" + captcha,
		function(obj) {
			if (obj.result == "fail") {
				document.getElementById("error_message").innerHTML = "<b>" + obj.error.message + "</b>";
				document.getElementById("info_message").innerHTML = "";
			} else {
				document.getElementById("info_message").innerHTML = "<b>" + obj.data.message + "</b>";
				document.getElementById("captcha_reg").value = "";
				document.getElementById("email").value = "";
			}
			document.getElementById('captcha_image_restore').src = 'captcha.php?rid=' + Math.random();
		}
	);
}

function registration()
{
	document.getElementById("error_message").innerHTML = "";
	document.getElementById("info_message").innerHTML = "Please wait...";
	
	var email = document.getElementById('email').value;
	var captcha = document.getElementById('captcha_reg').value;
	
	send_request(
		"api/index/registration.php?email="+email + "&captcha=" + captcha,
		function(obj) {
			if (obj.result == "fail") {
				document.getElementById("error_message").innerHTML = "<b>" + obj.error.message + "</b>";
				document.getElementById("info_message").innerHTML = "";
			} else {
				document.getElementById("info_message").innerHTML = "<b>" + obj.data.message + "</b>";
				document.getElementById("captcha_reg").value = "";
				document.getElementById("email").value = "";
			}
			document.getElementById('captcha_image_reg').src = 'captcha.php?rid=' + Math.random();
		}
	);
}



