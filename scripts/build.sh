#!/bin/bash

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

install_packages
build_lab
