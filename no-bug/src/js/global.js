
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
	var rowsArray = document.getElementById(textarea).value.split('\n');
	document.getElementById(textarea).rows = rowsArray.length + 1;
}