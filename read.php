91"}
<?php

$upload="upload/";

if(!file_exists($upload)){
mkdir($upload);
}

$tmp=$_FILES['ipa']['tmp_name'];

$target=$upload."app.ipa";

move_uploaded_file($tmp,$target);

$zip=new ZipArchive;

$extract=$upload."extract/";

if($zip->open($target)===TRUE){

$zip->extractTo($extract);

$zip->close();

}

$plistFile="";

$iterator=new RecursiveIteratorIterator(
new RecursiveDirectoryIterator($extract)
);

foreach($iterator as $file){

if(strpos($file,"Info.plist")!==false){

$plistFile=$file;

}

}

$plist=file_get_contents($plistFile);

function getValue($key,$plist){

preg_match('/<key>'.$key.'</key>[\s\S]?<string>(.?)</string>/',$plist,$match);

return $match[1] ?? "Unknown";

}

$appName=getValue("CFBundleName",$plist);
$bundle=getValue("CFBundleIdentifier",$plist);
$version=getValue("CFBundleShortVersionString",$plist);
$ios=getValue("MinimumOSVersion",$plist);

echo "<h2>IPA Information</h2>";

echo "App Name: ".$appName."<br><br>";

echo "Bundle ID: ".$bundle."<br><br>";

echo "Version: ".$version."<br><br>";

echo "Minimum iOS: ".$ios."<br><br>";
