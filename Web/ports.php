<!doctype html>
<?php 

session_start();

if(!isset($_SESSION["sess_user"])){

header("location:welcome.php");

} else {

?>
<html>
      <title>Network Mapping @BTH</title>
      <a href="info.php"><img align=left src= "http://maritimecoin.com/img/back.png" width = "55" height ="55"></a>
      <a href="welcome.php"><img align=right src= "http://img3.wikia.nocookie.net/__cb20131126195004/epicrapbattlesofhistory/images/a/aa/Home_icon_grey.png" width = "55" height ="55"></a>
      <center>
      <img src="https://www.bth.se/web2009/images/head_logo.png">
<head>

<body  bgcolor="#F5F5F5"
        text="#000000">

<h3> PUBLIC PAGE !</h3>
<h4>Advanced Device Details !</h4>
<p>

<?php

require ('configure.php');
header("Refresh: 4; ports.php");

$id = $_SESSION['id'];
$ip = $_SESSION['IP'];
$name = $_SESSION['Name'];
echo "<b>Device Selected - IP:</b> \"$ip\" &nbsp;&nbsp;&nbsp; <b>Device Name:</b> \"$name\"<br>";
// Issue the query
$result = mysql_query("SELECT * FROM `".$port_table."` WHERE `Sw_rowid`= '".$id."'") or die(mysql_error());

// Capture the result in an array, and loop through the array
echo "<table border='1' cellpadding='3' cellspacing='1'>
<tr bgcolor='#C0C0C0'>
<th> Port_Name </th>
<th> Status </th>
<th> Speed </th>
<th> Port_Type </th>


</tr>";

while($row = mysql_fetch_array($result)) 
{
if ($row['Status'] == "UP"){
echo "<tr bgcolor='#9ACD32'>";
}else{
echo "<tr bgcolor='#F5F5F5'>";
}
echo "<td>" . $row['Port_Name'] . "</td>";
echo "<td>" . $row['Status'] . "</td>";
echo "<td>" . $row['Speed'] . "</td>";
echo "<td>" . $row['Port_Type'] . "</td>";


echo "</tr>";

}

echo "</table>";

?>
<div id="footer" style="clear:both;text-align:center;">    
<br>    
<p align='center'><font size="5" color="red" ><strong>Note:</strong> This Tool is developed by Team3 for ANM project purpose only.</font></p>
<p style ="background-color:#FFA500;">Copyright&#169 2K14_ANM_Team3</p>
</div>


</p>
</body>
</head>
</html>
<?
}?>

