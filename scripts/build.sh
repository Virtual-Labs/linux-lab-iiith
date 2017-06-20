#!/bin/bash

ldap_config=/etc/ldapscripts/ldapscripts.conf
ldap_exec=/var/www/html/php/ldapexec.php

function install_packages()
{
 sudo apt-get install php5 -y
 sudo apt-get install libgd2-noxpm -y
 sudo apt-get install libgd2-xpm -y
 sudo apt-get install libgvc5 -y
 sudo apt-get install graphviz -y
 sudo apt-get install ldapscripts -y
 sudo apt-get install php5-ldap
}

function build_lab()
{
 cd ~/linux-lab-iiith/src/
 make
 sudo rsync -ar ~/linux-lab-iiith/build/ /var/www
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
    sed '0,/$ldap_admin_pass =.*/s//$ldap_admin_pass = "$ldap_password"/' $ldap_exec
 fi
}

install_packages
build_lab
update_ldapscripts
create_password_file
