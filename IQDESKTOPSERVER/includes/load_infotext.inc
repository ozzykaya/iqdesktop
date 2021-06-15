<?php
// Prefix for folder can be defined
if (!isset($prefix_folder)) {
	$prefix_folder = ".";
}
// Load infotext
$INFOTEXTDEFAULT = file_get_contents($prefix_folder."/settings/infotext_default.inc");
if (file_exists($prefix_folder."/settings/infotext.inc")) {
    $INFOTEXT = file_get_contents($prefix_folder."/settings/infotext.inc");
} else {
    $INFOTEXT = $INFOTEXTDEFAULT;
}
// Set default info text if empty
if (empty($INFOTEXT)) {
    $INFOTEXT = $INFOTEXTDEFAULT;
}
?>