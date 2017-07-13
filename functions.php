<?php



$ekNotes = new ekNotes();

class ekNotes
{
	var $version 		= '0.1';
	public static $ops =array
	(
		"noteSavedMessage" 	=> "Note Saved!",
		"noNotesMessage" 	=> "No Notes Found.",
		"saveButtonText"	=> "Save Note",
		"simpleEditor"		=> ""
	);
	
	//~~~~~
	function __construct ()
	{
//		$this->pluginFolder = plugins_url('', __FILE__);
//		$this->ops = $this->checkCompat();
		$this->addWPActions();
	}
	
/*	---------------------------
	PRIMARY HOOKS INTO WP 
	--------------------------- */	
	function addWPActions ()
	{
		
		// Add the auto note function
		add_action( 'the_content', array( $this, 'addNotesForm' ), 100 );
		
		// Register Shortcode
		add_shortcode( 'ek-notes', array( 'ekNotesDraw', 'drawShortcode' ) );

		//Add Front End Jquery and CSS
		add_action( 'wp_footer', array( $this, 'frontendEnqueues' ) );		
		
	
		// Hook to admin_head for the CSS to be applied earlier - removes everything but 'publish' for the CPT
		add_action('admin_head-post.php', array($this, 'hide_publishing_actions') );
		add_action('admin_head-post-new.php', array($this, 'hide_publishing_actions'));
		
		//Setup defaults if they don't exist
		$ek_notes_auto_options = get_option( 'ek-notes-autoAdd' );
		
		if(!$ek_notes_auto_options)
		{
			update_option( 'ek-notes-autoAdd', array() );						
		
		}
		
		
	}
	
	
	
/*	--------------------------------------------
	Add notes form to all pages / posts if applicable
	-------------------------------------------- */		
	//~~~~~
	function addNotesForm( $theContent )
	{
		return ekNotesDraw::drawAutoNotes( $theContent );
	}
	
	
	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');

		// Custom Styles		
		wp_enqueue_style( 'ek-notes-css', EK_NOTES_PLUGIN_URL . '/css/styles.css' );
		
		
		// Register Ajax script for front end
		wp_enqueue_script('ek_notes_custom_ajax', EK_NOTES_PLUGIN_URL.'/ajax/ajax.js', array( 'jquery' ) ); #Custom AJAX functions
		
		
		wp_enqueue_style( 'ek-notes-pure-buttons', '//cdnjs.cloudflare.com/ajax/libs/pure/0.6.2/pure-min.css' );
			
		
		//Localise the JS file
		$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_nonce' => wp_create_nonce('ek_notes_ajax_nonce')
		);
		wp_localize_script( 'ek_notes_custom_ajax', 'ek_notes_frontEndAjax', $params );	
		
		
		
	}	
	
	
	
	// Hide everything but 'publish' from the publish box for notes
	function hide_publishing_actions()
	{
		global $post;
		if($post->post_type == "ek_notes"){
		echo '
			<style type="text/css">
				#misc-publishing-actions,
				#minor-publishing-actions{
					display:none;
				}
			</style>
		';
		}

	}


}






?>