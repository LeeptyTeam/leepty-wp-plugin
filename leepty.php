<?php
/*
Plugin Name: Leepty - Twitter feed
Plugin URI: http://leepty.com/
Description: This plugin shows your readers related pages in a great looking box. You can even go beyond and customize what the box looks like, so you can make it your very own.
Version: 0.1
Author: KLK1 sf89 Techniv
License: GPL2
*/

#######################################################################
// define constants
define('LEEPTY_VERSION', '0.1');
#######################################################################

if (is_admin()) {
	require_once('ui-option.php');
}

/**
* Call when plugin is activate by user.
*/
function leepty_welcome () {
	global $wpdb;

	add_option('leepty_plugin_options');
	add_option('leepty_plugin_db');

	$leepty_tags_table = $wpdb->prefix.'leepty_tags';
	$sql = "CREATE  TABLE IF NOT EXISTS `".$leepty_tags_table."` (
  `post_id` BIGINT(20) UNSIGNED NOT NULL ,
  `tags` VARCHAR(255) NULL COMMENT 'JSON' ,
  INDEX `FK_POST_ID` (`post_id` ASC) ,
  PRIMARY KEY (`post_id`) ,
  CONSTRAINT `FK_POST_ID`
    FOREIGN KEY (`post_id` )
    REFERENCES `wp_posts` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
	ENGINE = InnoDB";
	
	//for the next function
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//execute query $sql
	dbDelta($sql);
	// table is create
	update_option("leepty_plugin_db", "true");
	// store settings default values
	leepty_add_defaults();
}

/**
*
* Call when plugin is deactivate 
*  
*/
function leepty_good_bye() {
	global $wpdb;

	//DELETE TABLE
	$leepty_tags_table = $wpdb->prefix.'leepty_tags';
	$wpdb->query('DROP TABLE `' . $leepty_tags_table . '`');
	//DELETE OPTION
	delete_option('leepty_plugin_options');
	delete_option('leepty_plugin_db');
	//UNREGISTER SETTING
	unregister_setting('leepty_plugin_options', 'leepty_plugin_options', 'leepty_options_validate');
}

/**
 * 
 *  retrieve post and meta
 *
 */
function leepty_new_content(){
	$id 	= get_the_ID();
	$tag 	= wp_get_post_tags($id, array('fields'=>'names'));// liste des tags du post
	$cat 	= wp_get_post_categories($id, array('fields'=>'names'));// liste des categories du post
	$meta 	= array_merge($tag, $cat);
	$post 	= get_post($id); // [, $output] avec $output = [OBJECT, ARRAY_A, ARRAY_N]

	/*
	%INSERT HERE PROCESSING FUNCTION%
	*/
}

/**
 * 
 * @param integer $id
 * @param array $tags
 * 
 * Insert into leepty_tags_table values $tags with JSON encode
 * associates with the post_id $id
 * 
 */
 function add_tags($id, array $tags) {
 	global $wpdb;

 	$leepty_tags_table = $wpdb->prefix.'leepty_tags';
 	$wpdb->insert($leepty_tags_table, array(
 		'post_id' 	=> $id,
 		'tags' 		=> json_encode($tags)
 		));
 }

/**
 * 
 * @param integer $id
 * @param string $output_type = "OBJECT"
 * 
 * @return Defaults to OBJECT
 * 
 * Read db where the post_id = $id and return tags with JSON encode
 * and can return the row as an object, an associative array, or as
 * a numerically indexed array
 * 
 */
 function read_tags($id, $output_type="OBJECT") {
 	global $wpdb;

 	$leepty_tags_table = $wpdb->prefix.'leepty_tags';
 	return $wpdb->get_row("SELECT tags FROM " . $leepty_tags_table . " WHERE link_id = " . $id, $output_type);

 }

/**
* @param string $content
*
* Add a div after article 
* 
*/
function leepty_add_box_div($content) {
	$options = get_option('leepty_plugin_options');
	$box = $options['leepty_checkbox2'];
	if (is_single() && $box) {
		$content .= "\n<div id=\"leepty\"></div>\n";
	}
	return $content;
}

/**
*  
* Insert the script when on the head
* 
*/
function leepty_add_js () {
	if (is_single()) {
		echo "\n<script>";
		echo "\nvar leeptyOption = {
			basePath: '".plugin_dir_url(__FILE__)."',
			moduleSettings:{
				LeeptyWidget:{
					template: 'sidebar',
					widgetBasePath: '".plugin_dir_url(__FILE__)."'
				},
				
				LeeptyClient: {
					pageLink: 'http://www.google.com'
				}
			}
		}
		
		
		var data = JSON.parse('NULL');
		console.log(data);
		LeeptyHelpers.config(leeptyOption);
		LeeptyHelpers.initLeeptyDependency();";
		echo "\n</script>\n";
		
		
	}
}

function leepty_import() {
	if (is_admin()) {
		wp_register_script( 'leepty-js', plugins_url('js/jscolor.js', __FILE__) );
		wp_enqueue_script( 'leepty-js' );
	}
	if (is_single()) {
		wp_register_script( 'leepty-js-helpers', plugins_url('js/LeeptyJSHelpers.js', __FILE__) );
		wp_enqueue_script( 'leepty-js-helpers' );
		wp_register_style( 'leepty-style', plugins_url('css/styles.php', __FILE__) );
		wp_enqueue_style( 'leepty-style' );
	}
}

################################################

register_activation_hook( __FILE__, 'leepty_welcome' );
register_deactivation_hook(__FILE__, 'leepty_good_bye');
add_action('publish_post', 'leepty_new_content');
add_action('wp_head', 'leepty_add_js');
add_action( 'wp_enqueue_scripts', 'leepty_import' );
add_action( 'admin_enqueue_scripts', 'leepty_import' );

add_filter('the_content', 'leepty_add_box_div');
?>