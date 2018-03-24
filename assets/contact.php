<?php require_once('../Connections/three_s_dbconn.php'); ?>
<?php require_once('../webassist/mysqli/queryobj.php'); ?>
<?php
$now = new DateTime();
$now = $now->setTimezone(new DateTimezone('Europe/London'));
$now = $now->format('Y-m-d H:i:s');
?>
<?php
 
// Email address verification
function isEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
 
if($_POST) {
    // Enter the email where you want to receive the message
    $emailTo = 'info@3-s.education';
 
    $clientEmail = addslashes(trim($_POST['email']));
    $subject = addslashes(trim($_POST['subject']));
    $message = addslashes(trim($_POST['message']));
 
    $array = array('nameMessage' => '', 'emailMessage' => '', 'subjectMessage' => '', 'messageMessage' => '');
 
    if(!isEmail($clientEmail)) {
        $array['emailMessage'] = 'Invalid email!';
    }
    if($subject == '') {
        $array['subjectMessage'] = 'Empty subject!';
    }
    if($message == '') {
        $array['messageMessage'] = 'Empty message!';
    }
    if(isEmail($clientEmail) && $subject != '' && $message != '') {
        // Send email
		/*
        $message = "Message from: " . $clientEmail . "\r\n" . $message;
        $headers = "From: " . $clientEmail . " <" . $clientEmail . ">" . "\r\n" . "Reply-To: " . $clientEmail;
        mail($emailTo, $subject . " - 3-S", $message, $headers);
		*/
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $InsertQuery = new WA_MySQLi_Query($three_s_dbconn);
  $InsertQuery->Action = "insert";
  $InsertQuery->Table = "contact";
  $InsertQuery->bindColumn("messEmail", "s", "".strtolower(((isset($_POST["email"]))?$_POST["email"]:""))  ."", "WA_DEFAULT");
  $InsertQuery->bindColumn("messSubject", "s", "".ucfirst(((isset($_POST["subject"]))?$_POST["subject"]:""))  ."", "WA_DEFAULT");
  $InsertQuery->bindColumn("messMessage", "s", "".((isset($_POST["message"]))?$_POST["message"]:"")  ."", "WA_DEFAULT");
  $InsertQuery->bindColumn("messDate", "t", "".($now)  ."", "WA_DEFAULT");
  $InsertQuery->bindColumn("messIP", "s", "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."", "WA_DEFAULT");
  $InsertQuery->bindColumn("messStatus", "i", "2", "WA_DEFAULT");
  $InsertQuery->saveInSession("newMessage");
  $InsertQuery->execute();
  $InsertGoTo = "";
  if (function_exists("rel2abs")) $InsertGoTo = $InsertGoTo?rel2abs($InsertGoTo,dirname(__FILE__)):"";
  $InsertQuery->redirect($InsertGoTo);
}
?>
<?php
    }
 
    echo json_encode($array);
 
}
 
?>