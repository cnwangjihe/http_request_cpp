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
	echo mysqli_query($link,"CREATE TABLE IF NOT EXISTS `".$table."`(
		`pos` INT UNSIGNED AUTO_INCREMENT,
		`origin` INT UNSIGNED,
		`data` LONGTEXT,
		PRIMARY KEY ( `pos` )
		)ENGINE=InnoDB DEFAULT CHARSET=utf8");
}
$link=mysqli_connect("127.0.0.1","root","123456","blockchain");
if (!$link)
	die("connect fail");
mysqli_set_charset($link, "utf8");
if ($_SERVER['REQUEST_METHOD']=="GET")
{
	if ($_GET["type"]==2)
	{
		$res=mysqli_query($link,"SELECT concat('drop table `',table_name,'`;') FROM information_schema.TABLES WHERE (table_name!=10001 and TABLE_SCHEMA=\"blockchain\");");
		$res_count=mysqli_num_rows($res);
		echo $res_count;
		for ($i=0;$i<$res_count;$i++)
		{
			$row=mysqli_fetch_row($res);
			mysqli_query($link,$row[0]);
		}
		echo "Success";
		mysqli_free_result($res);
		die();
	}
	$client=$_GET["client"];
	if (!CheckTable($link,$client))
	{
		echo 0;
		die();
	}
	$count=GetCount($link,$client);
	if ($_GET["type"]==0)
		echo $count;
	else
		echo mysqli_fetch_array(mysqli_query($link,"SELECT data FROM `".$client."` WHERE os=".$_GET["pos"]))["data"];

}
if ($_SERVER['REQUEST_METHOD']=="POST")
{
	$target=$_POST["target"];
	echo $target;
	if (!CheckTable($link,$target))
		CreateTable($link,$target);
	if (mysqli_query($link, "INSERT INTO `".$target."` ( `data`, `origin`) VALUES ('".mysqli_real_escape_string($link,$_POST["data"])."', '".$_POST["client"]."')"))
		echo "Success";
	else
		echo "Error: <br>" . mysqli_error($link);
}
mysqli_close($link);
?>