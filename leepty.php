<?php
/*
Plugin Name: Related Posts - Beautiful & Customizable
Plugin URI: http://leepty.com
Description: This plugin shows your readers related pages in a great looking box. You can even go beyond and customize what the box looks like, so you can make it your very own.
Version: 0.6-nw
Author: KLK1 sf89
License: GPL2
*/

#######################################################################
#       Code temporaire le temps de la mise a jour du back-end        #
#######################################################################
// define constants
define('URL_TEMPLATE', 'share.leepty.com/');
define('GETTER_URL', 'http://share.leepty.com/getter.php');
define('leepty_VERSION', '0.6-nw');
#######################################################################

/**
 * Ajoute l'iframe a sous l'article. le rendu
 * est different en fonction du theme
 *
 * @param string $content
 * @return string
 */
function leepty_content($content) {
if (is_single())
	{
		//TODO Choose methode from configuration.
		$content .= get_leepty_widget(get_the_ID());
	}
	return $content;
}

function tronquer($texte, $max_caracteres) {
	if (strlen($texte)>$max_caracteres){
		$texte = substr($texte, 0, $max_caracteres);
		$position_espace = strrpos($texte, " ");
		$texte = substr($texte, 0, $position_espace);
		$texte .= "...";
	}
	return $texte;
}

function leepty_get_internal_related($id){
	return $GLOBALS['wpdb']->get_results(sprintf(
			"SELECT DISTINCT object_id as ID, post_title 
			FROM {$GLOBALS['wpdb']->term_relationships} r, {$GLOBALS['wpdb']->term_taxonomy} t, {$GLOBALS['wpdb']->posts} p 
			WHERE t.term_id IN (SELECT t.term_id FROM {$GLOBALS['wpdb']->term_relationships} r, {$GLOBALS['wpdb']->term_taxonomy} t 
			WHERE r.term_taxonomy_id = t.term_taxonomy_id AND t.taxonomy = 'category' 
				AND r.object_id = $id) AND r.term_taxonomy_id = t.term_taxonomy_id 
				AND p.post_status = 'publish' AND p.ID = r.object_id AND object_id <> $id"
			),OBJECT);
}

function leepty_get_box($id) {
	$posts = leepty_get_internal_related($id);
		if ($posts)
		{
			$i = 1;
			$output =  '
	<div id="leepty"><div class="container">
      <div class="border">
        <div class="header">
          <p>Related Pages</p>
        </div>
';
			foreach ($posts as $post)
			{
				if ($i > 4){
				break;
				}
				$title = esc_attr($post->post_title);
				$rel = (!empty($params['rel']) ? (' rel="' .esc_attr($params['rel']). '"') : '');
				$hidden = (isset($params['hidden']) && $params['hidden'] == 'title' ? '' : $title);
				$inside = (isset($params['inside']) ? $params['inside'] : '');
				$outside = (isset($params['outside']) ? $params['outside'] : '');
				$before = (isset($params['before']) ? $params['before'] : '');
				$after = (isset($params['after']) ? $params['after'] : '');
				$output .= sprintf(
				"\t<div class=\"row r".$i."\">\n\t\t<div class=\"sr".$i."\"></div>\n\t\t<p>\n\t\t\t<span class=\"title t".$i."\"><a href=\"%s\" target=\"_blank\">%s</a></span><br />\n\t\t</p>\n\t</div>\n\t<div class=\"split\"></div>\n",
				esc_url(get_permalink($post->ID)),
				tronquer($title, 50)
				);
				$i++;
			}
			$output .='</div></div></div>';
			return $output;
		}
	}
	
function get_leepty_widget($id) {
	$posts = leepty_get_internal_related($id);
	
	$data = array();
	foreach ($posts as $post){
		$data[] = array(
			'title' => $post->post_title,
			'url'	=> get_permalink($post->ID)
		);
	}
	
	$data = array('posts' => $data);
	$json = json_encode($data);
	
	$request = preg_replace("#([\?\#].*)$#",'',$_SERVER['REQUEST_URI']);
	$widgetPath = $request.'wp-content/plugins/Leepty/';
	
	ob_start();?>
	<script type="text/javascript">
		var leeptyOption = {
			basePath: '<?php echo $widgetPath; ?>',
			moduleSettings:{
				LeeptyWidget:{
					template: 'sidebar',
					widgetBasePath: '<?php echo $widgetPath; ?>'
				},
				
				LeeptyClient: {
					pageLink: 'http://www.google.com'
				}
			}
		}
		
		
		var data = JSON.parse('<?php echo $json; ?>');
		console.log(data);
		LeeptyHelpers.config(leeptyOption);
		LeeptyHelpers.initLeeptyDependency();
		
	</script>
	<? 
	$out = ob_get_clean();
	
	return $out;
}
/**
 * Envoi l'URL de chaque nouvel article du blog vers notre serveur
 * @todo USE HTTP API
 * 
 */
function leepty_sendURL() {
	
	$POST['link1']=get_permalink(get_the_ID());
		
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, GETTER_URL);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_HEADER, false);
	curl_setopt($c, CURLOPT_POST,true);
	curl_setopt($c, CURLOPT_POSTFIELDS,$POST);
	$sortie = curl_exec($c);
	curl_close($c);
	
}

/**
 * Envoi les URLS du blog vers notre serveur
 * Sur l'adresse GETTER_URL
 * @todo USE HTTP API
 * 
 */
function leepty_plugin_activate() {

	leepty_add_defaults();
	$num_posts = wp_count_posts( 'post' );
	$num = $num_posts->publish;
	$num_page = wp_count_posts('page');
	$num =$num + $num_page->publish;
	$pages = ceil($num/1500);
	$i=0;
	$ipage=0;
	$post_per_page = 1500;
	$POST['test']=$num;
	$POST['version']=LBONSTERVERSION;
	while ($ipage < $pages) {
		if ($ipage == ($pages-1)) {
			$post_per_page= $num - ($post_per_page*$ipage);
		}
		$args = array( 'post_status' => 'publish', 'post_type' => array( 'post', 'page'), 'posts_per_page' => $post_per_page, 'paged'=>$ipage );
		query_posts($args);		 
		while ( have_posts() ) : the_post();
	
			$i++;
			$current = "link".$i;
			$POST[$current]=get_permalink();
		endwhile;
		$ipage++;
	}
	wp_reset_query();
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, GETTER_URL);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_HEADER, false);
	curl_setopt($c, CURLOPT_POST, true);
	curl_setopt($c, CURLOPT_POSTFIELDS, $POST);
	$sortie = curl_exec($c);
	curl_close($c);
}

/**
 * Ajoute les sous menu leepty_share dans le menu tools
 *
 */
function leepty_add_submenu() {
	add_submenu_page( 'options-general.php', 'Leepty Options Page', 'Leepty Options', 'manage_options', 'leepty_page', 'leepty_add_submenu_callback' );
}

/**
 * Callback de l'appel leepty_add_submenu
 *
 */
function leepty_add_submenu_callback() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Options Page</h2>
		<form action="options.php" method="post">
		<?php settings_fields('leepty_plugin_options'); ?>
		<?php do_settings_sections('leepty_page'); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

/**
 * Menu
 *
 */
function leepty_options_init(){
	register_setting('leepty_plugin_options', 'leepty_plugin_options', 'leepty_options_validate' );
	add_settings_section('leepty_main_section', 'Main Settings', 'leepty_main_section_text' , 'leepty_page');
	add_settings_field('leepty_dropdown1', 'Select a theme', 'leepty_setting_dropdown1', 'leepty_page', 'leepty_main_section');
	
	add_settings_section('leepty_custom_section', 'Custom Theme', 'leepty_custom_section_text' , 'leepty_page');
	add_settings_field('leepty_setting_headbox_color', 'Chose gradient for the header', 'leepty_setting_headbox_color', 'leepty_page', 'leepty_custom_section');
	add_settings_field('leepty_dropdow3', 'Pick a font for the header\'s text', 'leepty_setting_dropdown3', 'leepty_page', 'leepty_custom_section');
	add_settings_field('leepty_dropdown2', 'Pick a font for the box\'s text', 'leepty_setting_dropdown2', 'leepty_page', 'leepty_custom_section');
	add_settings_field('leepty_setting_in_box_color', 'Chose a background color for the box', 'leepty_setting_in_box_color', 'leepty_page', 'leepty_custom_section');

}

/**
 * Choix du theme
 * @todo Liste dynamique des themes
 *
 */
function  leepty_setting_dropdown1() {
	$options = get_option('leepty_plugin_options');
	$items = array("Classic" ,"Journal", "Custom");
	echo "<select id='leepty_dropdown1' name='leepty_plugin_options[leepty_dropdown1]'>";
	foreach($items as $item) {
		$selected = ($options['leepty_dropdown1']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}

function  leepty_setting_dropdown2() {
	$options = get_option('leepty_plugin_options');
	$items = array("Arial", "Calibri", "Comic Sans MS", "Courier", "Garamond", "Georgia", "Helvetica", "Impact", "Monospace", "Palatino", "Times New Roman", "Verdana");
	echo "<select id='leepty_dropdown2' name='leepty_plugin_options[leepty_dropdown2]'>";
	foreach($items as $item) {
		$selected = ($options['leepty_dropdown2']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}

function  leepty_setting_dropdown3() {
	$options = get_option('leepty_plugin_options');
	$items = array("Arial", "Calibri", "Comic Sans MS", "Courier", "Garamond", "Georgia", "Helvetica", "Impact", "Monospace", "Palatino", "Times New Roman", "Verdana");
	echo "<select id='leepty_dropdown2' name='leepty_plugin_options[leepty_dropdown2]'>";
	foreach($items as $item) {
		$selected = ($options['leepty_dropdown3']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}

function  leepty_setting_headbox_color() {
	$options = get_option('leepty_plugin_options');
	$color1 = $options['leepty_setting_headbox_color0'];
	$color2 = $options['leepty_setting_headbox_color1'];
	
	echo "<input class='color' type='text' id='leepty_setting_headbox_color' name='leepty_plugin_options[leepty_setting_headbox_color0]' size='6' value='$color1'/>";
	echo "<input class='color' type='text' id='leepty_setting_headbox_color' name='leepty_plugin_options[leepty_setting_headbox_color1]' size='6' value='$color2'/>";
}

function  leepty_setting_in_box_color() {
	$options = get_option('leepty_plugin_options');
	$color = $options['leepty_setting_in_box_color0'];
	
	echo "<input class='color' type='text' id='leepty_setting_in_box_color0' name='leepty_plugin_options[leepty_setting_in_box_color0]' size='6' value='$color'/>";
}

/**
 * Valide l'entree $input qu'elle recoit
 *
 * @param string $input
 * @return string
 */
function leepty_options_validate($input) {
	// Check our textbox option field contains no HTML tags - if so strip them out
	$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);	
	return $input; // return validated input
}

/**
 * @todo this
 *
 */
function leepty_main_section_text() {

}
function leepty_custom_section_text() {
	$options = get_option('leepty_plugin_options');
	if ($options['leepty_dropdown1'] != "Custom")
	{
		echo "If you wish to customize you box, please select the theme \"Custom\" before making any changes";
	}
}

/**
 * Initialise les options a defaut
 *
 */
function leepty_add_defaults() {
    $arr = array("leepty_dropdown1"=>"Classic");
    update_option('leepty_plugin_options', $arr);
}

/**
 * Supprime les options lors de la desactivation..
 *
 */
function leepty_good_bye() {
	delete_option('leepty_plugin_options');
	unregister_setting('leepty_plugin_options', 'leepty_plugin_options', 'leepty_options_validate');
}

/**
* Enqueue plugin style-file
*/
function leepty_add_stylesheet() {
	// Respects SSL, Style.css is relative to the current file
	wp_register_style( 'leepty-style', plugins_url('css/styles.php', __FILE__) );
	wp_enqueue_style( 'leepty-style' );
	wp_register_script( 'leepty-js', plugins_url('js/jscolor.js', __FILE__) );
	wp_register_script( 'leepty-js-helpers', plugins_url('js/LeeptyJSHelpers.js', __FILE__) );
	wp_enqueue_script( 'leepty-js' );
	wp_enqueue_script( 'leepty-js-helpers' );
    }

##############################################

register_activation_hook( __FILE__, 'leepty_plugin_activate' );
register_deactivation_hook(__FILE__, 'leepty_good_bye');

add_action('publish_post', 'leepty_sendURL');
add_action('publish_page', 'leepty_sendURL');

add_action('admin_init', 'leepty_options_init');
add_action('admin_menu', 'leepty_add_submenu');

add_filter('the_content', 'leepty_content'); 

add_action( 'wp_enqueue_scripts', 'leepty_add_stylesheet' );
add_action( 'admin_enqueue_scripts', 'leepty_add_stylesheet' );

?>