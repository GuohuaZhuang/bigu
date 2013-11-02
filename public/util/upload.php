<?php
/*
CKEditor_upload.php
monkee
2009-11-15 16:47
*/
$config=array();
$config['type']=array("flash","img"); //上传允许type值
$config['img']=array("jpg","bmp","gif","png"); //img允许后缀
$config['flash']=array("flv","swf"); //flash允许后缀
$config['flash_size']=200; //上传flash大小上限 单位：KB
$config['img_size']=500; //上传img大小上限 单位：KB
$config['message']="上传成功"; //上传成功后显示的消息，若为空则不显示
$config['name']=md5(microtime().rand(0, 100));//mktime(); //上传后的文件命名规则 这里以unix时间戳来命名
$today = date("Ymd");
$config['flash_dir']="/upload/flash/".$today; //上传flash文件地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/"
$config['img_dir']="/upload/img/".$today; //上传img文件地址 采用绝对地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/"
$config['thumb_dir']="/upload/thumb/".$today; //上传img对应thumb缩略图的文件地址 格式与img同
$config['site_url']=""; //网站的网址 这与图片上传后的地址有关 最后不加"/" 可留空
//文件上传
uploadfile();
function uploadfile()
{
    global $config;
    // 判断目录及创建目录
    if (!file_exists($_SERVER['DOCUMENT_ROOT'].$config['flash_dir']))
    {
        mkdir($_SERVER['DOCUMENT_ROOT'].$config['flash_dir'], 0777);
    }
    if (!file_exists($_SERVER['DOCUMENT_ROOT'].$config['img_dir']))
    {
        mkdir($_SERVER['DOCUMENT_ROOT'].$config['img_dir'], 0777);
    }
    
    global $config;
    //判断是否是非法调用
    if(empty($_GET['CKEditorFuncNum']))
        mkhtml(1,"","错误的功能调用请求");
    $fn=$_GET['CKEditorFuncNum'];
    if(!in_array($_GET['type'],$config['type']))
        mkhtml(1,"","错误的文件调用请求");
    $type=$_GET['type'];
    if(is_uploaded_file($_FILES['upload']['tmp_name']))
    {
        //判断上传文件是否允许
        $filearr=pathinfo($_FILES['upload']['name']);
        $filetype=$filearr["extension"];
        if(!in_array($filetype,$config[$type]))
        mkhtml($fn,"","错误的文件类型！");
        //判断文件大小是否符合要求
        if($_FILES['upload']['size']>$config[$type."_size"]*1024)
            mkhtml($fn,"","上传的文件不能超过".$config[$type."_size"]."KB！");
        //$filearr=explode(".",$_FILES['upload']['name']);
        //$filetype=$filearr[count($filearr)-1];
        $filename = $config['name'].".".$filetype;
        $file_abso=$config[$type."_dir"]."/".$config['name'].".".$filetype;
        $file_host=$_SERVER['DOCUMENT_ROOT'].$file_abso;
        if(move_uploaded_file($_FILES['upload']['tmp_name'],$file_host))
        {
            // 如果是图像则生成缩略图作为列表显示图
            // if ($type == 'img') generatethumb($filename, $filetype, 
            // 		$file_host, $fn);
        	mkhtml($fn,$config['site_url'].$file_abso,$config['message']);
        }
        else
       {
            mkhtml($fn,"","文件上传失败，请检查上传目录设置和目录读写权限");
        }
    }
}

function generatethumb($filename, $filetype, $filetmp, $fn)
{
	global $config;
	$thumb_path = $_SERVER['DOCUMENT_ROOT'].$config['thumb_dir'];
	if (!file_exists($thumb_path))
	{
		mkdir($thumb_path, 0777);
	}
	// Save thumb image file to thumb_path
	if (!is_writeable($thumb_path)) {
		mkhtml($fn,"","生成缩略图失败，请检查缩略图目录设置和目录读写权限");
		return false;
	} else {
		// 缩略图大小为210px*140px
		$tw = 210;
		$th = 140;
		list($w, $h) = getimagesize($filetmp);
		// 计算imagecopyresampled压缩参数值
		$wrate = $w / $tw;
		$hrate = $h / $th;
		if ($wrate > $hrate) {
			$minh = $h;
			$minw = $tw * $hrate;
		} else {
			$minh = $th * $wrate;
			$minw = $w;
		}
		$sx = ($w - $minw) / 2;
		$sy = ($h - $minh) / 2;
		
		// Load
		if ($filetype == 'jpg') $filetype = 'jpeg';
    	$thumb = imagecreatetruecolor($tw, $th);
    	$white = imagecolorallocate($thumb, 255, 255, 255);
    	imagefill($thumb, 0, 0, $white);
		$f_create = 'imagecreatefrom' . $filetype;
		$source = $f_create($filetmp);
		// Resize
		imagecopyresampled($thumb, $source, 0, 0, $sx, $sy, $tw, $th,
				$minw, $minh);
		// Output
		$f_save = 'image' . $filetype;
		$f_save($thumb, $thumb_path.'/'.$filename);
		imagedestroy($thumb);
		imagedestroy($source);
	}
}

//输出js调用
function mkhtml($fn,$fileurl,$message)
{
    $str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('
    	.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>';
    exit($str);
}
?>
