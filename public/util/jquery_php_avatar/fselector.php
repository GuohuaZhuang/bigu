<?php

require_once('./upload.php');

$error_log = null;

$thumbpath = '';
if (isset($_POST['thumbpath']) && !empty($_POST['thumbpath'])) {
	$thumbpath = $_POST['thumbpath'];
}
$imagepath = '';
if (isset($_FILES['imagefile'])) {
    $imagefile = $_FILES['imagefile'];
    $imagepath = Upload($imagefile);
} else {
    $error_log = 'Image file post null';
}

if (!empty($error_log)) {
    echo $error_log;
} else {
?>

<script type="text/javascript" src="/util/jquery_php_avatar/scripts/avatar.js"></script>
<script type="text/javascript">
<!--
window.onbeforeunload = function () {
	if ($('#issubmit').val() == '0') {
		if (window.confirm("正在修改头像，确认要离开吗？")) {
			Jsoncallback('/profile/index/removeupload', 
			    function (json) {
			    	if (null == json) return false;
			        if (json.err != undefined && json.err != "") {
			            alert(json.err);
			            return false;
			        }
			        if (json.success != undefined) {
			        }
		    	}, 
		        'POST', 
		        {'d': '', 'timestamp' : Date.parse(new Date()), 
		         'profile_image': '<?php echo $imagepath; ?>' }, 
		        'loading');
			return true;
		} else {
			// 维持这个页面
			return false;
		}
	}
	return true;
}
function BeforeSaveAvatar() {
	$('#issubmit').val('1');
	Jsoncallback('/profile/index/removethumb', 
	    function (json) {
	    	if (null == json) return false;
	        if (json.err != undefined && json.err != "") {
	            alert(json.err);
	            return false;
	        }
	        if (json.success != undefined) {
	        }
    	}, 
        'POST', 
        {'d': '', 'timestamp' : Date.parse(new Date()), 
         'profile_avatar': '<?php echo $thumbpath; ?>' }, 
        'loading');
}
//-->
</script>

<div style="overflow: hidden;">
  <div style="width: 300px;" class="div_avatar_container">
    <p>裁剪头像</p>
    <div style="width: 300px; height: 300px;">
      <img id="photo" style="max-height: 300px; max-width: 300px;" src="<?php echo $imagepath; ?>">
    </div>
  </div>
 
  <div style="width: 100px;" class="div_avatar_container">
    <p>头像预览</p>
    <div id="preview" style="width: 100px; height: 100px; overflow: hidden; margin: 0px">
      <img src="<?php echo $imagepath; ?>" style="width: 100px; height: 100px; margin: 0px">
    </div>
    
    <form action="./index.php?favatar=1&XDEBUG_SESSION_START=1" method="POST">
    <div class="div_avatar_container" style="width: 200px; margin: 5px 0px 0px 10px;">
    	<input type="hidden" id="issubmit" name="issubmit" value="0" />
        <input type="hidden" id="imagepath" name="imagepath" value="<?php echo $imagepath; ?>"/>
        <!-- <label for="si_x1">X1:</label> --><input type="hidden" id="si_x1" name="si_x1" />
        <!-- ,&nbsp;<label for="si_y1">Y1:</label> --><input type="hidden" id="si_y1" name="si_y1" />
        <!-- <br/><label for="si_width">W&nbsp;:</label> --><input type="hidden" id="si_width" name="si_width" />
        <!-- ,&nbsp;<label for="si_height">H<sub>&nbsp;&nbsp;</sub>:</label> --><input type="hidden" id="si_height" name="si_height" />
        <br/><input type="submit" onclick="return BeforeSaveAvatar();" name="si_submit" style="width: auto; margin-left: 0;" value="保存头像"/><br/>
    </div>
    </form>
    
  </div>
</div>

<?php } ?>

