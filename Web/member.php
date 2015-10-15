<!doctype html>
<?php 

session_start();

if(!isset($_POST['uname'])){

header("location:welcome.php");

} else {
            include "configure.php";
          ?>
<html>
    <head>
        <title>Network Mapping @BTH</title>
        <a href="welcome.php"><img align=left src= "http://img3.wikia.nocookie.net/__cb20131126195004/epicrapbattlesofhistory/images/a/aa/Home_icon_grey.png" width = "55" height ="55"></a>
        <center><img src="https://www.bth.se/web2009/images/head_logo.png">
        <body bgcolor="#F5F5F5" text="#000000">
            <div id="d1">
            <?php
            $u= $_POST['uname'];
            $p= $_POST['pass'];
            $_SESSION['uname']=$u;
            if ($u == "team3" && $p == "2k14")
            {
                header("Location: info1.php");
            }else {
                echo "<script type='text/javascript'>alert('Oops Admin_username/Passsword Combination are Wrong');</script>";
                header("Refresh: 0,welcome.php");
            }?>
            </div>
            <br><br><br><br><br><br><br><br><br><br><br>
            <br><br><br><br><br><br><br><br><br><br><br><br>
            <div id="footer" style="clear:both;text-align:center;">    
<br>    
<p align='center'><font size="5" color="red" ><strong>Note:</strong> This Tool is developed by Team3 for ANM project purpose only.</font></p>
<p style ="background-color:#FFA500;">Copyright&#169 2K14_ANM_Team3</p>
</div>           
        </body>
    </head>    
</html>

<?
}
?>