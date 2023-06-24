<?php
ob_start();

$servername = "";
$username = "";
$password = "";
$dbname = "";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check mysqliection
if ($mysqli->connect_error) {
  die("mysqli connection failed: " . $mysqli->mysqli->connect_error);
} else {
    $mysqli->set_charset("utf8");
}

// ImageID to be deleted
$imageID = mysqli_escape_string($mysqli, trim($_POST["id"]));
$response = $mysqli->query("SELECT * FROM image WHERE ImageID = $imageID");
if(!$response->fetch_assoc()){
    $_SESSION['error_message'] = "Error: ImageID not found.";
    header("Location: main.php", true, 404);
}

if(!is_numeric($imageID)){
    $_SESSION['error_message'] = "Error: ImageID not specified or invalid number.";
    header("Location: main.php");
}

$imageData = mysqli_escape_string($mysqli, trim($_POST["imageData"]));
if(!is_numeric($imageID)){
    $_SESSION['error_message'] = "Error: ImageData not specified or invalid number.";
    header("Location: main.php");
}

$mysqli->query("START TRANSACTION");
    // Delete from prompt
    $sql = "DELETE FROM prompt WHERE PromptID IN (SELECT PromptID FROM generate WHERE ImageID = $imageID)";
    $result = $mysqli->query($sql);
    if(!$result){
        $mysqli->query("ROLLBACK");
        $_SESSION['error_message'] = "Error: " . $mysqli->error;
        header("Location: main.php");
        exit();
    }

    // Delete from generate
    $sql = "DELETE FROM generate WHERE ImageID = $imageID";
    $result = $mysqli->query($sql);
    if(!$result){
        $mysqli->query("ROLLBACK");
        $_SESSION['error_message'] = "Error: " . $mysqli->error;
        header("Location: main.php");
        exit();
    }
    // Delete from image
    $sql = "DELETE FROM image WHERE ImageID = $imageID";
    $result = $mysqli->query($sql);
    if($result){
        $mysqli->query("COMMIT");
    } else {
        $mysqli->query("ROLLBACK");
        $_SESSION['error_message'] = "Error: " . $mysqli->error;
        header("Location: main.php");
        exit();
    }
    if(is_writable("./img/")){
        unlink("./img/" . $imageData);
    }

$mysqli->close();
header("Location: main.php");
exit();
?>
