#!/usr/bin/perl-w

                                    #$host="localhost"
                                    #Database Name="team3"
                                    #Database User Name="root"
                                    #Database Password="1"
                                    #Port="1161"
                                    #DEVICES/INPUT Table="switch";   
                                    #OUTPUT/PORT Table="ports";
                                    #OUTPUT/VLAN Table="vlans";
                                    #OUTPUT/MAC Table="macs";


use DBI;
use DBD::mysql;
use Net::SNMP qw(snmp_dispatcher oid_lex_sort);
use Net::Ping;
use Net::SNMP::Interfaces;
use Data::Dumper;

$|=1;

require 'config.pl';
$timeout=3;

$dbh=DBI->connect(
  "DBI:mysql::$host:$db_port",
  "$db_user",
  "$db_pw" , { 'PrintError' => 1, 'RaiseError' => 1, 'AutoCommit' => 1 }
) || die "Error connecting to database:".$data_base.":".$DBI::errstr."\n";

$databases = $dbh->do("SHOW DATABASES LIKE '$data_base'") or die "Error in the Database name: " .$dbh->errstr. "\n";
#printf "$databases\n";

if ($databases == "0") {
    printf "Database: ".$data_base." does not exists and need to create ".$data_base." database\n";
    $check=$dbh->prepare("CREATE DATABASE IF NOT EXISTS `".$data_base."`") or die ("Cannot create ".$data_base." database :  ". mysql_error()."\n");
    if (!$check->execute) {
        die "Can't use execute".$data_base." database ".$check->errstr."\n";
    }  
}
else {
    printf "Database: '$data_base' exists and no need to create ".$data_base." database again\n";
}
tables();
#$dbh->disconnect();

$dbh=DBI->connect(
  "DBI:mysql:$data_base",
  "$db_user",
  "$db_pw" , { 'PrintError' => 1, 'RaiseError' => 1, 'AutoCommit' => 1 }
) || die "Error connecting to database: ".$data_base.":".$DBI::errstr."\n";

$check_table = $dbh->do("SHOW TABLES FROM `".$data_base."` LIKE '$dev_table'") or die "Error in the Table name: " .$dbh->errstr. "\n";

#printf "$check_table\n";
if ($check_table != 1) {
	$check = $dbh->prepare("CREATE TABLE IF NOT EXISTS `".$dev_table."` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `IP` varchar(100) NOT NULL,  
      `Community` varchar(100) NOT NULL,
      `Port` int(11) NOT NULL,
      `Name` varchar(100) NOT NULL,
      PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ") or die ("Cannot create switch table : ". mysql_error()."\n");
	
        if (!$check->execute) {
            die "Can't execute switch table ".$check->errstr."\n";
        }
        $check->finish();
        printf "Input Tablename: ".$dev_table." doesn't exists, so its created\n";
    }

$sth = $dbh->prepare("SELECT * FROM `".$dev_table."`") or die "ERROR:".$dbh->errstr."\n";
$sth->execute
    or die "SQL Error: $DBI::errstr\n";
    $drows = $sth->rows;
    if ($drows == 0) {
	printf "Input Tablename: ".$dev_table." exists BUT there are NO DEVICES to FETCH in the ".$dev_table." table\n";
	printf "################\n";
	exit();
    }else{
	printf "Input Tablename: ".$dev_table." exists and no need to create ".$dev_table." table again\n";
	printf "################\n";
    }
    
#printf "\nUr Ready to fetch the data to probe\n";
@id=();@IP=();@comm=();
while ($ref = $sth->fetchrow_hashref) {
    $id = $ref->{'id'};
    $IP = $ref->{'IP'};
    $comm = $ref->{'Community'};
    $port = $ref->{'Port'};
    $p=Net::Ping->new("icmp", $timeout);
    if ($p->ping($IP)) {
        print "\"$IP\" is alive \n";
        $k=1;
        program();
    }else {
        print "\"$IP\" is NOT alive \n";
        $k=0;
        program();
    }
    $p->close;
}

sub program{
    print "IP: $IP, COMMUNITY: $comm, PORT: $port\n";
        #$sth1 = $dbh->prepare("SELECT * FROM `".$non."`") or die "ERROR:".$dbh->errstr."\n";
        #$sth1->execute or die "SQL Error: $DBI::errstr\n";    
    if ($k==0) {
        $refresh = "INSERT INTO `".$non."`(`IP`, `Active`) VALUES ('$IP', 'no')";
        $check = $dbh->prepare($refresh) or die "ERROR:".$dbh->errstr."\n";
        $check->execute or die "ERROR:".$dbh->errstr."\n";
	
	$in_active = $dbh->prepare("UPDATE `".$dev_table."` SET Name='$IP Is Not Alive' WHERE `IP`='$IP' AND `Community`='$comm' AND `Port` = '$port'");
	$in_active->execute or die "ERROR:".$dbh->errstr."\n";
    }else {
        ($session,$error) = Net::SNMP->session(Hostname => "$IP",
                                               Community => "$comm",
                                               Port => "$port",
                                               Translate => "1");
        die "Session error: $error" unless ($session);
        $interf = $session->get_request('1.3.6.1.2.1.2.1.0');
        if (!defined($interf)) {          
            $refresh1 = "INSERT INTO `".$non."`(`IP`, `Active`) VALUES ('$IP', 'disabled')";
          
            $check1 = $dbh->prepare($refresh1) or die "ERROR:".$dbh->errstr."\n";
            $check1->execute or die "ERROR:".$dbh->errstr."\n";            
	    $in_active = $dbh->prepare("UPDATE `".$dev_table."` SET Name='$IP is Unable To Fetch Data' WHERE `IP`='$IP' AND `Community`='$comm' AND `Port` = '$port'");
	    $in_active->execute or die "ERROR:".$dbh->errstr."\n";
            print "Error in Interface Response of the Device \"$IP\": $error\n";
        }    
        else {
            $interfaces = Net::SNMP::Interfaces->new(Hostname=>"$IP",
                                                    Port=>"$port",
                                                    Community=>"$comm",   
            ) or die "Error: $Net::SNMP::Interfaces::error";
            
            #@ifnames = $interfaces->all_interfaces();
            @inter = $interfaces->if_indices();
            $value=@inter;
            @inter = sort {$a <=> $b} @inter;
            
            #print "@inter\n";
            print "\nThe No. of Interfaces:\t $value\n";
	    
	    @if_name = (); @if_oper = (); @if_speed = (); @if_phy = (); @if_type = (); #Defining null arrays
	    
            for($i=0;$i<$value;$i++)
            {
                $ifname=".1.3.6.1.2.1.31.1.1.1.1".".$inter[$i]";
                $state=".1.3.6.1.2.1.2.2.1.8".".$inter[$i]";
                $speed=".1.3.6.1.2.1.2.2.1.5".".$inter[$i]";
                $iftype="1.3.6.1.2.1.2.2.1.3".".$inter[$i]";
                $ifphy=".1.3.6.1.2.1.2.2.1.6".".$inter[$i]";
		
                push (@if_name,$ifname);
                push (@if_oper,$state);
                push (@if_speed,$speed);
                push (@if_type,$iftype);
                push (@if_phy,$ifphy);
            } 
            $sysdescr=".1.3.6.1.2.1.1.1.0";
           if($value<50){
            	#@all=(@if_name,@if_oper,@if_speed,@if_phy,$sysdescr);
            	$if_n = $session->get_request(-varbindlist =>\@if_name);
            	$if_o = $session->get_request(-varbindlist => \@if_oper);
            	$if_s = $session->get_request(-varbindlist => \@if_speed);
            	$if_p = $session->get_request(-varbindlist => \@if_phy);
            	$if_sd = $session->get_request(-varbindlist => [$sysdescr]);
            	$if_t = $session->get_request(-varbindlist => \@if_type);
	    }else{
		@para1 = qw (if_name if_oper if_speed if_phy if_type);
		@para2 = qw (if_n if_o if_s if_p if_t);
		$mod = int($value/50);
		print "$mod\n";
		$n = $mod+1;
		$i1= 0;
		foreach $val1 (@para1){
    		 foreach $elem (@$val1) {
			 $val2 = "$val1"."_part";
        		 push @{ $$val2[$i1++ % $n] }, $elem;
    		 }
		}
		$i5=0;
		foreach $val1 (@para1){
		 $val2 = "$val1"."_part";
		 $i6 = 0;
		 while (defined $$val2[$i6]){
 		  @x = @{$$val2[$i6]};
		  $response = $session->get_request(-varbindlist =>\@x);
		  $val3 = "$para2[$i5]"."x";
		  push (@$val3,$response);
 		  $i6++;
		 }
		$i5++;
		}
		foreach $val1 (@para2){
		 $val2 = "$val1"."x";
		 foreach $val3 (@$val2){
		 #print $val;
		 while(($oid,$res) = each($val3)){
		  $$val1->{$oid} = $res;
		 }
		}
	       }
		$if_sd = $session->get_request(-varbindlist => [$sysdescr]);
	    } #Bracket for else
#print Dumper $if_o;

            if (!defined($if_t || $if_n || $if_o || $if_s || $if_p)) {
                print "Error in the Response\n";
            }
            else {
                if (defined($if_sd)) {
                    #$if3=$session->get_request("$sysdescr");
                    $sys = $if_sd->{"$sysdescr"};
                    @sys_split = split(',',$sys);
                    printf "$sys_split[0]\n";
		    
		    $refresh2 = " UPDATE `".$dev_table."` SET
                        `Name`='$sys_split[0]',
                        `id`='$id' WHERE `IP` = '$IP' AND `Community` = '$comm' AND `Port` = '$port'";
                        
                        $check2 = $dbh->prepare($refresh2) or die "ERROR:".$dbh->errstr."\n";
                        $check2->execute or die "ERROR:".$dbh->errstr."\n";
                    printf "The complete port details of the \"$sys_split[0]\" given below\n";
		    
                    for($j=0;$j<$value;$j++)
                    {
                        $port       =   $if_n->{"$if_name[$j]"};
                        $op_state   =   $if_o->{"$if_oper[$j]"};
                        $spd        =   $if_s->{"$if_speed[$j]"};
                        $type       =   $if_t->{"$if_type[$j]"};
                        $phy        =   $if_p->{"$if_phy[$j]"};
                        
                        $see = 'empty';
                        if ($phy) {
                            $see =substr $phy,2;
                            $leng = length $see;
                            $insert_pos =2;
                            if ($leng == 12) {
                            while ($insert_pos<=($leng+3)) {
                               substr $see, $insert_pos, 0, ':';
                               $insert_pos=$insert_pos+3;
                            }
                        }
                        }
                        
                        $num=1000000000;
                        $num1=1000000;
                        $a=$spd%$num;
                        if ($a!=0 && $spd != 0) {
                            $spd=($spd/$num1)."M";
                        }elsif ($a == 0 && $spd != 0){
                            $b=$spd/$num;
                            $spd="$b"."G";
                        }
                        else {
                            $spd="n/a";
                        }
                        
                        if ($op_state == 1) {
                            $op_state = "UP";
                        }elsif ($op_state == 2){
                            $op_state = "DOWN";                
                        }else {
                            $op_state = "n/a"; 
                        }
                        
                        
                        if ($type == 6) {
                            $type1 = "eth";
                        }elsif ($type == 117){
                            $type1 = "Giga_eth";
                        }elsif ($type == 135){
                            $type1 = "l2vlan";
                        }elsif ($type == 136){
                            $type1 = "l3ipvlan";
                        }elsif ($type == 142){
                            $type1 = "ipforward";
                        }elsif ($type == 53){
                            $type1 = "propVirtual";
                        }elsif ($type == 24){
                            $type1 = "softwareLoopback";
                        }
                        printf "Port:=$port\t State:=$op_state\t Speed:=$spd\t Type:=$type1\t PhyAdd:=$see\n";
                        
			if ($p1==0){
                            $refreshp = "INSERT INTO `".$port_table."`(`Port_Name`, `Status`, `Speed`, `Port_Type`, `Phy_Addr`, `Sw_rowid`)
                            VALUES ('$port', '$op_state', '$spd' , '$type1', '$see', '$id')";
                            
                            $checkp = $dbh->prepare($refreshp) or die "ERROR:".$dbh->errstr."\n";
                            $checkp->execute or die "ERROR:".$dbh->errstr."\n";
                        }elsif ($p1==1){
                            $refreshp = "INSERT INTO `".$port_table."`(`Port_Name`, `Status`, `Speed`, `Port_Type`, `Phy_Addr`, `Sw_rowid`)
                            VALUES ('$port', '$op_state', '$spd' , '$type1', '$see', '$id')";
                            
                            $checkmp = $dbh->prepare($refreshp) or die "ERROR:".$dbh->errstr."\n";
                            $checkmp->execute or die "ERROR:".$dbh->errstr."\n";
                        }
			
                        #printf "type: $type\n";
                        if (index($port, '802.1Q Encapsulation Tag') != -1) {
                            $string1 = $port;
                            $string1 =~ s/802.1Q Encapsulation Tag //gi;
                            #print "The resulting value is : $string1 \n";
                            $num=$string1/1;
                            #push (@vlans_name,$port);
                            push (@vlans,$num);
                        }elsif (index($port,'VLAN') != -1){
                            if ($port eq 'DEFAULT_VLAN') {
                                #push(@vlans_name,$port);
                                push (@vlans,'000');
                            }
                            else {
                                $string1 = $port;
                                $string1 =~ s/VLAN//gi;
                                #print "The resulting value is : $string1 \n";
                                $num=$string1/1;
                                #push (@vlans_name,$port);
                                push (@vlans,$num);
                            }
                        }
                    }    
                }
                    printf "\nThe complete \"vlan\" details of the \"$sys_split[0]\" given below\n";
                    $i=0; $a=scalar(@vlans);
                    while ($i<$a) {
                        printf "The number of vlan id: $vlans[$i]\n";
			 if ($v==0){
                            $refreshv = "INSERT INTO `".$vlan_table."`(`Vlan_Number`, `Sw_rowid`)
                            VALUES ('$vlans[$i]', '$id')";
                            
                            $checkv = $dbh->prepare($refreshv) or die "ERROR:".$dbh->errstr."\n";
                            $checkv->execute or die "ERROR:".$dbh->errstr."\n";
                        }elsif ($v==1){
                            $refreshv = "INSERT INTO `".$vlan_table."`(`Vlan_Number`, `Sw_rowid`)
                            VALUES ('$vlans[$i]', '$id')";
                            
                            $checkmv = $dbh->prepare($refreshv) or die "ERROR:".$dbh->errstr."\n";
                            $checkmv->execute or die "ERROR:".$dbh->errstr."\n";
                        }
                        $i++;
                    }
                    
                    #printf "@vlans";
                    printf "\n";
                    #=== OIDs queried to retrieve information ====
                    $TpFdbAddress = '1.3.6.1.2.1.17.4.3.1.1';
                    $TpFdbPort    = '1.3.6.1.2.1.17.4.3.1.2';
                    #=============================================
                    @mac=();
                    if (defined($result1 = $session->get_table(-baseoid => $TpFdbAddress))) {
                        foreach (oid_lex_sort(keys(%{$result1}))) {
                            $see =substr $result1->{$_},2;
                            $leng = length $see;
                            $insert_pos =2;
                            if ($leng == 12) {
                                while ($insert_pos<=($leng+3)) {
                                   substr $see, $insert_pos, 0, ':';
                                   $insert_pos=$insert_pos+3;
                                }
                            }
                            push (@mac,$see);
                            #printf("%s => %s\n", $_, $see);
                        } $b=scalar(@mac);
                    }else {
                        printf("error retrieving MAC addr: %s\n\n", $session->error());
                    }
                    #==========================================
                    @prt=();
                    if (defined($result2 = $session->get_table(-baseoid => $TpFdbPort))) {
                        foreach (oid_lex_sort(keys(%{$result2}))) {
                            #printf("%s => %s\n", $_, $result2->{$_});
                            push (@prt,$result2->{$_});
                        }$b=scalar(@prt);
                    }else {
                        printf("error retrieving port: %s\n\n", $session->error());
                    }
                    #=============================================
                    printf "\nThe list of \"MAC Addresses\" connected and \"Ports\" through which these MAC Addresses connected to the \"$sys_split[0]\" are given below\n";
                    printf "\nMAC_Addrs\t\tPorts\n";
                    
                    for($j=0;$j<$b;$j++) {
                        printf "$mac[$j]\t $prt[$j]\n";
                        if ($m==0){
                            $refreshm = "INSERT INTO `".$mac_table."`(`MAC_Addrs`, `Ports`, `Sw_rowid`)
                            VALUES ('$mac[$j]', '$prt[$j]', '$id')";
                            
                            $checkm = $dbh->prepare($refreshm) or die "ERROR:".$dbh->errstr."\n";
                            $checkm->execute or die "ERROR:".$dbh->errstr."\n";
                        }elsif ($m==1){
			    
			    $sthm = $dbh->prepare("SELECT * FROM `".$mac_table."`") or die "ERROR:".$dbh->errstr."\n";
			    $sthm->execute or die "SQL Error: $DBI::errstr\n";
			    
			    @mac_fet=();@port_fet=();@sw_fet=();@temp =();
			    $dev_str = "$mac[$j]:"."$prt[$j]";
			    while ($ref1 = $sthm->fetchrow_hashref) {
				$mac_fet = $ref1->{'MAC_Addrs'};
			        $port_fet = $ref1->{'Ports'};
			        $sw_fet = $ref1->{'Sw_rowid'};
			        $dev_str_fet = "$mac_fet:"."$port_fet";
			        push (@temp,$dev_str_fet);
				#$fet = mysql_query("SELECT * FROM `".$mac_table."`");
			        
			    }	
				if (($dev_str ~~ @temp) !=1){
				    $refreshmu = "INSERT INTO `".$mac_table."`(`MAC_Addrs`, `Ports`, `Sw_rowid`)
				    VALUES ('$mac[$j]', '$prt[$j]', '$id')";
				    
				    $checkmu = $dbh->prepare($refreshmu) or die "ERROR:".$dbh->errstr."\n";
				    $checkmu->execute or die "ERROR:".$dbh->errstr."\n";
				
				}  
                        }
                    }    
                }    
            }
               
            printf "\n";
        }
        $session->close();
    }

$sth->finish();

$dbh->disconnect();



sub tables{

$dbh=DBI->connect(
  "DBI:mysql:$data_base",
  "$db_user",
  "$db_pw" , { 'PrintError' => 1, 'RaiseError' => 1, 'AutoCommit' => 1 }
) || die "Error connecting to database: ".$data_base.":".$DBI::errstr."\n";

    
$ptable = $dbh->do("SHOW TABLES FROM ".$data_base." WHERE Tables_in_".$data_base." LIKE '".$port_table."'") or die ("Cannot find ".$port_table." table : ".$dbh->errstr."\n");

#printf "$table\n";

if ($ptable eq "0E0") {
    printf "################";
    printf "Tablename: ".$port_table." does not exists and need to create ".$port_table." table\n";
    printf "################";
    $check=$dbh->prepare("CREATE TABLE IF NOT EXISTS `".$port_table."` (
                                                        `ID` int(11) NOT NULL AUTO_INCREMENT,
                                                        `Port_Name` text NOT NULL,
                                                        `Status` tinytext COLLATE latin1_bin NOT NULL,
                                                        `Speed` text COLLATE latin1_bin NOT NULL,
                                                        `Port_Type` text NOT NULL,
                                                        `Phy_Addr` text NOT NULL,
                                                        `Sw_rowid` int(11) NOT NULL,
                                                        UNIQUE KEY (`ID`)
                                                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;") or die ("Cannot create ".$port_table." table :  ". mysql_error()."\n");
    if (!$check->execute) {
        die "Can't execute ".$port_table." table ".$check->errstr."\n";
    }
    $check->finish();
    printf "Output Tablename: ".$port_table." created\n";
    $p1=0;#insert
        my $st1 = $dbh->prepare("SELECT * FROM `".$port_table."`") or die "ERROR:".$dbh->errstr."\n";
        $st1->execute or die "ERROR:".$dbh->errstr."\n";
        $rowsp = $st1->rows;    
}
else {
    printf "################";
    printf "Output Tablename: ".$port_table." exists and no need to create ".$port_table." table again\n";
    printf "################";
    $p1=1;#update    
	$sthp = $dbh->prepare("DELETE FROM `".$port_table."` WHERE 1 ") or die "ERROR:".$dbh->errstr."\n";
        $sthp->execute or die "SQL Error: $DBI::errstr\n"; 
}

    
$vtable = $dbh->do("SHOW TABLES FROM ".$data_base." WHERE Tables_in_".$data_base." LIKE '".$vlan_table."'") or die ("Cannot find ".$vlan_table." table : ".$dbh->errstr."\n");

#printf "$table\n";

if ($vtable eq "0E0") {
    printf "Tablename: ".$vlan_table." does not exists and need to create ".$vlan_table." table\n";
    printf "################\n";
    $check=$dbh->prepare("CREATE TABLE IF NOT EXISTS `".$vlan_table."` (
                                                        `ID` int(11) NOT NULL AUTO_INCREMENT,
                                                        `Vlan_Number` int(11) NOT NULL,
                                                        `Sw_rowid` int(11) NOT NULL,
                                                        UNIQUE KEY (`ID`)
                                                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;") or die ("Cannot create ".$vlan_table." table :  ". mysql_error()."\n");
    if (!$check->execute) {
        die "Can't execute ".$vlan_table." table ".$check->errstr."\n";
    }
    $check->finish();
    printf "Output Tablename: ".$vlan_table." created\n";
    $v=0;#insert
            my $st1 = $dbh->prepare("SELECT * FROM `".$vlan_table."`") or die "ERROR:".$dbh->errstr."\n";
            $st1->execute
            or die "ERROR:".$dbh->errstr."\n";
        $rowsv = $st1->rows;    
}
else {
    printf "Output Tablename: ".$vlan_table." exists and no need to create ".$vlan_table." table again\n";
    printf "################";
    $v=1;#update    
	$sthv = $dbh->prepare("DELETE FROM `".$vlan_table."` WHERE 1 ") or die "ERROR:".$dbh->errstr."\n";
        $sthv->execute or die "SQL Error: $DBI::errstr\n"; 
}


$mtable = $dbh->do("SHOW TABLES FROM ".$data_base." WHERE Tables_in_".$data_base." LIKE '".$mac_table."'") or die ("Cannot find ".$mac_table." table : ".$dbh->errstr."\n");

#printf "$table\n";

if ($mtable eq "0E0") {
    printf "Tablename: ".$mac_table." does not exists and need to create ".$mac_table." table\n";
    printf "################\n";
    $check=$dbh->prepare("CREATE TABLE IF NOT EXISTS `".$mac_table."` (
                                                        `ID` int(11) NOT NULL AUTO_INCREMENT,
                                                        `MAC_Addrs` text NOT NULL,
                                                        `Ports` int NOT NULL,
							`Dev_Name` tinytext NOT NULL,
                                                        `Sw_rowid` int(11) NOT NULL,
                                                        UNIQUE KEY (`ID`)
                                                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;") or die ("Cannot create ".$mac_table." table :  ". mysql_error()."\n");
    if (!$check->execute) {
        die "Can't execute ".$mac_table." table ".$check->errstr."\n";
    }
    $check->finish();
    printf "Output Tablename: ".$mac_table." created\n";
    $m=0;#insert
            my $st1 = $dbh->prepare("SELECT * FROM `".$mac_table."`") or die "ERROR:".$dbh->errstr."\n";
            $st1->execute
            or die "ERROR:".$dbh->errstr."\n";
        $rowsm = $st1->rows;    
}
else {
    printf "Output Tablename: ".$mac_table." exists and no need to create ".$mac_table." table again\n";
    printf "################";
    $m=1;#update    
            
}

$non="nondevices";
$ptable = $dbh->do("SHOW TABLES FROM ".$data_base." WHERE Tables_in_".$data_base." LIKE '".$non."'") or die ("Cannot find ".$non." table : ".$dbh->errstr."\n");

#printf "$table\n";

if ($ptable eq "0E0") {
    printf "Tablename: ".$non." does not exists and need to create ".$non." table\n";
    printf "################\n";
    $check=$dbh->prepare("CREATE TABLE IF NOT EXISTS `".$non."` (
                                                        `ID` int(11) NOT NULL AUTO_INCREMENT,
                                                        `IP` text NOT NULL,
                                                        `Active` text NOT NULL,
                                                        UNIQUE KEY (`ID`)
                                                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;") or die ("Cannot create ".$non." table :  ". mysql_error()."\n");
    if (!$check->execute) {
        die "Can't execute ".$non." table ".$check->errstr."\n";
    }
    $check->finish();
    printf "Output Tablename: ".$non." created\n";
    $n=0;#insert
        my $st1 = $dbh->prepare("SELECT * FROM `".$non."`") or die "ERROR:".$dbh->errstr."\n";
        $st1->execute or die "ERROR:".$dbh->errstr."\n";
        $rowsn = $st1->rows;    
}
else {
    printf "Output Tablename: ".$non." exists and no need to create ".$non." table again\n";
    printf "################";
    $n=1;#update    
        $sthn = $dbh->prepare("DELETE FROM `".$non."` WHERE 1 ") or die "ERROR:".$dbh->errstr."\n";
        $sthn->execute or die "SQL Error: $DBI::errstr\n";  
}
 

}
