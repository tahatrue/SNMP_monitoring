<!doctype html>
<?php 

session_start();

if(!isset($_SESSION['uname'])){

header("location:welcome.php");

} else {
            include "configure.php";
            #header("Refresh: 15; insert.php");
            ?>
<html>
    <head>
        <title>Network Mapping @BTH</title>
        <link href="test.css" type="text/css" rel="stylesheet" />
        <script type = "text/javascript">
                function insert1(){
                    if ((document.getElementById('ip1').value=='') ||
                        (document.getElementById('port1').value=='') ||
                        (document.getElementById('comm1').value=='')) {
                        alert("Please Fill the form Completely\n");
                        return false;
                    }
                }
        </script> 
        <a href="info1.php"><img align=left src= "http://maritimecoin.com/img/back.png" width = "55" height ="55"></a>
        <a href="welcome.php"><img align=right src= "http://www.clker.com/cliparts/W/9/B/o/q/H/logout-button-md.png" width = "70" height ="65"></a>
        <center><img src="https://www.bth.se/web2009/images/head_logo.png">
        <body bgcolor="#F5F5F5" text="#000000">
        <?php
            if (isset($_POST['submit12'])){
                $ip = $_POST['ip'];
                $port = $_POST['port'];
                $comm = $_POST['community'];
                $dev_str = "$ip:"."$comm:"."$port";
                #echo $ip;
                $fet = mysql_query("SELECT * FROM `".$dev_table."`");
                $temp = array();
                while($data = mysql_fetch_array($fet)){
                  $ip_fet = $data['IP'];
                  $port_fet = $data['Port'];
                  $com_fet = $data['Community'];
                  $dev_str_fet = "$ip_fet:"."$com_fet:"."$port_fet";
                  array_push($temp,$dev_str_fet);
                }
                if(in_array($dev_str,$temp)){
                  echo "<p>Devices Already Exists</p>";
                  header("Refresh: 3; insert.php");
                }
                else {
                  $check1 = mysql_query("INSERT INTO `".$dev_table."`(`IP`, `Community`, `Port`) VALUES ('$ip', '$comm', '$port')") or die ("Query Error". mysql_error());
                
                  if($check1){
                    echo "<p>You have Successfully Inserted the Device.</p>";
                    header("Refresh: 3; info1.php");
                  }
                  else{
                    echo "<p>Sorry, Insertion Falied. Please Try Once again</p>";
                    header("Refresh: 3; info1.php");
                  }
                }
            }else{
              ?>
              <p>Adding an additional Device to the Table</p>    
                <fieldset style="width: 300px; border: solid 3px #099;">
                <legend align="left" style="border: solid 3px #FFA500;">ADD-Device Form</legend>                
                <form action="insert.php" method="POST">
                    <table>
                        <tr>
                            <td>
                            IP:    
                            </td>
                            <td>
                            <input type="text" name="ip" id="ip1">    
                            </td>
                        </tr>
                        <tr>
                            <td>
                            PORT:    
                            </td>
                            <td>
                            <input type="TEXT" name="port" id="port1">    
                            </td>
                        </tr>
                        <tr>
                            <td>
                            COMMUNITY:    
                            </td>
                            <td>
                            <input type="text" name="community" id="comm1">    
                            </td>
                        </tr>                       
                    </table>
                <input type="submit" value="Insert" name="submit12" onclick="return insert1()">
                </form></fieldset>
        <div id="nav" style="height: auto;width: 87px;float: left;">
            <div id="nav_wrapper">
                <ul>
                    <li><a href="#">Options<img src="http://www.mozartinshape.org/images/dropdown_arrow.png"/></a>
                    <ul>
                        <li><a href="info1.php">Back to Device List</a></li>
                    </ul>
                    </li>
                </ul>
            </div>
        </div>    
            <?
            }?>
            <br><br><br><br><br><br><br><br>
            <br><br><br><br><br><br><br>
<div id="footer" style="clear:both;text-align:center;">    
<br>    
<p align='center'><font size="5" color="red" ><strong>Note:</strong> This Tool is developed by Team3 for ANM project purpose only.</font></p>
<p style ="background-color:#FFA500;">Copyright&#169 2K14_ANM_Team3</p>
</div>          
        </body>
    </head>    
</html>
<?php
}
?>
