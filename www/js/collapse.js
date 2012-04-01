// This code enables expandable/collapsible elements.  Most is handled via CSS
// rather than the messier option of adding style rules to the elements.
// Collapsible elements should be given the "collapsible" class, especially if
// you intend for the expandAll and collapseAll functions to work on them.
// Give an element the "collapsed" class to set it as collapsed.
//
// Author: Zachary "Gamer_Z." Yaro

/**
 * Expands the given element
 * @param {HTMLElement} elem - The element to expand
 */
function expand(elem) {
	if(!!elem.classList) {
		elem.classList.remove("collapsed");
	} else {
		if(elem.className.indexOf("collapsed") != -1) {
			elem.className = elem.className.replace(" collapsed", "").replace("collapsed", "");
		}
	}
}

/**
 * Collapses the given element
 * @param {HTMLElement} elem - The element to expand
 */
function collapse(elem) {
	if(!!elem.classList) {
		elem.classList.add("collapsed");
	} else {
		if(elem.className.indexOf("collapsed") === -1) {
			elem.className = elem.className + " collapsed";
		}
	}
}

/**
 * Expands the given element if it is collapsed or collapses it if it is expanded
 * @param {HTMLElement} elem - The element to expand/collapse
 */
function toggleCollapsed(elem) {
	if(!!elem.classList) {
		elem.classList.toggle("collapsed");
	} else {
		if(elem.className.indexOf("collapsed") === -1) {
			elem.className = elem.className + " collapsed";
		} else {
			elem.className = elem.className.replace(" collapsed", "").replace("collapsed", "");
		}
	}
}

/**
 * Expands all collapsible elements on the page
 */
function expandAll() {
	for(var i = 0; i < document.getElementsByClassName("collapsible").length; i++) {
		expand(document.getElementsByClassName("collapsible")[i]);
	}
}

/**
 * Collapses all collapsible elements on the page
 */
function collapseAll() {
	for(var i = 0; i < document.getElementsByClassName("collapsible").length; i++) {
		collapse(document.getElementsByClassName("collapsible")[i]);
	}
}
