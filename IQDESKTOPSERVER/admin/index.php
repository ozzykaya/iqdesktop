<?php 
// Get passed GET variables
$do = $_GET["do"];

// Load settings (also includes max cores and max memory)
include("../settings/settings.inc");
$INFOTEXT = file_get_contents("../settings/infotext.inc");
?>

<html>

<head>
    <title>IQdesktopServer Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <h1><?php echo "Multi-User Admin Interface" ?></h1>
    <h2>
        <a href="https://iqdesktop.intiquan.com" target="new">More information</a> 
        | 
        <a href="https://www.intiquan.com" target="new">IntiQuan</a>
    </h2>

    <?php
    // Build the settings form
    ?>
    <h3>Settings</h3>
    <form action="index.php" method="get" id="form1">
    <input type="hidden" name="do" value="updateSettings">
    <button type="submit" form="form1" value="Submit" class="buttonSelectCSV">SAVE</button>

        <td><input type="text" name="safety_check" size="10"></td>


    <button type="submit" form="form1" value="Submit" class="buttonSelectCSV">SAVE</button>
    </form>

   

</body>

</html>