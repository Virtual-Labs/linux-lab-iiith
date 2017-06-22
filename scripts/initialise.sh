#!/bin/bash


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
 sudo apt-get install graphviz -y
 sudo apt-get install ldapscripts -y
 sudo apt-get install php5-ldap -y
 sudo apt-get install zenity -y
}

set_proxy
install_packages

