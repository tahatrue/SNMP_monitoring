<?php


$host="localhost";
$data_base="team3";
$db_user="root";
$db_pw="1";
$db_port="1161";
$dev_table="switch";
$port_table="ports";
$vlan_table="vlans";
$mac_table="macs";

mysql_connect("$host","$db_user","$db_pw") or die("Unable to Connect to MySQL".mysql_error());
mysql_select_db("$data_base") or die("Unable to Select the DB".mysql_error());
?>