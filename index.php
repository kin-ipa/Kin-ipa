<?php

$result = "";
$error = "";

if(isset($_FILES['ipa'])){

$uploadDir = "uploads/";
$extractDir = "extract/";

if(!file_exists($uploadDir)){
mkdir($uploadDir,0777,true);
}

if(!file_exists($extractDir)){
mkdir($extractDir,0777,true);
}

$fileName = basename($_FILES["ipa"]["name"]);
$tmpName = $_FILES["ipa"]["tmp_name"];
$filePath = $uploadDir.$fileName;

if(move_uploaded_file($tmpName,$filePath)){

$zip = new ZipArchive();

if($zip->open($filePath) === TRUE){

$folder = $extractDir.uniqid();
mkdir($folder,0777,true);

$zip->extractTo($folder);
$zip->close();

$plistFile = "";

$it = new RecursiveIteratorIterator(
new RecursiveDirectoryIterator($folder)
);

foreach($it as $file){

if(strpos($file,"Info.plist") !== false){
$plistFile = $file;
break;
}

}

if($plistFile != ""){

$content = file_get_contents($plistFile);

function getPlistValue($key,$data){

preg_match('/<key>'.$key.'<\/key>\s*<string>(.*?)<\/string>/',$data,$match);

return $match[1] ?? "غير موجود";

}

$appName = getPlistValue("CFBundleName",$content);
$bundleId = getPlistValue("CFBundleIdentifier",$content);
$version = getPlistValue("CFBundleShortVersionString",$content);
$build = getPlistValue("CFBundleVersion",$content);

$result = "
اسم التطبيق: $appName <br>
Bundle ID: $bundleId <br>
الإصدار: $version <br>
Build: $build
";

}else{

$error = "لم يتم العثور على Info.plist داخل IPA";

}

}else{

$error = "فشل فك ضغط ملف IPA";

}

}else{

$error = "فشل رفع الملف";

}

}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>IPA Analyzer</title>

<style>

body{
background:#0e0e0e;
color:white;
font-family:Arial;
text-align:center;
padding:40px;
}

.container{
background:#1c1c1c;
padding:30px;
border-radius:12px;
width:450px;
margin:auto;
}

input{
margin:15px;
}

button{
padding:10px 25px;
background:#00c8ff;
border:none;
color:white;
border-radius:6px;
cursor:pointer;
}

.result{
margin-top:20px;
background:#111;
padding:15px;
border-radius:8px;
}

.error{
margin-top:20px;
background:#400;
padding:15px;
border-radius:8px;
}

</style>

</head>

<body>

<div class="container">

<h2>تحليل ملف IPA</h2>

<form method="post" enctype="multipart/form-data">

<input type="file" name="ipa" accept=".ipa" required>

<br>

<button type="submit">رفع وتحليل</button>

</form>

<?php

if($result){
echo "<div class='result'>$result</div>";
}

if($error){
echo "<div class='error'>$error</div>";
}

?>

</div>

</body>
</html>
