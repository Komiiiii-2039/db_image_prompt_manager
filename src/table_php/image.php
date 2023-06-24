<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>image table</title>
	</head>
<body>
<table border="1">
<caption> image </caption>
<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");

mysqli_set_charset($conn, "utf8");
$sql = "SELECT * FROM image";
$res = mysqli_query($conn, $sql);
print("<tr>");
for( $i = 0; $i < mysqli_num_fields($res); $i++ ){
    print( "<th>".mysqli_fetch_field_direct( $res, $i)->name."</th>" );
}
print("</tr>");
while($row = $res->fetch_array(MYSQLI_NUM)){
    print("<tr>");
    for( $i = 0; $i < mysqli_num_fields($res); $i++ ){
	if($i == 1){
		$path = "../img/".$row[$i];
		print("<td><img width=30% src=\"".$path."\" /> </td>");
	}
	else print( "<td>".$row[$i]."</td>" );
	}
    print("</tr>");
}
?>
</table>
</body>
</html>
