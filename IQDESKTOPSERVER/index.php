<?php
// Define base folder name to iqdesktop.sh
// DO NOT CHANGE!
$path = "/home/iqdocker/IQDESKTOP/IQDESKTOPSERVER/run/";

// Define other things
$BACKGROUND = "background.jpg";
$MAX_CORES = 24;
$MAX_MEM = 128;
?>

<html>

<head>
    <title>IQdesktopServer Demo</title>
    <!-- Define Styles -->
    <style>
        body {
            font-family: Verdana;
            font-size: 16px;
            background-image: url("images/<?php echo $BACKGROUND; ?>");
            background-size: cover;
            background-attachment: fixed;
            margin: 0 0 0 20;
            color: #fff;
        }

        h1 {
            background: rgba(42, 42, 42, 0);
            color: #fff;
            padding: 20px;
            margin: 0 0 0 -20;
            font-weight: normal;
            text-align: right;
            background-image: url("images/logo.png");
            background-repeat: no-repeat;
            background-position: 20;
            background-size: 300px;
        }

        h2 {
            background: rgba(50, 50, 50, 0.6);
            color: #fff;
            padding: 5 20 5 0;
            margin: 0 0 0 -20;
            font-size: 18px;
            font-weight: normal;
            text-align: right;
        }

        a {
            color: #B1DFE1;
            text-decoration: none;
        }

        table {
            margin: 0 0 0 20;
        }

        table,
        th,
        td {
            border: 1px solid;
            border-color: #19283B;
            border-collapse: collapse;
            background: rgba(202, 202, 202, 0.5);
        }

        td,
        th {
            font-size: 12px;
            text-align: left;
            vertical-align: middle;
            padding: 0px;
            padding-left: 5px;
            padding-right: 5px;
            margin: 0;
            height: 30px;
        }

        td {
            padding-top: 2px;
        }

        select.style,
        option.style,
        input.style {
            font-size: 16px;
            font-weight: bold;
        }

        .buttonGreen {
            background-color: #92D050;
            /* Green */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 12px;
            width: 80px;
            height: 30px;
            border-radius: 8px;
            display: block;
            margin: auto;
        }

        .buttonRed {
            background-color: #A84A30;
            /* Red */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 12px;
            width: 80px;
            height: 30px;
            border-radius: 8px;
            display: block;
            margin: auto;
        }

        .buttonSelectCSV {
            background-color: #369398;
            /* Blue */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 12px;
            width: 120px;
            height: 30px;
            border-radius: 8px;
            display: inline;
        }

        .red {
            color: #A84A30;
        }

        .redbold {
            color: #A84A30;
            font-weight: bold;
        }

        .green {
            color: #92D050;
        }

        .greenbold {
            color: #92D050;
            font-weight: bold;
        }

        .blue {
            color: #315E71;
        }

        .bluebold {
            color: #315E71;
            font-weight: bold;
        }

        li {
            margin: 2px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <H1><?php echo "Multi-User Control Interface" ?></h1>
    <H2><a href="https://iqdesktop.intiquan.com" target="new">More information</a> | <a href="https://www.intiquan.com" target="new">IntiQuan</a></h2>

    <?php
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
    $safety_check = $_GET["safety_check"];
    $safety_check_required = $_GET["safety_check_required"];

    // -----------------------------------------------------------------------------
    // Get names of available CSV files and create selector 
    // Selector only if multiple CSV files ... otherwise directly control area
    // -----------------------------------------------------------------------------
    // Get all CSV files
    $filenamesCSV = glob($path . "*.csv");

    // If multiple ... then show form for selection
    if (count($filenamesCSV) > 1) {
        echo "<h3>Select User Group</h3>";
        echo '<form action="/index.php" method="get" id="form1">';
        echo '<input type="hidden" name="do" value="selectCSV">';
        echo '<select class="style" name="csvfile">';
        foreach ($filenamesCSV as $filename) {
            $filename = str_replace($path, "", $filename);
            echo '  <option class="style" value="' . $filename . '"';
            if ($csvfile == $filename) echo "selected";
            echo '>' . $filename . '</option>';
        }
        echo '</select>';
        echo '&nbsp;<button type="submit" form="form1" value="Submit" class="buttonSelectCSV">SELECT</button>';
        echo '</form>';
    } else {
        // If a single one then go to container start page
        if ($do == "") {
            header("Location: index.php?do=selectCSV&csvfile=" . str_replace($path, "", $filenamesCSV[0]));
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
                $command = "./iqdesktop.sh start " . $user . " " . $csvfile . " " . $image . " " . $nrcores . " " . $memgb . " " . $theme . " > /dev/null 2>/dev/null &";
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
        echo "<h3>Help</h3>";
        echo "<ol>";
        echo "<li><b>Starting an IQdesktop container</b>";
        echo "<ul>";
        echo "<li>Select your required settings (version of IQdesktop, number of cores, needed memory)";
        echo "<li>You can also select a 'dark' and a 'light' theme";
        echo "<li>To start a container you might have received a 'Start Password' - enter it. This is to avoid that someone else can start (or stop) a container under your username";
        echo "<li>Click 'START'";
        echo "</ul>";
        echo "<li><b>Connecting to a container</b>";
        echo "<ul>";
        echo '<li><a href="https://iqdesktop.intiquan.com/book/vnc.html" target="new">Get and execute TigerVNC Viewer (explained here)</a>';
        echo "<li>Enter: iqdesktop.intiquan.com:THE_NUMBER_IN_THE_VNC_Port column for your container";
        echo "<li>Click 'Connect'";
        echo "<li>Enter the password that was provided to you - for demo purposes same as the Start Password";
        echo "<li>Click 'OK'";
        echo "</ul>";
        echo "<li><b>Stopping an IQdesktop container</b>";
        echo "<ul>";
        echo "<li>Enter your 'Start Password' - if you have received one";
        echo "<li>Click 'STOP'";
        echo "</ul>";
        echo "</ol>";
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
            $IMAGE = $value[3];
            $VNCPORT = $value[4];
            $SSHPORT = $value[5];
            $PASSWORD = $value[6];
            $THEME = $value[8];
            $ALLOW_SUDO = $value[9];
            $MAP = $value[10];
            $NR_CORES = $value[13];
            $MEMORY_GB = $value[14];
            $ALLOW_SHINY_SERVER = $value[26];
            $SHINY_SERVER_PORT = $value[27];
            $JENKINSPORT = $value[28];

            if (!empty($USER)) {


                if (empty($MAP)) $MAP = "Not mapped";

                if ($header == 1) {
                  //echo "<tr>" . "<th>Control</th>" . "<th>" . $SAFETY_CHECK . "</th>" . "<th>" . $NAME . "</th>" . "<th class='bluebold'>" . $USER . "</th>" . "<th class='bluebold'>VNC Port</th>" . "<th class='bluebold'>SSH Port</th>" . "<th class='bluebold'>SHINY Port</th>" . "<th>" . $THEME . "</th>" . "<th>" . $IMAGE .  "</th>" . "<th>" . $NR_CORES . "</th>" . "<th>" . $MEMORY_GB . "</th></tr>";
                    echo "<tr>" . "<th>Control</th>" . "<th>    Start Password   </th>" . "<th>    Name     </th>" . "<th class='bluebold'>  Username   </th>" . "<th class='bluebold'>VNC Port</th>" . "<th class='bluebold'>SSH Port</th>" . "<th class='bluebold'>Shiny Port</th>" . "<th>    Theme     </th>" . "<th>IQdesktop Image</th>" . "<th>Number Cores     </th>" . "<th>Memory [GB]       </th></tr>";
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
                    }

                    echo "<tr>" . "<td>";
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
                    echo '<button type="submit" form="' . $form . '" value="Submit" class="' . $buttonStyle . '">' . $buttonText . '</button></td>';

                    # Handle the safety check
                    echo '<td><input class="style" type="text" name="safety_check" size="10"></td>';

                    # Continue with NAME etc.
                    echo "<td>" . $NAME . "</td>" . "<td class='bluebold'>" . $USER . "</td>";

                    echo "<td class='bluebold'>" . $VNCPORT . "</td>";
                    echo "<td class='bluebold'>" . $SSHPORT . "</td>";
                    echo "<td class='bluebold'>" . $SHINY_SERVER_PORT . "</td>";

                    // Selection of theme
                    echo '<td><select class="style" name="theme">';
                    echo '  <option class="style" value="light" ';
                    if ($THEME == "light") echo "selected";
                    echo '>light</option>';
                    echo '  <option class="style" value="dark" ';
                    if ($THEME == "dark") echo "selected";
                    echo '>dark</option>';
                    echo '</select></td>';

                    // Selection of image
                    echo '<td><select class="style" name="image">';
                    foreach ($versions as $version) {
                        $version = str_replace("   ", ":", $version);
                        echo '  <option class="style" value="' . $version . '" ';
                        if ($IMAGE == $version) echo "selected";
                        echo '>' . $version . '</option>';
                    }
                    echo '</td></select>';

                    // Selection of nr cores
                    echo '<td><select class="style" name="nrcores">';
                    echo '  <option class="style" value="1" ';
                    if ($NR_CORES == 1) echo "selected";
                    echo '>1</option>';
                    echo '  <option class="style" value="2" ';
                    if ($NR_CORES == 2) echo "selected";
                    echo '>2</option>';
                    echo '  <option class="style" value="3" ';
                    if ($NR_CORES == 3) echo "selected";
                    echo '>3</option>';
                    echo '  <option class="style" value="4" ';
                    if ($NR_CORES == 4) echo "selected";
                    echo '>4</option>';
                    echo '  <option class="style" value="6" ';
                    if ($NR_CORES == 6) echo "selected";
                    echo '>6</option>';
                    echo '  <option class="style" value="8" ';
                    if ($NR_CORES == 8) echo "selected";
                    echo '>8</option>';

                    if ($MAX_CORES >= 12) {
                        echo '  <option class="style" value="12" ';
                        if ($NR_CORES == 12) echo "selected";
                        echo '>12</option>';
                    }

                    if ($MAX_CORES >= 16) {
                        echo '  <option class="style" value="16" ';
                        if ($NR_CORES == 16) echo "selected";
                        echo '>16</option>';
                    }

                    if ($MAX_CORES >= 24) {
                        echo '  <option class="style" value="24" ';
                        if ($NR_CORES == 24) echo "selected";
                        echo '>24</option>';
                    }

                    if ($MAX_CORES >= 32) {
                        echo '  <option class="style" value="32" ';
                        if ($NR_CORES == 32) echo "selected";
                        echo '>32</option>';
                    }

                    if ($MAX_CORES >= 64) {
                        echo '  <option class="style" value="64" ';
                        if ($NR_CORES == 64) echo "selected";
                        echo '>64</option>';
                    }

                    if ($MAX_CORES >= 72) {
                        echo '  <option class="style" value="72" ';
                        if ($NR_CORES == 72) echo "selected";
                        echo '>72</option>';
                    }

                    if ($MAX_CORES >= 96) {
                        echo '  <option class="style" value="96" ';
                        if ($NR_CORES == 96) echo "selected";
                        echo '>96</option>';
                    }

                    echo '</select></td>';

                    // Selection of memory
                    echo '<td><select class="style" name="memgb">';
                    echo '  <option class="style" value="8" ';
                    if ($MEMORY_GB == 8) echo "selected";
                    echo '>8</option>';
                    echo '  <option class="style" value="12" ';
                    if ($MEMORY_GB == 12) echo "selected";
                    echo '>12</option>';
                    echo '  <option class="style" value="16" ';
                    if ($MEMORY_GB == 16) echo "selected";
                    echo '>16</option>';
                    echo '  <option class="style" value="32" ';
                    if ($MEMORY_GB == 32) echo "selected";
                    echo '>32</option>';

                    if ($MAX_MEM >= 64) {
                        echo '  <option class="style" value="64" ';
                        if ($MEMORY_GB == 64) echo "selected";
                        echo '>64</option>';
                    }

                    if ($MAX_MEM >= 128) {
                        echo '  <option class="style" value="128" ';
                        if ($MEMORY_GB == 128) echo "selected";
                        echo '>128</option>';
                    }

                    if ($MAX_MEM >= 196) {
                        echo '  <option class="style" value="196" ';
                        if ($MEMORY_GB == 196) echo "selected";
                        echo '>196</option>';
                    }

                    if ($MAX_MEM >= 256) {
                        echo '  <option class="style" value="256" ';
                        if ($MEMORY_GB == 256) echo "selected";
                        echo '>256</option>';
                    }

                    echo '</select></td>';
                    echo "</tr></form>";
                }
            }
        }
        echo "</table>";
    }
    ?>

</body>

</html>