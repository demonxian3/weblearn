<meta charset="gb2312">
<link rel="stylesheet" href="bs/css/bootstrap.min.css">
<script src="bs/js/jquery.js"></script>
<script src="bs/js/bootstrap.min.js"></script>
<style> body, .darkBg{background: #2C3E50;}</style>
<?php


#error_reporting(0);
//创建样式按钮
function createStyleBtn($fileName){
$fileName = str_replace(".css","", $fileName);
$shortName = str_replace("-","", $fileName);
$shortName = str_replace("atelier","ate", $shortName);
$shortName = str_replace("lakeside","lks", $shortName);
$shortName = str_replace("plateau","pla", $shortName);
$button = <<<EOF
<button onclick='changeStyle("$fileName")'
    class='btn btn-block '
    style='text-align:left; text-indent:1em; background:#ccc;color:#337ab7'>
    <span class="glyphicon glyphicon-ok"></span>
    $shortName
</button>
EOF;
echo $button;
}


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

//创建面板头部
function createPanelHead($name, $href, $parent){
$panelHead = <<<EOF
<div class="panel-heading">
    <h4 class="panel-title">
        <a data-toggle="collapse"
            data-parent="#$parent"
            href="#$href"
        >$name
        </a>
    </h4>
</div> <!-- heading -->
EOF;
echo $panelHead;
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


    #生成折叠
    echo '<div id="accordion" class="panel-group"  role="tablist" aria-multiselectable="true">';
        
        ## 窗口控制器面板
        echo '<div class="panel panel-primary">';
            ### 面板头
            createPanelHead("Controller", "winControl", "accordion");
            ### 面板体
            echo '    <div id="winControl" class="panel-collapse collapse darkBg">';
                #### 生成上网地址栏
                createUrlInp();
                #### 生成框架toggle按钮
                createRefreshBtn();
                createToggleBtn("btn-success", "viewParse", "Parse", "triangle-right");
                createToggleBtn("btn-warning", "viewSource","Code", "eye-open");
                createToggleBtn("btn-info", "viewSite","Site", "tint");
            ### END面板体
            echo '    </div>';
        ## END面板
        echo '</div><!-- panel -->';


    
        ## 代码样式切换器面板
        echo '<div class="panel panel-primary">';
            ### 面板头
            createPanelHead("StyleSwitcher", "stylenav", "accordion");
            ### 面板体
            echo '    <div id="stylenav" class="panel-collapse collapse darkBg" >';
            $styleFileArr = scandir("./plugin/styles");
            foreach($styleFileArr as $file){
                if($file == "." || $file == "..") continue;
                createStyleBtn($file);
            }
            ### END面板体
            echo '    </div>';
        ## END面板
        echo '</div><!-- panel -->';


        ##文件导航器面板
        echo '<div class="panel panel-primary">';
            ### 面板头
            createPanelHead("Navigation", "filenav", "accordion");
            ### 面板体
            echo '    <div id="filenav" class="panel-collapse collapse in darkBg" >';
                #### 显示path路径
                createPathInp($dirPath);
                #### 枚举文件生成按钮
                foreach($fileArr as $file){
                    if($file === ".") continue;
                    $filepath = $dirPath ."/". $file;
                    if(is_dir($filepath))
                        createDirBtn($file, $filepath);
                    else
                        createFileBtn($file, $filepath);
                }
            ### END面板体
            echo '</div>';
        ## END面板
        echo '</div><!-- panel -->';
    # END折叠
    echo '</div><!-- tablist -->';

}else{
    header('Location: nav.php?path=data');
    exit;
}

?>
<script>
    var curDomCount = 4;
    var rmDomArr = [];
    var curPathName = "README.md";
    var curStyleName = "atelier-heath-light";

	function changeFrm(src){
        if(parent.document.getElementById('viewParse') != null)
            parent.document.getElementById("viewParse").src = src;

        if(parent.document.getElementById('viewSource') != null){
            curPathName = src;
            viewSourceFun(curPathName, curStyleName);
        }
	}

    function viewSourceFun(path, style){
        var url = "viewSource.php?path=" + path + "&style=" + style;
        parent.document.getElementById('viewSource').src = url;
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

    function changeStyle(filename){
        if(parent.document.getElementById('viewSource') != null){
            curStyleName = filename;
            viewSourceFun(curPathName, curStyleName);
        }
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
                    console.log(src);
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

