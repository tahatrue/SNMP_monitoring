<!doctype html>
<?php 

session_start();

if(!isset($_SESSION['uname'])){
    //echo "<script type='text/javascript'>alert('Authorization Denied');</script>";
    //sleep(3);
header("location:welcome.php");

} else {

    include "configure.php";      
    #header("Refresh :10; dropselect.php");

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

$check = mysql_query("select * from `".$dev_table."`");
$res = mysql_num_rows($check);
#echo "rows: $res";
?>
       <div id="nav" style="height: auto;width: 87px;float: left;">
            <div id="nav_wrapper">
                <ul>
                    <li><a href="#">Options<img src="http://www.mozartinshape.org/images/dropdown_arrow.png"/></a>
                    <ul>
                        <li><a href="insert.php">Add a Switch Device</a></li>
                        <li><a href="drop.php">Drop All Tables</a></li>
                        <li><a href="dropselect.php">Remove Selected Switch</a></li>
                    </ul>
                    </li>
                </ul>
            </div>
        </div><?
if($res > 0){

echo "<table border='1' cellpadding='3' cellspacing='1'>
<tr bgcolor='#C0C0C0'>
<th> IP </th>
<th> Name </th>
<th> Delete </th>
</tr>";
?>
 

<?
$result = "SELECT * FROM `".$dev_table."`";

$res=mysql_query($result) or die(mysql_error());


echo "<form method=POST action='dropselect.php'>";

 while($row=mysql_fetch_array($res))
 {

    $id=$row['id'];
 echo "<tr>";
    //echo "<td>". "<input name=checkbox[] type=checkbox id=checkbox[] value=$rows['IP'] >" . "</td>";
    
    echo "<td>" . $row['IP'] . "</td>";
    echo "<td>" . $row['Name'] . "</td>";
    //echo "<td>". "<input type = button name=submit" . $i . " value=Details>" . " </td>";
    echo "<td><input type = submit name= delete$id value= Delete ></td>"; echo "</tr>";

  }

   echo "<tr align=center> <td colspan=3><input type = submit value= Delete_All name=delete_all > </td></tr>";
 echo"</form>";
 echo "</table>";
// Check if delete button active, start this
$count = mysql_num_rows($res);
//echo "$count";
$res1 = mysql_query("SELECT * FROM `".$dev_table."`");
while($row1=mysql_fetch_array($res1))
{
    $id1 = $row1['id'];
    if(isset($_POST["delete$id1"]))
    {
        $sql = "DELETE FROM `".$dev_table."` WHERE id='$id1'";
        $delete = mysql_query($sql);
        if($delete)
        {
            echo "<p><h5>Record deleted Successfully.</h5></p>";
            header("Refresh: 1; dropselect.php");
        }
    }
}
if(isset($_POST['delete_all']))
{
    $del_all = mysql_query("DELETE FROM `".$dev_table."` WHERE 1");
    if($del_all)
    {
        echo "<p><h5>Table is flushed.</h5></p>";
    }
}
#echo "<p><h5>ALL Tables are Successfully Dropped. Use the option \"Create Input Table\" to create input table again.</h5></p>";
header("Refresh: 1; dropselect.php");
}else {
    echo "<p><h5>The are No Rows in the table to Delete. Create the Rows before you Delete them.</h5></p>";
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


