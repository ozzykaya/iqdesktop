<?php
// Prefix for folder can be defined
if (!isset($prefix_folder)) {
	$prefix_folder = ".";
}
// Load settings 
if (file_exists($prefix_folder."/settings/settings.inc")) {
    include($prefix_folder."/settings/settings.inc");
} else {
    include($prefix_folder."/settings/settings_default.inc");
}
?>