<?php
/*
Plugin Name: EduKit Notes
Plugin URI: http://www.edu-kit.org
Description: Allows students to create notes on specific pages, or all page types
Version: 0.1
Author: Alex Furr & EduKit
*/

// Set Defaults and Version
$ek_notes_version = 0.1;


$ek_notes_path = dirname(__FILE__);

// Global defines
define( 'EK_NOTES_PLUGIN_URL', plugins_url('edukit-notes' , dirname( __FILE__ )) );
define( 'EK_NOTES_PATH', plugin_dir_path(__FILE__) );

include_once( $ek_notes_path . '/functions.php');
include_once( $ek_notes_path . '/classes/class-draw.php');
include_once( $ek_notes_path . '/classes/class-cpts.php' );
include_once( $ek_notes_path . '/classes/class-ajax.php');
include_once( $ek_notes_path . '/classes/class-database.php');
include_once( $ek_notes_path . '/classes/class-utils.php');



?>