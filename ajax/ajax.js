function ajaxSaveNotes(userID, postID)
{

	console.log("User = "+userID);
	console.log("postID = "+postID);
	
	// Force TONY MCE editor to return correct value
	tinyMCE.triggerSave();		
	
	var elementID = 'note_'+userID+'_'+postID;
	console.log("elementID = '"+elementID+"'");	
	
	
	noteContent = document.getElementById(elementID).value;
	
	console.log("noteContent = '"+noteContent+"'");
	console.log("nonce = '"+ek_notes_frontEndAjax.ajax_nonce+"'");	
	

	jQuery.ajax({
		type: 'POST',
		url: ek_notes_frontEndAjax.ajaxurl,
		data: {			
			"action": "add_ekNote",
			"noteContent": noteContent,
			"userID": userID,
			"postID": postID,
			"security": ek_notes_frontEndAjax.ajax_nonce
		},
		success: function(data){
			//console.log(data);
			var thisFeedbackDiv = "#ek-notes-feedback-"+postID;
			jQuery(thisFeedbackDiv).show("fast");	
			}
	});
	
	
	return false;		
	
}


