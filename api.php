<?php
$link=mysqli_connect("127.0.0.1","root","123456","blockchain");
if (!$link)
	die("connect fail");
mysqli_set_charset($link, "utf8");
if ($_SERVER['REQUEST_METHOD']=="GET")
{
	$results=mysqli_query($link,"SELECT count(*) FROM main WHERE target=".$_GET["client"]);
	$row=mysqli_fetch_array($results);
	$count=$row["count(*)"];
	mysqli_free_result($results);
	if ($_GET["type"]==0)
		echo $count;
	else
	{
		$results=mysqli_query($link,"SELECT data FROM main WHERE target=".$_GET["client"]." and pos=".$_GET["pos"]);
		$row=mysqli_fetch_array($results);
		echo $row["data"];
		mysqli_free_result($results);
	}
}
if ($_SERVER['REQUEST_METHOD']=="POST")
{
	$results=mysqli_query($link,"SELECT count(*) FROM main WHERE target=".$_POST["target"]);
	$row=mysqli_fetch_array($results);
	$count=$row["count(*)"];
	mysqli_free_result($results);
	$sql = "INSERT INTO `main` ( `target`, `data`, `origin`, `pos`) VALUES ('".$_POST["target"]."', '".mysqli_real_escape_string($link,$_POST["data"])."', '".$_POST["client"]."', '".strval($count+1)."')";
	if (mysqli_query($link, $sql))
		echo "Success";
	else
	    echo "Error: <br>" . mysqli_error($link);
}
mysqli_close($link);
?>