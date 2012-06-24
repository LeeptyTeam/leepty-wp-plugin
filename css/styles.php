<?php
header('Content-type: text/css');
require_once('../../../../wp-load.php');
$options = get_option('leepty_plugin_options');

if ($options['leepty_dropdown1'] != "Custom")
{
	if (file_exists(dirname(__FILE__)."/".strtolower($options['leepty_dropdown1']).".css"))
	{
		include(dirname(__FILE__)."/".strtolower($options['leepty_dropdown1']).".css");
	}
	else
	{
		include(dirname(__FILE__)."/classic.css");
	}
	
}
else
{
	echo "
	@font-face {
	  font-family: 'Lobster Two';
	  font-style: italic;
	  font-weight: 700;
	  src: local('Lobster Two Bold Italic'), local('LobsterTwo-BoldItalic'), url('http://themes.googleusercontent.com/static/fonts/lobstertwo/v4/LEkN2_no_6kFvRfiBZ8xpARV2F9RPTaqyJ4QibDfkzM.woff') format('woff');
	}
	@font-face {
	  font-family: 'Lobster Two';
	  font-style: normal;
	  font-weight: 400;
	  src: local('Lobster Two'), local('LobsterTwo'), url('http://themes.googleusercontent.com/static/fonts/lobstertwo/v4/Law3VVulBOoxyKPkrNsAaIbN6UDyHWBl620a-IRfuBk.woff') format('woff');
	}";
	
	echo "
	* {
		border:0;
		font:inherit;
		font-size:100%;
		margin:0;
		padding:0;
		vertical-align:baseline;
		font-family: \"Lucida Sans Unicode\", \"Lucida Grande\", Helvetica, Arial, Verdana, sans-serif;
	}";
	
	echo "
	.container {
		width : 585px;
	}";
	
	$color1 = $options['leepty_setting_headbox_color0'];
	$color2 = $options['leepty_setting_headbox_color1'];
	
	echo "
	.header p {
		padding: 10px;
		background: #555;
		color: #DDD;
		margin-bottom:0;
		font-family: 'Lobster Two', cursive;
		font-size: 20px;
		background: linear-gradient(bottom, $color1, $color2);
		background: -o-linear-gradient(bottom, $color1, $color2);
		background: -moz-linear-gradient(bottom, $color1, $color2);
		background: -webkit-linear-gradient(bottom, $color1, $color2);
		background: -ms-linear-gradient(bottom, $color1, $color2);
		border-radius: 5px 5px 0 0;
	}";
	
		$font = $options['leepty_dropdown2'];
	if (strstr($font, " "))
	{
		$font = "\"".$font."\"";
	}
		$color3 = $options['leepty_setting_in_box_color0'];
	echo "
	.row {
		padding: 10px 0 10px 0;
		background: $color3;
		border-left: 1px solid #bcbcbc;
		border-right: 1px solid #bcbcbc;
		font-family: $font, \"Lucida Grande\", Helvetica, Arial, Verdana, sans-serif;
	}";
	
	echo "
	.row:hover {
		background: #eee;
		border-right: 7px solid #bcbcbc;
	}";
	
	echo "
	.row .title>a {
		text-decoration: none;
		display: block;
		padding-left: 20px;
		color: #888;
		margin-bottom: -10px;
		font-size: 14px;
		text-shadow: 0px 1px 1px #FFF;
		font-weight: bold;
	}";
	
	echo "
	.row .title>a:hover, .row .title>a:focus {
		color: #555;
	}
	";
	
	echo "
	.r4 {
		border-radius: 0 0 5px 5px;
		border-bottom: 1px solid #bcbcbc;
	}";
	
	
	echo "
	.row p {
		margin : 0;
		padding-left : 10px;
		color : #777;
		font-size : 12px;
	}";
}