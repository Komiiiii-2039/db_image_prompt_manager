<!DOCTYPE html>
<html>
<head>
    <title>Add Image Form</title>
    <link rel="stylesheet" href="simple.css">
    <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>


    <style>
        body {
            background-color: #E6E6FA; /* Lavender color */
        }
        .frame {
            width: 80%;
            margin: 30px auto 20px;
            padding: 30px 20px 0;
            border: 3px solid #800080; /* Purple color */
            position: relative;
            border-radius: 3px;
            background-color: #fff;
        }
        .frame-title {
            position: absolute;
            top: -13px;
            left: 20px;
            padding: 0 5px;
            background-color: #fff;
        }
        .frame-title .fa {
            margin-right: 5px;
        }
        .caution {
            background-color: #ff4500;
            border: 2px solid #ff4500;
            color: #fff;
            border-radius: 3px;
        }
        .frame-red {
            border-color: #ff4500;
        }
   </style>

</head>
<body>
    <h1>Add Image Form</h1>
    <div class="frame frame-red">
    <div class="frame-title caution">
        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>CAUTION
    </div>
    <ul>
        <li>画像形式はjpeg，pngのみです．</li>
        <li>画像をアップロードすると各項目が自動的に追加されます．ただし，間違っている場合があるので必ず確認してください．</li>
        <li>追加後は更新できません．更新する場合は，削除してから追加してください．</li>
        <li>*は必須項目です．</li>
    </ul>
    </div>
    <form id = "imageForm" enctype="multipart/form-data">
        <label for="data">Image*:</label>
        <input type="file" name="data" id="data" required><br><br>
        
        <div class="form-group">
        <label for="positive_prompt">Positive Prompt:</label>
        <!-- <input type="text" name="positive_prompt" id="positive_prompt" required><br><br> -->
        <textarea name="PositivePrompt" id="positive_prompt" placeholder="input positive prompt"></textarea><br><br>
        </div>

        <label for="negative_prompt">Negative Prompt:</label>
        <!-- <input type="text" name="negative_prompt" id="negative_prompt" required><br><br> -->
        <textarea name="NegativePrompt" id="negative_prompt" placeholder="input negative prompt"></textarea><br><br>

        <label for="seed">Seed*:</label>
        <input type="number" name="seed" id="seed" required><br><br>
        
        <label for="cfg">CFG*:</label>
        <input type="number" name="cfg" id="cfg" required><br><br>
        
        <label for="steps">Steps*:</label>
        <input type="number" name="steps" id="steps" required><br><br>
        
        <label for="width">Width*:</label>
        <input type="number" name="width" id="width" required><br><br>
        
        <label for="height">Height*:</label>
        <input type="number" name="height" id="height" required><br><br>
        
        <label>Model*:
        <input type ="text" name="ModelName" id = "ModelName" list="ModelName" autocomplete="off" required>
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
                    echo '<option value='.$row['Name'].'>';
                }
            }
            ?>
        </datalist>
        </input><br><br>
        
        <label>VAE:
        <input type ="text" name="VAEName" id = "VAEName" list="VAEName" autocomplete="off" >
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
                    echo '<option value='.$row['Name'].'>';
                }
            }
            ?>
        </datalist>
        </input><br><br>
 
        <label>Sampler:
        <input type ="text" name="SamplerName" id = "SamplerName" list="SamplerName" autocomplete="off" required>
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
                    echo '<option value='.$row['Name'].'>';
                }
            }
            ?>
        </datalist>
        </input><br><br>
        
        <input type="submit" value="Add image"/>
    </form>
   </body>

<script>
    // Wait for the page to load
    window.onload = function() {
        // Get the file input element
        const fileInput = document.getElementById("data");

        // Add an event listener for when a file is selected
        fileInput.addEventListener("change", function(e) {
            // Get the selected file
            const file = e.target.files[0];
            console.log(file);

            // Ensure the file is an image
            if (file && file.type.match('image.*')) {
                // Create a new FileReader object
                const reader = new FileReader();

                // Use the exif-js library to read the Exif data
                EXIF.getData(file, function() {
                    // Get the Exif data
                    const allMetaData = EXIF.getAllTags(this);
                    let exifArray = allMetaData.UserComment; // Your exif data should be in the UserComment tag

                    // If exifArray is an array, convert it into a string
                    if (Array.isArray(exifArray)) {
                        let exifString = '';
                        for(let i = 0; i < exifArray.length; i += 1) {
                            if(exifArray[i] === 0) continue; // Skip null character
                            exifString += String.fromCharCode(exifArray[i]);
                        }
                        exifArray = exifString;
                    }

                    // Check if exifArray is defined and is a string
                    extractDataFromExif(exifArray);
                });
            }
        });
    };


    // Function to extract specific data from your Exif string and fill the form fields
    function extractDataFromExif(inputText) {
        // Extract the data from the Exif array
        let positivePromptMatch = inputText.match(/^UNICODE(.*?)\n/);
        let positivePrompt = positivePromptMatch ? positivePromptMatch[1].trim() : "";

        let negativePromptMatch = inputText.match(/Negative prompt: (.*?)\n/);
        let negativePrompt = negativePromptMatch ? negativePromptMatch[1].trim() : "";

        let stepsMatch = inputText.match(/Steps: (.*?),/);
        let steps = stepsMatch ? parseInt(stepsMatch[1]) : "";

        let samplerMatch = inputText.match(/Sampler: (.*?),/);
        let sampler = samplerMatch ? samplerMatch[1].trim() : "";

        let cfgScaleMatch = inputText.match(/CFG scale: (.*?),/);
        let cfgScale = cfgScaleMatch ? parseInt(cfgScaleMatch[1]) : "";

        let seedMatch = inputText.match(/Seed: (.*?),/);
        let seed = seedMatch ? parseInt(seedMatch[1]) : "";

        let vaeMatch = inputText.match(/VAE: (.*?),/);
        let vae = vaeMatch ? vaeMatch[1] : "";

        let modelMatch = inputText.match(/Model: (.*?)$/);
        let model = modelMatch ? modelMatch[1].trim() : "";

        let sizeMatch = inputText.match(/Size: (\d*)x(\d*),/);
        let height = sizeMatch ? sizeMatch[1].trim() : "";
        let width = sizeMatch ? sizeMatch[2].trim() : "";

        console.log({
            inputText,
            positivePrompt,
            negativePrompt,
            steps,
            sampler,
            cfgScale,
            seed,
            model,
            vae,
            width,
            height
        });

        // Populate the form fields with the extracted data
        document.getElementById('positive_prompt').value = positivePrompt;
        document.getElementById('negative_prompt').value = negativePrompt;
        document.getElementById('steps').value = steps;
        document.getElementById('cfg').value = cfgScale;
        document.getElementById('seed').value = seed;
        document.getElementById('SamplerName').value = sampler;
        document.getElementById('VAEName').value = vae;
        document.getElementById('ModelName').value = model;
        document.getElementById('height').value = height;
        document.getElementById('width').value = width;
    }

    $(document).ready(function() {
    $('#imageForm').on('submit', function(e) {
        e.preventDefault(); // prevent the form from 'submitting'

        var form = $('#imageForm')[0];
        var data = new FormData(form);

        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            url: 'add_image.php',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function(response) {
                // Display the status message
                alert(JSON.parse(response).message);
            },
            error: function(response) {
                // Handle error here
                alert('An error occurred while uploading the image');
            },
        });
    });
});
</script>
</html>
