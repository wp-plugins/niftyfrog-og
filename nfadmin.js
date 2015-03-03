/*
	Plugin Name: NiftyFrog OG
	Plugin URI: http://niftyfrog.com/plugins/niftyfrog.php/
	Description: Places meta tags in your blog's header, so a suitable image and description show, when crossposting to Facebook or generating a Twitter Card.
	Version: 0.3
	Author: Michelle Thompson
	Author URI: http://niftyfrog.com/
	License: GPLv3
*/

window.onload = initAll;

var findfbid;

function initAll() {
	findfbid = document.getElementById("findfbid");
	viewFindIdLink();
}

// Prompt for username
function getFbUsername() {
	var fbuname = prompt("Enter your Facebook username:", "");
	if (fbuname != null && fbuname !== '') {
		getFbNumWin(fbuname);
	}
}

// Show popup window
function getFbNumWin(fbuname) {
	fburl = 'http://graph.facebook.com/' + fbuname;
	var fbNumWin = window.open(fburl, "graphwin", "width=400, height=400");
}

// Only show find-id link if js enabled
function viewFindIdLink() {
	var findIdLink = 'Don&#39;t know your numeric Facebook ID? <a href="#" onclick="getFbUsername();">Click here</a> to find it. Requires pop-up windows to be enabled.';
	findfbid.innerHTML = findIdLink;
}
