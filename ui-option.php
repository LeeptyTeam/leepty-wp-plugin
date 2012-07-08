<?php

function leepty_options_init(){
	register_setting('leepty_plugin_options', 'leepty_plugin_options', 'leepty_options_validate' );
############################################
	// Main settings section
	add_settings_section('leepty_main_section', 'Main Settings', 'leepty_main_section_text' , 'leepty_page');
	//Theme Choice
	add_settings_field('leepty_droplist1', 'Select a theme for box', 'leepty_setting_droplist1', 'leepty_page', 'leepty_main_section');
	//View of box
	add_settings_field('leepty_checkbox1', 'Show box after article ?', 'leepty_setting_checkbox1', 'leepty_page', 'leepty_main_section');
	//display of thread
	add_settings_field('leepty_checkbox2', 'display Thread ?', 'leepty_setting_checkbox2', 'leepty_page', 'leepty_main_section');
	//display description
	//add_settings_field('leepty_checkbox3', 'display description ?', 'leepty_setting_checkbox3', 'leepty_page', 'leepty_main_section');
############################################
	// Custom settings section
	add_settings_section('leepty_custom_section', 'Custom', 'leepty_custom_section_text' , 'leepty_page');
	//gradient header background color
	add_settings_field('leepty_input_color1_', 'Chose gradient for the header background color', 'leepty_setting_input_color1_', 'leepty_page', 'leepty_custom_section');
	// font for header's text
	add_settings_field('leepty_droplist2', 'Pick a font for the header\'s text', 'leepty_setting_droplist2', 'leepty_page', 'leepty_custom_section');
	//
	add_settings_field('leepty_setting_color2', 'Chose a background color for the header\'s text', 'leepty_setting_input_color2', 'leepty_page', 'leepty_custom_section');
	//box background color
	add_settings_field('leepty_input_color4', 'Chose a background color for the box', 'leepty_setting_input_color4', 'leepty_page', 'leepty_custom_section');
	//
	add_settings_field('leepty_droplist3', 'Pick a font for the box\'s text', 'leepty_setting_droplist3', 'leepty_page', 'leepty_custom_section');
	//
	add_settings_field('leepty_setting_color3', 'Chose a color for the box\'s text', 'leepty_setting_input_color3', 'leepty_page', 'leepty_custom_section');
	
}

function leepty_main_section_text() {

}

function leepty_custom_section_text() {
	$options = get_option('leepty_plugin_options');
	if ($options['leepty_droplist1'] != "Custom")
	{
		echo "If you wish to customize your box, please select the theme \"Custom\" before making any changes";
	}
}

function leepty_options_validate($input) {
	return $input; // return validated input
}

function leepty_setting_droplist1() {
	$options = get_option('leepty_plugin_options');
	$items = array("Classic" ,"Journal", "Custom");
	echo "\n<select id='leepty_droplist1' name='leepty_plugin_options[leepty_droplist1]'>";
	foreach($items as $item) {
		$selected = ($options['leepty_droplist1']==$item) ? 'selected="selected"' : '';
		echo "\n\t<option value='$item' $selected>$item</option>";
	}
	echo "\n</select>\n";
}

function leepty_setting_checkbox1() {
	$options = get_option('leepty_plugin_options');
	$selected = ($options['leepty_checkbox1']== 1) ? 'checked' : '';
	echo "<input type='checkbox' id='leepty_checkbox1' name='leepty_plugin_options[leepty_checkbox1]' value=1 ".$selected.">";
}

function leepty_setting_checkbox2() {
	$options = get_option('leepty_plugin_options');
	$selected = ($options['leepty_checkbox2']== 1) ? 'checked' : '';
	echo "<input type='checkbox' id='leepty_checkbox2' name='leepty_plugin_options[leepty_checkbox2]' value=1 ".$selected.">";
}

function leepty_setting_checkbox3() {
	$options = get_option('leepty_plugin_options');
	$selected = ($options['leepty_checkbox3']== 1) ? 'checked' : '';
	echo "<input type='checkbox' id='leepty_checkbox3' name='leepty_plugin_options[leepty_checkbox3]' value=1 ".$selected.">";
}

function leepty_setting_input_color1_() {
	$options = get_option('leepty_plugin_options');
	$color1 = $options['leepty_input_color1_1'];
	$color2 = $options['leepty_input_color1_2'];
	
	echo "<input class='color' type='text' id='leepty_input_color1_1' name='leepty_plugin_options[leepty_input_color1_1]' size='6' value='$color1'/>";
	echo "<input class='color' type='text' id='leepty_input_color1_2' name='leepty_plugin_options[leepty_input_color1_2]' size='6' value='$color2'/>";
}

function leepty_setting_input_color2 () {
	$options = get_option('leepty_plugin_options');
	$color = $options['leepty_input_color2'];
	
	echo "<input class='color' type='text' id='leepty_input_color2' name='leepty_plugin_options[leepty_input_color2]' size='6' value='$color'/>";
}

function leepty_setting_droplist2 () {
	$options = get_option('leepty_plugin_options');
	$items = array("Arial", "Calibri", "Comic Sans MS", "Courier", "Garamond", "Georgia", "Helvetica", "Impact", "Monospace", "Palatino", "Times New Roman", "Verdana");
	echo "<select id='leepty_droplist2' name='leepty_plugin_options[leepty_droplist2]'>";
	foreach($items as $item) {
		$selected = ($options['leepty_droplist2']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}

function leepty_setting_input_color3 () {
	$options = get_option('leepty_plugin_options');
	$color = $options['leepty_input_color3'];
	
	echo "<input class='color' type='text' id='leepty_input_color3' name='leepty_plugin_options[leepty_input_color3]' size='6' value='$color'/>";
}

function leepty_setting_droplist3 () {
	$options = get_option('leepty_plugin_options');
	$items = array("Arial", "Calibri", "Comic Sans MS", "Courier", "Garamond", "Georgia", "Helvetica", "Impact", "Monospace", "Palatino", "Times New Roman", "Verdana");
	echo "<select id='leepty_droplist3' name='leepty_plugin_options[leepty_droplist3]'>";
	foreach($items as $item) {
		$selected = ($options['leepty_droplist3']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}

function leepty_setting_input_color4 () {
	$options = get_option('leepty_plugin_options');
	$color = $options['leepty_input_color4'];
	
	echo "<input class='color' type='text' id='leepty_input_color4' name='leepty_plugin_options[leepty_input_color4]' size='6' value='$color'/>";
}

function leepty_add_submenu() {
	add_submenu_page( 'options-general.php', 'Leepty Options Page', 'Leepty Options', 'manage_options', 'leepty_page', 'leepty_add_submenu_callback' );
}

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

function leepty_add_defaults() {
    $arr = array("leepty_droplist1" => "Classic", "leepty_checkbox1" => 0, "leepty_checkbox2" => 0, "leepty_checkbox3" => 0);
    update_option('leepty_plugin_options', $arr);
}

add_action('admin_init', 'leepty_options_init');
add_action('admin_menu', 'leepty_add_submenu');

?>