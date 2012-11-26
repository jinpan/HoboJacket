<?php 
require_once("credentials.php");

function lookUp($state,$university){
	$q = "SELECT name FROM Universities WHERE state='$state' AND id='$university'";
	$r = mysql_query($q);
	$data = mysql_fetch_assoc($r);
	return $data['name'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="keywords" content="donate hobo college rival competition MIT">
	<meta name="description" content="Competitive platform where people can donate their rival schools' clothes to the homeless">
	<meta name="author" content="fluffyunicorns@mit.edu">
	<title>Hobo Jacket</title>
	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/css/bootstrap-combined.min.css" rel="stylesheet">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="ga.js"></script>
</head>
<body style="padding-top:60px">
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="index.html">HoboJacket &#8211; The politically incorrect but right thing to do.</a>
			<ul class="nav pull-right">
				<li><a href="leaderboard.php">Leaderboards</a></li>
			</ul>
		</div>
	</div>
</div>
<div class="container">

<div class="row">
	<div class="well well-small span6">
		<p class="lead">HoboJacket Leaderboards</p>
	</div>
	
	<div class="well well-small span5 pull-right" style="height:50px">
		<div class="form-search">
		<?php if (isset($_REQUEST['q'])){?>
		<input class="input-medium search-query" type="text" id='query' placeholder="<?php print $_REQUEST['q']; ?>"/>
		<?php } else {?>
		<input class="input-medium search-query" type="text" id='query' placeholder="Search for your college"/>
		<?php }?>
		<button class="btn" onClick="window.location='leaderboard.php?q='+$('#query').val()">Search</button>
		<?php if (isset($_REQUEST['q'])){?>
		<button class="btn btn-danger" onClick="window.location='leaderboard.php'">Reset</button>
		<?php }?>
		</div>
	</div>

</div>
<?php if (isset($_REQUEST['q'])){
function sanitize($string){return mysql_real_escape_string($string);}
$query = sanitize($_REQUEST['q']);
$q = "SELECT * FROM Universities WHERE MATCH (name) AGAINST ('$query')";
$data = array();
$result = mysql_query($q);
while ($row = mysql_fetch_assoc($result)){
	$data[] = $row;
}?>
	<div id="searchResults" class="blockquote">
		<p class="lead">Search results for <?php print $query; ?> include . . .</p>
		<p><?php print $data[0]["name"]; for($i=1;$i!=count($data);++$i){print "; ";print $data[$i]["name"];}?></p>
		<hr>

		<p class="lead">Jackets donated by <?php print $query; ?></p>
		<table class="table table-hover">
		<tr><th>College Name</th><th>Jackets donated</th></tr>
		<?php
		for ($i=0;$i!=count($data);++$i){
			$r = mysql_query("SELECT amount FROM GivingUniversity WHERE state='{$data[$i]["state"]}' and university='{$data[$i]["id"]}'");
			$row = mysql_fetch_assoc($r);
			$amount = $row['amount']/10.0;
			print "<tr><td>{$data[$i]["name"]}</td><td>$amount</td></tr>";
		}
		?>
		</table>
		
		<p class="lead">Jackets donated to <?php print $query; ?></p>
		<table class="table table-hover">
		<tr><th>College Name</th><th>Jackets donated to</th></tr>
		<?php
		for ($i=0;$i!=count($data);++$i){
			$r = mysql_query("SELECT amount FROM ReceivingUniversity WHERE state='{$data[$i]["state"]}' and university='{$data[$i]["id"]}'");
			$row = mysql_fetch_assoc($r);
			$amount = $row['amount']/10.0;
			print "<tr><td>{$data[$i]["name"]}</td><td>$amount</td></tr>";
		}
		?>
		</table>
	</div>

<?php }
else {

$numShown = 10;

$result1 = mysql_query("SELECT * FROM GivingUniversity ORDER BY amount DESC LIMIT $numShown");
$data1 = array();
$data1_sorted = array();

$q1 = "SELECT * FROM Universities WHERE";

$temp = mysql_fetch_assoc($result1);
$data1[$temp["state"]][$temp["university"]] = intval($temp["amount"]/10.0);
$data1_sorted[]=array($temp["state"],$temp["university"]);
$q1.= "(state='{$temp["state"]}' AND id='{$temp["university"]}')";
while ($temp = mysql_fetch_assoc($result1)){
	$data1[$temp["state"]][$temp["university"]] = intval($temp["amount"]/10.0);
	$data1_sorted[]=array($temp["state"],$temp["university"]);
	$q1.= " OR (state='{$temp["state"]}' AND id='{$temp["university"]}')";
}
$r1 = mysql_query($q1);
$names1 = array();
while ($temp = mysql_fetch_assoc($r1)){
	$names1[$temp["state"]][$temp["id"]] = $temp["name"];
}
for ($i=0;$i!=count($data1_sorted);$i++){
	$temp = $data1_sorted[$i];
	$data1_sorted[$i] = array($names1[$temp[0]][$temp[1]], $data1[$temp[0]][$temp[1]]);
}

$result2 = mysql_query("SELECT * FROM ReceivingUniversity ORDER BY amount DESC LIMIT $numShown");
$data2 = array();
$data2_sorted = array();

$q2 = "SELECT * FROM Universities WHERE";

$temp = mysql_fetch_assoc($result2);
$data2[$temp["state"]][$temp["university"]] = intval($temp["amount"]/10.0);
$data2_sorted[]=array($temp["state"],$temp["university"]);
$q2.= "(state='{$temp["state"]}' AND id='{$temp["university"]}')";
while ($temp = mysql_fetch_assoc($result2)){
	$data2[$temp["state"]][$temp["university"]] = intval($temp["amount"]/10.0);
	$data2_sorted[]=array($temp["state"],$temp["university"]);
	$q2.= " OR (state='{$temp["state"]}' AND id='{$temp["university"]}')";
}
$r2 = mysql_query($q2);
$names2 = array();
while ($temp = mysql_fetch_assoc($r2)){
	$names2[$temp["state"]][$temp["id"]] = $temp["name"];
}
for ($i=0;$i!=count($data2_sorted);$i++){
	$temp = $data2_sorted[$i];
	$data2_sorted[$i] = array($names2[$temp[0]][$temp[1]], $data2[$temp[0]][$temp[1]]);
}

?>

	<p class="lead">Colleges that contributed the most.</p>
	<table class="table table-hover">
	<tr><th>College Name</th><th>Jackets donated</th></tr>
	<?php
	for ($i=0;$i!=count($data1_sorted);$i++){
		if ($data1_sorted[$i][1]){print "<tr><td>".$data1_sorted[$i][0]."</td><td>{$data1_sorted[$i][1]}</td></tr>";}
	}
	?>
	</table>

	<hr>
	
	<p class="lead">Universities that were contributed to the most.</p>
	<table class="table table-hover">
	<tr><th>College Name</th><th>Jackets donated to</th></tr>
	<?php
	for ($i=0;$i!=count($data2_sorted);$i++){
		if ($data2_sorted[$i][1]){print "<tr><td>".$data2_sorted[$i][0]."</td><td>{$data2_sorted[$i][1]}</td></tr>";}
	}
	?>

	</table>
<?php }?>
</body>
</html>
