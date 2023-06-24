<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>VAE table</title>
	</head>
<body>
<table border="1">
<caption> VAE </caption>
<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");
$sql = "SELECT * FROM VAE";
$res = mysqli_query($conn, $sql);
print("<tr>");
for( $i = 0; $i < mysqli_num_fields($res); $i++ ){
    print( "<th>".mysqli_fetch_field_direct( $res, $i)->name."</th>" );
}
print("</tr>");
while($row = mysqli_fetch_array($res)){
    print("<tr>");
    for( $i = 0; $i < mysqli_num_fields($res); $i++ ){
        print( "<td>".$row[$i]."</td>" );
    }
    print("</tr>");
}
?>
</table>
</body>
</html>
