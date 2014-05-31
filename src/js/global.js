// Global Javascript functions for NO-BUG

/**
 * Show or hide an given element (show the element if its hidden and vice versa)
 * @param which element to show/hide
 */
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

/**
 * Resize a textarea to the size of its content
 * @param textarea textarea to resize
 */
function resizeTextarea (textarea) {
	window.setTimeout("realresizeTextarea('" + textarea + "')", 20);
}

/**
 * Resize a given textarea (but please use the function @see resizeTextarea(textarea) to do this
 * @param textarea textarea to resize
 */
function realresizeTextarea (textarea) {
	var rowsArray = document.getElementById(textarea).value.split('\n');
	document.getElementById(textarea).rows = rowsArray.length + 2;
}

/**
 * Create a popup to ask the user if he is really sure to do an action
 * @param $message message to display in the popup
 * @returns TRUE if the user click on OK
 */
function askBeforSending ($message) {
	return window.confirm($message);
}
