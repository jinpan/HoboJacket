<?php
require_once("credentials.php");

function sanitize($string){
	return mysql_real_escape_string(htmlspecialchars($string));
}

$myState = sanitize($_REQUEST['myState']);
$myCollege = sanitize($_REQUEST['myCollege']);
$theirState = sanitize($_REQUEST['theirState']);
$theirCollege = sanitize($_REQUEST['theirCollege']);
$amount = max(0,min(500,(float)sanitize($_REQUEST['amount'])));

$query = "INSERT INTO Donations (myState,myCollege,theirState,theirCollege,amount) VALUES ('$myState','$myCollege','$theirState','$theirCollege','$amount')";
mysql_query($query);

$q2 = "UPDATE GivingUniversity SET amount=amount+$amount WHERE state='$myState' AND university='$myCollege'";
mysql_query($q2);

$q3 = "UPDATE ReceivingUniversity SET amount=amount+$amount WHERE state='$theirState' AND university='$theirCollege'";
mysql_query($q3);

?>
