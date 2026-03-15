55"}
<?php

$upload="upload/";

if(!file_exists($upload)){
mkdir($upload);
}

$ipa=$_FILES['ipa']['tmp_name'];

$name=$_POST['name'];
$bundle=$_POST['bundle'];
$version=$_POST['version'];

$target=$upload."app.ipa";

move_uploaded_file($ipa,$target);

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

$plist=preg_replace('/(<key>CFBundleName</key>.?<string>)(.?)(</string>)/s',"$1".$name."$3",$plist);

$plist=preg_replace('/(<key>CFBundleIdentifier</key>.?<string>)(.?)(</string>)/s',"$1".$bundle."$3",$plist);

$plist=preg_replace('/(<key>CFBundleShortVersionString</key>.?<string>)(.?)(</string>)/s',"$1".$version."$3",$plist);

file_put_contents($plistFile,$plist);

$newipa=$upload."edited.ipa";

$zip2=new ZipArchive;

$zip2->open($newipa,ZipArchive::CREATE);

$files=new RecursiveIteratorIterator(
new RecursiveDirectoryIterator($extract)
);

foreach($files as $name=>$file){

if(!$file->isDir()){

$filePath=$file->getRealPath();

$relativePath=substr($filePath,strlen($extract));

$zip2->addFile($filePath,$relativePath);

}

}

$zip2->close();

echo "<h2>IPA Edited</h2>";
echo "<a href='upload/edited.ipa'>Download IPA</a>";
