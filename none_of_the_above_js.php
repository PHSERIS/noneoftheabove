<?php

/* 
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
 * 
 * Credit for this script to:
 * Andrew Martin
 * Stanford University
 */

?>

<script type='text/javascript'>
	$(document).ready(function() {
		var notaFields = <?php print json_encode($startup_vars) ?>;
    
		// Go through each field with a NOTA option
		$.each(notaFields, function(field,params) {
			var tr = $('tr[sq_id='+field+']');
			var notaValue = params.params;
			var inputs = $('input:checkbox',tr);
			
      // Ensure notaValue is a string containing only numbers
      if ( (! notaValue) || (! /^\d+$/.test(notaValue))) {
        console.log("Invalid custom action tag parameter: " + notaValue);
        return true;
      }
      
      // Replace term from note if defined there
      var note = $('div.note', tr);
      $(note).text($(note).text().replace('<?php echo $tag ?>=' + notaValue, ''));

      // Add event handler to click events
      $(inputs).on("change", {'field': field, 'code': notaValue},notaCheck);
      
		});
	});
	
	// This changes checkbox selections based on user response from confirmation dialog
	function notaUpdate(field, notaValue, erase) {
		var tr = $('tr[sq_id='+field+']');
		if (erase) {
      
			// Clear all non-nota checkboxes
			var otherCheckedItems = $('input:checkbox:checked[code!="'+notaValue+'"]', tr);
			$(otherCheckedItems).parents().click();
		} else {
      
			// Undo the nota checkbox (this is all messy due to way REDCap handles checkboxes - very odd...)
			var notaOption = $('input:checkbox[code="'+notaValue+'"]', tr);
			$(notaOption).parents().click();
			$(notaOption).prop('checked', false);
			calculate();doBranching();
		}
	};
	
	// This is called when a checkbox is modified in a monitored field
	function notaCheck(event) {
    
		// Ignore uncheck events
		if (!$(this).prop('checked')) {
      
			//console.log ($(this).prop('name') + ': Ignoring uncheck call');
			return true;
		}
		
		// Get the field name and tr elements
		var field = event.data.field;
		var notaValue = event.data.code;
		var tr = $('tr[sq_id='+field+']');
		var notaOption = $('input:checkbox[code="'+notaValue+'"]', tr);
		var notaText = $(notaOption).parent().text().trim();
		
		// Ignore if the NOTA is not checked
		if (!$(notaOption).prop('checked')) return true;
		
    // Get other checked items
		var otherCheckedItems = $('input:checkbox:checked[code!="'+notaValue+'"]', tr);
		if (otherCheckedItems.length) {
      
			// Prepare a modal dialog to confirm action
			var labels = [];
			$(otherCheckedItems).each(function(){
				labels.push($(this).parent().text().trim());
			});
      
			var content = "The option, <b>" + notaText + "</b>, can only be selected by itself.<br><br>Press <b>Keep \"" + notaText + "\"</b> to uncheck the other selected option(s) listed below:<div style='padding:5px 20px;'>" + labels.join(',<br>') + "</div>Or, press <b>Keep others</b> to uncheck  <b>" + notaText + "</b>";
			var undo_js = "notaUpdate('" + field + "','"+notaValue+"', false)";
			var accept_js = "notaUpdate('" + field + "','"+notaValue+"', true)";
			simpleDialog(content, "Incompatible Checkbox Selection", null, 400, accept_js, "Keep \"" + notaText + "\"", undo_js, "Keep others");
		}
	}
</script>