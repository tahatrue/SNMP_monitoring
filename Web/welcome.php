<html>
<head>
<title>Network Mapping @BTH</title>
</head>
            <script type = "text/javascript">
                function validate_form1(){
                    if ((document.getElementById('user11').value=='') ||
                        (document.getElementById('pass11').value=='')) {
                        alert("Please enter the fields Completely\n");
                        return false;
                    }
                }
                //function function1() {
                    
                //}
            </script>
            <style>
                .imgtext {
                    float: right;margin: 10px;
                }
            </style>
<body   bgcolor="#F5F5F5"
        text="#000000">
<center>
<img src="https://www.bth.se/web2009/images/head_logo.png">

<P>
	<br> 
	<ul>
	<h3>Welcome to the Network Mapping Tool</h3>
	<P>
        Enter the Admin_Login details. Then click on submit.
	</ul>
	<P>
        
        
	<form name="details" action="member.php" method="post">
	
	<table border=1 width=30% align=center cellpadding="3" cellspacing="1">
	<tr>
                <td>
                    <center>Admin_Username:</center>    
                </td>
                <td>
                    <center><input type="text" name="uname" id="user11" maxlength='10'></center>    
                </td>
        </tr>
	<tr>
                <td>
                    <center>Password:</center>  
                </td>
                <td>
                    <center><input type="password" name="pass" id="pass11"></center>   
                </td>
        </tr>
        <td colspan=2>
	    <center>
		<input type="submit" value="Login" name="submit" onclick="return validate_form1()"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset">
	    </center>       
	</table>
 </form>       
        <?
        session_start();
	$_SESSION["sess_user"]=1;
	?>
        <br><br><br><br><br><br><br>
        <a href="info.php"><img align=right src= "http://cvisioncentral.com/wp-content/themes/buddyboss-child-fixed-navbar/_inc/images/avatar-group.jpg" border = "0" alt= "enter" width = "100" height ="90"></a>
        <br><br><br><br><br><div class="imgtext"><i>Public_View</i></div>
    
    
    <br><br><br><br>
<div id="footer" style="clear:both;text-align:center;">    
<br>    
<p align='center'><font size="5" color="red" ><strong>Note:</strong> This Tool is developed by Team3 for ANM project purpose only.</font></p>
<p style ="background-color:#FFA500;">Copyright&#169 2K14_ANM_Team3</p>
</div>
</body>
</html>

