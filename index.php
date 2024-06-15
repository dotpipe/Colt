<?php
if (!isset($_SESSION))
	session_start();
include_once('swatch/required.php');

	class appt {
		function __construct() {
			$map = null;
			$bool = false;
			$xm = new \Swatch\Container\XML();
			$this->map1 = new \Swatch\Container\Map();
			if (isset($_GET['rem']) && $_GET['rem'] == true) {
				$xm = new \Swatch\Container\XML();
				$this->map1 = new \Swatch\Container\Map();
				if (file_exists(md5($_GET['appt'])))
					$this->map1 = $xm->xmlIn(md5($_GET['appt']));
				$this->map1->removeKV($_GET['time'], $_GET['acct_no']);
				$dom = new \DOMDocument();
				$dom = $xm->xmlOut($this->map1,$dom);
				$dom->save(md5($_GET['appt']));
				$bool = true;
			}
			else if (($bool == false && !file_exists(md5($_GET['acct_no']))) || ($bool == false && isset($_GET['upd']) && $_GET['upd'] == true)) {
				echo "AFA" . $_GET['time'];
				$this->map1->add("acct_no",$_GET['acct_no']);
				$this->map1->add("last",$_GET['last']);
				$this->map1->add("first",$_GET['first']);
				$this->map1->add("middle",$_GET['middle']);
				$this->map1->add("address",$_GET['address']);
				$this->map1->add("city",$_GET['city']);
				$this->map1->add("state",$_GET['state']);
				$this->map1->add("zip",$_GET['zip']);
				$this->map1->add("notes",$_GET['notes']);
			}
			else if ($bool == false) {
				$this->map1 = $xm->xmlIn(md5($_GET['acct_no']));
				$notes = $this->map1->get("notes");
				$this->map1->replace("notes", $notes . " " . $_GET['notes']);
			}
			$dom = new \DOMDocument();
			$dom = $xm->xmlOut($this->map1,$dom);
			$dom->save(md5($_GET['acct_no']));
			
			if (file_exists(md5($_GET['appt']))) {
				$map = $xm->xmlIn(md5($_GET['appt']));
				$x = 0;
				while ($x < $map->size()) {
					$m = $map->at($x++);
					if ($_GET['time'] == $m[0] && $m[1] != $_GET['acct_no']) {
						$_SESSION['ERROR'] = "DOUBLE BOOKING";
						return;
					}
				}
				$x = 0;
				$y = 0;
				while ($x < $map->size()) {
					$m = $map->at($x++);
					if ($_GET['time'] != $m[0] && $m[1] == $_GET['acct_no']) {
						$y++;
						if ($y > 1) $_SESSION['ERROR'] = "Client booked " . $y . "x today";
					}
				}
				$dom = new \DOMDocument();
				$xs = new \Swatch\Container\Set();
				if (file_exists("all_appts.xml"))
					$xs = $xm->xmlIn("all_appts.xml");
				$xs->add($_GET['appt']);
				$dom = $xm->xmlOut($xs,$dom);
				$dom->save("all_appts.xml");

				$map->add($_GET['time'],$_GET['acct_no']);
				$dom = new \DOMDocument();
				$dom = $xm->xmlOut($map,$dom);
				$dom->save(md5($_GET['appt']));
			}
			else {

				$dom = new \DOMDocument();
				$xs = new \Swatch\Container\Set();
				if (file_exists("all_appts.xml"))
					$xs = $xm->xmlIn("all_appts.xml");
				$xs->add(md5($_GET['appt']));
				$dom = $xm->xmlOut($xs,$dom);
				$dom->save("all_appts.xml");

				$dom = new \DOMDocument();
				$map = new \Swatch\Container\Map();
				if (file_exists(md5($_GET['appt'])))
					$map = $xm->xmlIn(md5($_GET['appt']));
				$map->add($_GET['time'],$_GET['acct_no']);
				$dom = $xm->xmlOut($map,$dom);
				$dom->save(md5($_GET['appt']));
			}
			if (!file_exists("all_accts.xml") || !file_exists(md5($_GET['appt']))) { //(xmlSearch($_GET['time'], $_GET['appt']))) {
				$temp = \Swatch\Container\newObj("Set");
				if (file_exists("all_accts.xml"))
					$temp = $xm->xmlIn("all_accts.xml");
				$temp->add(md5($_GET['appt']));
				$dom1 = new \DOMDocument();
				$dom1 = $xm->xmlOut($temp, $dom1);
				$dom1->save("all_accts.xml");
			}
		}
	}
	if (!((isset($_GET['acct_no']) && isset($_GET['last']) && isset($_GET['first']) && isset($_GET['middle']) && 
		isset($_GET['address']) && isset($_GET['city']) && isset($_GET['state']) && isset($_GET['zip']) && 
		isset($_GET['appt']) && 
		isset($_GET['time']) && isset($_GET['notes'])))) {}
	else
		$newins = new \appt();

function xmlSearch($date) {
	if ($date == null)
		return null;
	$newset = \Swatch\Container\newObj("Set");
	$xm = new \Swatch\Container\XML();
	if (file_exists("all_appts.xml"))
		$newset = $xm->xmlIn("all_appts.xml");
	$g = null;
	$newset->add(md5($date));
	if (file_exists(md5($date)))
		$g = $xm->xmlIn(md5($date));
	$dn = new \DOMDocument();
	$dn = $xm->xmlOut($newset,$dn);
	$dn->save("all_appts.xml");
	return $g;
}
?>
<?php
function xmlHistSearch($searchkey, $file, $f) {
	if (!file_exists($file))
		return null;
	$xm = new \Swatch\Container\XML();
	$xml = $xm->xmlIn($file);
	
	if ($xml->parentType == "mMap") {
		if ($xml->keyIsIn($searchkey))
			return $xml->getMap($x);
	}
	if ($xml->parentType == "Set")
		return null;
	return null;
}
?>

<script>
var f = 0;
var g = 0;
function changeDetails(i) {
	all_i = document.getElementsByClassName("detailing");
	for (v = 0 ; v < all_i.length ; v++)
		all_i[v].style.display = "none";
	all_i[i].style.display = "block";

}
function getMonth(i) {
	all_i = document.getElementsByClassName("mi");
	for (v = 0 ; v < all_i.length ; v++)
		all_i[v].style.display = "none";
	///if (all_i[v].style.display == "block") {
		if (i == 1)
			f--;
		else if (i == 0)
			f++;
		if (f < 0)
			f = (12 + f)%12
		else if (f > 11)
			f = 0;
		thisMonth(f);
		return;
	//}
	thisMonth(f);
}
function prevMonth() {
	all_i = document.getElementsByClassName("mi");
	all_p = document.getElementsByClassName("mp");
	for (v = 0 ; v < all_i.length ; v++)
		all_i[v].style.display = "none"
	for (v = 0 ; v < all_p.length ; v++) {
		all_p[v].style.background = "white";
	}
	all_p[f].style.background = "blue";
	all_i[f].style.display = "block";
	if (f <= 0)
		f = (12 + f)%12;
	f--;
}
function nextMonth() {
	all_i = document.getElementsByClassName("mi");
	all_p = document.getElementsByClassName("mp");
	for (v = 0 ; v < all_i.length ; v++)
		all_i[v].style.display = "none"
	for (v = 0 ; v < all_p.length ; v++) {
		all_p[v].style.background = "white";
	}
	f++;
	f = f%12;
	all_p[f].style.background = "blue";
	all_i[f].style.display = "block";

}
function thisMonth(i) {
	all_i = document.getElementsByClassName("mi");
	all_p = document.getElementsByClassName("mp");
	if (all_i[i].style.display == "block") {
		all_i[i].style.display = "none";
		//all_p[i].style.display = "block";
		all_p[i].style.background = "white";
		//for (v = 0 ; v < all_p.length ; v++)
		//	all_p[v].style.background = "white"
		return;
	}
	for (v = 0 ; v < all_i.length ; v++)
		all_i[v].style.display = "none"
	if (all_i[i].style.display == "none") {
		all_i[i].style.display = "block";
		//all_p[i].style.display = "none";	
		for (v = 0 ; v < all_p.length ; v++)
			all_p[v].style.background = "white"
		all_p[i].style.background = "blue";
		g = i;
		return;
	}
	f = i;
} 
</script>

<?php
function is_month($mo = "1", $fwd = "1", $days, $YEAR) {
	$xm = new \Swatch\Container\XML();
	$tempxml = $xm->xmlIn("dates_conf.xml");
	$beta = \Swatch\Container\newObj("Matrix", "Any");
	$months = array('January', 'February', 'March', 'April', 'May',
		'June', 'July', 'August', 'September', 'October',
		'November', 'December');
	$temp = array('Sun', 'Mon', 'Tues', 'Wed', 'Thurs',
		'Fri', 'Sat');
	$alpha =  \Swatch\Container\newObj("Vector","String");
	foreach ($temp as $day)
		$alpha->push($day);
	$beta->push($alpha);
	$alpha =  \Swatch\Container\newObj("Vector", "String");
	$bean = 1;
	$mo_name = $months;
	$month = $mo_name[($mo-1)%12];
	$tempxml->Iter();
	$mo_cnt = $days;
	$mkt = mktime(0, 0, 0, $mo, 1, $YEAR);
	$fwd = date("w", $mkt);
	for ($i = -1; $i < $fwd-1 ; $i++)
		$alpha->push(null);
	$end_day = 7;
	$bean = 0;
	while ($bean <= $days) {
		for ($i = $bean+1 ; $alpha->size() < 8 ; $i++, $bean++) {
			if ($alpha->size() > 6)
				break;
			if ($bean < $days) {
				$date = '<a href="/?d=' . ($bean + 1) . '&m=' . ($mo) . '&y=' . $YEAR . '">' . $i . '</a>';
				$alpha->push($date);
			}
			else {
				if ($end_day == 7)
					$end_day = $alpha->size();
				$alpha->push(null);
			}
			
		}
		if ($end_day != 0)
			$beta->push($alpha);
		$alpha =  \Swatch\Container\newObj("Vector", "String");
	}
	$cal = "
	background-color:orange;
	color:black;
	height:20;
	width:600;
	border:2px solid black;
	cell-padding:0em;
	cell-spacing:-1em;
	text-align:center;
	vertical-align:center;
	font-size:200%;
	";
	$sidebar = "
	background-color:white;
	color:black;
	height:20;
	width:100%;
	cell-padding:0em;
	cell-spacing:-1em;
	text-align:center;
	vertical-align:center;
	font-size:125%;
	border-bottom:1px solid black;
	";
	$v = '<tr><td>';

	$v .= '<table style="' . $sidebar .';border:3px solid black"><tr>';

	$v .= '<td onclick="javascript:getMonth(0);" style="color:blue;font-size:125%;text-align:left">&times;</td><td>';

	$v .= '<table class="mp" style="' . $sidebar . ';border-bottom:0px solid black;border-radius:20px"><tr>';
	$v .= '<td onclick="javascript:thisMonth(' . ($mo+1)%12 . ');" style="width:175;font-size:90%;">&nbsp;' . $month . ' ' . $YEAR . '&nbsp;</td></tr></table></td>';

	$v .= '<td onclick="javascript:getMonth(1);" style="color:green;font-size:125%;text-align:right">&minus;</td>';
	$v .= '</td></tr></table>';
	$v .= '</td>';

	$b = '<table class="mi" style="border:3px solid black;border-radius:20px;display:none"><tr>';
	$b .= '<td style="' . $cal. '">' . $month . '</td></tr>';
	$b .= '<tr><td>' . $beta->table(null,array($cal));
	$b .= '</td></tr></table>';

	$v .= '</td></tr>';
	return array($v, $b, $end_day);
}
?>
<html>
<head>
<title>Calendars</title>
<style>
a {
	text-decoration:none;
	font-size:125%;
}
</style>	
</head>
<?php

$first_month = date('n');
$vr = array(0, 0, -1);
$j = 0;
$months = "";
$calendar_page = "";
$YEAR = date("Y");
$mkt = mktime(0, 0, 0, ($first_month%12), 1, $YEAR);
for ($i = $first_month; $j < 12 ; $i++) {
	$days = date("t", $mkt);
	$mo = date("n", $mkt);
	$week = date("N", $mkt);
	$vr = is_month($mo, $week, $days, $YEAR);
	if (($i)%12 == 0)
		$YEAR++;
	$mkt = mktime(0, 0, 0, (($mo+1)%12), 1, $YEAR);
	$j++;
	$months .= $vr[0];
	$calendar_page .= $vr[1];
}
?>
<?php
	if (isset($_GET['m']) && isset($_GET['d']) && isset($_GET['y'])) {
		$m = $_GET['m']; $d = $_GET['d'];
		if (strlen($_GET['d']) == 1)
			$d = "0" . $_GET['d'];
		if (strlen($_GET['m']) == 1)
			$m = "0" . $_GET['m'];
		$xdate = $_GET['y'] . '-' . $m . '-' . $d;
		echo '<body onload="javascript:f = ' . ($_GET['m']+1) . ';getMonth(f);" style="margin:5px;overflow-x:auto">';
	}
	else
		echo '<body style="margin:5px;">'; //overflow-x:hidden">';
		
?>
<?php
	$m = 0; $d = 0; $y = 0; $xdate = 0;
	if ((isset($_GET['m']) && isset($_GET['d']) && isset($_GET['y']))) {
		$m = $_GET['m'];
		$d = $_GET['d'];
		if (strlen($_GET['d']) == 1)
			$d = "0" . $_GET['d'];
		if (strlen($_GET['m']) == 1)
			$m = "0" . $_GET['m'];
		$xdate = $_GET['y'] . '-' . $m . '-' . $d;
	}
?>

<?php
function getToday($d) {
	$cnt = 0;
	$n = null;
	$xlinks = null;
	if ($d == null)
		return;
	$d->sync();
	do {
		$f = $d->map;
		$v = "";
		$xm = new \Swatch\Container\XML();
		if (file_exists(md5($f[1])))
			$v = $xm->xmlIn(md5($f[1]));
		else
			return;
		$w = $f[0];
		$hr = $w[0] . $w[1];
		$aft = "pm";
		if ($hr < 12)
			$aft = "am";
		if ($hr > 12)
			$hr = $hr - 12;
		else if ($hr < 10) {
			$hr = $w[1];
			$aft = "am";
		}
		if (is_integer($v->get("first"))) {
			$cnt++;
			continue;
		}
		$xlinks[] = '<a onclick="javascript:changeDetails(' . ($cnt-1) . ');" href="#">' . $v->get("first") . ' ' . $v->get("last") . ' ' . $hr . ':' . $w[3] . $w[4] . $aft . '</a><br>';
		$n = $hr . ':' . $w[3] . $w[4] . $aft;
		$cnt++;
	} while ($d->Iter());
	return $xlinks;
}

?>
<center>
<table>
	<tr>
		<td style='background-color:orange;border:3px solid black;border-radius:20px;text-align:center'>
			<table style='background-color:white;border:1px solid black;text-align:center'>
			<?php echo $months; ?>
			</table>
		</td>
		<td style='width:400;vertical-align:top;text-align:center'>
			<?php echo $calendar_page; ?>&nbsp<br>
			
			<?php
				$xl = null;
				if (isset($_SESSON['ERROR']))
					echo $_SESSION['ERROR'];
				unset($_SESSION['ERROR']);
				if (isset($_GET['m']) && isset($_GET['d']) && isset($_GET['y'])) {

					$m = $_GET['m']; $d = $_GET['d'];
					if (strlen($_GET['d']) == 1)
						$d = "0" . $_GET['d'];
					if (strlen($_GET['m']) == 1)
						$m = "0" . $_GET['m'];
					$date_value = $_GET['y'] . '-' . $m . '-' . $d;
					echo '<div style="font-size:125%">Appointments for ' . $_GET['y'] . '/' . $_GET['m'] . '/' . $_GET['d'] . '</div><br><hr>';
				}

				$xx = xmlSearch($xdate);
				$xl = getToday($xx);
				$v = 0;
				if (is_array($xl))
					while ($v < sizeof($xl))
						echo $xl[$v++];
			?>
		</td>
		<td style="border:3px solid black;border-radius:20px;vertical-align:center;text-align:right;width:350">
	<?php
		$date_value = "";
			if (isset($_GET['m']) && isset($_GET['d']) && isset($_GET['y'])) {
			$m = $_GET['m']; $d = $_GET['d'];
			if (strlen($_GET['d']) == 1)
				$d = "0" . $_GET['d'];
			if (strlen($_GET['m']) == 1)
				$m = "0" . $_GET['m'];
			$date_value = $_GET['y'] . '-' . $m . '-' . $d;
		}
		if ($xl != null) {
			$xlm = xmlSearch($date_value);
			$x = 0;
			{
				$vc = new \Swatch\Container\Vector("Any");
				$xlm->sync();
				$st = new \Swatch\Container\XML();
				for ($i = 0 ; $i < $xlm->size() ; $i++) {
					$d = array($st->xmlIn(md5($xlm->map[1])), $xlm->map[0]);
					$vc->push($d);
					$xlm->Iter();
				}
				//return $d;
			}
			$vc->setIndex(0);
			$vc->sync();
			do {
				$xl = $vc->vect[0];
				if ($xl->value == null) {
					continue;
				}
			echo '
			<table class="detailing" style="text-align:right;display:none"><tr><td>
			<form action="index.php" method="GET">
				<p style="font-size:150%;text-align:center">Enter Visit Details Here:</p>
				<p>Acct. No: <input type="text" name="acct_no" value="' . $xl->value[0] . '"></p>
				<p>Last Name: <input type="text" name="last" value="' . $xl->value[1] . '"></p>
				<p>First Name: <input type="text" name="first" value="' . $xl->value[2] . '"></p>
				<p>Middle Name: <input type="text" name="middle" value="' . $xl->value[3] . '"></p>
				<p>Address: <input type="text" name="address" value="' . $xl->value[4] . '"></p>
				<p>City: <input type="text" name="city" value="' . $xl->value[5] . '"></p>
				<p>State: <input type="text" name="state" value="' . $xl->value[6] . '"></p>
				<p>Zip: <input type="text" name="zip" value="' . $xl->value[7] . '"></p>
				<p>Update: <input type="checkbox" name="upd" value="false"></p>
				<p>Remove: <input type="checkbox" name="rem" value="false"></p>
				<p>Next Appointment: ' . $xdate . '</p>';
			echo	'<p>Time of Appt.: <input name="time" type="time"  value="' . $vc->vect[1] . '"></p>
				<input type="hidden" value="1" name="g">
				<input type="hidden" value="' . $xdate . '" name="appt">
				<p><textarea  cols="35" name="notes" rows="5">' . $xl->value[8] . '</textarea>
				&nbsp;&nbsp;<input name="submit" type="submit" value="save">&nbsp;&nbsp;</p>
			</form>
			</td></tr></table>
			';
			} while ($vc->Iter());
		}
	//	if ($xvals != null) {
			echo '
			<table class="detailing" style="text-align:right;display:block"><tr><td>
			<form action="index.php" method="GET">
				<p style="font-size:150%;text-align:center">Enter Visit Details Here:</p>
				<p>Acct. No: <input type="text" name="acct_no" placeholder="Acct No."></p>
				<p>Last Name: <input type="text" name="last" placeholder="Surname"></p>
				<p>First Name: <input type="text" name="first" placeholder="Given name"></p>
				<p>Middle Name: <input type="text" name="middle" placeholder="Middle name"></p>
				<p>Address: <input type="text" name="address" placeholder="Address"></p>
				<p>City: <input type="text" name="city" placeholder="City"></p>
				<p>State: <input type="text" name="state" placeholder="State"></p>
				<p>Zip: <input type="text" name="zip" placeholder="Zip"></p>
				<p>Next Appointment: ' . $xdate . '</p>';
			echo	'<p>Time of Appt.: <input name="time" type="time"></p>
				<input type="hidden" value="1" name="g">
				<input type="hidden" value="' . $xdate . '" name="appt">
				<p><textarea placeholder="Jot Notes Here" cols="35" name="notes" rows="5"></textarea>
				&nbsp;&nbsp;<input name="submit" type="submit" value="save">&nbsp;&nbsp;</p>
			</form>
			</td></tr><table>
			';
	//	}
	?>
		</td>
	</tr>
</table></center>
</body></html>