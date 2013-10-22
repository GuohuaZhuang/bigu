<!DOCTYPE>
<html>
<head>
	<meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/util/jquery_php_avatar/css/imgareaselect-default.css" />
    <link rel="stylesheet" type="text/css" href="/util/jquery_php_avatar/css/avatar.css" />
    <script type="text/javascript" src="/util/jquery_php_avatar/scripts/jquery.min.js"></script>
    <script type="text/javascript" src="/util/jquery_php_avatar/scripts/jquery.imgareaselect.pack.js"></script>
    <script type="text/javascript" src="/js/util.js"></script>
</head>

<body>


<?php
	global $thumbpath;
    if (isset($_GET['fselector']) && !empty($_GET['fselector'])) {
        include('./fselector.php');
    } else {
    	if (isset($_GET['thumbpath'])) {
			$thumbpath = isset($_GET['thumbpath']) ? $_GET['thumbpath'] : null;
    	}
        include('./favatar.php');
    }
?>

</body>
</html>
