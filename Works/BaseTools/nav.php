<meta charset="gb2312">
<link rel="stylesheet" href="bs/css/bootstrap.min.css">
<script src="bs/js/jquery.js"></script>
<script src="bs/js/bootstrap.min.js"></script>
<style> body, .darkBg{background: #2C3E50;}</style>
<?php

#error_reporting(0);
//创建文件按钮
function createFileBtn($fileName, $clickParm){
$button = <<<EOF
<button onclick='changeFrm("$clickParm")' 
    class='btn btn-block btn-success' 
    style='text-align:left; text-indent:1em'>
    <span class="glyphicon glyphicon-file" ></span> 
    $fileName
</button>
EOF;
echo $button;
}

//创建目录按钮
function createDirBtn($fileName, $clickParm){
$button= <<<EOF
<button 
    onclick='changeUrl("$_SERVER[PHP_SELF]?path=$clickParm")' 
    class='btn btn-info btn-block' 
    style='text-align:left; text-indent:1em'>
    <span class="glyphicon glyphicon-folder-open" ></span> &nbsp;
    $fileName
</button>
EOF;
echo $button;
}

//创建刷新按钮
function createRefreshBtn(){
$button= <<<EOF
<button 
    onclick='changeUrl("$_SERVER[REQUEST_URI]")' 
    class='btn btn-danger btn-block' >
    <span class="glyphicon glyphicon-refresh" ></span> 
    reload
</button>
EOF;
echo $button;
}

//创建切换按钮
function createToggleBtn($color, $frmId, $btnName,$icon){
$button= <<<EOF
<button 
    onclick='toggleFrm("$frmId")' 
    class='btn $color btn-block' >
    <span class="glyphicon glyphicon-$icon" ></span> 
    $btnName
</button>
EOF;
echo $button;
}


//创建上网表单
function createUrlInp(){
$urlForm= <<<EOF
</form>
<div class="input-group">
    <div class="input-group-addon">URL</div>
    <input class="form-control" type="" id="siteUrl">
    <div class="input-group-btn">
        <button class="btn btn-link" onclick="visitSite()">Go</button>
    </div>
</div>
EOF;
echo $urlForm;
}


//创建路径表单
function createPathInp($path){
$pathArr = explode("/", $path);
$lastIdx = count($pathArr) - 1;
$pathTracker = "";
echo '<ol class="breadcrumb bg-info" style="">';
foreach($pathArr as $k => $p){
    $pathTracker .= $p."/";
    if($k == $lastIdx) {
        echo "<li>$p</li>";
    }else{
        echo "<li><a href='$_SERVER[PHP_SELF]?path=$pathTracker'>$p</a></li>";
    }
}
echo '</ol>';
}


//MAIN: 如果没有path参数，默认读取data目录
if(isset($_GET['path'])){

    //简单探测是否目录遍历攻击
    $dirPath = $_GET['path'];
    if(preg_match("/(^[\.]{2})|(^\\/)|([\.]{2}\\/[\.]{2})/",$dirPath)){
        $dirPath = "data";
    };

    //去除路径最后的斜杆
    if(substr($dirPath, -1) == "/") $dirPath = substr($dirPath,0,-1);
    $fileArr = scandir($dirPath);

    //简单的目录锁，防止目录遍历
    if($fileArr[0] === ".") unset($fileArr[0]);
    if($dirPath === "data" || $dirPath === "data/")if($fileArr[1] === "..")unset($fileArr[1]);


    //生成折叠菜单
    echo '<div id="accordion" class="panel-group"  role="tablist" aria-multiselectable="true">';
    //生成窗口控制器
    echo '<div class="panel panel-primary">';
    echo '    <div class="panel-heading" id="heading2" >';
    echo '        <h4 class="panel-title">';
    echo '            <a';
    echo '                data-toggle="collapse"';
    echo '                data-parent="#accordion"';
    echo '                href="#winControl"';
    echo '            >Controller';
    echo '            </a>';
    echo '        </h4>';
    echo '    </div> <!-- heading -->';
    echo '    <div id="winControl" class="panel-collapse collapse darkBg">';

    //生成上网地址栏
    createUrlInp();
    //生成框架toggle按钮
    createRefreshBtn();
    createToggleBtn("btn-success", "viewParse", "Parse", "triangle-right");
    createToggleBtn("btn-warning", "viewSource","Code", "eye-open");
    createToggleBtn("btn-info", "viewSite","Site", "tint");

    echo '    </div>';
    echo '</div><!-- panel -->';

    //生成文件导航器
    echo '<div class="panel panel-primary">';
    echo '    <div class="panel-heading" id="heading1" role="tab">';
    echo '        <h4 class="panel-title">';
    echo '            <a';
    echo '                role="button"';
    echo '                data-toggle="collapse"';
    echo '                data-parent="#accordion"';
    echo '                href="#filenav"';
    echo '            >Navigation';
    echo '            </a>';
    echo '        </h4>';
    echo '    </div> <!-- heading -->';
    echo '    <div id="filenav" class="panel-collapse collapse in darkBg" >';

    //显示path路径
    createPathInp($dirPath);
    //枚举文件生成按钮
    foreach($fileArr as $file){
        if($file === ".") continue;
        $filepath = $dirPath ."/". $file;
        if(is_dir($filepath))
            createDirBtn($file, $filepath);
        else
            createFileBtn($file, $filepath);
    }

    echo '    </div>';
    echo '</div><!-- panel -->';
    echo '</div>';
}else{
    header('Location: nav.php?path=data');
    exit;
}

?>
<script>
    var curDomCount = 4;
    var rmDomArr = [];

	function changeFrm(src){
        if(parent.document.getElementById('viewParse') != null)
            parent.document.getElementById("viewParse").src = src;

        if(parent.document.getElementById('viewSource') != null)
            parent.document.getElementById("viewSource").src = "viewSource.php?path="+src;
	}

	function changeUrl(src){
		//遇到..删除路径直到上一层为止
		if(src.substr(-3) === "/.."){
			idx = src.lastIndexOf("/");
			src = src.substr(0,idx);
			idx = src.lastIndexOf("/");
			src = src.substr(0,idx);
			console.log(src.substr(0,idx));
		}
		
		console.log(src);
        document.location = src;
	}

    function visitSite(){
        var siteUrl = document.getElementById('siteUrl').value;
        if(siteUrl)
            parent.document.getElementById('viewSite').src = siteUrl;
    }

    function toggleFrm(id){
        var frm = parent.document.getElementById(id);
        var frmset = parent.document.getElementById("frmset");
        //如果框架已经消失则让其显示，否则消失
        if(frm == null){ 
            insertFrm(id);
        }else{
            frmset.removeChild(frm);
        }
        //更新frameset的cols值
        updateFrmsetCols(frmset);
    }

    //为了使得新加框架保持原先的位置，通过此函数处理
    function insertFrm(id){
        var frmset = parent.document.getElementById("frmset");
        var frm = document.createElement("frame");
        var viewParse = parent.document.getElementById('viewParse');
        var viewSource = parent.document.getElementById('viewSource');
        var viewSite = parent.document.getElementById('viewSite');

        frm.name = frm.id = id;

        switch(id){
            case 'viewParse':
                frm.src = "main.html";
                if(viewSource != null){
                    //如果Source页面有代码，那么显示代码对应的解析内容
                    src = viewSource.src.replace('http://<?php echo $_SERVER['HTTP_HOST']?>/base/viewSource.php?path=','');
                    frm.src = src;
                    frmset.insertBefore(frm, viewSource);

                }
                else if(viewSite != null)
                    frmset.insertBefore(frm, viewSite);
                else 
                    frmset.appendChild(frm);
                break;

            case 'viewSource':
                frm.src = "main.html";
                //如果Parse页面读取了内容，那么显示Parse页面的代码
                if(viewParse != null){
                    var src = viewParse.src
                    src = src.replace("http://<?php echo $_SERVER['HTTP_HOST']?>/base/","");
                    frm.src = "viewSource.php?path="+src;
                }
                    
                if(viewSite != null)
                    frmset.insertBefore(frm, viewSite);
                else 
                    frmset.appendChild(frm);
                break;

            case 'viewSite':
                frm.src = "http://www.w3school.com.cn/";
                    frmset.appendChild(frm);
                break;

            default:
                    frmset.appendChild(frm);
        }
    }

    //保持新加框架和就框架之间的显示比例
    function updateFrmsetCols(obj){
        var count = obj.childElementCount;
        switch(count){
            case 1: 
                obj.cols = "100%";
                break;
            case 2:
                obj.cols = "260,*";
                break;
            case 3:
                obj.cols = "260,40%,40%";
                break;
            case 4:
                obj.cols = "260,25%,25%,25%";
                break;
        }
    }
</script>

