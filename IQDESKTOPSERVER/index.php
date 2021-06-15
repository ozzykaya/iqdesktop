<?php
// Load settings (also includes max cores and max memory)
if (file_exists("settings/settings.inc")) {
    include("settings/settings.inc");
} else {
    include("settings/settings_default.inc");
}
?>
<html>

<head>
<title><?php echo $SERVER_NAME; ?></title>
    <link rel="icon" href="images/favIQ.png">
	<frameset cols="100%">
        <frame src="main.php" frameborder="0">
    </frameset>
</head>

</html>