<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* @param $date
* @param Converts Date from dd-mm-yyyy to yyyy-mm-dd
* @return Date must be in yyyy-mm-dd
*/
function convDate($date, $separator = '-') {
	$result = '0000-00-00';
	if (strlen($date) >= 16)
		$result = '0000-00-00 00:00:00';
	if (is_string($date) && (strlen($date) == 10 OR strlen($date) == 16 OR strlen($date) == 19)) {
		if (preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $date, $regs)) 
			$result = "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]:$regs[6]";
		elseif (preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})/", $date, $regs)) 
			$result = "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]";
		elseif (preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/", $date, $regs)) 
			$result = "$regs[3]-$regs[2]-$regs[1]";
	}
	return $result;
}


function closeWindow() {
	return <<< END
<html>
<head>
<script type="text/javascript" language="JavaScript">
<!--
self.close();
// -->
</script>
</head>
<body></body>
</html>
END;
}

function closeSelfAndRefreshParent() {
	return <<< END
<html>
<head>
<script type="text/javascript" language="JavaScript">
<!--
top.opener.location.reload(true);
self.close();
// -->
</script>
</head>
<body></body>
</html>
END;
}

/**
* Function : EnglishDigitGroup
*
* @param $Num
*/
function englishDigitGroup ($Num) {
	$Words = "";
	$Flag = 0;

	switch (($Num - ($Num % 100)) / 100) {
		case 0: $Flag = 0; break;
		case 1: $Words = "One Hundred"; 	$Flag = 1; break;
		case 2: $Words = "Two Hundred"; 	$Flag = 1; break;
		case 3: $Words = "Three Hundred"; 	$Flag = 1; break;
		case 4: $Words = "Four Hundred"; 	$Flag = 1; break;
		case 5: $Words = "Five Hundred"; 	$Flag = 1; break;
		case 6: $Words = "Six Hundred"; 	$Flag = 1; break;
		case 7: $Words = "Seven Hundred";	$Flag = 1; break;
		case 8: $Words = "Eight Hundred";	$Flag = 1; break;
		case 9: $Words = "Nine Hundred"; 	$Flag = 1; break;
	}

	if ($Flag) { $Num %= 100; }
	if ($Num)  {	if ($Flag) { $Words .= " "; } }
	else { return $Words;	}

	switch (($Num - ($Num % 10)) / 10) {
		case 0:
		case 1: $Flag = 0; break;
		case 2: $Words .= "Twenty";  $Flag = 1; break;
		case 3: $Words .= "Thirty";  $Flag = 1; break;
		case 4: $Words .= "Forty";   $Flag = 1; break;
		case 5: $Words .= "Fifty";   $Flag = 1; break;
		case 6: $Words .= "Sixty";   $Flag = 1; break;
		case 7: $Words .= "Seventy"; $Flag = 1; break;
		case 8: $Words .= "Eighty";  $Flag = 1; break;
		case 9: $Words .= "Ninety";  $Flag = 1; break;
	}

	if ($Flag) { $Num %= 10; }
	if ($Num)  { if ($Flag) { $Words .= "-"; } }
	else { return $Words; }

	switch ($Num) {
		case 1:  $Words .= "One";   break;
		case 2:  $Words .= "Two";   break;
		case 3:  $Words .= "Three"; break;
		case 4:  $Words .= "Four";  break;
		case 5:  $Words .= "Five";  break;
		case 6:  $Words .= "Six";   break;
		case 7:  $Words .= "Seven"; break;
		case 8:  $Words .= "Eight"; break;
		case 9:  $Words .= "Nine";  break;
		case 10: $Words .= "Ten";   break;
		case 11: $Words .= "Eleven";    break;
		case 12: $Words .= "Twelve";    break;
		case 13: $Words .= "Thirteen";  break;
		case 14: $Words .= "Fourteen";  break;
		case 15: $Words .= "Fifteen";   break;
		case 16: $Words .= "Sixteen";   break;
		case 17: $Words .= "Seventeen"; break;
		case 18: $Words .= "Eighteen";  break;
		case 19: $Words .= "Nineteen";  break;
	}

	return $Words;
}


/**
* Function : NumberToWords
*
* @param $Num
* @param $argOnly = true
*/
function numberToWords ($Num, $argOnly = true) {
	$Words = "";

	if ($Num == 0) {
		$Words = "Zero";
	}

	if ($Num < 0) {
		$Words = "Negative ";
		$Num -= $Num;
	}

	$arrNum = explode (".", $Num);
	$Num = $arrNum[0];

	if ($Num >= 10000000) {
		$Words .= EnglishDigitGroup (($Num - ($Num % 10000000)) / 10000000) . " Crore";
	$Num %= 10000000;
		if ($Num) { $Words .= " "; }
	}

	if ($Num >= 100000) {
		$Words .= EnglishDigitGroup (($Num - ($Num % 100000)) / 100000) . " Lacs";
		$Num %= 100000;
		if ($Num) { $Words .= " "; }
	}

	if ($Num >= 1000) {
		$Words .= EnglishDigitGroup (($Num - ($Num % 1000)) / 1000) . " Thousand";
		$Num %= 1000;
		if ($Num) { $Words .= " "; }
	}

	if ($Num > 0) {
		$Words .= EnglishDigitGroup($Num);
	}

	if ($argOnly) {
		$Words .= " Only";
	}

	return $Words;
}


/**
* @name TimeInWords
* @param $argTime
* @return Time In English Words
*/
function timeInWords ($argTime) {

	$strTime = NULL;
	$Time = explode(":", $argTime);

	$strTime = numberToWords($Time[0], false);
	if ($Time[1] == "00") {
		$strTime .= " Hundred Hrs.";
	}
	else {
		$strTime .= " " . numberToWords($Time[1], false) . " Hrs.";
	}

	return $strTime;
}



function insert_comma($input, $group_digits = 2) {
    if(strlen($input) <= $group_digits) { 
    	return $input; 
   	}
    $length = substr($input, 0, strlen($input) - $group_digits);
    $formatted_input = insert_comma($length) . "," . substr($input, - $group_digits);
    return $formatted_input;
}

function inr_format($num) {
	if (is_null($num))
		return '';
	
    $pos = strpos((string)$num, ".");
    if ($pos === false) { 
    	$decimalpart = "00";
    }
    else { 
    	$decimalpart = substr($num, $pos+1, 2); 
    	$num = substr($num, 0, $pos); 
    }

    if (strlen($num) > 3 & strlen($num) <= 12) {
        $last3digits = substr($num, -3);
        $numexceptlastdigits = substr($num, 0, -3);
        $formatted = insert_comma($numexceptlastdigits);
        $stringtoreturn = $formatted . "," . $last3digits . "." . str_pad($decimalpart, '2', '0', STR_PAD_RIGHT);
    }
    elseif (strlen($num) <= 3)
		$stringtoreturn = $num . "." . str_pad($decimalpart, '2', '0', STR_PAD_RIGHT);
    elseif (strlen($num) > 12)
		$stringtoreturn = number_format($num, 2);

    if (substr($stringtoreturn, 0, 2) == "-,")
    	$stringtoreturn = "-" . substr($stringtoreturn, 2);

    return $stringtoreturn;
}

function fixedHeaderJS($table, $fixed = '#FixedHeader', $position = 40) {
	return "
	$('" . $table . "').find('thead tr').children().each(function(i, e) {
	    $($('" . $fixed . "').find('thead tr').children()[i]).width($(e).width());
	});
	$('" . $fixed . "').width($('" . $table . "').width());

	if (!($.browser == 'msie' && $.browser.version < 7)) {
        $(window).scroll(function(event) {
            $('" . $fixed . "').css({
			 	left: ($('" . $table . "').offset().left - $(window).scrollLeft()) + 'px'
			});
            if ($(this).scrollTop() > $position) {
                $('" . $fixed . "').addClass('fixedTop show');
                $('" . $fixed . "').addClass('hide');
            } else {
                $('" . $fixed . "').removeClass('fixedTop show');
                $('" . $fixed . "').addClass('hide');
            }
        });
    }
";
}

function currencyWords($number) {

	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ', ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = [
		0    => 'zero',
		1    => 'one',
		2    => 'two',
		3    => 'three',
		4    => 'four',
		5    => 'five',
		6    => 'six',
		7    => 'seven',
		8    => 'eight',
		9    => 'nine',
		10   => 'ten',
		11   => 'eleven',
		12   => 'twelve',
		13   => 'thirteen',
		14   => 'fourteen',
		15   => 'fifteen',
		16   => 'sixteen',
		17   => 'seventeen',
		18   => 'eighteen',
		19   => 'nineteen',
		20   => 'twenty',
		30   => 'thirty',
		40   => 'fourty',
		50   => 'fifty',
		60   => 'sixty',
		70   => 'seventy',
		80   => 'eighty',
		90   => 'ninety',
		100  => 'hundred',
		1000 => 'thousand',
		1000000             => 'million',
		1000000000          => 'billion',
		1000000000000       => 'trillion',
		1000000000000000    => 'quadrillion',
		1000000000000000000 => 'quintillion'
	];

	if (!is_numeric($number)) {
		return false;
	}

	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
	        // overflow
		trigger_error(
			'currencyWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
			E_USER_WARNING
			);
		return false;
	}

	if ($number < 0) {
		return $negative . currencyWords(abs($number));
	}

	$string = $fraction = null;

	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}

	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . currencyWords($remainder);
			}
			break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = currencyWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= currencyWords($remainder);
			}
			break;
	}

	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}

	return $string;
}