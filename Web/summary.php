    
<?php 

session_start();
$u=$_SESSION['next'];
if(!$u){

header("location:welcome.php");

} else {
    include "configure.php";      

?> 
<html>
<style>
#table1{
float: left;
width: auto;
margin: 6px 6px 0px 0px;
text-align:center;

}
#table2{
float: left;
width: 35%;
margin: 6px 6px 0px 0px;
text-align:center;
}
#table3{
float: left;
width: auto;
margin: 6px 6px 0px 0px;
text-align:center;
}

#box{

background-color: #C0C0C0;
border: 5px solid #388E8E;
position: absolute;
padding: 1px;

}

</style>
 <title>Network Mapping @BTH</title>
      <link href="test.css" type="text/css" rel="stylesheet" />
      <a href="info1.php"><img align=left src= "http://maritimecoin.com/img/back.png" width = "55" height ="55"></a>
      <a href="welcome.php"><img align=right src= "http://www.clker.com/cliparts/W/9/B/o/q/H/logout-button-md.png" width = "70" height ="65"></a>
      <center>
      <img src="https://www.bth.se/web2009/images/head_logo.png">
<body  bgcolor="#F5F5F5"
        text="#000000">
<h4> Summary page ! </h4>
<p>
<?php

if($_GET["id"]){
$id= $_GET["id"];
$count = mysql_query("select * from `$dev_table`");
$device=mysql_query("select `IP`,`Name` from `$dev_table` where `id` = $id");
$devicenames=mysql_query("select `Dev_Name` from `$mac_table` where `Sw_rowid`=".$id);
$switch_connection= mysql_query("select `MAC_Addrs` from `$mac_table` where `Sw_rowid`=$id");
$switches= mysql_query("select `id`,`Name` from `$dev_table` where `id` != $id");
$rows = mysql_num_rows($count);
$primary_names=array();
$macs_primary=array();
$connected = array();
$count=0;

while($row = mysql_fetch_array($device)){
echo "<b>Device Selected - IP:</b> \"$row[IP]\" &nbsp;&nbsp;&nbsp; <b>Device Name:</b> \"$row[Name]\"";
echo "<br>";
}
while($row = mysql_fetch_array($devicenames)){
array_push($primary_names,$row[0]);
}
if(!empty($primary_names)){
?>
<div id="box">
<div id="table1">
<table>
<tr>
<th><i><b>Connected Devices</b></i></th>
</tr>
<?
$size=sizeof($primary_names);
for($i=0;$i <= ($size / 4) ; $i++){
$output=array_slice($primary_names, ($i*4), 4);
$string =implode("; ",$output);
?>
<tr>
<td><?echo$string;?><td>
</tr>
<?
}
}
?>
</table>
</div>
<?php
while($row = mysql_fetch_array($switch_connection)){
array_push($macs_primary,$row[0]);
}
?>
<div id="table2">
<table>
<tr>
<th><i>Inter-Connected Switches</i></th>
</tr>
<?
$seed = $id+1;
while($row = mysql_fetch_array($switches)){
$mac=mysql_query("select `MAC_Addrs`,`Ports` from `$mac_table` where `Sw_rowid`=$row[0]");
	
while($rest = mysql_fetch_array($mac)){

foreach($macs_primary as $val){

if($val == $rest['MAC_Addrs']){
array_push($connected,$row['id']);
$count++;
$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
srand($seed);
$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
$seed++;
?>
<tr>
<td>
<? echo "The switch <b><i><font color='$color'>$row[Name]</font></i></b> is connected to selected switch via port $rest[Ports].";?><br><br>
</td>
</tr>
<?php
goto end;

}

}

}
end:
}
if($rows == 1 || $count == 0){
?>
<tr>
    <td>
        This switch is not connected to any switches
    </td>
</tr>
<?
}

?>
</table>
</div>

<?
if(!empty($connected)){
?>
<div id="table3">
<table>
<tr>
<th><i>Devices to Inter-Connected Switches</i></th>
</tr>
<?

$device_names = array();
$seed = $id+1;
foreach($connected as $val){
$rest_devices = mysql_query("select `Dev_Name` from macs where `Sw_rowid` = $val");
while($rest = mysql_fetch_array($rest_devices)){
array_push($device_names,$rest[0]);
}
$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
srand($seed);
$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
$seed++;
$size=sizeof($device_names);
for($i=0;$i <= ($size / 4) ; $i++){
$output=array_slice($device_names, ($i*4), 4);
$string =implode("; ",$output);
echo "<tr><td><font color='$color'>$string</font></td></tr>";
}
$device_names = array();?>
<tr><td><br></td></tr>
<?
}

?>
</table>
</div>
</div>
<div id="footer" style="clear:both;text-align:center;">    
<br>    
<p align='center'><font size="5" color="red" ><strong>Note:</strong> This Tool is developed by Team3 for ANM project purpose only.</font></p>
<p style ="background-color:#FFA500;">Copyright&#169 2K14_ANM_Team3</p>
</div>
<?

}

}
}
?>

