<style><!--
P { text-align:center; font:bold 1.1em sans-serif }
A { color:#444; text-decoration:none }
A:HOVER { text-decoration: underline; color:#44E }
--></style>
<?php
$host = "";
$user = "";
$pass = "";
$dbname = "mmny";

$connection = mysql_connect($host,$user,$pass) or die (mysql_errno().": ".mysql_error()."<BR>");
mysql_select_db($dbname);

$activation=$_GET['hash'];

$query="UPDATE users SET status='1' WHERE hash='$activation'";
mysql_query($query);

mysql_close();

?>
<p><a href="/newyork/make-music">Thank you! You will soon receive the MMNY Newsletter!</a></p>