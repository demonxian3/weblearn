<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>index</title>
	<link rel="stylesheet" href="uploadify.css">
	<script src='jquery.min.js'></script>
	<script src='jquery.uploadify.js'></script>
</head>
<body>
	<form action="">
		<p>上传文件:</p>
		<p>
			<input type="file" name="img" id='img'>
		</p>
	</form>
</body>
<script type="text/javascript">
	<?php $timestamp = time();?>
	$(function() {
		$('#img').uploadify({
			'formData'     : {
				'timestamp' : '<?php echo $timestamp;?>',
				'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
			},
			'swf'      : 'uploadify.swf',
			'uploader' : 'uploadify.php'
		});
	});
</script>
</html>