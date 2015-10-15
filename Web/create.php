<!doctype html>
<html>
    <head>
        <title>Network Mapping @BTH</title>
        <a href="info1.php"><img align=left src= "http://maritimecoin.com/img/back.png" width = "55" height ="55"></a>
        <a href="welcome.php"><img align=right src= "http://www.clker.com/cliparts/W/9/B/o/q/H/logout-button-md.png" width = "70" height ="65"></a>
        <center><img src="https://www.bth.se/web2009/images/head_logo.png">
        <body bgcolor="#F5F5F5" text="#000000">
            <div id="d1">
            <?php
            require ('configure.php');
            $query = mysql_query("SELECT * FROM `".$dev_table."`");
            if(!$query){
                $sql = mysql_query("CREATE TABLE IF NOT EXISTS `".$dev_table."` (
                                                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                                                              `IP` varchar(100) NOT NULL,  
                                                                              `Community` varchar(100) NOT NULL,
                                                                              `Port` int(11) NOT NULL,
                                                                              `Name` varchar(100) NOT NULL,
                                                                               PRIMARY KEY (`id`)
                ) ") or die ("Cannot create input table \"$dev_table\" : ". mysql_error()."\n");
                if($sql){
                    echo "<p><h5>The Input Table \"$dev_table\" is Successfully Created. Now add a Switch Device to it using the option \"ADD a Switch Device\".</h5></p>";
                    #echo "<script type='text/javascript'>alert('The input table \"$dev_table\" is already exists, so no need to create again');</script>";
                    #header("Location: info1.php");
                    header("Refresh: 1; info1.php");
                }
                
            }else {
                echo "<p><h5>The Input Table \"$dev_table\" is Already Exists so no need to Create Again.</h5></p>";
                #echo "<script type='text/javascript'>alert('The input table \"$dev_table\" is already exists, so no need to create again');</script>";
                #header("Location: info1.php");
                header("Refresh: 1; info1.php");
            }
            ?>
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
