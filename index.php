<?php

require_once 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

/**Uploading image file */
$connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
$containerName = "blockblobscomputervision";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

if (isset($_POST['submit'])) {
    $fileToUpload = strtolower($_FILES["imageFile"]["name"]);
	$content = fopen($_FILES["imageFile"]["tmp_name"], "r");
	// echo fread($content, filesize($fileToUpload));
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
    header("Location: azurecomputervision.php");
}
    // List blobs (uploaded files)
    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);

    //echo "These are the blobs present in the container: <br />";
    do{
        $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
        foreach ($result->getBlobs() as $blob)
        {
            //echo $blob->getName().": ".$blob->getUrl()."<br />";
        }

        $listBlobsOptions->setContinuationToken($result->getContinuationToken());
    } while($result->getContinuationToken());
    //echo "<br />";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Azure Computer Vision</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script type="text/javascript">
        var openFile = function(event) {
          var input = event.target;
      
          var reader = new FileReader();
          reader.onload = function(){
            var dataURL = reader.result;
            var output = document.getElementById('selectedImage');
            output.src = dataURL;
          };
          reader.readAsDataURL(input.files[0]);
          document.getElementById("description").innerHTML="";
        };
    </script>
</head>
<body>
  <div id="wrapper">
      <h1 align="center">Analisa Gambar dengan Azure Computer Vision</h1>
      <hr />
      <p>Pilih Gambar yang Akan Dianalisa</p>
      <form action="azurecomputervision.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="imageFile" id="imgFile" accept="image/*" onchange="openFile(event)"><br />
        <input type="submit" name="submit" value="Upload and Analyze">
      </form>
      <div id="imagewrapper" style="width: 1280px; display: block; text-align: center;">
        <!-- <h4>Total Files : <?php //echo sizeof($result->getBlobs())?></h4><br /> -->
        <!-- <?php //echo $blob->getUrl()?><br /> -->
        <img id="selectedImage" width="500"><br />
        <h2 name="analyzingResult" id="description"></h2>
    </div>
  </div>
</body>
</html>