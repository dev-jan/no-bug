
function hideshow(which){
	if (!document.getElementById) {
		return;
	}
	if (which.style.display=="block") {
		which.style.display="none";
	}
	else {
		which.style.display="block";
	}
}

function resizeTextarea (textarea) {
	window.setTimeout("realresizeTextarea('" + textarea + "')", 20);
}

function realresizeTextarea (textarea) {
	var rowsArray = document.getElementById(textarea).value.split('\n');
	document.getElementById(textarea).rows = rowsArray.length + 2;
}

function askBeforSending ($message) {
	return window.confirm($message);
}
