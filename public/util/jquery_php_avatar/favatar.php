<?php

require_once('./upload.php');

$error_log = null;

global $thumbpath;
if (isset($_POST['si_submit'])) {
    $x1 = isset($_POST['si_x1']) ? $_POST['si_x1'] : 0;
    $y1 = isset($_POST['si_y1']) ? $_POST['si_y1'] : 0;
    $swidth = isset($_POST['si_width']) ? $_POST['si_width'] : 0;
    $sheight = isset($_POST['si_height']) ? $_POST['si_height'] : 0;
    $imagepath = isset($_POST['imagepath']) ? $_POST['imagepath'] : null;
    $thumbpath = Generatethumb($imagepath, $x1, $y1, $swidth, $sheight, 100, 100);
?>
<script type="text/javascript">
<!--
$(function() {
	Jsoncallback('/profile/index/saveavatar', 
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
         'profile_avatar': '<?php echo $thumbpath; ?>', 
         'profile_image': '<?php echo $imagepath; ?>' }, 
        'loading');
})

//-->
</script>

<?php 
}

if (!empty($error_log)) {
    echo $error_log;
} else {
?>

<script type="text/javascript" src="/util/jquery_php_avatar/scripts/avatar_container.js"></script>

<form action="./index.php?fselector=1" method="POST" enctype="multipart/form-data">
    <div id="dropzone_container">
        <?php if (isset($thumbpath) && !empty($thumbpath)) { ?>
		<input type="hidden" id="thumbpath" name="thumbpath" 
		    value="<?php echo (isset($thumbpath) ? $thumbpath : ''); ?>"/>
        <!-- has avatar already -->
        <div class="div_change_avatar">
            <div class="default_text">
                <div class="photo_icon"></div>
                <b>修改头像</b>
            </div>
        </div>
        <div class="change_btn" title="Reset">
            <img id="photo" style="max-height: 100px; max-width: 100px; border-radius: 10px;" 
                src="<?php echo (isset($thumbpath) ? $thumbpath : ''); ?>">
        </div>
        <?php } else { ?>
        <!-- has no avatar -->
        <div class="reset_btn" title="Reset"><b class="icon"></b></div>
        <div class="help_text">
            <div class="default_text">
                <div class="photo_icon"></div>
                <span>上传头像</span>
            </div>
        </div>
        <?php } ?>
        <div>
            <input type="file" id="imagefile" name="imagefile" accept="image/*" size="1"
                onchange="javascript:this.form.submit();" title="点击上传头像">
        </div>
    </div>
</form>

<?php } ?>

