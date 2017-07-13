<?php


$ekNotesDB = new ekNotesDB();

// Define the table as a global


class ekNotesDB {
	
	//~~~~~
	function __construct ()
	{
		
		global $wpdb;
		global $ekNotesTable;		
		$ekNotesTable = $wpdb->prefix . 'ek_notes';
		
		register_activation_hook( __FILE__, array( $this, 'databaseInit' ) );
		add_action( 'plugins_loaded', array($this, 'myplugin_update_db_check' ) );
		
		$this->installDB();
		
		
	}	
	
	function databaseInit()
	{
		global 	$ek_notes_version;	
		add_option( 'ek_notes_version', $ek_notes_version );
		$this->installDB();
	}
		
	
	
	//~~~~~
	function installDB()
	{
		

		// Get plugin version and set option fo current version
		global $wpdb;
		global $ekNotesTable;
		
		$sql = "CREATE TABLE $ekNotesTable (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            noteContent text,
            postID bigint(20) NOT NULL,
            userID bigint(20) NOT NULL,
            createDate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            modifiedDate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY id (id)
        );";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
	}
	
	
	// Function to check latest evrsino and then update DB if needed
	function myplugin_update_db_check()
	{
		global $ek_notes_version;
		if ( get_option( 'ek_notes_version' )< $ek_notes_version )
		{

			// Update version op
			update_option( 'ek_notes_version', $ek_notes_version );
			$this->installDB();
		}
	}
	
	
	
	/**
	 * Gets one row from table by post id and user id
	 * @param int $post_id
	 * @param int $user_id
	 * @return stdClass data set
	 */
	public static  function getNote( $postID, $userID )
	{
		global $wpdb;	
		global $ekNotesTable;	
		$query = $wpdb->prepare( "SELECT * FROM $ekNotesTable WHERE postID= %d AND userID = %d", $postID, $userID );	
		$noteInfo = $wpdb->get_row($query, ARRAY_A);
		
		return $noteInfo;
	}

	/**
	 * Creates or updates entry in table depending if note already exists
	 * @param array $note
	 */
	public function save( $note )
	{
		$entry = ekNotesDB::getNote( $note['postID'], $note['userID'] );
		if ( $entry ) {
			ekNotesDB::update( $entry, $note );
		} else {
			ekNotesDB::insert( $note );
		}
	}

	/**
	 * Inserts new entry into table.
	 * @param array $note
	 */
	public function insert( $note )
	{
		global $wpdb;
		global $ekNotesTable;
		
		//Insert the user note 
		$myFields="INSERT into ".$ekNotesTable." (userID, postID, noteContent, createDate, modifiedDate) ";
		$myFields.="VALUES (%u, %u, '%s', '%s', '%s')";	
		
		$RunQry = $wpdb->query( $wpdb->prepare($myFields,
			$note['userID'],
			$note['postID'],
			wp_kses_data($note['noteContent']),
			current_time( 'mysql' ),
			current_time( 'mysql' )
		));		


	}

	/**
	 * Updates entry with new data from note.
	 *
	 * @param object $entry
	 * @param array $note
	 */
	public function update( $entry, $note )
	{
		
		global $wpdb;
		global $ekNotesTable;
		
		$myFields ="UPDATE ".$ekNotesTable." SET ";
		$myFields.="noteContent='%s', ";
		$myFields.="modifiedDate='%s' ";
		$myFields.="WHERE id=%u";	

		$RunQry = $wpdb->query( $wpdb->prepare($myFields,
			$note['noteContent'],
			current_time( 'mysql' ),
			$entry['id']
		));		
		
		
		
	}

	/**
	 * Deletes entry from table found by $note
	 * @param array $note
	 */
	public function delete( $note )
	{
		$entry = $this->get_entry( $note['postID'], $note['userID'] );
		if ( $entry ) {
			$where        = array( 'id' => $entry->id );
			$where_format = array( '%d' );

			$this->_wpdb->delete( $ekNotesTable, $where, $where_format );
		}
	}

	
}
?>