<?php

	if ( ! defined( 'ABSPATH' ) ) 
	{
		die();	// Exit if accessed directly
	}
	
	// Only let them view if admin		
	if(!current_user_can('manage_options'))
	{
		die();
	}	
?>

<h1>Note Settings</h1>



<?php

if(isset($_GET['action']))
{
	
	
	// Check the nonce before proceeding;	
	$retrieved_nonce="";
	if(isset($_REQUEST['_wpnonce'])){$retrieved_nonce = $_REQUEST['_wpnonce'];}
	if (wp_verify_nonce($retrieved_nonce, 'ek-notes-settings-nonce' ) )
	{	
		if($_GET['action']=="saveSettings")
		{
			
			// Create new array of auto add notes
			$autoTypeNoteArray = array();
			foreach($_POST as $postName => $postValue)
			{
				if (strpos($postName, 'notes_') !== false)
				{
					$thisPostName = substr($postName, 6);
					$autoTypeNoteArray[] = $thisPostName;
				}			
			}
			
			// Update Option
			update_option( 'ek-notes-autoAdd', $autoTypeNoteArray );
			
			
			// Save No note Message
			$noNotesMessage = wp_kses_post($_POST['noNotesMessage']);
			update_option( 'ek-notes-noNotesMesage', $noNotesMessage );
			
			
			// Save Message Saved Text
			$noteSavedMessage = wp_kses_post($_POST['noteSavedMessage']);
			update_option( 'ek-notes-noteSavedMessage', $noteSavedMessage );
			
			// Save Button Text
			$saveButtonText = wp_kses_post($_POST['saveButtonText']);
			update_option( 'ek-notes-saveButtonText', $saveButtonText );	
			
			// Save Editor Options
			$simpleEditor="";
			if(isset($_POST['simpleEditor']))
			{
				$simpleEditor = wp_kses_post($_POST['simpleEditor']);
			}
			update_option( 'ek-notes-simpleEditor', $simpleEditor );						
			
			
		}
		
		echo '<div class="updated notice"><p>Settings Updated</p></div>';
		
		
	}
	
}



// Get Saved options
$ek_notes_auto_options = get_option( 'ek-notes-autoAdd' );

$noNotesMessage = ekNotesUtils::formatNote(get_option( 'ek-notes-noNotesMesage' ));
if(!$noNotesMessage)
{
	$noNotesMessage = ekNotes::$ops['noNotesMessage'];	
}

$noteSavedMessage = ekNotesUtils::formatNote(get_option( 'ek-notes-noteSavedMessage' ));
if(!$noteSavedMessage)
{
	$noteSavedMessage = ekNotes::$ops['noteSavedMessage'];	
}


$saveButtonText = ekNotesUtils::formatNote(get_option( 'ek-notes-saveButtonText' ));
if(!$saveButtonText)
{
	$saveButtonText = ekNotes::$ops['saveButtonText'];	
}

$simpleEditor = get_option( 'ek-notes-simpleEditor' );
if(!$simpleEditor)
{
	$simpleEditor = ekNotes::$ops['simpleEditor'];	
}






// Do not bother showing these CPTs
$excludedArray = array (
	"revision",
	"nav_menu_item",
	"ek_notes",
	"custom_css",
	"customize_changeset"
);


echo '<form action="edit.php?post_type=ek_notes&page=ek-notes-settings&action=saveSettings" method="post">';


echo '<i>Automatically add notes to each of the following content types</i><br/>';
$post_types = get_post_types( '', 'objects' );

foreach ( $post_types  as $post_type ) {

	$post_name = $post_type->name;
	$post_label = $post_type->label;	

	if(!in_array($post_name, $excludedArray))
	{
		echo '<label for="notes_'.$post_name.'"><input type="checkbox" id="notes_'.$post_name.'" name="notes_'.$post_name.'"';		
		if(in_array($post_name, $ek_notes_auto_options)){ echo ' checked ';}		
		echo '/>';
		echo $post_label.' ('.$post_name.')</label><br/>';	
	}
}

echo '<hr/>';
echo '<label for="noteSavedMessage"><i>Message to display upon note saved</i></label><br/>';
echo '<input type="text" value="'.$noteSavedMessage.'" id="noteSavedMessage" name="noteSavedMessage" />';

echo '<hr/>';
echo '<label for="saveButtonText"><i>Save Button Text</i></label><br/>';
echo '<input type="text" value="'.$saveButtonText.'" id="saveButtonText" name="saveButtonText" />';

echo '<hr/>';
echo '<label for="noNotesMessage"><i>Message to display if no notes found</i></label><br/>';
echo '<input type="text" value="'.$noNotesMessage.'" id="noNotesMessage" name="noNotesMessage" />';

echo '<hr/>';

echo '<h3>Editor Settings</h3>';
echo '<label for="simpleEditor"><input type="checkbox" name="simpleEditor" id="simpleEditor" ';
if($simpleEditor=="on"){echo ' checked'; }
echo '/> Use a simple Editor (No toolbar options)</label><hr/>';




echo '<input type="submit" value="Save Note Settings" class="button-primary"/>';

// Add nonce field
wp_nonce_field('ek-notes-settings-nonce');



echo '</form>';


?>
<div style="margin:10px; padding:20px; border:1px solid #666; background:#fff;">
<h1>How to use this plugin</h1>
<h3>Basic Use</h3>
You can automatically add a 'User Notes' form to every page or post, or any other custom post type using the checkboxes above
<h3>Advanced Use</h3>
If you need custom notes outside specific pages then you can create a new notes using the 'Add New Note' option. Give it a name to identify it - this name is never shown outside the admin interface.<br />
Each new note will have its own unique shortcode for you to paste anywhere ontyo a post or page.

<h3>Shortcodes</h3>
To add a note to an existing page use the shortcode:<br/>
[ek-notes]<br /><br />
To save to a specific note ID:<br />
[ek-notes id=1]<br />
where 1 is the ID of the note (or post / page)

<h4>Shortcode Parameters</h4>
To display the note content:<br/>
[ek-notes readonly=true]


</div>






