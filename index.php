<?php
    require_once 'vendor/autoload.php';

    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

    //$connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=kflowwebappstorage;AccountKey=gp7y9PREbgYfjZqF/Ddm3WR1vxZ0sLGLMu+ub7aIBa4qsQz92hbALvsQNuF1PJ2lMQbGSpgEAe3H7huEaAmUDw==;EndpointSuffix=core.windows.net";
    $containerName = "blockblobscomputervision";
    // Create blob client.
    $blobClient = BlobRestProxy::createBlobService($connectionString);

    if (isset($_POST['submit'])) {
        /**Uploading image file */
        $fileToUpload = strtolower($_FILES["imageFile"]["name"]);
        $content = fopen($_FILES["imageFile"]["tmp_name"], "r");
        // echo fread($content, filesize($fileToUpload));
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        //header("Location: index.php");
        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setPrefix($fileToUpload);
        $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
        // List blobs (uploaded files)
        //echo "These are the blobs present in the container: <br />";
        do{
            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob)
            {
                //echo $blob->getName().": ".$blob->getUrl()."<br />";
            }
            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());
    } else {
        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setPrefix("");
        $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
        // List blobs (uploaded files)
        //echo "These are the blobs present in the container: <br />";
        do{
            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob)
            {
                //echo $blob->getName().": ".$blob->getUrl()."<br />";
            }
            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Azure Computer Vision</title>
    <script src="jquery.min.js"></script>
    <!-- Showing image file to upload -->
</head>
<body>
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

    // <!-- Analyzing image file -->
    $(document).ready(function() {
        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
        // Replace <Subscription Key> with your valid subscription key.
        var subscriptionKey = "adcc7abd04c341189aa26b49ed5e7001";
        // You must use the same Azure region in your REST API method as you used to
        // get your subscription keys. For example, if you got your subscription keys
        // from the West US region, replace "westcentralus" in the URL
        // below with "westus".
        //
        // Free trial subscription keys are generated in the "westus" region.
        // If you use a free trial subscription key, you shouldn't need to change
        // this region.
            var uriBase = "https://kflowvision.cognitiveservices.azure.com/vision/v2.0/analyze";
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
        // Display the image.
        var sourceImageUrl = "<?php echo $blob->getUrl() ?>";
        document.getElementById("selectedImage").src = sourceImageUrl;
        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key", subscriptionKey);
            },
            type: "POST",
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
        .done(function(data) {
            // Show image description from JSON data on webpage.
            $("#description").text(data.description.captions[0].text);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
            errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
            jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    });
</script>

<div id="wrapper">
    <h1 align="center">Analisa Gambar dengan Azure Computer Vision</h1>
    <hr />
    <p>Pilih Gambar yang Akan Dianalisa</p>
    <form action="index.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="imageFile" id="imgFile" accept="image/*" onchange="openFile(event)"><br />
    <input type="submit" name="submit" value="Upload and Analyze">
    </form>
    <div id="imagewrapper" style="width: 1280px; display: block; text-align: center;">
    <img id="selectedImage" width="500px" /><br />
    <h2 id="description">Analyzing image...</h2>
</div>
</div>
</body>
</html>