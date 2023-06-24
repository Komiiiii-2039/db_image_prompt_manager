<?php
// Database connection
$servername = "";
$username = "";
$password = "";
$dbname = "";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    // die("Connection failed: " . $mysqli->connect_error);
    sendResponse("error", "Connection failed: " . $mysqli->connect_error);
} else {
    $mysqli->set_charset("utf8");
    $mysqli->query("start transaction");
}

$target_dir = "./img/";
$target_file = $target_dir . basename($_FILES["data"]["name"]);
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); //return image extension(png, jpg, etc)
$new_file_name = uniqid().".".$imageFileType;
$target_file = $target_dir . $new_file_name;

$response = array();

// Check if image file is an actual image or fake image
if($_FILES['data']['tmp_name'] == ""){
    // die("Over data size. Upload less than 2MB.");
    sendResponse("error", "Over data size. Upload less than 2MB.");
}
$check = getimagesize($_FILES["data"]["tmp_name"]);
if(!$check) {
    mysqli->query("rollback");
    sendResponse("error", "File is not an image.");
    // die("File is not an image.");
}



$data = $new_file_name;
$seed = mysqli_real_escape_string($mysqli, $_POST["seed"]);
$cfg = mysqli_real_escape_string($mysqli, $_POST["cfg"]);
$steps = intval($_POST["steps"]);
$width = intval($_POST["width"]);
$height = intval($_POST["height"]);

//default values
$ModelID = -1;
$VAEID = -1;
$SamplerID = -1;
$positivePrompt = -1;
$negativePrompt = -1;

// Fetch or create model id
if(!empty(trim($_POST['ModelName']))){
    $ModelName = mysqli_escape_string($mysqli, $_POST["ModelName"]);
    $ModelID = fetchOrCreateId($mysqli, 'model', 'Name', $ModelName);
} else {
    $mysqli->query("rollback");
    sendResponse("error", "Error: Empty Model Name");
    // die("Error: Empty Model Name");
}

// Fetch or create VAE id
if(!empty(trim($_POST['VAEName']))){
    $VAEName = mysqli_escape_string($mysqli, $_POST["VAEName"]);
    $VAEID = fetchOrCreateId($mysqli, 'VAE', 'Name', $VAEName);
} else { //May be empty
    $VAEName = "";
    $VAEID = fetchOrCreateId($mysqli, 'VAE', 'Name', $VAEName); 
    //$mysqli->query("rollback");
    //die("Error: Empty VAE Name");
}

// Fetch or create Sampler id
if(!empty(trim($_POST['SamplerName']))){
    $SamplerName = mysqli_escape_string($mysqli, $_POST["SamplerName"]);
    $SamplerID = fetchOrCreateId($mysqli, 'sampler', 'Name', $SamplerName);
} else {
    $mysqli->query("rollback");
    // die("Error: Error Sampler Name");
    sendResponse("error", "Error: Error Sampler Name");
}

// Insert into prompt table positive prompt and negative prompt
// then, get IDs for each
$positivePrompt = mysqli_escape_string($mysqli, trim($_POST['PositivePrompt']));
if(!empty($positivePrompt)) {
    //$positivePromptID = fetchOrCreateId($mysqli, 'prompt','Text', $positivePrompt);
    $sql = "INSERT INTO prompt (Text) VALUES('$positivePrompt')";
    if($mysqli->query($sql)){
        $positivePromptID = $mysqli->insert_id;
    } else {
        $mysqli->query("rollback");
        // die("Error: " . $sql . "<br>" . $mysqli->error);
        sendResponse("error", "Error: " . $sql . "<br>" . $mysqli->error);
    }
}
$negativePrompt = mysqli_escape_string($mysqli, trim($_POST['NegativePrompt']));
if(!empty($negativePrompt)) {
    //$negativePromptID = fetchOrCreateId($mysqli, 'prompt', 'Text', $negativePrompt);
    $sql = "INSERT INTO prompt (Text) VALUES('$negativePrompt')";
    if($mysqli->query($sql) == TRUE){
        $negativePromptID = $mysqli->insert_id;
    } else {
        $mysqli->query("rollback");
        //die("Error: " . $sql . "<br>" . $mysqli->error);
        sendResponse("error", "Error: " . $sql . "<br>" . $mysqli->error);
    }
}

$sql = "INSERT INTO image (Data, Seed, CFG, Steps, Width, Height, ModelID, vaeID, SamplerID) 
VALUES ('$data', '$seed', '$cfg', '$steps', '$width', '$height', '$ModelID', '$VAEID', '$SamplerID')";

$imageID = -1;
if ($mysqli->query($sql)) {
    $imageID = $mysqli->insert_id;
} else {
    $mysqli->query("rollback");
    //die("Error: " . $sql . "<br>" . $mysqli->error);
    sendResponse("error", "Error: " . $sql . "<br>" . $mysqli->error);
}

//Insert into generate table imageID and prompts
if($positivePromptID != -1){
    $sql = "INSERT INTO generate (ImageID, PromptID, Type) VALUES('$imageID', '$positivePromptID', 'Positive')";
    if (!$mysqli->query($sql)) {
        $mysqli->query("rollback");
        // die("Error: " . $sql . "<br>" . $mysqli->error);
        sendResponse("error", "Error: " . $sql . "<br>" . $mysqli->error);
    }
} 
if($negativePromptID != -1){
    $sql = "INSERT INTO generate (ImageID, PromptID, Type) VALUES('$imageID', '$negativePromptID', 'Negative')";
    if (!$mysqli->query($sql)) {
        $mysqli->query("rollback");
        //die("Error: " . $sql . "<br>" . $mysqli->error);
        sendResponse("error", "Error: " . $sql . "<br>" . $mysqli->error);
    }
}

if($positivePromptID == -1 && $negativePromptID == -1){
        $mysqli->query("rollback");
        $response["status"] = "error";
        $response["message"] = "There was an error uploading the file.";
        // die("Error: Empty prompts.");
        sendResponse("error", "Error: Empty prompts.");
} else {
    // Move file to img folder
    if (!move_uploaded_file($_FILES["data"]["tmp_name"], $target_file)) {
        mysqli->query("rollback");
        //die("Failed to upload image.");
        sendResponse("error", "Failed to upload image.");
    }
    $mysqli->query("commit");
    //echo "New record created successfully";
    sendResponse("success", "New record created successfully");
    $mysqli->close();
}

echo json_encode($response);

function fetchOrCreateId($mysqli, $table, $column, $value) {
    $sql = "SELECT * FROM $table WHERE $column = \"$value\"";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        // if name exists, fetch the id
        $row = $result->fetch_array(MYSQLI_NUM);
        return $row[0];
    } else {
        // if name does not exist, create a new entry and return the id
        $sql = "INSERT INTO $table ($column) VALUES ('$value')";
        if ($mysqli->query($sql)) {
            return $mysqli->insert_id;
        } else {
            $mysqli->query("rollback");
            // die("Error: " . $sql . "<br>" . $mysqli->error);
            sendResponse("error", "Error: " . $sql . "<br>" . $mysqli->error);
        }
    }
}

function sendResponse($status, $message) {
    $response["status"] = $status;
    $response["message"] = $message;
    echo json_encode($response);
    exit();
}

?>
