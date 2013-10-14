<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Sample &mdash; CKEditor</title>
	<link rel="stylesheet" href="sample.css">
</head>
<body>
	<h1 class="samples">
		CKEditor &mdash; Posted Data
	</h1>
	<table border="1" cellspacing="0" id="outputSample">
		<colgroup><col width="120"></colgroup>
		<thead>
			<tr>
				<th>Field&nbsp;Name</th>
				<th>Value</th>
				<td>真实文章</th>
			</tr>
		</thead>
<?php

function unhtmlspecialchars( $string )
{
  $string = str_replace ( '&amp;', '&', $string );
  $string = str_replace ( '&#039;', '\'', $string );
  $string = str_replace ( '&quot;', '"', $string );
  $string = str_replace ( '&lt;', '<', $string );
  $string = str_replace ( '&gt;', '>', $string );
  $string = str_replace ( '&uuml;', '', $string );
  $string = str_replace ( '&Uuml;', '', $string );
  $string = str_replace ( '&auml;', '', $string );
  $string = str_replace ( '&Auml;', '', $string );
  $string = str_replace ( '&ouml;', '', $string );
  $string = str_replace ( '&Ouml;', '', $string );    
  return $string;
}

if (!empty($_POST))
{
	foreach ( $_POST as $key => $value )
	{
		if ( ( !is_string($value) && !is_numeric($value) ) || !is_string($key) )
			continue;

		if ( get_magic_quotes_gpc() )
			$value = htmlspecialchars( stripslashes((string)$value) );
		else
			$value = htmlspecialchars( (string)$value );
?>
		<tr>
			<th style="vertical-align: top"><?php echo htmlspecialchars( (string)$key ); ?></th>
			<td><pre class="samples"><?php echo $value; ?></pre></td>
			<td><div><?php echo unhtmlspecialchars($value); ?></div></td>
		</tr>
	<?php
	}
}
?>
	</table>
</body>
</html>
