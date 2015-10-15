<!doctype html>
<?php 

session_start();

if(!isset($_SESSION["sess_user"])){
          //echo "<script type='text/javascript'>alert('Authorization Denied');</script>";
          //sleep(3);

header("location:welcome.php");

} else {

?>
<html>
      <title>Network Mapping @BTH</title>
      <a href="welcome.php"><img align=right src= "http://img3.wikia.nocookie.net/__cb20131126195004/epicrapbattlesofhistory/images/a/aa/Home_icon_grey.png" width = "55" height ="55"></a>
      <center>
      <img src="https://www.bth.se/web2009/images/head_logo.png">
<head>
<body  bgcolor="#F5F5F5"
        text="#000000">

<h3> PUBLIC PAGE !</h3>
<h4>Device Info !</h4>
<p>

<?php
require ('configure.php');
header("Refresh :4,info.php");
// Issue the query
$result = mysql_query("SELECT * FROM `".$dev_table."`");

if (!$result){
            echo "<p><h5>Right now There are no Devices available to view as the Input Table Doesn't Exists in the Database.</h5></p>";
            echo "<p><h5>Please Come back again after sometime. Thank You.</h5></p>";
}else {
// Capture the result in an array, and loop through the array
$row = mysql_num_rows($result);
if ($row == 0) {
            echo "<p><h5>Right now There are no Devices available to view as the Input Table is EMPTY in the Database.</h5></p>";
            echo "<p><h5>Please Come back again after sometime. Thank You.</h5></p>";
}else {
echo "<table border='1' cellpadding='3' cellspacing='1'>
<tr bgcolor='#C0C0C0'>
<th> IP </th>
<th> Name </th>
<th> Submit </th>
</tr>";

$i=1;

$result1 = mysql_query("SELECT * FROM nondevices");

while($row = mysql_fetch_array($result))
{
      if(isset($_POST["submit$i"])){
        $id=$row['id'];
        $ip=$row['IP'];
        $name=$row['Name'];
            
        while($row1 = mysql_fetch_array($result1))
        {
            $dbip = $row1['IP'];
            $check = $row1['Active'];
            if($ip == $dbip){
                  if ($check == "no"){
                        #echo "There is no response with $ip";
                        echo "<script type='text/javascript'>alert('There is no response with $ip');</script>";
                        $sql = mysql_query("DELETE FROM `$dev_table` WHERE `id`='".$id."'");
                        $sql1 = mysql_query("DELETE FROM `nondevices` WHERE `IP`='".$dbip."'");
                        #header("Refresh: 0;info.php");
                  }else {
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
            
            $query1 = mysql_query("SELECT * FROM `".$dev_table."` WHERE `IP`='".$ip."'") or die ("Query error". mysql_error());
            
            if($query1)
            {
                header("Location: ports.php");
            }
        }
      }
      else
        {
            echo "<form action='info.php' method=POST>";    
            echo "<tr>";
            echo "<td>" . $row['IP'] . "</td>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>". "<input type=submit name=submit" . $i . " value=Details>" . " </td>";
        }
        
      $i++;  
}
        echo"</form>";
        echo "</table>";
}
}
?>

<br><br><br><br><br><br>
<br><br><br><br><br>
    
    <br><br><br><br>
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


