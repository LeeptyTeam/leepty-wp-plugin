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
define('LEEPTY_TABLE_NAME', 'leepty_tags');
define('LEEPTY_TWITER_MAX_KEYWORD', 2);
#######################################################################

if (is_admin()) {
	require_once('ui-option.php');
}

class leepty_wp_plugin{
	
	private static $libs = array(
		'analyzer'		=> 'libs/analyzer/LeeptyAnalyzer.php',
	);


	/**
	* Call when plugin is activate by user.
	*/
	public static function welcome () {
		global $wpdb;

		add_option('leepty_plugin_options');
		add_option('leepty_plugin_db');

		$leepty_tags_table = self::get_table_name();
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
						ENGINE = MyISAM";

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
	* Call when plugin is deactivate 
	*/
	public static function good_bye() {
		global $wpdb;

		//DELETE TABLE
		$leepty_tags_table = self::get_table_name();
		$wpdb->query('DROP TABLE `' . $leepty_tags_table . '`');
		//DELETE OPTION
		delete_option('leepty_plugin_options');
		delete_option('leepty_plugin_db');
		//UNREGISTER SETTING
		unregister_setting('leepty_plugin_options', 'leepty_plugin_options', 'leepty_options_validate');
	}


	/**
	*  retrieve post and meta
	*/
	public static function new_content(){
		$id 	= get_the_ID();
		$tags 	= wp_get_post_tags($id, array('fields'=>'names'));// liste des tags du post
		$cat 	= wp_get_post_categories($id, array('fields'=>'names'));// liste des categories du post
		$meta 	= array_merge($tags, $cat);
		$post 	= get_post($id); // [, $output] avec $output = [OBJECT, ARRAY_A, ARRAY_N]
		
		ob_start();
		
		var_dump($post, $meta);
		$key_words = self::process_analyse($post, $tags);
		$key_words = array_keys($key_words);
		var_dump($key_words);
		self::add_tags($id, $key_words);
		$out = ob_get_clean();
		
		
		$fs=fopen(__DIR__.'/log.html', 'w');
		fwrite($fs, $out);
		fclose($fs);
	}

	/**
	* Insert into leepty_tags_table values $tags with JSON encode
	* associates with the post_id $id
	* 
	* @param integer $id
	* @param array $tags
	*/
	public static function add_tags($id, array $tags) {
		global $wpdb;

		$leepty_tags_table = self::get_table_name();
		$register_tags = self::get_tags($id);
		var_dump('reg',$register_tags);
		$tags = json_encode($tags);
		if(empty($register_tags)){		
			$wpdb->insert($leepty_tags_table, array(
				'post_id' 	=> $id,
				'tags' 		=> $tags
			));
		} else {
			$wpdb->update($leepty_tags_table
					, array('tags' 		=> $tags)
					, array('post_id' 	=> $id)
			);
		}
	}

	/**
	 * Read db where the post_id = $id and return tags with JSON encode
	 * and can return the row as an object, an associative array, or as
	 * a numerically indexed array
	 * 
	 * @param integer $id
	 * @param string $output_type = "OBJECT"
	 * 
	 * @return stdClass
	 */
	public static function get_tags($id, $as_json = true) {
		global $wpdb;

		$leepty_tags_table = self::get_table_name();
		$result = $wpdb->get_row("SELECT tags FROM " . $leepty_tags_table . " WHERE post_id = " . $id);
		$tags = $result->tags;
		if($as_json) return $tags;
		else return json_decode($tags);

	}
	
	/**
	 * @param string $content
	 *
	 * Add a div after article 
	 * 
	 */
	public static function add_box_div($content) {
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
	public static function insert_thread() {
		if (!is_single()) return;
		
		$id			= get_the_ID();
		$plugin_url = plugin_dir_url(__FILE__);
		$tags		= self::get_tags($id);
		
		include 'templates/include_post_head.php';
	}


	public static function js_import() {
		if (is_single()) {
			wp_register_script( 'leepty-js-helpers', plugins_url('js/LeeptyJSHelpers.js', __FILE__) );
			wp_enqueue_script( 'leepty-js-helpers' );
			wp_register_style( 'leepty-style', plugins_url('css/styles.php', __FILE__) );
			wp_enqueue_style( 'leepty-style' );
		}
	}
	
	public static function js_admin_import(){
		wp_register_script( 'leepty-js', plugins_url('js/jscolor.js', __FILE__) );
		wp_enqueue_script( 'leepty-js' );
	}


	private static function get_table_name(){
		global $wpdb;
		return $wpdb->prefix.LEEPTY_TABLE_NAME;
	}
	
	/**
	 * Analyze the given post and return the result.
	 * @param stdClass $post
	 * @return array 
	 */
	private static function process_analyse($post, $tags){
		if(!self::lib_import('analyzer')) return false;
		
		$title		= (string)$post->post_title;
		$text		= (string)$post->post_content;
		$locales	= explode('_', get_locale());
		$lang		= $locales[0];
		
		$t1 =  floatval(microtime());
		/* @var $analyzer LeeptyAnalyzer */
		$analyzer = leeptyAnalyzer();
		$dictionary = CommonDictionary::getDictionary($lang, true);
		
		$analyzer->setDictionary($dictionary);
		
		$analyzer->setTitle($title, 2);
		$analyzer->setText($text);
		
		$result = $analyzer->fireAnalyse();
		$t2 = floatval(microtime());
		var_dump($result, ($t2-$t1));
		
		$key_word = array_slice($result, 0, LEEPTY_TWITER_MAX_KEYWORD);
		
		var_dump($key_word);
		return $key_word;
	}
	
	private static function lib_import($lib){
		if(!isset(self::$libs[$lib])) return false;
		$path = dirname(__FILE__);
		$path = realpath($path.'/'.self::$libs[$lib]);
		require_once $path;
		return true;
	}
}

################################################

register_activation_hook( __FILE__, 'leepty_wp_plugin::welcome' );
register_deactivation_hook(__FILE__, 'leepty_wp_plugin::good_bye');
add_action('publish_post', 'leepty_wp_plugin::new_content');
add_action('wp_head', 'leepty_wp_plugin::insert_thread');
add_action( 'wp_enqueue_scripts', 'leepty_wp_plugin::js_import' );
add_action( 'admin_enqueue_scripts', 'leepty_wp_plugin::js_admin_import' );

add_filter('the_content', 'leepty_wp_plugin::add_box_div');
?>