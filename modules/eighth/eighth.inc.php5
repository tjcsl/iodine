<?php

/**
 * Sort method for vp_delinquents
 */
function eighth_delin_sort_name($a, $b) {
	return strcasecmp($a['name'], $b['name']);
}

/**
 * Sort method for vp_delinquents
 */
function eighth_delin_sort_name_desc($a, $b) {
	return -1 * eighth_delin_sort_name($a, $b);
}

/**
 * Sort method for vp_delinquents
 */
function eighth_delin_sort_grade($a, $b) {
	return ($a['grade'] < $b['grade']) ? -1 : 1;
}

/**
 * Sort method for vp_delinquents
 */
function eighth_delin_sort_grade_desc($a, $b) {
	return -1 * eighth_delin_sort_grade($a, $b);
}

/**
 * Sort method for vp_delinquents
 */
function eighth_delin_sort_absences($a, $b) {
	return ($a['absences'] < $b['absences']) ? -1 : 1;
}

/**
 * Sort method for vp_delinquents
 */
function eighth_delin_sort_absences_desc($a, $b) {
	return -1 * eighth_delin_sort_absences($a, $b);
}
?>
