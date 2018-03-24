<?php require_once('../Connections/three_s_dbconn.php'); ?>
<?php require_once('../webassist/mysqli/queryobj.php'); ?>
<?php require_once('../webassist/mysqli/rsobj.php'); ?>
<?php
@session_start();
if(!isset($_SESSION["new_subscribe"]))     {
  $_SESSION["new_subscribe"] = "";
}
?>
<?php
$now = new DateTime();
$now = $now->setTimezone(new DateTimezone('Europe/London'));
$now = $now->format('Y-m-d H:i:s');
?>
<?php
// Email address verification
function isEmail($mail) {
    return filter_var($mail, FILTER_VALIDATE_EMAIL);
}
$email = '';
$email = addslashes( trim( isset($_POST['email'])?$_POST['email']:'' ) );

if(($_POST) && !isEmail($email) ) {
        $array = array();
        $array['valid'] = 0;
        $array['message'] = 'Please insert a valid email address!';
        echo json_encode($array);
    }
	
if(($_POST) && isEmail($email) ) {	
?>
<?php
$checkEmail = new WA_MySQLi_RS("checkEmail",$three_s_dbconn,1);
$checkEmail->setQuery("SELECT subcription.subID FROM subcription WHERE subcription.subEmail = ?");
$checkEmail->bindParam("s", "".(isset($_POST['email'])?$_POST['email']:'' )  ."", "-1"); //paramEmail
$checkEmail->execute();
?>
<?php
if ((($checkEmail->getColumnVal("subID")) == "") && ((isEmail($email)?$_POST["email"]:""))) {
  $InsertQuery = new WA_MySQLi_Query($three_s_dbconn);
  $InsertQuery->Action = "insert";
  $InsertQuery->Table = "subcription";
  $InsertQuery->bindColumn("subEmail", "s", "".strtolower(isset($_POST['email'])?$_POST['email']:'' )  ."", "WA_DEFAULT");
  $InsertQuery->bindColumn("subDate", "t", "".($now!=''?$now:'')  ."", "WA_DEFAULT");
  $InsertQuery->bindColumn("subIP", "s", "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."", "WA_DEFAULT");
  $InsertQuery->bindColumn("status", "i", "2", "WA_DEFAULT");
  $InsertQuery->saveInSession("new_subscribe");
  $InsertQuery->execute();
  $InsertGoTo = "";
  if (function_exists("rel2abs")) $InsertGoTo = $InsertGoTo?rel2abs($InsertGoTo,dirname(__FILE__)):"";
  $InsertQuery->redirect($InsertGoTo);
}?>
<?php if ((isset($_SESSION['new_subscribe']) && $_SESSION['new_subscribe']>0) && (isset($_POST['email']) && $_POST['email']!='' )) { // Show if mysqli recordset not empty ?>
<?php 
$message = ("Great, we've received and saved your email address <b>".(isset($_POST['email'])?$_POST['email']:'' )."</b>"); 
$valid = "1";
echo json_encode(array('valid' => $valid, 'message' => $message));
?>
<?php } // Show if mysqli recordset not empty ?>
<?php if (($checkEmail->TotalRows > 0) && (isset($_POST['email'])?$_POST['email']:'' )) { // Show if mysqli recordset not empty ?>
<?php 
$message = ("Thanks but we already have your email address <b>".(isset($_POST['email'])?$_POST['email']." ":'' )."</b>in our records" ); 
$valid = "0";
echo json_encode(array('valid' => $valid, 'message' => $message));
?>
<?php } // Show if mysqli recordset not empty ?>
<?php 
@session_start();
if ("" == ""){
  // WA_ClearSession
	$clearAll = FALSE;
	$clearThese = explode(",","new_subscribe");
	if($clearAll){
		foreach ($_SESSION as $key => $value){
			unset($_SESSION[$key]);
		}
	}
	else{
		foreach($clearThese as $value){
			unset($_SESSION[$value]);
		}
	}
}
?>
<?php
}
?>