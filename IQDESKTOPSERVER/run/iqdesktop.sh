#!/bin/bash
# ------------------------------------------------------------------------------------------------
#  iqdesktop start username config.csv image ncores memorygb theme swapspace sudo privileged mount_basename iqrtoolscompliance sshserver shinyserver macaddress timezone iqreporttemplate nonmemlicensekey monolixlicensekey organization licensekey [timedelaystophours]
#  test stop all|username 
# ------------------------------------------------------------------------------------------------

# # ------------------------------------------------------------------------
# # Create the network - if not present yet
# AVOID FOR NOW!
# # ------------------------------------------------------------------------

# ./create_network.sh

# ------------------------------------------------------------------------
# Input argument checks
# ------------------------------------------------------------------------

# Number input arguments
NARGS=$#

# Require correct number of input arguments
if [[ $NARGS != 2 ]] && [[ $NARGS < 19 ]]; then 
	echo "Usage:"
	echo "        iqdesktop start username config.csv image ncores memorygb theme swapspace sudo privileged mount_basename iqrtoolscompliance sshserver shinyserver macaddress timezone iqreporttemplate nonmemlicensekey monolixlicensekey organization licensekey [timedelaystophours]"
	echo "        iqdesktop stop username"
	exit 0
fi

# Get first ones
COMMAND=$1
USERS=$2

# Check right syntax
if [[ $COMMAND == "stop" ]];  then
    if [[ $NARGS != 2 ]]; then 
        echo "stop command requires 2 input arguments"
        exit 0
    fi
fi

if [[ $COMMAND == "start" ]]; then
    if [[ $NARGS < 21 ]]; then
        echo "start command requires 21 or 22 input arguments - not useful for command line ..."
        exit 0
    fi
fi

if [[ $COMMAND != "start" ]] && [[ $COMMAND != "stop" ]]; then 
    echo "wrong action command"
    exit 0
fi

if [[ $COMMAND == "start" ]]; then 
    if [[ ! -f $3 ]]; then 
        echo "$3 file not found"; 
        exit 99;
    fi
fi 

# ------------------------------------------------------------------------
# Assign input values
# ------------------------------------------------------------------------

COMMAND=$COMMAND
USERS=$USERS
CSVFILE=$3
ARGIMAGE=$4
ARGNCORES=$5
ARGMEM=$6
ARGTHEME=$7
ARGSWAP=$8
ARGSUDO=$9
ARGPRIVILEGED=${10}
ARGMOUNTBASENAME=${11}
ARGiqrtoolscompliance=${12}
ARGsshserver=${13}
ARGshinyserver=${14}
ARGmacaddress=${15}
ARGtimezone=${16}
ARGiqreporttemplate=${17}
ARGnonmemlicensekey=${18}
ARGmonolixlicensekey=${19}
ARGorganization=${20}
ARGlicensekey=${21}
DELAYHOURS=${22}

if [[ $ARGiqreporttemplate == "default" ]]; then
    ARGiqreporttemplate=
fi

# ------------------------------------------------------------------------
# Ensure gen_runs.sh and stop_runs.h are executable
# ------------------------------------------------------------------------

chmod +x gen_runs.sh
chmod +x stop_runs.sh

# ------------------------------------------------------------------------
# Handle "start" of iqdesktop
# ------------------------------------------------------------------------

# Basically, read the CSV file and search the user to start
if [[ $COMMAND == "start" ]]; then 
    OLDIFS=$IFS
    IFS=','
    while read NAME USER SAFETY_CHECK PASSWORD VOLUME_MAP VNCPORT SSHPORT SHINY_SERVER_PORT USER_ID IQREPORT_LICENSE_KEY AWS_ACCESS_KEY_ID AWS_SECRET_ACCESS_KEY MOUNT_1_LABEL MOUNT_1_SERVER_IP MOUNT_1_SERVER_FOLDER MOUNT_1_OPTIONS MOUNT_2_LABEL MOUNT_2_SERVER_IP MOUNT_2_SERVER_FOLDER MOUNT_2_OPTIONS MOUNT_3_LABEL MOUNT_3_SERVER_IP MOUNT_3_SERVER_FOLDER MOUNT_3_OPTIONS MOUNT_4_LABEL MOUNT_4_SERVER_IP MOUNT_4_SERVER_FOLDER MOUNT_4_OPTIONS MOUNT_5_LABEL MOUNT_5_SERVER_IP MOUNT_5_SERVER_FOLDER MOUNT_5_OPTIONS
     
    do
        # Do not handle header row ("USER" in "USER" column)
        if [[ $USER == "USER" ]]; then 
            continue
        else
          
            # -----------------------------------------------------
            # Modify Monolix Server License key file content 
            # \n replaced by ::: in CSV file => setting to " " is OK
            # " replaced by &&& in CSV file
            ARGmonolixlicensekey="$(sed s!:::!\ !g <<<$ARGmonolixlicensekey)"
            ARGmonolixlicensekey="$(sed s!\&\&\&!\\\\\\\"!g <<<$ARGmonolixlicensekey)"
            # -----------------------------------------------------

            # -----------------------------------------------------
            # Handle VNC based on present files on the server
            if [ -f "../admin/iqdesktop_VNC_key.pem" ]; then
                if [ -f "../iqdesktop_VNC_cert.pem" ]; then
                    # Read files into variables
                    # Replace "\n" with ":"
                    VNCKEY=$(tr '\n' ':' < ../admin/iqdesktop_VNC_key.pem)
                    VNCCERT=$(tr '\n' ':' < ../iqdesktop_VNC_cert.pem)
                    # Replace "&" with ":::"
                    VNC_PRIVATE_KEY=$(sed s#:#:::#g <<<$VNCKEY)
                    VNC_CERTIFICATE=$(sed s#:#:::#g <<<$VNCCERT)
                fi
            fi
            # -----------------------------------------------------
            
            # -----------------------------------------------------
            # Modify VNC_PRIVATE_KEY and VNC_CERTIFICATE file content 
            # \n replaced by ::: in CSV file
            VNC_PRIVATE_KEY="$(sed s#:::#\\\\\\\\n#g <<<$VNC_PRIVATE_KEY)"
            VNC_CERTIFICATE="$(sed s#:::#\\\\\\\\n#g <<<$VNC_CERTIFICATE)"
            # -----------------------------------------------------

            if [[ $USERS == $USER ]]; then 
                # Start only for selected user
                echo "Handling setup for: $NAME"
                ./gen_runs.sh "$USER" "$PASSWORD" "$ARGIMAGE" "$VOLUME_MAP" "$VNCPORT" "$SSHPORT" "$SHINY_SERVER_PORT" \
                    "$ARGSUDO" "$ARGsshserver" "$ARGshinyserver" "$USER_ID" "$ARGTHEME" "$ARGmacaddress" "$ARGSWAP" "$ARGNCORES" \
                    "$ARGMEM" "$ARGtimezone" "$ARGiqrtoolscompliance" "$ARGiqreporttemplate" "$IQREPORT_LICENSE_KEY" "$ARGnonmemlicensekey" \
                    "$ARGmonolixlicensekey" "$VNC_PRIVATE_KEY" "$VNC_CERTIFICATE" "$AWS_ACCESS_KEY_ID" "$AWS_SECRET_ACCESS_KEY" \
                    "$ARGPRIVILEGED" "$ARGMOUNTBASENAME" \
                    "$MOUNT_1_LABEL" "$MOUNT_1_SERVER_IP" "$MOUNT_1_SERVER_FOLDER" "$MOUNT_1_OPTIONS" \
                    "$MOUNT_2_LABEL" "$MOUNT_2_SERVER_IP" "$MOUNT_2_SERVER_FOLDER" "$MOUNT_2_OPTIONS" \
                    "$MOUNT_3_LABEL" "$MOUNT_3_SERVER_IP" "$MOUNT_3_SERVER_FOLDER" "$MOUNT_3_OPTIONS" \
                    "$MOUNT_4_LABEL" "$MOUNT_4_SERVER_IP" "$MOUNT_4_SERVER_FOLDER" "$MOUNT_4_OPTIONS" \
                    "$MOUNT_5_LABEL" "$MOUNT_5_SERVER_IP" "$MOUNT_5_SERVER_FOLDER" "$MOUNT_5_OPTIONS" \
                    "$ARGorganization" "$ARGlicensekey"
					
				# If defined then start the time delayed stopping of the container
				if [[ -n $DELAYHOURS ]]; then 
					echo -e "\nSetting up automatic shut down for $USER in $DELAYHOURS hours\n"
					./stop_runs.sh $USER $DELAYHOURS
				fi
					
            fi
        fi 
    done < $CSVFILE
    IFS=$OLDIFS
    exit 0
fi

# ------------------------------------------------------------------------
# Handle "stop" of iqdesktop
# ------------------------------------------------------------------------

if [[ $COMMAND = "stop" ]]; then 
	./stop_runs.sh $USERS
fi
