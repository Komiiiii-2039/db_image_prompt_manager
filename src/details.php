<!DOCTYPE html>
<html lang="ja">
	<head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>image details</title>

        <style>
        body {
            background-color: #F5F5F5;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .main-image {
            max-width: 100%;
            height: auto;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .info-card {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }

        .info-card p {
            font-size: 16px;
        }

        .btn {
            margin-top: 20px;
            color: white;
            font-size: 16px;
        }

        .btn-danger {
            background-color: #ff5b5b;
        }

        .btn-primary {
            background-color: #5555ff;
        }
        </style>

	</head>

<body>
<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    //die("Connection failed: " . $mysqli->connect_error);
} else {
    $mysqli->set_charset("utf8");
}

$id = $mysqli->escape_string(trim($_GET['id']));
if(!is_numeric($id)){
    //die("Invalid ID");
}
$response = $mysqli->query("SELECT * FROM image WHERE ImageID = $id");
if(!$response->fetch_assoc()){
    header("Location: main.php", true, 404);
}

$sql = " 
SELECT img.ImageID AS ImageID, img.Data AS ImageData, positive_prompt.Text AS PositivePrompt, negative_prompt.Text AS NegativePrompt, model.Name AS ModelName, VAE.Name AS VAEName, sampler.Name AS SamplerName,
img.Seed AS Seed, img.CFG AS CFG, img.Steps AS Steps, img.Width AS Width, img.Height AS Height
FROM image AS img
LEFT JOIN
model ON img.ModelID = model.ModelID
LEFT JOIN
VAE ON img.VAEID = VAE.VAEID
LEFT JOIN
sampler ON img.SamplerID = sampler.SamplerID
INNER JOIN 
generate AS positive_gen ON img.ImageID = positive_gen.ImageID
INNER 
JOIN prompt AS positive_prompt ON positive_gen.PromptID = positive_prompt.PromptID 
AND positive_gen.Type = 'Positive'
LEFT JOIN 
generate AS negative_gen ON img.ImageID = negative_gen.ImageID
INNER JOIN 
prompt AS negative_prompt ON negative_gen.PromptID = negative_prompt.PromptID
AND negative_gen.Type = 'Negative'
WHERE img.ImageID = $id
";

$result = $mysqli->query($sql);
$image = $result->fetch_assoc();
?>

    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <img class="main-image" src="./img/<?php echo $image['ImageData']; ?>">
            </div>
            <div class="col-lg-6">
                <div class="info-card">
                    <p>Positive Prompt: <?php echo $image['PositivePrompt']; ?></p>
                    <p>Negative Prompt: <?php echo $image['NegativePrompt']; ?></p>
                    <p>Model: <?php echo $image['ModelName']; ?></p>
                    <p>VAE: <?php echo $image['VAEName']; ?></p>
                    <p>Sampler: <?php echo $image['SamplerName']; ?></p>
                    <p>Seed: <?php echo $image['Seed']; ?></p>
                    <p>CFG: <?php echo $image['CFG']; ?></p>
                    <p>Steps: <?php echo $image['Steps']; ?></p>
                    <p>Width: <?php echo $image['Width']; ?></p>
                    <p>Height: <?php echo $image['Height']; ?></p>

                    <form action="delete.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="imageData" value="<?php echo $image['ImageData']; ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
               </div>
            </div>
        </div>
    </div>


</body>