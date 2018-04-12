# noneoftheabove
@NONEOFTHEABOVE action tag / custom hook for REDCap. Requires the Hooks Framework

This requires Andy Martin's Hooks Framework to be setup.

You have to add this to your "hooks/custom/global/global_hooks.php" file

  // INCLUDE NONE OF THE ABOVE
	$file = HOOK_PATH_UTILITIES . "noneoftheabove/none_of_the_above.php";
	if (file_exists($file)) {
		include_once $file;
	} else {
		hook_log ("Unable to include $file for project $project_id while in " . __FILE__);
	}
  
  
