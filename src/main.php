<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>search</title>
        <link rel="stylesheet" href="simple.css">

	</head>
	<body>
    <a class="add-image-button" href="add_image_form.php">Add New Image</a>
	<h1> Image Searcher </h1>

	<div style="text-align: center">
	<form action = "search_prompt.php" method = "get" enctype="multipart/form-data">
        <label for="positive_prompt">Positive Prompt:</label>
        <textarea name="PositivePrompt" id="positive_prompt" ></textarea><br><br>

        <label for="negative_prompt">Negative Prompt:</label>
        <textarea name="NegativePrompt" id="negative_prompt" ></textarea><br><br>

        <label>Model:
        <input type ="text" name="ModelName" list="ModelName" autocomplete="off" >
            </label>
            <datalist id="ModelName"> 
            <?php
            // Database connection
            $servername = "";
            $username = "";
            $password = "";
            $dbname = "";

            // Create a new mysqli instance
            $mysqli = new mysqli($servername, $username, $password, $dbname);

            // Check for connection errors
            if ($mysqli->connect_error) {
                die("MySQL connection error<br>");
            } else {
                $mysqli->set_charset("utf8");
            }

            // Retrieve existing model names from the model table
            $query = "SELECT ModelID, Name FROM model";
            $result = $mysqli->query($query);
            //Get all names exist in table
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    //send number only?
                    echo '<option value="'.$row['Name'].'">';
                }
            }
            ?>
        </datalist>
        </input><br><br>
        
        <label>VAE:
        <input type ="text" name="VAEName" list="VAEName" autocomplete="off" >
            </label>
            <datalist id="VAEName"> 
            <?php
            // Create a new mysqli instance
            $mysqli = new mysqli($servername, $username, $password, $dbname);

            // Check for connection errors
            if ($mysqli->connect_error) {
                die("MySQL connection error<br>");
            } else {
                $mysqli->set_charset("utf8");
            }

            // Retrieve existing model names from the model table
            $query = "SELECT vaeID, Name FROM VAE";
            $result = $mysqli->query($query);
            //Get all names exist in table
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    //send number only?
                    echo '<option value="'.$row['Name'].'">';
                }
            }
            ?>
        </datalist>
        </input><br><br>
 
        <label>Sampler:
        <input type ="text" name="SamplerName" list="SamplerName" autocomplete="off" >
            </label>
            <datalist id="SamplerName"> 
            <?php
            // Create a new mysqli instance
            $mysqli = new mysqli($servername, $username, $password, $dbname);

            // Check for connection errors
            if ($mysqli->connect_error) {
                die("MySQL connection error<br>");
            } else {
                $mysqli->set_charset("utf8");
            }

            // Retrieve existing model names from the model table
            $query = "SELECT SamplerID, Name FROM sampler";
            $result = $mysqli->query($query);
            //Get all names exist in table
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    //send number only?
                    echo '<option value="'.$row['Name'].'">';
                }
            }
            ?>
        </datalist>
        </input><br><br>
        
        <input type="submit" value="Search image" />
    </form>

	</div>


</body>
</html>
