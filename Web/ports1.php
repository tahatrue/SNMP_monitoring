<!doctype html>
<?php 

session_start();
$u=$_SESSION['next'];
if(!$u){

header("location:welcome.php");

} else {
    include "configure.php";      
    header("Refresh: 4; ports1.php");

?>                
<html>
<head>
      <title>Network Mapping @BTH</title>
      <a href="info1.php"><img align=left src= "http://maritimecoin.com/img/back.png" width = "55" height ="55"></a>
      <a href="welcome.php"><img align=right src= "http://www.clker.com/cliparts/W/9/B/o/q/H/logout-button-md.png" width = "70" height ="65"></a>
      <center>
      <img src="https://www.bth.se/web2009/images/head_logo.png">
<body  bgcolor="#F5F5F5"
        text="#000000">
<h3> MEMBER PAGE !</h3>

<h4>Advanced Devices Details ! </h4>
<p>
    
<?php
if(isset($_POST["info"]))
    {
        header("Location: mac.php");
    }
?>
<?php
require ('configure.php');
$_SESSION['mid']=1;
$id = $_SESSION['id'];
$ip = $_SESSION['IP'];
$name = $_SESSION['Name'];

echo "<b>Device Selected - IP:</b> \"$ip\" &nbsp;&nbsp;&nbsp; <b>Device Name:</b> \"$name\"";
echo "<br>";

$result2 = mysql_query("SELECT * FROM `".$port_table."` WHERE `Sw_rowid`= '".$id."'") or die(mysql_error());
$i=0;
echo "<tr>";

echo "<td> ID -----> </td>";

while($fetch = mysql_fetch_array($result2)) 
{ 
    if ($fetch['Port_Type'] == "l2vlan")
    {
        $vlan[$i]= $fetch['ID'];
        
#echo "<tr>";
echo "<td> $vlan[$i], </td>";
echo "</tr>";
    }
$i++;
}
echo "<br>";

echo "<tr>";
echo "<td> VLAN_ID ----> </td>";

// Issue the query
$result3 = mysql_query("SELECT * FROM `".$vlan_table."` WHERE `Sw_rowid`= '".$id."'") or die(mysql_error());

// Capture the result in an array, and loop through the array

$i=0;
while($row2 = mysql_fetch_array($result3)) 
{

$vlan_number[$i]= $row2['Vlan_Number'];
echo "<td> $vlan_number[$i], </td>";
echo "</tr>";
$i++;
}

// Issue the query
$result = mysql_query("SELECT * FROM `".$port_table."` WHERE `Sw_rowid`= '".$id."'") or die(mysql_error());

// Capture the result in an array, and loop through the array
echo "<table border='1' cellpadding='3' cellspacing='1'>
<tr bgcolor='#C0C0C0'>
<th> Port_Name </th>
<th> Status </th>
<th> Speed </th>
<th> Port_Type </th>
<th> Phy_Addr </th>
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
echo "<td>" . $row['Phy_Addr'] . "</td>";
echo "</tr>";
}
echo "</table>";?></p>
<form action="" method="POST">
<input type="submit" value="More Info" name="info" />
<br />
</form>
<?
// Free the result set    
#mysql_free_result($result);
?> 
<div id="footer" style="clear:both;text-align:center;">    
<br>    
<p align='center'><font size="5" color="red" ><strong>Note:</strong> This Tool is developed by Team3 for ANM project purpose only.</font></p>
<p style ="background-color:#FFA500;">Copyright&#169 2K14_ANM_Team3</p>
</div>
</p>
</body>
</html>
<?
}
?>

