<?php

/*
 * © 2016 Partners HealthCare System, Inc. All Rights Reserved.
 * @author Bob Gorczyca
 * 
 * This is a hook that allows you to prevent multiple selections of checkbox values 
 * when one is marked as a 'none of the above' option
 * 
 * So, if you have:
 *
 * 1, Apples
 * 2, Bananas
 * 3, Cherries
 * 98, None of the Above
 *
 * The trigger is similar to REDCap "action tags".
 * You can define a hook as @NONEOFTHEABOVE
 * or
 * @NONEOFTHEABOVE=xx
 * If the xx parameter is defined, it will prevent the selection of xx with any other values.
 * In the above example, if xx is '99', then if 'None of the Above' is selected, the user
 * will be required to de-select any other selections.
 *
**/
	
// "Action tag" for this hook
$tag = '@NONEOFTHEABOVE';

// init_tags() returns an array of all project instrument fields in which some tag 
// appears. Each array contains the variable Name of the field, and an array of
// elements_index and params
$tag_functions = init_tags(__FILE__);
if (empty($tag_functions)) return;

// If this particular tag is not used on this survey/form, no need to load
if (!isset($tag_functions[$tag])) return;

// Ok, tag is used in this project - process it
hook_log ("Running $tag for PID: $project_id, INSTR: $instrument", "DEBUG");

// Step 1 - Create array of fields containing the tag, by variable name
$startup_vars = $tag_functions[$tag];

// Include processing file
include_once 'none_of_the_above_js.php';


