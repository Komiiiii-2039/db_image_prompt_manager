<!DOCTYPE html>
<head lang=ja>
	<title>search image result</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

	<style>
		.frame {
			max-width: 90%;
			margin: 0 auto;
			border-radius: 10px;
			background-color: #FFFFFF;
			box-shadow: 0 0 30px rgba(0,0,0,0.1);
			padding: 20px 30px;
			color: #f0f0f0;
			margin-bottom: 50px;
		}

		.frame-title {
			font-weight: bold;
			color: #cc99ff;
			border-bottom: 1px solid #cc99ff;
			margin-bottom: 30px;
			padding-bottom: 10px;
		}

        body {
            background-color: #F5F5F5;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .row > .col-md-4 {
            margin-bottom: 20px;
        }

        .row > .col-md-4 > a > img {
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
            transition: all 0.3s ease-in-out;
        }

        .row > .col-md-4 > a > img:hover {
            transform: scale(1.05);
            box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.2);
        }

        a {
            color: #800080;  /* Purple */
        }

        a:hover {
            color: #9932CC; /* Darker purple */
        }
    </style>
</head>
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
		die("Connection failed: " . $mysqli->connect_error);
	} else {
		$mysqli->set_charset("utf8");
	}

	$condition = "";
	$ModelName="";
	$VAEName="";
	$SamplerName="";
	$PositivePrompt="";
	$NegativePrompt="";
	if(isset($_GET["ModelName"])){
		$ModelName = mysqli_escape_string($mysqli, trim($_GET["ModelName"]));
		if($ModelName !="") {
			$condition .= "model.Name = \"$ModelName\" ";
		}
	}
	if(isset($_GET["VAEName"])) {
		$VAEName = mysqli_escape_string($mysqli, trim($_GET["VAEName"]));
		if($VAEName != ""){
			if($condition != ""){
				$condition .= " AND VAE.Name = \"$VAEName\" ";
			} else {
				$condition .= "VAE.Name = \"$VAEName\" ";
			}
		}
	}
	if(isset($_GET["SamplerName"])) {
		$SamplerName = mysqli_escape_string($mysqli, trim($_GET["SamplerName"]));
		if($SamplerName != ""){
			if($condition != ""){
				$condition .= " AND sampler.Name = \"$SamplerName\" ";
			} else {
				$condition .= "sampler.Name = \"$SamplerName\" ";
			}
		}
	}
	if(isset($_GET["PositivePrompt"])){ 
    	$PositivePrompt = mysqli_escape_string($mysqli, trim($_GET["PositivePrompt"]));
		if($PositivePrompt !=""){
			//split by space and comma
			$PositivePromptWords = preg_split('/[\s,]+/', $PositivePrompt);
			$condition_pos = "";
			for($i = 0; $i < sizeof($PositivePromptWords); $i++){
				$condition_pos .= " AND positive_prompt.Text LIKE \"%".$PositivePromptWords[$i]."%\"";
			}
			if($condition != ""){
				$condition .= $condition_pos;
			} else {
				$condition .= preg_replace('/^ AND /', '', $condition_pos);
			}
		}
	}
	if(isset($_GET["NegativePrompt"])){
		$NegativePrompt = mysqli_escape_string($mysqli, trim($_GET["NegativePrompt"]));
		if($NegativePrompt !=""){
			$NegativePromptWords = preg_split('/[\s,]+/', $NegativePrompt);
			$condition_neg = "";
			for($i = 0; $i < sizeof($PositivePromptWords); $i++){
				$condition_neg .= " AND negative_prompt.Text LIKE \"%".$NegativePromptWords[$i]."%\"";
			}
			if($condition != ""){
				$condition .= $condition_neg;
			} else {
				$condition .= preg_replace('/^ AND /', '', $condition_neg);
			}
		}
	}

	//データを結合を繰り返しまくってGET!!!
	//image-generate と　generate-promptを結合を2回する
	//それぞれ別のariasをつけて，negativeとpositiveにする
	//negative の方はLEFT JOINしないとnegative promptIDがなかった場合に結果が出ない
	if($condition != ""){
		$condition = "WHERE ".$condition; //WHEREは$conditionが空の場合にはつけない
	}
	$sql = " 
	SELECT img.ImageID AS ImageID, img.Data AS ImageData, positive_prompt.Text AS PositivePrompt, negative_prompt.Text AS NegativePrompt, model.Name AS ModelName, VAE.Name AS VAEName, sampler.Name AS SamplerName
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
	$condition
	";

	$result = $mysqli->query($sql);

	if(!$result) {
    	die("Error executing query: (" . $mysqli->errno . ") " . $mysqli->error);
	}

?>
	 <div class="container">
	 <div class="frame">
	 <h2 class="frame-title">Result <?php echo "(" . $result->num_rows . ")"; ?></h2>
        <div class="row">
            <?php
            // Assume $rows is an array with the required data
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                ?>
                <div class="col-md-4">
                    <a href="details.php?id=<?php echo $row['ImageID']; ?>">
                        <img src="<?php echo "./img/".$row['ImageData']; ?>" class="img-fluid">
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
	</div>
	</div>
    </div>	

</body>
</html>
