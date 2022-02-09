<?
////////////////////////////////////////////////////////////////
// makeImgPpt.php
// made by momo (2022.2.9)
//              (2018.4.7), (2017.10.18), (2017.4.7), (2013.7.7)
////////////////////////////////////////////////////////////////

$debug = 0;
$version_info = "ver 0.5 by momo 22/02/09\n\n";

date_default_timezone_set('Asia/Seoul');
set_time_limit(1200); //20 min.

// include
include_once "SimpleImage.php";

echo "Usage : $argv[0] [outfile] [n]\n    outfile: filename  -- default=myppt\n   n: w, 1, 2, 3, 4 -- default=1\n\n";
echo $version_info;

if($argv[1]){ $outfile = $argv[1];
}else{ 	$outfile = "myppt"; }; //default outfile
if($argv[2]){ 	$type = $argv[2];
}else{ 	$type = 1; }; //default out type
$type = 1; // only type 1, yet

echo "outfile = $outfile\n";
echo "type = $type\n";

//initiate variables
//$A4Width=798;
//$A4Height=1170;

//$A4Width=1029;
//$A4Height=735;

$A4Width=840;
$A4Height=600;

$gDate="0000/00/00";
$files = array();
$noImgPerSlide = 1; // will be redefined according to type

//foldername, filename
$folder = $outfile . "_" . $type . ".files";
echo "folder : $folder\n";
$htmlfile = $outfile . "_" . $type . ".htm";

// read all the image files in current directory
if ($handle = opendir('.')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
			if(	preg_match("/.jpg/i",$file)
			  or preg_match("/.gif/i",$file)
			  or preg_match("/.png/i",$file)
			 ){
				$files[] = $file; 
			};
		};
	};
	closedir($handle); 
};

// sort by filename 
asort($files); 

switch($type){
//	case 'w':
//		$width = 1040;
//		$width2 = $width;
//		$noImgPerSlide=1;
//		break;
	case 1: 
		// landscape
		$width = 1029;
		$noImgPerSlide=1; //image per page
		
		// portrait
		$width2 = ceil($width / 1.4);
		$noImgPerSlide2=1;
		
		break;
//	case 2:
//		$width = 720;
//		$width2 = $width;
//		$noImgPerSlide=2;
//		break;
//	case 3:
//		$width = 480;
//		$width2 = $width;
//		$noImgPerSlide=3;
//		break;
//	case 4:
//		$width = 240;
//		$width2 = $width;
//		$noImgPerSlide=4;
//		break;
	default:
		$width = 1029;
		$noImgPerSlide=1;
		
		$width2 = ceil($width / 1.4);
		$noImgPerSlide2=1;
		break;
};

if (!file_exists($folder)) mkdir($folder);

$noPage=1;
$ptPic=0;
$pic = array();

foreach($files as $file){
	list($oriImageWidth, $oriImageHeight) = getimagesize($file);
	if($oriImageWidth >= $oriImageHeight) {
		$oriPosition="Garo";
	}else{
		$oriPosition="Sero";
	};
	
	$exif = read_exif_data($file);
	echo "$file ";
	
	$newfile = "./$folder/" . $file;
	
	if (!array_key_exists('Orientation', $exif)) {
		$exif['Orientation'] = 1;
	}

	if($debug) echo "$oriPosition..." . $exif['Orientation'] . "... \n";
	if($oriPosition == "Garo" and ($exif['Orientation'] !=6 and $exif['Orientation'] !=8)){ 
		makeImageResize($file, $newfile, $oriImageWidth, $width);};
	if($oriPosition == "Garo" and ($exif['Orientation'] ==6 or $exif['Orientation'] ==8)){
		makeImageResize($file, $newfile, $oriImageHeight, $width);};
	if($oriPosition == "Sero" and ($exif['Orientation'] !=6 and $exif['Orientation'] !=8)){
		makeImageResize($file, $newfile, $oriImageWidth, $width);};
	if($oriPosition == "Sero" and ($exif['Orientation'] ==6 or $exif['Orientation'] ==8)){
		makeImageResize($file, $newfile, $oriImageHeight, $width);};

	if($oriPosition == "Garo"){
		if($debug) echo "가로다\n";
		if($debug) echo $exif['Orientation']."\n";
		switch($exif['Orientation']){
			case 6:
				$ptPic =0;
				DeletePicArray($pic);
				echo "   -- rotate cw 90";
				$source = @imagecreatefromjpeg($newfile);
				$rotate = imagerotate($source, -90.0, 0);
				imagejpeg($rotate, $newfile);
				
				$ptPic=$noImgPerSlide-1;
				break;
			case 8:
				$ptPic =0;
				DeletePicArray($pic);
				echo "   -- rotate ccw 90";
				$source = @imagecreatefromjpeg($newfile);
				$rotate = imagerotate($source, 90.0, 0);
				imagejpeg($rotate, $newfile);
				$ptPic=$noImgPerSlide-1;
				break;
		};
	}else{
		if($debug) echo "세로다\n";
		$ptPic =0;
		DeletePicArray($pic);
		$ptPic=$noImgPerSlide-1;
	}
	echo "\n";
	
	if($debug) echo "InsertPicArray(\$pic, $ptPic, $file, $exif[DateTime], $exif[Orientation])\n";
	InsertPicArray($pic, $ptPic, $file, $exif['DateTime'], $exif['Orientation']);
	
	if($debug) echo "pic array\n";
	if($debug) print_r($pic);
	$ptPic++;
	if($ptPic >= $noImgPerSlide){
		makeSlidefile($folder, $pic);
		$ptPic =0;
		DeletePicArray($pic);
	};
};

makeSlidefile($folder, $pic);
$ptPic =0;
DeletePicArray($pic);

makeMainHtmlfile($htmlfile, $folder);
makeFileListfile($htmlfile, $folder);
makePresfile($folder, $noPage);
exit;

/////// functions /////////////////////////////////////////////////////
//------------------------------------------------------------------
function makeImageResize($file, $newfile, $imgBase, $spaceBase){
		$image = new SimpleImage();
		$image->load($file);
		if($imgBase > $spaceBase){
			$image->resizeToWidth($spaceBase);
		}else{
			echo "  -- do not resize";
		}
		$image->save($newfile);
}

//------------------------------------------------------------------
function makeMainHtmlfile($htmlfile, $folder){
	$fp = fopen($htmlfile, 'w');
	$contents = "<html xmlns:v='urn:schemas-microsoft-com:vml'
xmlns:o='urn:schemas-microsoft-com:office:office'
xmlns:p='urn:schemas-microsoft-com:office:powerpoint'
xmlns:oa='urn:schemas-microsoft-com:office:activation'
xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta http-equiv=Content-Type content='text/html; charset=ks_c_5601-1987'>
<meta name=Generator content='Microsoft PowerPoint 12'>
<link rel=File-List href='$folder/filelist.xml'>
<link rel=Presentation-XML href='$folder/pres.xml'>
</head>
<body></body>
</html>";
	fwrite($fp, $contents);
	fclose($fp);
};

//------------------------------------------------------------------
function makeFileListfile($htmlfile, $folder){
	$fp = fopen("$folder/filelist.xml", 'w');
	$contents = "<xml xmlns:o='urn:schemas-microsoft-com:office:office'>
 <o:File HRef='pres.xml'/>
 <o:File HRef='filelist.xml'/>
 <o:MainFile HRef='../$htmlfile'/>
</xml>";
	fwrite($fp, $contents);
	fclose($fp);
};

//------------------------------------------------------------------
function makePresfile($folder, $noPage){
	$fp = fopen("$folder/pres.xml", 'w');
	$contents = "<xml xmlns:v='urn:schemas-microsoft-com:vml'
xmlns:o='urn:schemas-microsoft-com:office:office'
xmlns:p='urn:schemas-microsoft-com:office:powerpoint'
xmlns:oa='urn:schemas-microsoft-com:office:activation'>
<p:presentation sizeof='custom' slidesizex='6350' slidesizey='4536'
   gridspacingx='46448' gridspacingy='46448' subsetembed='1'>\n";
   
	for($i=1; $i<$noPage; $i++){
		$contents .= sprintf("  <p:slide id=$i slidesn='1CE8078,1F3%04d' href='slide%04d.htm'/>\n", $i,$i);
	};
	$contents .= "</p:presentation>\n</xml>\n";
	fwrite($fp, $contents);
	fclose($fp);
};

//------------------------------------------------------------------
function makeSlidefile($folder, $img){
	global $noPage;
	global $noImgPerSlide;
	global $A4Width;
	global $A4Height;
	global $gDate;
	
	$debug  = 0;
	if($debug) echo "\n\n\n\nstart of makeSlideFile : $folder, \$img\n";
	if($debug) print_r($img);
	if($debug) echo "count(img) : " . count($img) . "\n";
	
	$no = 0;
	foreach($img as $i){
		if($i['filename'] != "") $no++; 
	};
	
	if($debug) echo "no : $no\n";
	if ($no == 0) {
		if($debug) echo "no가 0이라서 그냥 리턴\n";
		if($debug) echo "end of makeSlideFile 2\n";
		return;
	}
	$filename = sprintf("slide%04d.htm",$noPage);
	
	if($debug) echo "이제 파일만듦 : $filename\n";
	$fp = fopen("$folder/$filename", 'w');
	$contents = "<html xmlns:v='urn:schemas-microsoft-com:vml'>\n<body>\n";
	
	$n=0;
	foreach($img as $pic){
		if($pic['filename'] == "") continue;
		if($debug) echo "파일명이 있을 때만 실행 : $pic[filename]\n";
	
		$size=getimagesize("$folder/".$pic['filename']);
		$pWidth = $size[0];
		$pHeight = $size[1];
		$aspect = $pWidth/$pHeight; // width / height ==> 1.4 (5X7)

		$ns = 1; // 무조건 한페이지에 하나의 이미지를 넣음
		if($aspect > 1.4){
			// 가로의 길이가 페이지비율보다 더 길다.
			$widthRatio = 1;
			$heightRatio = 1.4 / $aspect;
			$tposition= (1-$heightRatio)/2;
			$lposition= 0;
		}else{
			// 세로의 길이가 페이지비율보다 더 길다.
			$widthRatio = $aspect / 1.4;
			$heightRatio = 1;
			$tposition= 0;
			$lposition= (1-$widthRatio)/2;
		};
		
		$pWidth = ceil($widthRatio * $A4Width);
		$pHeight = ceil($heightRatio * $A4Height);
		$tposition = ceil($tposition * $A4Height);
		$lposition = ceil($lposition * $A4Width);
		
		$contents .= "<v:shape type='#_x0000_t75' style='position:absolute;left:$lposition;top:$tposition;width:$pWidth;height:$pHeight' o:preferrelative='f' filled='t' stroked='f'>
	<v:imagedata src='$pic[filename]' o:title='$pic[filename]'/>
</v:shape>\n\n";
		
		if($pic['picdate'] != $gDate){ // 이전 날짜와 다를 때만 날짜를 적는다.
		$contents .= "<v:shape id='txt0$n' style='position:absolute;left:$lposition;top:$tposition;width:400pt;height:64pt'>
</v:shape>
<div v:shape='txt0$n' style='text-align:left;position:absolute;left:$lposition;top:$tposition;width:400pt;height:64pt'>
	<span lang=KO style='font-size:20pt;font-family:arial;color:black;'>$pic[picdate]</span>
</div>\n\n";
		};
		$gDate = $pic['picdate'];
		$n++;
	};
	$contents .= "\n</body>\n</html>\n";
	$noPage++;

	fwrite($fp, $contents);
	fclose($fp);
	if($debug) echo "end of makeSlideFile\n";
};

//------------------------------------------------------------------
function InsertPicArray(&$pic, $no, $filename, $datetime, $Orientation){
	$pic[$no]['filename']=$filename;
	$pic[$no]['picdate']= date("Y/m/d", filemtime($filename));
	if(!$Orientation) $Orientation = 1;
	$pic[$no]['orientation']=$Orientation;
}

//------------------------------------------------------------------
function DeletePicArray(&$pic){
	for($i=0; $i< count($pic); $i++){
		$pic[$i]['filename'] = "";
		$pic[$i]['picdate'] = "";
		$pic[$i]['orientation'] = "";
	};
}

?>