<?php
function CheckTable($link,$table)
{
	if (mysqli_num_rows(mysqli_query($link,"SHOW TABLES LIKE '". $table."'"))==1)
		return 1;
	else
		return 0;
}
function GetCount($link,$table)
{
	$results=mysqli_query($link,"SELECT count(*) FROM `".$table."`");
	$row=mysqli_fetch_array($results);
	$count=$row["count(*)"];
	mysqli_free_result($results);
	return $count;
}
function CreateTable($link,$table)
{
	mysqli_query($link,"CREATE TABLE IF NOT EXISTS `".$table."`(
		`pos` INT UNSIGNED AUTO_INCREMENT,
		`origin` INT UNSIGNED,
		`data` TEXT,
		PRIMARY KEY ( `pos` )
		)ENGINE=InnoDB DEFAULT CHARSET=utf8");
}
$link=mysqli_connect("127.0.0.1","root","123456","blockchain");
if (!$link)
	die("connect fail");
mysqli_set_charset($link, "utf8");
if ($_SERVER["REQUEST_METHOD"]=="GET")
{
	$client=$_GET["client"];
	if (!CheckTable($link,$client))
	{
		die(0);
	}
	$count=GetCount($link,$client);
	if ($_GET["type"]==0)
		echo $count;
	else
	{
		$results=mysqli_query($link,"SELECT data FROM `".$client."` WHERE pos=".$_GET["pos"]);
		$row=mysqli_fetch_array($results);
		echo $row["data"];
		mysqli_free_result($results);
	}
}
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	$target=$_POST["target"];
	echo $target;
	if (!CheckTable($link,$target))
		CreateTable($link,$target);
	$sql = "INSERT INTO `".$target."` ( `data`, `origin`) VALUES ('".mysqli_real_escape_string($link,$_POST["data"])."', '".$_POST["client"]."')";
	if (mysqli_query($link, $sql))
		echo "Success";
	else
	    echo "Fail " . mysqli_error($link);
}
mysqli_close($link);
?>