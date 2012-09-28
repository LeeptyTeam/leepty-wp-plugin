<?php
	//$plugin_url = plugin_dir_url(__FILE__);
?>

<script>
	var leeptyOption = {
		basePath: '<?php echo $plugin_url; ?>',
		moduleSettings:{
			LeeptyWidget:{
				template: 'sidebar',
				widgetBasePath: '<?php echo $plugin_url; ?>'
			},

			LeeptyTwitterClient: {
				tags: JSON.parse('<?php echo $tags; ?>')
			}
		}
	}


//	var data = JSON.parse('NULL');
//	console.log(data);
	LeeptyHelpers.config(leeptyOption);
	LeeptyHelpers.initLeeptyDependency();
</script>