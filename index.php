<?php
    require_once 'vendor/autoload.php';

    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

    /**Uploading image file */
    //$connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=kflowwebappstorage;AccountKey=gp7y9PREbgYfjZqF/Ddm3WR1vxZ0sLGLMu+ub7aIBa4qsQz92hbALvsQNuF1PJ2lMQbGSpgEAe3H7huEaAmUDw==;EndpointSuffix=core.windows.net";
    $containerName = "blockblobscomputervision";
    // Create blob client.
    $blobClient = BlobRestProxy::createBlobService($connectionString);

    if (isset($_POST['submit'])) {
        $fileToUpload = strtolower($_FILES["imageFile"]["name"]);
        $content = fopen($_FILES["imageFile"]["tmp_name"], "r");
        // echo fread($content, filesize($fileToUpload));
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        header("Location: index.php");
    }
    // List blobs (uploaded files)
    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);

    echo "These are the blobs present in the container: <br />";
    do{
        $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
        foreach ($result->getBlobs() as $blob)
        {
            echo $blob->getName().": ".$blob->getUrl()."<br />";
        }

        $listBlobsOptions->setContinuationToken($result->getContinuationToken());
    } while($result->getContinuationToken());
    echo "<br />";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Azure Computer Vision</title>
    <script type="javascript/text" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <!-- Showing image file to upload -->
    <script type="text/javascript">
    // $(document).ready(function () {
        // <!-- Analyzing image file -->
        alert("cobaaaa");
        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
        // Replace <Subscription Key> with your valid subscription key.
         var subscriptionKey = "adcc7abd04c341189aa26b49ed5e7001";
        //var subscriptionKey = "e919c3b04f734ae3bb2e844d3c1c4ea6";
        //var subscriptionKey = "5244d0b5afb846009d2d0c8f14313367";
        // You must use the same Azure region in your REST API method as you used to
        // get your subscription keys. For example, if you got your subscription keys
        // from the West US region, replace "westcentralus" in the URL
        // below with "westus".
        //
        // Free trial subscription keys are generated in the "westus" region.
        // If you use a free trial subscription key, you shouldn't need to change
        // this region.
         var uriBase = "https://kflowvision.cognitiveservices.azure.com/vision/v2.0/analyze";
        //var uriBase = "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
        // Display the image.
        var sourceImageUrl = "<?php echo $blob->getUrl() ?>";
        alert(sourceImageUrl);
        document.querySelector("#selectedImage").src = "<?php echo $blob->getUrl() ?>";
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
            // Show formatted JSON on webpage.
            //$("#responseTextArea").val(JSON.stringify(data, null, 2));
            // console.log(data);
            // var json = $.parseJSON(data);
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
    //  });

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
      <form action="index.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="imageFile" id="imgFile" accept="image/*" onchange="openFile(event)"><br />
        <input type="submit" name="submit" value="Upload and Analyze">
      </form>
      <div id="imagewrapper" style="width: 1280px; display: block; text-align: center;">
        <!-- <h4>Total Files : <?php //echo sizeof($result->getBlobs())?></h4><br /> -->
        <!-- <?php //echo $blob->getUrl()?><br /> -->
        
        <img id="selectedImage" name="img" width="500px"><br />
        <h2 id="description"></h2>
    </div>
  </div>
</body>
</html>