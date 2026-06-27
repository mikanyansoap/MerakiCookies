<?php
$_GET['action'] = 'submit_feedback';
$_POST['Customer_Alias'] = 'Test User';
$_POST['likes'] = 'The cookies are great!';
$_POST['improvements'] = '';
$_POST['additional'] = '';

ob_start();
require "store.php";
$output = ob_get_clean();

echo "RAW OUTPUT:\n";
echo $output;
?>
