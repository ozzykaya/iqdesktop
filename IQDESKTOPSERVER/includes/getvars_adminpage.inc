<?php
// Admin page - Get passed GET variables
$do = $_GET["do"]; if (empty($do)) $do = "showsettings";
$action = $_GET["action"];

$C_vnccert = $_GET["C_vnccert"]; if (empty($C_vnccert)) $C_vnccert = "CH";
$ST_vnccert = $_GET["ST_vnccert"]; if (empty($ST_vnccert)) $ST_vnccert = "BS";
$L_vnccert = $_GET["L_vnccert"]; if (empty($L_vnccert)) $L_vnccert = "Basel";
$O_vnccert = $_GET["O_vnccert"]; if (empty($O_vnccert)) $O_vnccert = "IntiQuan GmbH";
$OU_vnccert = $_GET["OU_vnccert"]; if (empty($OU_vnccert)) $OU_vnccert = "IQdesktop";
$CN_vnccert = $_GET["CN_vnccert"]; if (empty($CN_vnccert)) $CN_vnccert = "iqdesktop.intiquan.com";
$BIT_vnccert = $_GET["BIT_vnccert"]; if (empty($BIT_vnccert)) $BIT_vnccert = 2048;
$days_vnccert = $_GET["days_vnccert"]; if (empty($days_vnccert)) $days_vnccert = 3333;

$set_SERVER_NAME = $_GET["set_SERVER_NAME"];
$set_SERVER_ADDRESS = $_GET["set_SERVER_ADDRESS"];
$set_SHOW_INFOTEXT = $_GET["set_SHOW_INFOTEXT"]; if ($set_SHOW_INFOTEXT != "TRUE") $set_SHOW_INFOTEXT = "FALSE";
$set_SHOW_ADMINLINK = $_GET["set_SHOW_ADMINLINK"]; if ($set_SHOW_ADMINLINK != "TRUE") $set_SHOW_ADMINLINK = "FALSE";
$set_START_PASSWORD_HIDDEN = $_GET["set_START_PASSWORD_HIDDEN"]; if ($set_START_PASSWORD_HIDDEN != "TRUE") $set_START_PASSWORD_HIDDEN = "FALSE";
$set_IGNORE_DEMO_CSV = $_GET["set_IGNORE_DEMO_CSV"]; if ($set_IGNORE_DEMO_CSV != "TRUE") $set_IGNORE_DEMO_CSV = "FALSE";

$set_MAX_CORES = $_GET["set_MAX_CORES"];
$set_MAX_MEM = $_GET["set_MAX_MEM"];
$set_PRIVILEGED = $_GET["set_PRIVILEGED"]; if ($set_PRIVILEGED != "TRUE") $set_PRIVILEGED = "FALSE";
$set_MOUNT_BASENAME = $_GET["set_MOUNT_BASENAME"]; if ($set_MOUNT_BASENAME != "TRUE") $set_MOUNT_BASENAME = "FALSE";
$set_MAC_ADDRESS = $_GET["set_MAC_ADDRESS"];
$set_TIMEZONE = $_GET["set_TIMEZONE"];
$set_IQREPORT_TEMPLATE = $_GET["set_IQREPORT_TEMPLATE"];
$set_IQRTOOLS_COMPLIANCE = $_GET["set_IQRTOOLS_COMPLIANCE"]; if ($set_IQRTOOLS_COMPLIANCE != "TRUE") $set_IQRTOOLS_COMPLIANCE = "FALSE";
$set_NONMEM_LICENSE_KEY = $_GET["set_NONMEM_LICENSE_KEY"];
$set_MONOLIX_LICENSE_KEY = $_GET["set_MONOLIX_LICENSE_KEY"];

$set_ALLOW_SUDO = $_GET["set_ALLOW_SUDO"]; if ($set_ALLOW_SUDO != "TRUE") $set_ALLOW_SUDO = "FALSE";
$set_SSH_SERVER = $_GET["set_SSH_SERVER"]; if ($set_SSH_SERVER != "TRUE") $set_SSH_SERVER = "FALSE";
$set_ALLOW_SHINY_SERVER = $_GET["set_ALLOW_SHINY_SERVER"]; if ($set_ALLOW_SHINY_SERVER != "TRUE") $set_ALLOW_SHINY_SERVER = "FALSE";
$set_THEME = $_GET["set_THEME"];
$set_SHM_SIZE_GB = $_GET["set_SHM_SIZE_GB"];
$set_NR_CORES = $_GET["set_NR_CORES"];
$set_MEMORY_GB = $_GET["set_MEMORY_GB"];

// Process the license keys
$set_NONMEM_LICENSE_KEY = trim($set_NONMEM_LICENSE_KEY); if ($set_NONMEM_LICENSE_KEY=="") $set_NONMEM_LICENSE_KEY = "Undefined";
$set_MONOLIX_LICENSE_KEY = trim($set_MONOLIX_LICENSE_KEY); if ($set_MONOLIX_LICENSE_KEY=="") $set_MONOLIX_LICENSE_KEY = "Undefined";
$set_MONOLIX_LICENSE_KEY = str_replace("\r","",$set_MONOLIX_LICENSE_KEY);
$set_MONOLIX_LICENSE_KEY = str_replace("\n",":::",$set_MONOLIX_LICENSE_KEY);
$set_MONOLIX_LICENSE_KEY = str_replace("\"","&&&",$set_MONOLIX_LICENSE_KEY);

$set_NAME_SHOW = $_GET["set_NAME_SHOW"]; if ($set_NAME_SHOW != "TRUE") $set_NAME_SHOW = "FALSE";
$set_SAFETY_CHECK_SHOW = $_GET["set_SAFETY_CHECK_SHOW"]; if ($set_SAFETY_CHECK_SHOW != "TRUE") $set_SAFETY_CHECK_SHOW = "FALSE";
$set_USER_SHOW = $_GET["set_USER_SHOW"]; if ($set_USER_SHOW != "TRUE") $set_USER_SHOW = "FALSE";
$set_PASSWORD_SHOW = $_GET["set_PASSWORD_SHOW"]; if ($set_PASSWORD_SHOW != "TRUE") $set_PASSWORD_SHOW = "FALSE";
$set_IMAGE_SHOW = $_GET["set_IMAGE_SHOW"]; if ($set_IMAGE_SHOW != "TRUE") $set_IMAGE_SHOW = "FALSE";
$set_VOLUME_MAP_SHOW = $_GET["set_VOLUME_MAP_SHOW"]; if ($set_VOLUME_MAP_SHOW != "TRUE") $set_VOLUME_MAP_SHOW = "FALSE";
$set_VNCPORT_SHOW = $_GET["set_VNCPORT_SHOW"]; if ($set_VNCPORT_SHOW != "TRUE") $set_VNCPORT_SHOW = "FALSE";
$set_SSHPORT_SHOW = $_GET["set_SSHPORT_SHOW"]; if ($set_SSHPORT_SHOW != "TRUE") $set_SSHPORT_SHOW = "FALSE";
$set_SHINY_SERVER_PORT_SHOW = $_GET["set_SHINY_SERVER_PORT_SHOW"]; if ($set_SHINY_SERVER_PORT_SHOW != "TRUE") $set_SHINY_SERVER_PORT_SHOW = "FALSE";
$set_ALLOW_SUDO_SHOW = $_GET["set_ALLOW_SUDO_SHOW"]; if ($set_ALLOW_SUDO_SHOW != "TRUE") $set_ALLOW_SUDO_SHOW = "FALSE";
$set_SSH_SERVER_SHOW = $_GET["set_SSH_SERVER_SHOW"]; if ($set_SSH_SERVER_SHOW != "TRUE") $set_SSH_SERVER_SHOW = "FALSE";
$set_ALLOW_SHINY_SERVER_SHOW = $_GET["set_ALLOW_SHINY_SERVER_SHOW"]; if ($set_ALLOW_SHINY_SERVER_SHOW != "TRUE") $set_ALLOW_SHINY_SERVER_SHOW = "FALSE";
$set_USER_ID_SHOW = $_GET["set_USER_ID_SHOW"]; if ($set_USER_ID_SHOW != "TRUE") $set_USER_ID_SHOW = "FALSE";
$set_THEME_SHOW = $_GET["set_THEME_SHOW"]; if ($set_THEME_SHOW != "TRUE") $set_THEME_SHOW = "FALSE";
$set_MAC_SHOW = $_GET["set_MAC_SHOW"]; if ($set_MAC_SHOW != "TRUE") $set_MAC_SHOW = "FALSE";
$set_SHM_SIZE_GB_SHOW = $_GET["set_SHM_SIZE_GB_SHOW"]; if ($set_SHM_SIZE_GB_SHOW != "TRUE") $set_SHM_SIZE_GB_SHOW = "FALSE";
$set_NR_CORES_SHOW = $_GET["set_NR_CORES_SHOW"]; if ($set_NR_CORES_SHOW != "TRUE") $set_NR_CORES_SHOW = "FALSE";
$set_MEMORY_GB_SHOW = $_GET["set_MEMORY_GB_SHOW"]; if ($set_MEMORY_GB_SHOW != "TRUE") $set_MEMORY_GB_SHOW = "FALSE";
$set_TIMEZONE_SHOW = $_GET["set_TIMEZONE_SHOW"]; if ($set_TIMEZONE_SHOW != "TRUE") $set_TIMEZONE_SHOW = "FALSE";
$set_IQRTOOLS_COMPLIANCE_SHOW = $_GET["set_IQRTOOLS_COMPLIANCE_SHOW"]; if ($set_IQRTOOLS_COMPLIANCE_SHOW != "TRUE") $set_IQRTOOLS_COMPLIANCE_SHOW = "FALSE";
$set_IQREPORT_TEMPLATE_SHOW = $_GET["set_IQREPORT_TEMPLATE_SHOW"]; if ($set_IQREPORT_TEMPLATE_SHOW != "TRUE") $set_IQREPORT_TEMPLATE_SHOW = "FALSE";
$set_MOUNT_SHOW = $_GET["set_MOUNT_SHOW"]; if ($set_MOUNT_SHOW != "TRUE") $set_MOUNT_SHOW = "FALSE";

$set_INFOTEXT = trim($_GET["set_INFOTEXT"]); if (empty($set_INFOTEXT)) $set_INFOTEXT = $INFOTEXTDEFAULT;
?>