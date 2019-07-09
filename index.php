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