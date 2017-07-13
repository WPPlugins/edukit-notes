<?php

$ekNotes_CPT = new ekNotes_CPT();

class ekNotes_CPT
{
	
	
	
	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}	
	
	
/*	---------------------------
	PRIMARY HOOKS INTO WP 
	--------------------------- */	
	function addWPActions ()
	{
		//Admin Menu
		add_action( 'init',  array( $this, 'create_CPTs' ) );		
		add_action( 'admin_menu', array( $this, 'create_AdminPages' ));
		

		// Remove and add columns in the projects table
		add_filter( 'manage_posts_columns', array( $this, 'my_custom_post_columns' ), 10, 2 );		
		add_action('manage_pages_custom_column', array($this, 'customColumnContent'), 10, 2);
		

	}
	
	
/*	---------------------------
	ADMIN-SIDE MENU / SCRIPTS 
	--------------------------- */
	function create_CPTs ()
	{
		
	
		//Projects
		$labels = array(
			'name'               =>  'Notes',
			'singular_name'      =>  'Note',
			'menu_name'          =>  'Notes',
			'name_admin_bar'     =>  'Notes',
			'add_new'            =>  'Add New Note',
			'add_new_item'       =>  'Add New Note',
			'new_item'           =>  'New Note',
			'edit_item'          =>  'Edit Note',
			'view_item'          => 'View Notes',
			'all_items'          => 'All Notes',
			'search_items'       => 'Search Notes',
			'parent_item_colon'  => '',
			'not_found'          => 'No Notes found.',
			'not_found_in_trash' => 'No Notes found in Trash.'
		);
	
		$args = array(
			'menu_icon' => 'dashicons-paperclip',		
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_nav_menus'	 => false,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => 65,
			'supports'           => array( 'title', 'editor'  )
			
		);
		
		register_post_type( 'ek_notes', $args );
		remove_post_type_support('ek_notes', 'editor');		
	}
	
	function create_AdminPages()
	{
		
		/* Create Admin Pages */

		/* Groups CSV Edit Page */		
		$parentSlug = "edit.php?post_type=ek_notes";
		$page_title="Note Settings";
		$menu_title="Settings";
		$menu_slug="ek-notes-settings";
		$function=  array( $this, 'draw_noteSettings' );
		$myCapability = "manage_options";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

	
		
	}
	

	// Draws the main notes settings page
	function draw_noteSettings()
	{
		include_once( EK_NOTES_PATH . '/admin/settings.php' );
	}
	
	

	
	// Remove Date Columns on projects
	function my_custom_post_columns( $columns, $post_type )
	{
	  
	  switch ( $post_type )
	  {    
		
			case 'ek_notes':
			
			// Remove Date column then stick it at the end	
			unset(
				$columns['date']
			);				

			$columns['ek-notes-shortcode'] = 'Shortcode';			
			$columns['date'] = 'Date';

			break;
		}
		 
	  return $columns;
	}	
	

	
	// Content of the custom columns for Topics Page
	function customColumnContent($column_name, $post_ID)
	{
		
		switch ($column_name)
		{
			
			case "ek-notes-shortcode":
			
				echo '[ek-notes id='.$post_ID.']';
			
			break;
		}

	}	
	
	
	
	
} //Close class
?>