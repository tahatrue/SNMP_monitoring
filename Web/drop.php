<!doctype html>
<?php 

session_start();

if(!isset($_SESSION['uname'])){
    //echo "<script type='text/javascript'>alert('Authorization Denied');</script>";
    //sleep(3);
header("location:welcome.php");


} else {

    include "configure.php";      
    #header("Refresh :15; drop.php");
    header("Refresh: 3; drop.php");

?>
<html>
<head>
      <title>Network Mapping @BTH</title>
      <link href="test.css" type="text/css" rel="stylesheet" />
        <a href="info1.php"><img align=left src= "http://maritimecoin.com/img/back.png" width = "55" height ="55"></a>
        <a href="welcome.php"><img align=right src= "http://www.clker.com/cliparts/W/9/B/o/q/H/logout-button-md.png" width = "70" height ="65"></a>
        <center>
      <img src="https://www.bth.se/web2009/images/head_logo.png">
<body  bgcolor="#F5F5F5"
        text="#000000">

<h3> MEMBER PAGE !</h3>

<h4> Devices Info ! </h4>
<p>

<?php
require ('configure.php');
$check = mysql_query("SHOW TABLES");
$res = mysql_num_rows($check);
if($res > 0){
// Issue the query
$result = mysql_query("SELECT * FROM `".$dev_table."`");
        if($result){
            $sql = mysql_query("DROP TABLE IF EXISTS `".$dev_table."` ") or die ("Cannot drop input table \"$dev_table\" : ". mysql_error()."\n");
            if($sql){
                echo "<p><h5>The Input Table \"$dev_table\" is Successfully Dropped. Use the option \"Create Input Table\" to create input table again.</h5></p>";
                #echo "<script type='text/javascript'>alert('The input table \"$dev_table\" is already exists, so no need to create again');</script>";
                #header("Location: info1.php");
                header("Refresh :1; info1.php");
            }        
        }else {
            echo "<p><h5>The Input Table \"$dev_table\" Doesn't exists in the Database to Delete it. Create the input table before you Delete it.</h5></p>";
            #echo "<script type='text/javascript'>alert('The input table \"$dev_table\" is already exists, so no need to create again');</script>";
            #header("Location: info1.php");
            header("Refresh :1; info1.php");
        }

$result = mysql_query("SELECT * FROM `".$port_table."`");
if($result){
$sql = mysql_query("DROP TABLE IF EXISTS `".$port_table."` ") or die ("Cannot drop input table \"$port_table\" : ". mysql_error()."\n");
}
$result1 = mysql_query("SELECT * FROM `".$vlan_table."`");
if($result1){
$sql1 = mysql_query("DROP TABLE IF EXISTS `".$vlan_table."` ") or die ("Cannot drop input table \"$vlan_table\" : ". mysql_error()."\n");
}    
$result2 = mysql_query("SELECT * FROM `".$mac_table."`");
if($result2){
$sql2 = mysql_query("DROP TABLE IF EXISTS `".$mac_table."` ") or die ("Cannot drop input table \"$mac_table\" : ". mysql_error()."\n");
}                    
$result3 = mysql_query("SELECT * FROM `nondevices`");
if($result3){
$sql = mysql_query("DROP TABLE IF EXISTS `nondevices` ") or die ("Cannot drop input table \"nondevices\" : ". mysql_error()."\n");
}    
echo "<p><h5>ALL Tables are Successfully Dropped. Use the option \"Create Input Table\" to create input table again.</h5></p>";
header("Refresh: 3; info1.php");
}else {
    echo "<p><h5>The one or many tables Doesn't exists in the Database to Delete them. Create the table before you Delete them.</h5></p>";
    header("Refresh: 3; info1.php");
}

?>
    <br><br><br><br>
    <br><br><br><br><br>
    <br><br>
<div id="footer" style="clear:both;text-align:center;">    
<br>    
<p align='center'><font size="5" color="red" ><strong>Note:</strong> This Tool is developed by Team3 for ANM project purpose only.</font></p>
<p style ="background-color:#FFA500;">Copyright&#169 2K14_ANM_Team3</p>
</div>

</p>
</body>
</head>
</html>
<?php
}
?>


