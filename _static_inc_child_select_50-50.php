<?

if($_GET['adp'] == "afrika"){

$ONLY_PROJECT = " AND (	project_id = '201703' OR 
						project_id = '179632' OR 
						project_id = '190683' OR 
						project_id = '196791' OR 
						project_id = '179878' OR 
						project_id = '191915' OR 
						project_id = '174377'
						) ";

	
} else if($_GET['adp'] == "lateinamerika"){
	
$ONLY_PROJECT = " AND (	project_id = '176571' OR 
						project_id = '187982' OR 
						project_id = '201498' OR 
						project_id = '12345' OR 
						project_id = '191859' OR 
						project_id = '195502' OR 
						project_id = '179250'
						) ";

	
} else if($_GET['adp'] == "asien"){


	
$ONLY_PROJECT = " AND (	project_id = '179134' OR 
						project_id = '179472' OR 
						project_id = '179004' OR 
						project_id = '179349' OR 
						project_id = '181111'
						) ";


} else {

// Projekte für Herbstkampagne
$ONLY_PROJECT = " AND (	project_id = '201703' OR 
						project_id = '179632' OR 
						project_id = '190683' OR 
						project_id = '196791' OR 
						project_id = '179878' OR 
						project_id = '191915' OR 
						project_id = '174377' OR 
						project_id = '176571' OR 
						project_id = '187982' OR 
						project_id = '181005' OR 
						project_id = '191859' OR 
						project_id = '195502' OR 
						project_id = '179250' OR 
						project_id = '179134' OR 
						project_id = '179472' OR 
						project_id = '179004' OR 
						project_id = '179349' OR 
						project_id = '181111'
						) ";
						
#project_id = '201498' OR 

}


// echo $ONLY_PROJECT;
// exit;
# Nur bestimmtes Projekt anzeigen
$projectID = mysql_escape_string(trim($projectID));

if($projectID != ""){
	$SQL_PROJECT = " AND project_id = ".$projectID." ";
	$ONLY_PROJECT = "";
} else {
	$SQL_PROJECT = "";
}


#-------------------------------------------------------------------------------------------

# Geschlecht bestimmen

$last_gender = file_get_contents(dirname(__FILE__)."/../_txt/child_gender.txt");
if($last_gender != "M"){$next_gender = "M";} else {$next_gender = "F";}

$fp = fopen(dirname(__FILE__)."/../_txt/child_gender.txt", "w+");
fputs($fp, $next_gender);
fclose($fp);
		
#-------------------------------------------------------------------------------------------

# Keine geblockten Kinder
$blockOffset = 86400*28; // 4 Wochen
$blocktime = time() + $blockOffset;
$CHILD_BLOCK = " AND (vorschlag_date = '0' OR vorschlag_date < '".$blocktime."') ";


### Letzte ID aus DB erkennen ###
$child_res1 = mysql_query("SELECT id FROM $child_db WHERE status = 0 $CHILD_BLOCK ORDER BY id DESC LIMIT 1");
$child_cur1 = mysql_fetch_array($child_res1);
$last_id = $child_cur1['id'];

### zuletzt aufgerufenen ID auslesen ###
$old_id = @implode("",file($ADM_PATH."_txt/last_child.txt"));
if ($old_id == ""){$old_id = 0;}

#-------------------------------------------------------------------------------------------

### Nächste ID auswählen ###
$child_res = mysql_query("SELECT * FROM $child_db WHERE status = 0 AND gender = '$next_gender' AND video_available = '1' $CHILD_BLOCK $SQL_PROJECT $ONLY_PROJECT ORDER BY RAND() LIMIT 1");
#$child_res = mysql_query("SELECT * FROM $child_db WHERE status = 0 AND video_available = '1' $CHILD_BLOCK $SQL_PROJECT $ONLY_PROJECT ORDER BY RAND() LIMIT 1");
$child_cur = mysql_fetch_array($child_res);
$the_id = $child_cur['id'];


if (mysql_num_rows($child_res) == 0){
	### Nächste ID auswählen ###
	#$child_res = mysql_query("SELECT * FROM $child_db WHERE status = 0 gender = '$next_gender' AND $CHILD_BLOCK $SQL_PROJECT $ONLY_PROJECT ORDER BY RAND() LIMIT 1");
	$child_res = mysql_query("SELECT * FROM $child_db WHERE status = 0 $CHILD_BLOCK $SQL_PROJECT $ONLY_PROJECT ORDER BY RAND() LIMIT 1");
	$child_cur = mysql_fetch_array($child_res);
	$the_id = $child_cur['id'];
	
	if (mysql_num_rows($child_res) == 0){ // Auswahl ohne spezifisches Land oder Projekt
		$child_res = mysql_query("SELECT * FROM $child_db WHERE status = 0 AND id > 0 $CHILD_BLOCK ORDER BY id ASC");
		$child_cur = mysql_fetch_array($child_res);
		$the_id = $child_cur['id'];
	}
}


#-------------------------------------------------------------------------------------------
if (mysql_num_rows($child_res) != 0){
#-------------------------------------------------------------------------------------------
	if ($the_id >= $last_id){
		$the_id = 0;
		#### Datei speichern ###
		$filename = $ADM_PATH."_txt/last_child.txt";
		$fp = fopen($filename,"w+");
		fputs($fp, $the_id);
		fclose($fp);
	}
	
	$birthdate = $child_cur['age'];
	

	$age = explode(".", (time() - $birthdate) / (86400 * 365.25));
	$age = $age[0];
	
	if ($child_cur['gender'] == "M") {
		$gender = "ihm";
		$gender2 = "Junge";
	} else {
		$gender = "ihr";
		$gender2 = "M&auml;dchen";
	}

#-----------------------------------------------------------------------------------------------------------------------
}
#-----------------------------------------------------------------------------------------------------------------------

?>