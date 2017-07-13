<?php



class ekNotesDraw {
	
	
	//~~~~~
	static function drawAutoNotes( $theContent )
	{
		
		global $post;
		$postID= $post->ID;		
		$post_type = $post->post_type;
		
		
		// Get the post type - see if its auto adding the form or not
		$ek_notes_auto_options = get_option( 'ek-notes-autoAdd' );
		
		
		// If default settings don't exist thenn return
		if(!is_array($ek_notes_auto_options))
		{
			return $theContent;
		}
		
		// Do nothing if they are not logged in or the page is not auto add
		if(!in_array($post_type, $ek_notes_auto_options) ||!is_user_logged_in() )
		{
			return $theContent;
		}
		
		$userID = get_current_user_id();
		
		$args = array
		(
			"userID"			=> $userID,
			"postID"			=> $postID,
			"customFeedback"	=> ""
		);
	
		
		$formStr = ekNotesDraw::ekNoteForm($args);
		
		$theContent = $theContent.$formStr;
		
		return $theContent;
	}
	
	
	// Shortcode Function
	static function drawShortcode($atts)
	{
		$atts = shortcode_atts( 
			array(
				'id'		=> '',
				'readonly'	=> '',
				'feedback'	=> ''
				), 
			$atts
		);
		
		$postID = (int) $atts['id'];
		$readonly = $atts['readonly'];
		$customFeedback = $atts['feedback'];		
		
		$userID = get_current_user_id();
		
		if(!$userID)
		{
			return;	
		}
		
		
		if(!$postID)
		{
			global $post;
			$postID= $post->ID;		
		}
		
		
		if($readonly==true)
		{
			$noteData = ekNotesDB::getNote( $postID, $userID);
			$noteContent = "";
			if($noteData)
			{
				$noteContent =  wpautop(ekNotesUtils::formatNote($noteData['noteContent']));
				return $noteContent;				
			}	
			else
			{
				// Get the message for no notes found
				$noNotesMessage = get_option( 'ek-notes-noNotesMesage' );
				return $noNotesMessage;
			}
		}
	
		
		$args = array
		(
			"userID"			=> $userID,
			"postID"			=> $postID,
			"customFeedback"	=> $customFeedback
		);
		
		
		$notesStr = ekNotesDraw::ekNoteForm($args);
		return $notesStr;
	}
	
	// ACtual draw functino for the note form
	static function ekNoteForm($args)
	{
		
		
		
		// Hide the Visual Tab
		echo '<style>
		.wp-editor-tools, .post .wp-editor-tools {
			display: none;
		}
		</style>
		';
		
		
		$userID = $args['userID'];
		$postID= $args['postID'];
		$customFeedback= $args['customFeedback'];		
		
		
		$noteData = ekNotesDB::getNote( $postID, $userID);
		$noteContent = "";
		if($noteData)
		{
			$noteContent = ekNotesUtils::formatNote($noteData['noteContent']);
		}
	
	
		// Start of form string	
		$formStr = '<div class="ek-notes-editor-container" id="ekNotesDiv'.$postID.'">';		
		
		
		// Check for editor type - simple or advanced
		$simpleEditor = get_option( 'ek-notes-simpleEditor' );
		
		// Load TINY MCE
		$editor_settings = array
		(
			"media_buttons"	=> false, 
			"editor_class"	=> "ek-notes-editor",
			"textarea_rows"	=> 6,
			"tinymce"		=> array(
			'toolbar1'	=> 'bold,italic,underline,bullist,numlist,forecolor,undo,redo',
			'toolbar2'	=> ''
			)
		);		
		
		if($simpleEditor=="on")
		{
			$editor_settings['tinymce']['toolbar1'] = 'undo,redo';	// ONLY add the undo and redo buttons		
		}

			
		ob_start();
		wp_editor($noteContent, 'note_'.$userID.'_'.$postID, $editor_settings);		
		$formStr.= ob_get_contents();
		ob_end_clean();		
		
		
		$clickAction='ajaxSaveNotes('.$userID.', '.$postID.')';
		
		$saveButtonText= get_option( 'ek-notes-saveButtonText' );
		if(!$saveButtonText){$saveButtonText = ekNotes::$ops['saveButtonText'];}
		$formStr.='<button class="ek-notes-button-primary pure-button-primary pure-button" onclick="javascript:'.$clickAction.'">'.$saveButtonText.'</button>';		
		$formStr.='</div>';
		
		
		$noteSavedMessage= get_option( 'ek-notes-noteSavedMessage' );
		
		if($customFeedback){$noteSavedMessage = $customFeedback;}
		
		if(!$noteSavedMessage){$noteSavedMessage = ekNotes::$ops['noteSavedMessage'];}
		$formStr.='<div class="ek-notes-feedback" id="ek-notes-feedback-'.$postID.'">'.$noteSavedMessage.'</div>';
		
		return $formStr;
		
	}
	
	
	

}
?>