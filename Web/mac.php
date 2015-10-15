<!doctype html>
<script type="text/javascript">  
  function DoNav(theUrl)
  {
  document.location.href = theUrl;
  }
</script>
<style>	  
#leftbox {
float: left;
width: 50%;
margin: 6px 6px 0px 0px;
text-align:center;
}
#rightbox {
float: right;
width: 38%;
margin: 6px 6px 0px 0px;
text-align:center;
}
</style>


<?php 
//$i=0;
session_start();
$u=$_SESSION['mid'];
if(!$u){
header("location:welcome.php");

} else {
    include "configure.php";      
    #header("Refresh: 3; mac.php");
    header("url=mac.php");

?>                
<html>
<head>
      <title>Network Mapping @BTH</title>
      <link href="test.css" type="text/css" rel="stylesheet" />
      <a href="ports1.php"><img align=left src= "http://maritimecoin.com/img/back.png" width = "55" height ="55"></a>
      <a href="welcome.php"><img align=right src= "http://www.clker.com/cliparts/W/9/B/o/q/H/logout-button-md.png" width = "70" height ="65"></a>
      <center>
      <img src="https://www.bth.se/web2009/images/head_logo.png">
<body  bgcolor="#F5F5F5"
        text="#000000">

<h3> MEMBER PAGE !</h3>
<h4>List of MAC Addresses Connected to Device !</h4>
<p>

<?php
require ('configure.php');


$id = $_SESSION['id'];
$ip = $_SESSION['IP'];
$name = $_SESSION['Name'];

echo "<b>Device Selected - IP:</b> \"$ip\" &nbsp;&nbsp;&nbsp; <b>Device Name:</b> \"$name\"";
echo "<br>";
?>
<div id="nav" style="height: auto;width: 87px;float: left;">
            <div id="nav_wrapper">
                <ul>
                    <li><a href="#">Options<img src="http://www.mozartinshape.org/images/dropdown_arrow.png"/></a>
                    <ul>
                        <li><a href="insert.php">Add Device</a></li><li>
                        <a href="info1.php">Back to Device List</a></li>
                    </ul>
                    </li>
                </ul>
            </div>
        </div>
       
<div id='leftbox'>
<?
// Issue the query
$result = mysql_query("SELECT * FROM `".$mac_table."` WHERE `Sw_rowid`= '".$id."'") or die(mysql_error());

// Capture the result in an array, and loop through the array
echo "<table width ='80%' border='1' cellpadding='3' cellspacing='1' align='center'>
<tr bgcolor='#C0C0C0'>

<th> MAC_Addrs </th>
<th> Ports </th>
<th> Device_Name </th>
<th>Edit</th>
</tr>";
?>

<?
while($row = mysql_fetch_array($result)) 
{

$dname=$row['Dev_Name'];
$mac=$row['MAC_Addrs'];
$i=$row['ID'];
if($dname == ""){
//echo "<td>".$dname."</td>";
//echo "<td> Device ".$id.".".$i."</td>";
$opt="Device $id.$i";
    $check1 = mysql_query("UPDATE `".$mac_table."` SET Dev_Name='$opt' WHERE MAC_Addrs='$mac'") or die ("Query Error". mysql_error());
    header("Refresh: 1; mac.php");
}
    //header("Refresh: 1; mac1.php");
$url="mac.php?mac=$mac";
echo "<tr>";

echo "<td>" . $row['MAC_Addrs'] . "</td>";
echo "<td>" . $row['Ports'] . "</td>";    
echo "<td>" . $row['Dev_Name'] . "</td>";
echo "<td align=center><input type=radio onclick= DoNav('".$url."')></td>";
echo "</tr>";
//echo $dname;
}
echo "</table>";


// Free the result set    
mysql_free_result($result);
?>
</div>

<?
if($_GET["mac"]){
$id=($_GET["mac"]);
?>
<div id='rightbox'>
<form action="mac.php?mac=<?echo$id;?>" method="post" action="">
<label><strong>Enter Device Name for MAC => <?echo $id?></strong> </label><br/>
<INPUT TYPE = "TEXT" NAME ="devicename" SIZE = "50"/><br/>
<input name = "submit" type ="submit" value="Save"/>
</form>

<?

if($_POST['submit'])
{

  $devicename = $_POST["devicename"];
	if(!empty($devicename)){
		#echo  $devicename;
		#echo $id;
		$sql = "update `$mac_table` set `Dev_Name`= '$devicename' where MAC_Addrs = '$id'";
		$sql2 =mysql_query("select `Dev_Name` from `$mac_table` where`Dev_Name`='".$devicename."'") or die ("Query Error". mysql_error());		
		#echo $sql;
		$check =array();
		$check = mysql_fetch_array($sql2);		
		if(empty($check[0])){ 
		
		mysql_query($sql) or die ("Query Error". mysql_error());
		header("location:mac.php");
				}
		else{
                    echo "<script type='text/javascript'>alert('Device name already exists.Enter new name!');</script>";
		    //echo"Device name already exists.Enter new name!";
			}
				}
	else{
                //echo "<script type='text/javascript'>alert('Device name can't be empty!');</script>";
		echo"Device name can't be empty!";
	    }
}
?>
</div>
<?
}
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
