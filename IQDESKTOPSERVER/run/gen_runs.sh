#!/bin/bash
# ------------------------------------------------------------------------------------------------
# Starts an IDesktop container for defined input arguments 
# ------------------------------------------------------------------------------------------------

MATLAB_LICENSE_FILE=

# ------------------------------------------------------------------------
# Get input arguments
# ------------------------------------------------------------------------

USER=$1
PASSWORD=$2
IMAGE=$3
VOLUME_MAP=$4
VNCPORT=$5
SSHPORT=$6
SHINY_SERVER_PORT=$7
JENKINSPORT=$8
ALLOW_SUDO=$9
SSH_SERVER=${10}
ALLOW_SHINY_SERVER=${11}
USER_ID=${12}
THEME=${13}
MAC=${14}
SHM_SIZE_GB=${15}
NR_CORES=${16}
MEMORY_GB=${17}
TIMEZONE=${18}
IQRTOOLS_COMPLIANCE=${19}
IQREPORT_TEMPLATE=${20}
IQREPORT_LICENSE_KEY=${21}
NONMEM_LICENSE_KEY=${22}
MONOLIX_LICENSE_KEY=${23}
VNC_PRIVATE_KEY=${24}
VNC_CERTIFICATE=${25}
AWS_ACCESS_KEY_ID=${26}
AWS_SECRET_ACCESS_KEY=${27}

# ------------------------------------------------------------------------
# Handle upper to lower case
# ------------------------------------------------------------------------

ALLOW_SUDO=${ALLOW_SUDO,,}
SSH_SERVER=${SSH_SERVER,,}
IQRTOOLS_COMPLIANCE=${IQRTOOLS_COMPLIANCE,,}
ALLOW_SHINY_SERVER=${ALLOW_SHINY_SERVER,,}

# ------------------------------------------------------------------------
# Copy template docker-compose.yml file 
# in user dependent folder
# ------------------------------------------------------------------------

mkdir -p yml_custom/$USER
CUSTOM_YML_FILE=yml_custom/$USER/docker-compose.yml 
cp docker-compose_template.yml $CUSTOM_YML_FILE

# ------------------------------------------------------------------------
# Customize docker-compose file
# Handle all with exception of VOLUME_MAP!
# ------------------------------------------------------------------------

sed -i "s#%USER%#$USER#g" $CUSTOM_YML_FILE
sed -i "s#%IMAGE%#$IMAGE#g" $CUSTOM_YML_FILE
sed -i "s#%VNCPORT%#$VNCPORT#g" $CUSTOM_YML_FILE
sed -i "s#%SSHPORT%#$SSHPORT#g" $CUSTOM_YML_FILE
sed -i "s#%SHINY_SERVER_PORT%#$SHINY_SERVER_PORT#g" $CUSTOM_YML_FILE  
sed -i "s#%JENKINSPORT%#$JENKINSPORT#g" $CUSTOM_YML_FILE  

sed -i "s#%HOST_ID%#$HOSTNAME#g" $CUSTOM_YML_FILE  

sed -i "s#%PASSWORD%#$PASSWORD#g" $CUSTOM_YML_FILE
sed -i "s#%USER_ID%#$USER_ID#g" $CUSTOM_YML_FILE
sed -i "s#%THEME%#$THEME#g" $CUSTOM_YML_FILE
sed -i "s#%ALLOW_SUDO%#$ALLOW_SUDO#g" $CUSTOM_YML_FILE

sed -i "s#%SHM_SIZE_GB%#$SHM_SIZE_GB#g" $CUSTOM_YML_FILE
sed -i "s#%NR_CORES%#$NR_CORES#g" $CUSTOM_YML_FILE
sed -i "s#%MEMORY_GB%#$MEMORY_GB#g" $CUSTOM_YML_FILE

sed -i "s#%ALLOW_SHINY_SERVER%#$ALLOW_SHINY_SERVER#g" $CUSTOM_YML_FILE
sed -i "s#%SSH_SERVER%#$SSH_SERVER#g" $CUSTOM_YML_FILE
sed -i "s#%TIMEZONE%#$TIMEZONE#g" $CUSTOM_YML_FILE
sed -i "s#%VNC_RESOLUTION%#$VNC_RESOLUTION#g" $CUSTOM_YML_FILE

sed -i "s#%IQREPORT_LICENSE_KEY%#$IQREPORT_LICENSE_KEY#g" $CUSTOM_YML_FILE

sed -i "s#%IQRTOOLS_COMPLIANCE%#$IQRTOOLS_COMPLIANCE#g" $CUSTOM_YML_FILE
sed -i "s#%IQREPORT_TEMPLATE%#$IQREPORT_TEMPLATE#g" $CUSTOM_YML_FILE

sed -i "s#%NONMEM_LICENSE_KEY%#$NONMEM_LICENSE_KEY#g" $CUSTOM_YML_FILE
sed -i "s#%MONOLIX_LICENSE_KEY%#$MONOLIX_LICENSE_KEY#g" $CUSTOM_YML_FILE

sed -i "s#%VNC_PRIVATE_KEY%#$VNC_PRIVATE_KEY#g" $CUSTOM_YML_FILE
sed -i "s#%VNC_CERTIFICATE%#$VNC_CERTIFICATE#g" $CUSTOM_YML_FILE

sed -i "s#%AWS_ACCESS_KEY_ID%#$AWS_ACCESS_KEY_ID#g" $CUSTOM_YML_FILE
sed -i "s#%AWS_SECRET_ACCESS_KEY%#$AWS_SECRET_ACCESS_KEY#g" $CUSTOM_YML_FILE

sed -i "s#%MATLAB_LICENSE_FILE%#$MATLAB_LICENSE_FILE#g" $CUSTOM_YML_FILE

# ------------------------------------------------------------------------
# Add MAC address if desired
# ------------------------------------------------------------------------

if [ -z $MAC ]; then
  echo "Do nothing"
else
  echo -e "\n    mac_address: $MAC" >> $CUSTOM_YML_FILE
fi

# ------------------------------------------------------------------------
# Add single share if defined ($VOLUME_MAP empty => no share)
# ------------------------------------------------------------------------

if [ -z $VOLUME_MAP ]; then
  echo "Do nothing"
else
  # Add the sharing
  echo -e "\n    volumes:\n      - $VOLUME_MAP:/IQDESKTOP/SHARE/" >> $CUSTOM_YML_FILE
fi

# ------------------------------------------------------------------------
# Start container
# ------------------------------------------------------------------------

echo "==> Starting $IMAGE docker container for $USER ..."
docker-compose -f $CUSTOM_YML_FILE up &
echo "    Running ..."
