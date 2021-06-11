<?php
// Load settings (also includes max cores and max memory)
if (file_exists("settings/settings.inc")) {
    include("settings/settings.inc");
} else {
    include("settings/settings_default.inc");
}
if (file_exists("settings/infotext.inc")) {
    $INFOTEXT = file_get_contents("settings/infotext.inc");
} else {
    $INFOTEXT = file_get_contents("settings/infotext_default.inc");
}

// Handle setting flags
$privileged = "FALSE";
if ($PRIVILEGED) {
    $privileged = "TRUE";
}

// Define displayed column names in table
$NAME_TH_TEXT = "Name";
$USER_TH_TEXT = "Username";
$SAFETY_CHECK_TH_TEXT = "Start Password";
$PASSWORD_TH_TEXT = "Password";
$IMAGE_TH_TEXT = "IQdesktop Version";
$VOLUME_MAP_TH_TEXT = "Mapped Drive";
$VNCPORT_TH_TEXT = "VNC Port";
$SSHPORT_TH_TEXT = "SSH Port";
$SHINY_SERVER_PORT_TH_TEXT = "Shiny Server Port";
$ALLOW_SUDO_TH_TEXT = "Sudo Rights";
$SSH_SERVER_TH_TEXT = "SSH Server";
$ALLOW_SHINY_SERVER_TH_TEXT = "Shiny Server";
$USER_ID_TH_TEXT = "User ID";
$THEME_TH_TEXT = "Theme";
$MAC_TH_TEXT = "MAC Address";
$SHM_SIZE_GB_TH_TEXT = "Swap Size";
$NR_CORES_TH_TEXT = "Nr Cores";
$MEMORY_GB_TH_TEXT = "Memory [GB]";
$TIMEZONE_TH_TEXT = "Timezone";
$IQRTOOLS_COMPLIANCE_TH_TEXT = "IQR Tools Compliance";
$IQREPORT_TEMPLATE_TH_TEXT = "IQReport Template";

// Get passed GET variables
$do = $_GET["do"];
$csvfile = $_GET["csvfile"];
$control = $_GET["control"];
$user = $_GET["user"];
$action = $_GET["action"];
$image = $_GET["image"];
$nrcores = $_GET["nrcores"];
$memgb = $_GET["memgb"];
$theme = $_GET["theme"];
$allow_sudo = $_GET["allow_sudo"];

$safety_check = $_GET["safety_check"];
$safety_check_required = $_GET["safety_check_required"];

$path = "run/"
?>

<html>

<head>
    <title>IQdesktopServer Demo</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1><?php echo "Multi-User Control Interface" ?></h1>
    <h2>
        <a href="index.html">Home</a>
        |
        <a href="https://iqdesktop.intiquan.com" target="new">More information</a>
        |
        <a href="https://www.intiquan.com" target="new">IntiQuan</a>
        <?php
        if ($SHOW_ADMINLINK) {
        ?>
            |
            <a href="admin" target="admin">Admin</a>
        <?php
        }
        ?>
    </h2>

    <?php

    if ($do == "") {

        // -----------------------------------------------------------------------------
        // Get names of available CSV files and create selector 
        // Selector only if multiple CSV files ... otherwise directly control area
        // -----------------------------------------------------------------------------
        // Get all CSV files
        $filenamesCSV = glob($path . "*.csv");

        // If multiple ... then show form for selection
        if (count($filenamesCSV) > 1) {
            echo "<h3>Select User Group</h3>";
    ?>
            Select the user group that applies to you.<br>
            &nbsp;<br>
            <?php
            echo '<form action="/index.php" method="get" id="form1">';
            echo '<input type="hidden" name="do" value="selectCSV">';
            echo '<select name="csvfile">';
            foreach ($filenamesCSV as $filename) {
                $filename = str_replace($path, "", $filename);
                echo '  <option value="' . $filename . '"';
                if ($csvfile == $filename) echo "selected";
                echo '>' . $filename . '</option>';
            }
            echo '</select>';
            echo '&nbsp;<button type="submit" form="form1" value="Submit" class="buttonSelectCSV"><span>SELECT</span></button>';
            echo '</form>';
        } else {
            // If a single one then go to container start page
            if ($do == "") {
                header("Location: index.php?do=selectCSV&csvfile=" . str_replace($path, "", $filenamesCSV[0]));
            }
        }
    }

    // -----------------------------------------------------------------------------
    // Perform control action if selected
    // -----------------------------------------------------------------------------

    if ($do == "control") {

        # Handle stopping with and without safety check
        # Safety check entry is a component to ensure that only the person with the safety check password can stop a container
        if ($action == "stop") {
            if (trim($safety_check_required) == trim($safety_check) || empty($safety_check_required)) {
                $command = "./iqdesktop.sh stop " . $user . " > /dev/null 2>/dev/null &";
            } else {
                header("Location: safetycheck.html");
            }
        }

        # Handle starting with and without safety check
        # Safety check entry is a component to ensure that only the person with the safety check password can start a container
        if ($action == "start") {
            if (trim($safety_check_required) == trim($safety_check) || empty($safety_check_required)) {
                $sudo = "false";
                $command = "./iqdesktop.sh start " . $user . " " . $csvfile . " " . $image . " " . $nrcores . " " . $memgb . " " . $theme . " " . $allow_sudo . " " . $privileged . " > /dev/null 2>/dev/null &";
                // echo $command."<br>";
            } else {
                header("Location: safetycheck.html");
            }
        }

        echo '<pre>';
        $old_path = getcwd();
        chdir($path);
        shell_exec("chmod +x iqdesktop.sh");
        shell_exec($command);
        chdir($old_path);
        echo '</pre>';

        // sleep for 5 seconds to let the shell script run
        sleep(5);
    }

    // -----------------------------------------------------------------------------
    // Find available iqdesktop versions
    // -----------------------------------------------------------------------------
    system('docker images --filter=reference=intiquan/iqdesktop:*.*.* > temp');
    $content = file_get_contents(temp);
    //print_r($content);
    preg_match_all('/intiquan\/iqdesktop[ ]+([0-9.]+)/', $content, $m);
    //print_r($m[0]);
    $versions = $m[0];
    //print_r($versions);

    // -----------------------------------------------------------------------------
    // Read CSV if filename defined and build table
    // -----------------------------------------------------------------------------
    if (!empty($csvfile)) {
        if ($SHOW_INFOTEXT) {
            echo "<div class='help'>";
            echo $INFOTEXT;
            echo "</div>&nbsp;<br>";
        }

        # Write out information in case of general VNC certificates active
        if (file_exists("iqdesktop_VNC_cert.pem")) {
    ?>
            <div class='help'>
            <h3>VNC Certificate</h3>
            The VNC connection is set up to be encrypted. Do the following:
            <ul>
                <li>Download the certificate: <a href="iqdesktop_VNC_cert.pem" target="new">iqdesktop_VNC_cert.pem</a> and store it on your system.
                <li>Open the TigerVNC GUI and provide path to certificate in Options->Security->Path to X509 CA certificate.
                <li>Connect to your IQdesktop container (with encryption).
            </ul>
            </div>

    <?php 
        }

        echo "<h3>Control Containers</h3>";
        // Add path to filename
        $fullfilename = $path . $csvfile;

        // Read the CSV file contents into an array
        $csvinfo = [];
        // Open the file for reading
        if (($h = fopen("{$fullfilename}", "r")) !== FALSE) {
            // Each line in the file is converted into an individual array that we call $data
            // The items of the array are comma separated
            while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
                // Each individual array is being pushed into the nested array
                $csvinfo[] = $data;
            }
            // Close the file
            fclose($h);
        }

        // Build a table with form
        echo "<table>";
        $header = 1;
        foreach ($csvinfo as $value) {

            // print_r($value);
            // echo "<p>";
            $NAME = $value[0];
            $USER = $value[1];
            $SAFETY_CHECK = $value[2];
            $PASSWORD = $value[3];
            $IMAGE = $value[4];
            $VOLUME_MAP = $value[5];
            $VNCPORT = $value[6];
            $SSHPORT = $value[7];
            $SHINY_SERVER_PORT = $value[8];
            $ALLOW_SUDO = $value[9];
            $SSH_SERVER = $value[10];
            $ALLOW_SHINY_SERVER = $value[11];
            $USER_ID = $value[12];
            $THEME = $value[13];
            $MAC = $value[14];
            $SHM_SIZE_GB = $value[15];
            $NR_CORES = $value[16];
            $MEMORY_GB = $value[17];
            $TIMEZONE = $value[18];
            $IQRTOOLS_COMPLIANCE = $value[19];
            $IQREPORT_TEMPLATE = $value[20];

            if (!empty($USER)) {

                if (empty($VOLUME_MAP)) $VOLUME_MAP = "Not mapped";
                if (empty($IQREPORT_TEMPLATE)) $IQREPORT_TEMPLATE = "Default";

                if ($header == 1) {
                    echo "<tr>";
                    echo "<th>Control</th>";
                    if ($SAFETY_CHECK_SHOW) echo "<th>" . $SAFETY_CHECK_TH_TEXT . "</th>";
                    if ($NAME_SHOW) echo "<th>" . $NAME_TH_TEXT . "</th>";
                    if ($USER_SHOW) echo "<th>" . $USER_TH_TEXT . "</th>";
                    if ($PASSWORD_SHOW) echo "<th>" . $PASSWORD_TH_TEXT . "</th>";
                    if ($VNCPORT_SHOW) echo "<th>" . $VNCPORT_TH_TEXT . "</th>";
                    if ($SSHPORT_SHOW) echo "<th>" . $SSHPORT_TH_TEXT . "</th>";
                    if ($SHINY_SERVER_PORT_SHOW) echo "<th>" . $SHINY_SERVER_PORT_TH_TEXT . "</th>";
                    if ($IMAGE_SHOW) echo "<th>" . $IMAGE_TH_TEXT . "</th>";
                    if ($THEME_SHOW) echo "<th>" . $THEME_TH_TEXT . "</th>";
                    if ($NR_CORES_SHOW) echo "<th>" . $NR_CORES_TH_TEXT . "</th>";
                    if ($MEMORY_GB_SHOW) echo "<th>" . $MEMORY_GB_TH_TEXT . "</th>";
                    if ($ALLOW_SUDO_SHOW) echo "<th>" . $ALLOW_SUDO_TH_TEXT . "</th>";
                    if ($SHM_SIZE_GB_SHOW) echo "<th>" . $SHM_SIZE_GB_TH_TEXT . "</th>";
                    if ($VOLUME_MAP_SHOW) echo "<th>" . $VOLUME_MAP_TH_TEXT . "</th>";
                    if ($SSH_SERVER_SHOW) echo "<th>" . $SSH_SERVER_TH_TEXT . "</th>";
                    if ($ALLOW_SHINY_SERVER_SHOW) echo "<th>" . $ALLOW_SHINY_SERVER_TH_TEXT . "</th>";
                    if ($USER_ID_SHOW) echo "<th>" . $USER_ID_TH_TEXT . "</th>";
                    if ($MAC_SHOW) echo "<th>" . $MAC_TH_TEXT . "</th>";
                    if ($TIMEZONE_SHOW) echo "<th>" . $TIMEZONE_TH_TEXT . "</th>";
                    if ($IQRTOOLS_COMPLIANCE_SHOW) echo "<th>" . $IQRTOOLS_COMPLIANCE_TH_TEXT . "</th>";
                    if ($IQREPORT_TEMPLATE_SHOW) echo "<th>" . $IQREPORT_TEMPLATE_TH_TEXT . "</th>";
                    echo "</tr>";
                    $header = 0;
                } else {
                    // Check if container running
                    $container_running = FALSE;
                    if (file_exists($path . "yml_custom/" . $USER . "/docker-compose.yml")) {
                        $container_running = TRUE;
                        // Also load the yml file and parse image, ncores, and memory to replace CSV defaults
                        $content = file_get_contents($path . "yml_custom/" . $USER . "/docker-compose.yml");
                        // Parse the image information
                        preg_match_all('/intiquan\/iqdesktop:([0-9\.]+)/', $content, $m);
                        $IMAGE = $m[0][0];
                        // Parse the ncores information
                        preg_match_all('/cpus: ([0-9]+)/', $content, $m);
                        $NR_CORES = $m[1][0];
                        // Parse the memory information
                        preg_match_all('/mem_limit: (["0-9]+)/', $content, $m);
                        $MEMORY_GB = str_replace('"', '', $m[1][0]);
                        // Parse theme information
                        preg_match_all('/THEME=(["a-zA-z0-9]+)/', $content, $m);
                        if ($m[1][0] != "") {
                            $THEME = $m[1][0];
                        } else {
                            $THEME = $THEME;
                        }
                        // Parse allow_sudo information
                        preg_match_all('/ALLOW_SUDO=(["a-zA-z0-9]+)/', $content, $m);
                        if ($m[1][0] != "") {
                            $ALLOW_SUDO = strtoupper($m[1][0]);
                        } else {
                            $ALLOW_SUDO = $ALLOW_SUDO;
                        }
                    }

                    echo "<tr>" . '<td class="control">';
                    // The buttons
                    $form = "form_" . $USER;
                    echo '<form action="/index.php" method="get" id="' . $form . '">';
                    echo '<input type="hidden" name="safety_check_required" value="' . $SAFETY_CHECK . '">';
                    echo '<input type="hidden" name="do" value="control">';
                    echo '<input type="hidden" name="csvfile" value="' . $csvfile . '">';
                    $buttonText = "START";
                    $buttonStyle = "buttonRed";
                    $action = "start";
                    if ($container_running) {
                        $buttonText = "STOP";
                        $buttonStyle = "buttonGreen";
                        $PASSWORD = "-";
                        $action = "stop";
                    }
                    echo '<input type="hidden" name="user" value="' . $USER . '">';
                    echo '<input type="hidden" name="action" value="' . $action . '">';
                    echo '<button type="submit" form="' . $form . '" value="Submit" class="' . $buttonStyle . '"><span>' . $buttonText . '</span></button></td>';

                    ///////////////////////////////////////////////////////////////////////////////////
                    // Start table cells
                    ///////////////////////////////////////////////////////////////////////////////////

                    if ($SAFETY_CHECK_SHOW) echo '<td><input type="text" name="safety_check" size="10"></td>';
                    if ($NAME_SHOW) echo "<td>" . $NAME . "</td>";
                    if ($USER_SHOW) echo "<td class='blue'>" . $USER . "</td>";
                    if ($PASSWORD_SHOW) echo "<td class='blue'>" . $PASSWORD . "</td>";
                    if ($VNCPORT_SHOW) echo "<td class='blue'>" . $VNCPORT . "</td>";
                    if ($SSHPORT_SHOW) echo "<td class='blue'>" . $SSHPORT . "</td>";
                    if ($SHINY_SERVER_PORT_SHOW) echo "<td class='blue'>" . $SHINY_SERVER_PORT . "</td>";

                    // Selection of image
                    if ($IMAGE_SHOW) {
                        echo '<td><select name="image">';
                        foreach ($versions as $version) {
                            $version = str_replace("   ", ":", $version);
                            echo '  <option value="' . $version . '" ';
                            if ($IMAGE == $version) echo "selected";
                            echo '>' . $version . '</option>';
                        }
                        echo '</td></select>';
                    }

                    if ($THEME_SHOW) {
                        // Selection of theme
                        echo '<td><select name="theme">';
                        echo '  <option value="light" ';
                        if ($THEME == "light") echo "selected";
                        echo '>light</option>';
                        echo '  <option value="dark" ';
                        if ($THEME == "dark") echo "selected";
                        echo '>dark</option>';
                        echo '</select></td>';
                    }

                    if ($NR_CORES_SHOW) { // Selection of nr cores
                        echo '<td><select name="nrcores">';
                        echo '  <option value="1" ';
                        if ($NR_CORES == 1) echo "selected";
                        echo '>1</option>';
                        if ($MAX_CORES >= 2) {
                            echo '  <option value="2" ';
                            if ($NR_CORES == 2) echo "selected";
                            echo '>2</option>';
                        }
                        if ($MAX_CORES >= 3) {
                            echo '  <option value="3" ';
                            if ($NR_CORES == 3) echo "selected";
                            echo '>3</option>';
                        }
                        if ($MAX_CORES >= 4) {
                            echo '  <option value="4" ';
                            if ($NR_CORES == 4) echo "selected";
                            echo '>4</option>';
                        }
                        if ($MAX_CORES >= 6) {
                            echo '  <option value="6" ';
                            if ($NR_CORES == 6) echo "selected";
                            echo '>6</option>';
                        }
                        if ($MAX_CORES >= 8) {
                            echo '  <option value="8" ';
                            if ($NR_CORES == 8) echo "selected";
                            echo '>8</option>';
                        }
                        if ($MAX_CORES >= 12) {
                            echo '  <option value="12" ';
                            if ($NR_CORES == 12) echo "selected";
                            echo '>12</option>';
                        }
                        if ($MAX_CORES >= 16) {
                            echo '  <option value="16" ';
                            if ($NR_CORES == 16) echo "selected";
                            echo '>16</option>';
                        }
                        if ($MAX_CORES >= 24) {
                            echo '  <option value="24" ';
                            if ($NR_CORES == 24) echo "selected";
                            echo '>24</option>';
                        }
                        if ($MAX_CORES >= 32) {
                            echo '  <option value="32" ';
                            if ($NR_CORES == 32) echo "selected";
                            echo '>32</option>';
                        }
                        if ($MAX_CORES >= 64) {
                            echo '  <option value="64" ';
                            if ($NR_CORES == 64) echo "selected";
                            echo '>64</option>';
                        }
                        if ($MAX_CORES >= 72) {
                            echo '  <option value="72" ';
                            if ($NR_CORES == 72) echo "selected";
                            echo '>72</option>';
                        }
                        if ($MAX_CORES >= 96) {
                            echo '  <option value="96" ';
                            if ($NR_CORES == 96) echo "selected";
                            echo '>96</option>';
                        }
                        echo '</select></td>';
                    }

                    // Selection of memory
                    if ($MEMORY_GB_SHOW) {
                        echo '<td><select name="memgb">';
                        echo '  <option value="8" ';
                        if ($MEMORY_GB == 8) echo "selected";
                        echo '>8</option>';

                        if ($MAX_MEM >= 12) {
                            echo '  <option value="12" ';
                            if ($MEMORY_GB == 12) echo "selected";
                            echo '>12</option>';
                        }

                        if ($MAX_MEM >= 16) {
                            echo '  <option value="16" ';
                            if ($MEMORY_GB == 16) echo "selected";
                            echo '>16</option>';
                        }

                        if ($MAX_MEM >= 24) {
                            echo '  <option value="24" ';
                            if ($MEMORY_GB == 24) echo "selected";
                            echo '>24</option>';
                        }

                        if ($MAX_MEM >= 32) {
                            echo '  <option value="32" ';
                            if ($MEMORY_GB == 32) echo "selected";
                            echo '>32</option>';
                        }

                        if ($MAX_MEM >= 48) {
                            echo '  <option value="48" ';
                            if ($MEMORY_GB == 48) echo "selected";
                            echo '>48</option>';
                        }

                        if ($MAX_MEM >= 64) {
                            echo '  <option value="64" ';
                            if ($MEMORY_GB == 64) echo "selected";
                            echo '>64</option>';
                        }

                        if ($MAX_MEM >= 128) {
                            echo '  <option value="128" ';
                            if ($MEMORY_GB == 128) echo "selected";
                            echo '>128</option>';
                        }

                        if ($MAX_MEM >= 196) {
                            echo '  <option value="196" ';
                            if ($MEMORY_GB == 196) echo "selected";
                            echo '>196</option>';
                        }

                        if ($MAX_MEM >= 256) {
                            echo '  <option value="256" ';
                            if ($MEMORY_GB == 256) echo "selected";
                            echo '>256</option>';
                        }

                        echo '</select></td>';
                    }

                    if ($ALLOW_SUDO_SHOW) {
                        if (!$ALLOW_SUDO_CHOICE) {
                            echo "<td class='blue'>" . $ALLOW_SUDO . "</td>";
                            echo '<input type="hidden" name="allow_sudo" value="' . $ALLOW_SUDO . '">';
                        } else {
                            // Selection of theme
                            echo '<td><select name="allow_sudo">';
                            echo '  <option value="FALSE" ';
                            if ($ALLOW_SUDO == "FALSE") echo "selected";
                            echo '>FALSE</option>';
                            echo '  <option value="TRUE" ';
                            if ($ALLOW_SUDO == "TRUE") echo "selected";
                            echo '>TRUE</option>';
                            echo '</select></td>';
                        }
                    }

                    if ($SHM_SIZE_GB_SHOW) echo "<td class='blue'>" . $SHM_SIZE_GB . "</td>";
                    if ($VOLUME_MAP_SHOW) echo "<td class='blue'>" . $VOLUME_MAP . "</td>";
                    
                    if ($SSH_SERVER_SHOW) echo "<td class='blue'>" . $SSH_SERVER . "</td>";
                    if ($ALLOW_SHINY_SERVER_SHOW) echo "<td class='blue'>" . $ALLOW_SHINY_SERVER . "</td>";
                    if ($USER_ID_SHOW) echo "<td class='blue'>" . $USER_ID . "</td>";

                    if ($MAC_SHOW) echo "<td class='blue'>" . $MAC . "</td>";

                    if ($TIMEZONE_SHOW) echo "<td class='blue'>" . $TIMEZONE . "</td>";
                    if ($IQRTOOLS_COMPLIANCE_SHOW) echo "<td class='blue'>" . $IQRTOOLS_COMPLIANCE . "</td>";
                    if ($IQREPORT_TEMPLATE_SHOW) echo "<td class='blue'>" . $IQREPORT_TEMPLATE . "</td>";

                    echo "</tr></form>";
                }
            }
        }
        echo "</table>";
    }
    ?>

</body>

</html>