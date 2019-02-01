<meta charset="utf-8">
<style>body{background:#C7EDCC}</style>
<?php
if(isset($_GET['path'])){
    //解决windows默认编码gbk无法打开中文名文件
    $filename=iconv('utf-8','gb2312',$_GET['path']); 
	$content = file_get_contents("./".$filename);
	echo "<pre>";
	$content = htmlspecialchars($content,ENT_QUOTES,"UTF-8");
    echo $content;
	echo "</pre>";
}
?>
