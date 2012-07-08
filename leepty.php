<?php
/*
Plugin Name: Related Posts - Beautiful & Customizable
Plugin URI: http://leepty.com/
Description: This plugin shows your readers related pages in a great looking box. You can even go beyond and customize what the box looks like, so you can make it your very own.
Version: 0.7
Author: KLK1 sf89
License: GPL2
*/

#######################################################################
// define constants
define('LEEPTY_URL_TEMPLATE', 'share.leepty.com/');
define('LEEPTY_RECEIVER', 'http://share.leepty.com/receiver.php');
define('LEEPTY_VERSION', '0.7');
#######################################################################

if (is_admin()) {
	require_once('ui-option.php');
}
/**
* Call when plugin is activate by user.
* Send All permalink of posts and pages of blog
*/
function leepty_welcome () {
	add_option('leepty_plugin_options');
	leepty_add_defaults();
	$data 		= array();
	$num_posts 	= wp_count_posts();
	$num 		= $num_posts->publish;
	$num_page 	= wp_count_posts('page');
	$num 		+= $num_page->publish;
	$pages 		= ceil($num/1500)-1;
	$i			=0;
	$ipage		=0;
	$post_per_page = 1500;
	$data['lenght']=$num;
	$data['version']=LEEPTY_VERSION;
	while ($ipage <= $pages) {
		if ($ipage == ($pages)) {
			$post_per_page= $num - ($post_per_page*$ipage);
		}
		$args = array( 'post_status' => 'publish', 'post_type' => array( 'post', 'page'), 'posts_per_page' => $post_per_page, 'paged'=>$ipage );
		query_posts($args);		 
		while (have_posts()) {
			the_post();
			$i++;
			$current = "link".$i;
			$data[$current]=get_permalink();
		}
		$ipage++;
	}
	wp_reset_query();
	
	leepty_sending_content($data);
}

/**
* Call for each new article or page is creating 
*/
function leepty_new_content() {
	$data = array();
	$data['link1']=get_permalink(get_the_ID());
	leepty_sending_content($data);	
}

/**
* @param array $data
* @param string $url
* 
* Send $data on $url using http_api of WP
* 
*/
function leepty_sending_content(array $data, $url=LEEPTY_RECEIVER) {
	if (!empty($data)) {
		$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 90,
			'redirection' => 0,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => $data,
			'cookies' => array()
		   	)
		);
		foreach ($response as $key=>$values) {
			$sending .="\n".$key." ".$values;
		}
		$sending .="\n#######################################\n";
		foreach ($data as $key=>$values) {
			$sending .="\n".$key." ".$values;
		}
		wp_mail('lebrun.kevin93@gmail.com', 'The subject', ''.$sending.'');	
	}	
}

/**
* @param  $content
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
* @param $id
* 
* generate related post by category of this blog 
* 
*/
function leepty_gen_local_result($id) {
	$posts_query = $GLOBALS['wpdb']->get_results(sprintf("SELECT DISTINCT object_id as id, LEFT(post_content, 100) AS excerpt, post_title FROM {$GLOBALS['wpdb']->term_relationships} r, {$GLOBALS['wpdb']->term_taxonomy} t, {$GLOBALS['wpdb']->posts} p WHERE t.term_id IN (SELECT t.term_id FROM {$GLOBALS['wpdb']->term_relationships} r, {$GLOBALS['wpdb']->term_taxonomy} t WHERE r.term_taxonomy_id = t.term_taxonomy_id AND t.taxonomy = 'category' AND r.object_id = $id) AND r.term_taxonomy_id = t.term_taxonomy_id AND p.post_status = 'publish' AND p.ID = r.object_id AND object_id <> $id"),ARRAY_A);
	if ($posts_query) {
		$i=1;
		foreach ($posts_query as $value) {
			if ($i > -1)
				break;
			$post = array(
				'title'		=> esc_attr($value['post_title']),
				'url'		=> esc_url(get_permalink($value['id'])),
				'sample'	=> esc_attr($value['excerpt'])
			);
			$posts[] = $post;
			$i++;
		}
		$json = json_encode(array('posts' => $posts));
		return $json;
	}
}

/**
*
* Call when plugin is deactivate 
*  
*/
function leepty_goodbye() {
	delete_option('leepty_plugin_options');
	unregister_setting('leepty_plugin_options', 'leepty_plugin_options', 'leepty_options_validate');
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
		
		
		var data = JSON.parse('".leepty_gen_local_result(get_the_ID())."');
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
//register_deactivation_hook(__FILE__, 'leepty_good_bye');
add_action('publish_post', 'leepty_new_content');
add_action('publish_page', 'leepty_new_content');
add_action('wp_head', 'leepty_add_js');
add_action( 'wp_enqueue_scripts', 'leepty_import' );
add_action( 'admin_enqueue_scripts', 'leepty_import' );

add_filter('the_content', 'leepty_add_box_div');
?>