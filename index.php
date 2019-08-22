<?php


require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=submit2;AccountKey=hihMS1X+II2o+6WJIZ8BTrEqSWO5uWcnUYK98eCnj9DTQW4gUyWDGRsu2zjqWpClcPlYF+F2fgo/PPMv4/KRqQ==;EndpointSuffix=core.windows.net";
$containerName = "blobb";

$blobClient = BlobRestProxy::createBlobService($connectionString);

$cekUpload = "";


if (isset($_POST['submit'])) {
    $fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
    $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
    $cekUpload = "sukses";
    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
    header("Location: index.php");
}

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>


<html>

<head>

</head>

<body>
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
        <input type="submit" name="submit" value="Upload Gambar">
    </form>

    <br>
    <?php
    if (isset($cekUpload)) {
        echo $cekUpload;
        do {
            foreach ($result->getBlobs() as $hasil) {
                echo "Upload Berhasil <br>";

                ?>

                <?php echo $hasil->getName() . "<br>" ?>
                <?php echo $hasil->getUrl() . "<br>" ?>

                <form action="azureCognitive.php" method="post">
                    <input type="hidden" name="url" value="<?php echo $hasil->getUrl() ?>">
                    <input type="submit" name="submit" value="Analisa">
                </form>

            <?php
        }
        $listBlobsOptions->setContinuationToken($result->getContinuationToken());
    } while ($result->getContinuationToken());
}
?>

<br>
<br>
<h2><a href="index.php">KEMBALI KE HALAMAN UTAMA</a></h2>

</body>

</html>
