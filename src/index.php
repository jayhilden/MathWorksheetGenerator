<?
//error_reporting(E_STRICT);


$schoolName = "Robbinsdale Schools";
$numerator; $denominator;

$numberToPrint = $_GET["numberToPrint"];
$tmp = explode("x", $numberToPrint);
$across = $tmp[0];
$down = $tmp[1];
$action = $_GET["actionType"];

$numerator = $_GET['numerator'];
$denominator = $_GET['denominator'];

$numerators = GetIntArray($numerator, ",");
$denominators = GetIntArray($denominator, ",");

//debugPrint($across); debugPrint($down);
//debugPrint($numerators); debugPrint($denominators);

if (isset($_GET["submit"])) {
	generate($numerators, $denominators, $action, $across, $down);	
	$numerator = implode(",", $numerators);
	$denominator = implode(",", $denominators);
} else {
	$numerator = "1,2,3,4,5,6,7,8,9,10";
	$denominator = "1,2,3,4,5,6,7,8,9,10";
}

$fontSize = ($down < 6) ? 20 : 12;
printHead($fontSize);
printForm($numerator, $denominator, $action, $numberToPrint);

print '</body></html>';

/*************************************************************************************/
function GetIntArray($string, $delimiter) {
	$retval = Array();
	$tmp = explode($delimiter, $string);
	foreach ($tmp as &$x) {
		$i = intval($x);
		if ($i) {
			array_push($retval, $i);
		} 	
	}	
	return $retval;
}

function generate($numberators, $denominators, $action, $across, $down) {	
	$actionPrint;
	$topMustBeGreater = false;
	if ($action == "addition") {$actionPrint = "+";}
	else if ($action == "subtraction") {
		$actionPrint = "-";
		$topMustBeGreater = true;
	}
	else if ($action == "multiplication") {$actionPrint = "x";}
	else if ($action == "division") {
		$actionPrint = "/";
		$topMustBeGreater = true;
	}
	

		
	$numLength = count($numberators) - 1;
	$denLength = count($denominators) - 1;
	/*
	$max = $_GET["numberToPrint"];
	if ($max > 100 || $max < 0) {
		$max = 100;
	}
	*/
	$uc = ucfirst($action);
	
	$answerKey;
	$student;
	
	$student = "
		<table border='0' class='headerTable'>
			<tr>
				<td align='left'>$schoolName Formative Skill Check $uc</td>
				<td align='right'>
					Name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<br/>
					Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
		</table>
		<table border='0' class='mainTable'>
	";
	
	$answerKey = "
		<table border='0' style='page-break-before:always;' class='headerTable'>
			<tr>
				<td align='left'>$schoolName Formative Skill Check $uc ANSWER KEY</td>
				<td align='right'> &nbsp;</td>
			</tr>
		</table>
		<table border='0' class='mainTable'>
			<tr>
	";		
	
	for($row = 1; $row <= $down; $row++) {
		$answerKey .= '<tr>';
		$student .= '<tr>';
		for ($column = 1; $column <= $across; $column++) {
			$denominator = GetDeoniminator($denominators, $denLength, $action);
			$numerator;
			$iter = 0;
			$maxIter = 50;
			while (true && $iter++ < 50) {
				$numerator = $numberators[rand(0, $numLength)];
									
				if ($action == "division") {//result must be a whole number
					//print "j = $j, $numerator % $denominator = " . $numerator % $denominator . '<br/>';
					if ($denominator % $numerator == 0) {break;}
				}
				else {
					if ($topMustBeGreater == false || $numerator >= $denominator) {break;}
				}
				
			}
			
			$student .= GetProblemTD($numerator, $denominator, $action, $actionPrint, false);
			$answerKey .= GetProblemTD($numerator, $denominator, $action, $actionPrint, true);			
		}
		$answerKey .= '</tr>';
		$student .= '</tr>';		
	}
	
	print $student;
	print $answerKey;
	
}

function GetDeoniminator($denominators, $length, $action) {	
	//debugPrint(Array($denominators, $length, $action));
	$denom = $denominators[rand(0, $length)];	
	if ($action == "division") {
		while ($denom == 0) {
			$denom = $denominators[rand(0, $length)];
		}
	}
	return $denom;
}

function GetProblemTD($numerator, $denominator, $action, $actionPrint, $isAnswerKey) {
	$answer;
	if ($isAnswerKey) {
		if ($action == "addition") {$answer = $numerator + $denominator;}
		else if ($action == "subtraction") {$answer = $numerator - $denominator;}
		else if ($action == "multiplication") {$answer = $numerator * $denominator;}
		else if ($action == "division") {$answer = $denominator / $numerator;}
	}
	
	$formattedAnswer = ($answer != null) ? number_format($answer) : null;
	$formattedNumerator = number_format($numerator);
	$formattedDenominator = number_format($denominator);
	if ($action == "division") {
		return "
			<td  align='center' class='problemContainer'>
					<table border='0' cellspacing='0' cellpadding='3' class='problemTable'>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;$formattedAnswer</td>
						</tr>					
						<tr>
							<td style='padding-right:8px;'>$formattedNumerator</td>
							<td class='division'>$formattedDenominator</td>
						</tr>
					</table>
			</td>	
		";		
	}
	else {
		return "
			<td  align='center' class='problemContainer'>
					<table border='0' cellspacing='0' cellpadding='3' class='problemTable'>
						<tr>
							<td></td>
							<td>$formattedNumerator</td>
						</tr>
						<tr>
							<td>$actionPrint</td>
							<td>$formattedDenominator</td>
						</tr>
						<tr>
							<td class='answerTD'>&nbsp;</td>
							<td class='answerTD'>&nbsp;$formattedAnswer</td>
						</tr>
					</table>
			</td>	
		";
	}
}

function printForm($numerator, $denominator, $actionType, $numberToPrint) {
	print "
		<form method='get' action='index.php'>
			<table border='0' cellspacing='3' cellpadding='1' class='noprint adminTable'>
				<tr>
					<td colspan='2'>Note: this section will not be printed</td>
				</tr>
				<tr>
					<th>Action</th>
					<td>				
						<select name='actionType' id='actionType'>
							<option value='addition'>addition</option>
							<option value='subtraction'>subtraction</option>
							<option value='multiplication'>multiplication</option>
							<option value='division'>division</option>
						</select>
					</td>			
				</tr>
				<tr>
					<th>Available Numerators/Divisors (separated by a comma)</th>
					<td>
						<input type='textbox' name='numerator' value='$numerator' />
					</td>
				</tr>
				<tr>
					<th>Available Denominators/Dividends (separated by a comma)</th>
					<td>
						<input type='textbox' name='denominator' value='$denominator' />
					</td>
				</tr>				
				<tr>
					<th>Number of Problems to Print</th>
					<td>
						<select name='numberToPrint' id='numberToPrint'>
							<option value='2x5'>10 (2 by 5)</option>
							<option value='4x5'>20 (4 by 5)</option>
							<option value='5x6'>30 (5 by 6)</option>
							<option value='10x4'>40 (10 by 4)</option>
							<option value='10x5'>50 (10 by 5)</option>							
							<option value='10x6'>60 (10 by 6)</option>
							<option value='10x7'>70 (10 by 7)</option>
							<option value='10x8'>80 (10 by 8)</option>
							<option value='10x9'>90 (10 by 9)</option>
							<option value='10x10'>100 (10 by 10)</option>
						</select>
					</td>
				</tr>
				<tr>
					<th colspan='2' align='left'>
						<input type='submit' name='submit' value='submit' />
					</th>
				</tr>	
			</table>
		</form>
		<script>
		$(document).ready(function() {
			$('#numberToPrint').val('$numberToPrint');
			$('#actionType').val('$actionType');
		});		
		</script>
	";
}

function printHead($fontSize) {
	print "
	<html>
	<title>Math Problem Generator</title>
	<head>
		<script type='text/javascript' src='http://code.jquery.com/jquery-1.7.1.min.js'></script>
		<style type='text/css'>
	@media print {
		.noprint {display:none !important;}
	}
	
	body, td, div {
		font-size: $fontSize\px;
		font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
	}
	
	.adminTable {
		border:1px dashed black;	
	}
	
	.adminTable th {
		text-align:right;	
	}
	
	.adminTable td {
		text-align:left;	
	}
	
	.headerTable {
		page-break-inside:avoid;
		width:100%;
	}
	.mainTable { 
		page-break-inside:avoid;
		width:100%; 
		height: 850px;
	}
	
	.problemContainer {
		border:1px solid black;
		/*
		padding:10px;
		padding-top:15px;
		width: 50%;
		*/
		text-align:center;
		margin: auto;
		
	}
	
	.problemTable {
		margin-left: auto;
		margin-right: auto;
		margin-top: 10%;
		margin-bottom: 10%;
		text-align: right;
	}
	
	.problemTable td {
		/*	
		padding-left: 10px;
		padding-right: 10px;
		*/
	}
	
	.answerTD {
		border-top:1px solid black; 
		text-align: right;
	}
	
	.division {
		border-top:1px solid black; 
		border-left:1px solid black;  
		margin-top:0px;
		padding-left:5px;
	}	
		
		</style>
	<script type='text/javascript'>
	
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>		
	</head>
	<body>
	";	
}

function debugPrint($obj) {
	print "<pre>";
	print_r($obj);
	print "</pre>";	
}

?>
