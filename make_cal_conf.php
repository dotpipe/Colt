<?php
require("swatch/required.php");
$cal_setup = new  \Swatch\Container\Matrix("Any");
$cal_month = new  \Swatch\Container\Vector("String");
$cal_day = new  \Swatch\Container\Vector("String");
$days = array('31', '28', '31', '30', '31', '30', '31', '31', '30', '31', '30', '31');
$months = array('January', 'February', 'March', 'April', 'May',
		'June', 'July', 'August', 'September', 'October',
		'November', 'December');
foreach ($months as $bean) {
	$cal_month->push($bean);
}
$cal_setup->push($cal_month);
foreach ($days as $bean) {
	$cal_day->push($bean);
}
$cal_setup->push($cal_day);
$dom1 = new \DOMDocument();
$xm = new \Swatch\Container\XML();
$dom1 = $xm->xmlOut($cal_setup, $dom1);
$dom1->save("dates_conf.xml");
$alpha = new  \Swatch\Container\Vector("String");
$beta = new  \Swatch\Container\Matrix("Any");
$beans = 0;
$dom1 = new DOMDocument();
foreach($cal_month->dat as $mo) {
	$week_end = 1;
	for ($j = $beans ; $j < sizeof($cal_day->dat) ; $j++) {
		while ($alpha->size() < $cal_day->dat[$j]) {
			for ($i = $week_end ; $i < $week_end*7+1 ; $i++) {
				$alpha->push($i);
				if ($alpha->size() >= $cal_day->dat[$j])
					break;
			}
			$week_end = $week_end * 7 + 1;
		}
		break;
	}
	$beans++;
	$beta->push($alpha);
	$alpha =  \Swatch\Container\newObj("Vector","String");
}

$dom1 = $xm->xmlOut($beta, $dom1);
$dom1->save("master_cal.xml");
?>