<?php
$appInfo = null;

if(isset($_FILES['ipa'])){

    $tmp = $_FILES['ipa']['tmp_name'];

    $zip = new ZipArchive;

    if($zip->open($tmp) === TRUE){

        for($i=0;$i<$zip->numFiles;$i++){

            $name = $zip->getNameIndex($i);

            if(strpos($name,'Info.plist') !== false){

                $plist = $zip->getFromIndex($i);

                preg_match('/CFBundleName<\/key>\s*<string>(.*?)<\/string>/',$plist,$nameMatch);
                preg_match('/CFBundleIdentifier<\/key>\s*<string>(.*?)<\/string>/',$plist,$bundleMatch);
                preg_match('/CFBundleShortVersionString<\/key>\s*<string>(.*?)<\/string>/',$plist,$versionMatch);
                preg_match('/CFBundleVersion<\/key>\s*<string>(.*?)<\/string>/',$plist,$buildMatch);
                preg_match('/MinimumOSVersion<\/key>\s*<string>(.*?)<\/string>/',$plist,$iosMatch);

                $appInfo = [
                    "name"=>$nameMatch[1] ?? "Unknown",
                    "bundle"=>$bundleMatch[1] ?? "Unknown",
                    "version"=>$versionMatch[1] ?? "Unknown",
                    "build"=>$buildMatch[1] ?? "Unknown",
                    "ios"=>$iosMatch[1] ?? "Unknown"
                ];

                break;
            }
        }

        $zip->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>IPA Viewer</title>

<style>

body{
background:#111;
font-family:Arial;
color:white;
text-align:center;
padding:40px;
}

.box{
background:#222;
width:400px;
margin:auto;
padding:25px;
border-radius:10px;
}

input{
padding:10px;
margin:10px;
}

button{
padding:10px 20px;
background:#00aaff;
border:none;
color:white;
cursor:pointer;
}

.info{
margin-top:20px;
text-align:left;
}

</style>

</head>

<body>

<div class="box">

<h2>IPA Info Viewer</h2>

<form method="post" enctype="multipart/form-data">

<input type="file" name="ipa" required>

<br>

<button type="submit">Upload</button>

</form>

<?php if($appInfo){ ?>

<div class="info">

<h3>App Information</h3>

<p><b>Name:</b> <?php echo $appInfo["name"]; ?></p>
<p><b>Bundle ID:</b> <?php echo $appInfo["bundle"]; ?></p>
<p><b>Version:</b> <?php echo $appInfo["version"]; ?></p>
<p><b>Build:</b> <?php echo $appInfo["build"]; ?></p>
<p><b>Minimum iOS:</b> <?php echo $appInfo["ios"]; ?></p>

</div>

<?php } ?>

</div>

</body>
</html>
