<?php
$c = file_get_contents('app/Http/Controllers/InvestmentFundController.php');
$o = 0;
$cl = 0;
for($i=0; $i<strlen($c); $i++) {
    if($c[$i]=='{') $o++;
    if($c[$i]=='}') $cl++;
}
echo "Open: $o, Close: $cl\n";
