<!doctype html>
<script type="text/javascript">  
  function DoNav(theUrl)
  {
  document.location.href = theUrl;
  }
</script>
<?php

session_start();

if(!isset($_SESSION['uname'])){
    header("location:welcome.php");
} else {
    include "configure.php";
    //require "mac.php";
    header("Refresh: 4; info1.php");
    #header("Refresh :15,info1.php");

?>
<html>
<head>
      <title>Network Mapping @BTH</title>
      <link href="test.css" type="text/css" rel="stylesheet" />
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

// Issue the query
$result = mysql_query("SELECT * FROM `".$dev_table."`");

if (!$result){           
            echo "<p><h5>The Input Table \"$dev_table\" Doesn't Exists in the Database. Use the option \"Create Input Table\" to create input table again.</h5></p>";
            echo "<p><h5>Thank You.</h5></p>";?>
            <div id="nav" style="height: auto;width: 87px;float: left;">
            <div id="nav_wrapper">
                <ul>
                    <li><a href="#">Options<img src="http://www.mozartinshape.org/images/dropdown_arrow.png"/></a>
                    <ul>
                        <li><a href="create.php">Create Input Table</a></li>
                    </ul>
                    </li>
                </ul>
            </div>
        </div><? 
}else {
    $row = mysql_num_rows($result);
    if ($row == 0) {
            echo "<p><h5>Right now There are no Devices available to view as the Input Table is EMPTY in the Database.</h5></p>";
            echo "<p><h5>Please Use the option \"Add a Switch Deivce\" to add a device. Thank You.</h5></p>";?>
            <div id="nav" style="height: auto;width: 87px;float: left;">
            <div id="nav_wrapper">
                <ul>
                    <li><a href="#">Options<img src="http://www.mozartinshape.org/images/dropdown_arrow.png"/></a>
                    <ul>
                        <li><a href="insert.php">Add a Switch Device</a></li>
                        <li><a href="drop.php">Drop All Tables</a></li>
                    </ul>
                    </li>
                </ul>
            </div>
        </div><?
    }else {
    
// Capture the result in an array, and loop through the array
echo "<table border='1' cellpadding='3' cellspacing='1'>
<tr bgcolor='#C0C0C0'>
<th> IP </th>
<th> Name </th>
<th> Submit </th>
<th> Summary </th>
</tr>";
$_SESSION['next']=1;
$i=1;?>
        <div id="nav" style="height: auto;width: 87px;float: left;">
            <div id="nav_wrapper">
                <ul>
                    <li><a href="#">Options<img src="http://www.mozartinshape.org/images/dropdown_arrow.png"/></a>
                    <ul>
                        <li><a href="insert.php">Add a Switch Device</a></li>
                        <li><a href="drop.php">Drop All Tables</a></li>
                        <li><a href="dropselect.php">Drop Selected Row from I/P Table</a></li>
                    </ul>
                    </li>
                </ul>
            </div>
        </div>

<?
$result1 = mysql_query("SELECT * FROM nondevices");

 
while($row = mysql_fetch_array($result))
{
    $id=$row['id'];
      if(isset($_POST["submit$i"]))
     {
        
        $ip=$row['IP'];
        $name=$row['Name'];
     
        while($row1 = mysql_fetch_array($result1))
        {
            $dbip = $row1['IP'];
            $check = $row1['Active'];
            if($ip == $dbip){
                  if ($check == "no"){
                        #echo "There is no response with $ip";
                        echo "<script type='text/javascript'>alert('There is no response with the given IP:$ip');</script>";
                        $sql = mysql_query("DELETE FROM `$dev_table` WHERE `id`='".$id."'");
                        $sql1 = mysql_query("DELETE FROM `nondevices` WHERE `IP`='".$dbip."'");
                        #header("Refresh: 0;info.php");
                  }elseif($check == "disabled") {
                        #echo "There is a problem with the $ip details, Please add again with correct details<br>";
                        echo "<script type='text/javascript'>alert('There is a problem with the \"$ip\" details, Please add again with correct details');</script>";
                        $sql = mysql_query("DELETE FROM `$dev_table` WHERE `id`='".$id."'");
                        $sql1 = mysql_query("DELETE FROM `nondevices` WHERE `IP`='".$dbip."'");
                        #header("Refresh: 3;info.php");
                  }
                  $k=1;
            }
        }
        #$id=1;
        if ($k!=1){
            $_SESSION['id']= $id;
            $_SESSION['IP']= $ip;
            $_SESSION['Name']= $name;
        
            $query1 = mysql_query("SELECT * FROM `".$dev_table."` WHERE `id`='".$id."'") or die ("Query error". mysql_error());
        
            if($query1){
               header("Location: ports1.php");
            }
        }
    }
    else
        {
            echo "<form action='info1.php' method=POST>";    
            echo "<tr>";
            echo "<td>" . $row['IP'] . "</td>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>". "<input type=submit name=submit" . $i . " value=Details>" . " </td>";
		$url="summary.php?id=".$row['id'];
	    echo "<td align='center'>". "<input type=radio  value=".$row['id']." onclick= DoNav('".$url."')> </td>";

        }
        $i++;
        $result3 = mysql_query("show tables from `$data_base` where `Tables_in_$data_base` like '%".$mac_table."%'");
        $tables = mysql_num_rows($result3);
        if($tables !=0){
            $result2 = mysql_query("SELECT * FROM `".$mac_table."` WHERE `Sw_rowid`= '".$id."'") or die(mysql_error());
            while($row = mysql_fetch_array($result2)){
                $dname=$row['Dev_Name'];
                $mac=$row['MAC_Addrs'];
                $i=$row['ID'];
                if($dname == ""){
                    //echo "<td>".$dname."</td>";
                    //echo "<td> Device ".$id.".".$i."</td>";
                    $opt="Device $id.$i";
                    $check1 = mysql_query("UPDATE `".$mac_table."` SET Dev_Name='$opt' WHERE MAC_Addrs='$mac'") or die ("Query Error". mysql_error());
                }
            }
        }
}
        echo"</form>";
        echo "</table>";

}
}
// Free the result set    
#mysql_free_result($result);
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


