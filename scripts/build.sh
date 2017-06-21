#!/bin/bash

ldap_config=/etc/ldapscripts/ldapscripts.conf
ldap_exec=/var/www/html/php/ldapexec.php
ldap_runtime=/usr/share/ldapscripts/runtime
gateone=/opt/gateone/server.conf
nsswitch=/etc/nsswitch.conf
port=8000
content_html=/var/www/html/content.html
frame_html=/var/www/html/exp4/interaction-frame.html

######## Function for setting proxy
function set_proxy()
{
###### Adding proxy in ~/.bashrc file

  LINE='export http_proxy="http://proxy.iiit.ac.in:8080"'
  FILE=~/.bashrc
  grep -qF "$LINE" "$FILE" || echo "$LINE" >> "$FILE" 
  LINE='export https_proxy="http://proxy.iiit.ac.in:8080"'
  FILE=~/.bashrc
  grep -qF "$LINE" "$FILE" || echo "$LINE" >> "$FILE"

  export http_proxy="http://proxy.iiit.ac.in:8080"
  export https_proxy="http://proxy.iiit.ac.in:8080"

####### Adding proxy in /etc/apt/apt.conf

  touch /etc/apt/apt.conf
  LINE='Acquire::http::Proxy "http://proxy.iiit.ac.in:8080";'
  FILE=/etc/apt/apt.conf
  grep -qF "$LINE" "$FILE" || echo "$LINE" >> "$FILE"
  
  LINE='Acquire::https::Proxy "http://proxy.iiit.ac.in:8080";'
  FILE=/etc/apt/apt.conf
  grep -qF "$LINE" "$FILE" || echo "$LINE" >> "$FILE"
 
}
function install_packages()
{
 sudo apt-get update -y
 sudo apt-get install git -y
 sudo apt-get install php5 -y
# sudo apt-get install libgd2-noxpm -y
# sudo apt-get install libgd2-xpm -y
# sudo apt-get install libgvc5 -y
 sudo apt-get install graphviz -y
 sudo apt-get install ldapscripts -y
 sudo apt-get install php5-ldap
}

function build_lab()
{
 ########################
 #git clone https://github.com/Virtual-Labs/linux-lab-iiith.git
 #cd ~/linux-lab-iiith/
 #git checkout linux-lab-on-single-host
 ########################

 #cd ~/linux-lab-iiith/src/
 #make
 sudo rsync -ar ~/linux-lab-iiith/build/ /var/www/html #### added /html 
}

function update_ldapscripts()
{
 sed -i 's/#SERVER=.*/SERVER="ldap:\/\/localhost"/g' "$ldap_config"
 sed -i "s/#SUFFIX=.*/SUFFIX='dc=virtual-labs,dc=ac,dc=in'/g" "$ldap_config"
 sed -i "s/#GSUFFIX=.*/GSUFFIX='ou=Group'/g" "$ldap_config"
 sed -i "s/#USUFFIX=.*/USUFFIX='ou=People'/g" "$ldap_config"
 sed -i "s/#MSUFFIX=.*/MSUFFIX='ou=Computers'/g" "$ldap_config"
 sed -i "s/BINDDN=.*/BINDDN='cn=admin,dc=virtual-labs,dc=ac,dc=in'/g" "$ldap_config"
 sed -i 's/MIDSTART=.*/MIDSTART="10000"/g' "$ldap_config"
}

function create_password_file()
{
 sudo sh -c "echo -n 'password' > /etc/ldapscripts/ldapscripts.passwd"
 sudo chmod 440 /etc/ldapscripts/ldapscripts.passwd
}

function update_ldap_runtime()
{
 sed -i '0,/USER=.*/s//USER=$(whoami 2>\/dev\/null)/' $ldap_runtime
}

function add_www-data_to_root-group()
{
 usermod -a -G root www-data
}

function restart_apache2()
{
 service apache2 restart
}

#################### Gateone Server

function install_tornado_and_python-support()
{
 sudo apt-get install python-pip -y
 pip install tornado==2.4.1
 sudo apt-get install python-support -y
}

function download_and_install_gateone()
{
 wget https://github.com/downloads/liftoff/GateOne/gateone_1.1-1_all.deb -P ~
 dpkg -i ~/gateone*.deb
}

function generate_server_conf()
{
 cd /opt/gateone
 ./gateone.py &
 # Get its PID
 PID=$!
 # Wait for 4 seconds
 sleep 4
 # Kill it
 kill $PID
 cd -
}

function update_gateone_config()
{
 echo "enter the available port for gateone server: "
 read port
 sed -i '0,/PORT =.*/s//PORT = $port/' $gateone
 echo "enter the ip for gateone server: "
 read ip
 sed -i '0,/origins =.*/s//origins = "http:\/\/localhost;https:\/\/localhost;http:\/\/127.0.0.1;https:\/\/127.0.0.1;https:\/\/test;https:\/\/$ip:$port"
 /' $gateone
}

###################################### Gateone Server END


######################################## SSH server
function install_nscd()
{
#export DEBIAN_FRONTEND=noninteractive ## For making non-interactive
sudo apt-get install libpam-ldap nscd
}


function modify_nsswitch_conf()
{
 sed '0,/passwd:.*/s//passwd:         ldap compat/' $nsswitch
 sed '0,/group:.*/s//group:         ldap compat/' $nsswitch
 sed '0,/shadow:.*/s//shadow:         ldap compat/' $nsswitch
 sed '0,/hosts:.*/s//hosts:         files dns ldap/' $nsswitch
}

function edit_common_session()
{
  LINE='session required pam_mkhomedir.so skel=/etc/skel umask=0022'
  FILE=/etc/pam.d/common-session
  grep -qF "$LINE" "$FILE" || echo "$LINE" >> "$FILE"
}

function restart_nscd()
{
 /etc/init.d/nscd restart
}
######################################## SSH server END


######################################## LDAP server configuration
function install_ldap()
{
 sudo apt-get install slapd ldap-utils
}

function configure_slapd()
{
 dpkg-reconfigure slapd
}
function create_organizational_units()
{
 touch ~/units.ldif ~/group.ldif ~/testuser1.ldif
 echo "dn: ou=People,dc=virtual-labs,dc=ac,dc=in
ou: People
objectClass: organizationalUnit

dn: ou=Group,dc=virtual-labs,dc=ac,dc=in
ou: Group
objectClass: organizationalUnit" > ~/units.ldif

 echo "dn: cn=vlusers,ou=Group,dc=virtual-labs,dc=ac,dc=in
cn: vlusers
gidNumber: 20000
objectClass: top
objectClass: posixGroup" > ~/group.ldif

 echo "dn: uid=testuser1,ou=People,dc=virtual-labs,dc=ac,dc=in
uid: testuser1
uidNumber: 20000
gidNumber: 20000
cn: Test User 1
sn: User
objectClass: top
objectClass: person
objectClass: posixAccount
objectClass: shadowAccount
loginShell: /bin/bash
homeDirectory: /home/testuser1" > ~/testuser1.ldif

ldapadd -x -D 'cn=admin,dc=virtual-labs,dc=ac,dc=in' -W -f units.ldif
ldapadd -x -D 'cn=admin,dc=virtual-labs,dc=ac,dc=in' -W -f group.ldif
ldapadd -x -D 'cn=admin,dc=virtual-labs,dc=ac,dc=in' -W -f testuser1.ldif

}

function create_ldap_log_file()
{
 touch /var/log/ldapscripts.log
 chmod o-r /var/log/ldapscripts.log
 chown www-data:www-data /var/log/ldapscripts.log
}
 
function update_ldapexec_file()
{
 echo "enter ldap ip: "
 read ldap_ip
 sed '0,/$ldap_host =.*/s//$ldap_host = "$ldap_ip"/' $ldap_exec
 echo "enter ldap password: "
 read -s ldap_password
 echo "confirm password: "
 read -s ldap_confirm_password

 if [ $ldap_password != $ldap_confirm_password ]
 then
    echo "password does not match"
 else
    sed -i '0,/$ldap_admin_pass =.*/s//$ldap_admin_pass = "$ldap_password"/' $ldap_exec
 fi
}

######################################## ldap END
function final_setup()
{
 echo "enter gateone ip: "
 read gateone_ip
 echo "enter gateone port: "
 read gateone_port
 sed -i '0,/.*.gateone.*/s//                accessed <a href="https:\/\/$gateone_ip:$gateone_port">here<\/a>./' $content_html
 sed -i '0,/.*.gateone.*/s//    <frame src="http:\/\/$gateone_ip:$gateone_port" \/>/' $frame_html

 /opt/gateone/gateone.py > /dev/null &
 sudo service apache2 restart
}
######################################## FINAL setup


#######################################

set_proxy
install_packages
build_lab
update_ldapscripts
create_password_file
update_ldap_runtime
add_www-data_to_root-group
restart_apache2

########### gateone
install_tornado_and_python-support
download_and_install_gateone
generate_server_conf
update_gateone_config
###################

########### ssh
install_nscd
#configure_ldap
modify_nsswitch_conf
edit_common_session
restart_nscd
###############

###### ldap
install_ldap
configure_slapd
create_organizational_units
create_ldap_log_file
update_ldapexec_file
##########

final_setup
