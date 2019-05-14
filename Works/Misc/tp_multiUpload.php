<!-- 上传组件 -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                上传图片
            </div>
        </div>
        
        <div class="panel-body">
              <div class="form-group">
                <label for="image_name">图片名称</label>
                <input class="form-control" name="image_name" id="image_name" placeholder="注意批量上传多个图片名按顺序使用逗号分隔，不要加空格" required="required">
              </div>
              <div class="form-group">
                <label for="image_file" >批量上传</label>
                <input type="file" name="image_file[]" multiple id="image_file" onchange="showImages()" accept="image/*" style="display:none;">
                <div class="well" id="imgDocker" style="min-height:200px;color:#ccc">支持把图片拖拽进来，选中后可以双击删除不想上传图片，或者重新选择</div>
                <input type="button" value="选择图片" class="btn btn-block btn-success" onclick="selectImg()">
              </div>
               <input type="button" value="提交" id="jQsubmit" class="btn btn-primary btn-block">  
        </div>
    </div>


<script>
    let no_upload_list = [];            //保存不上传的图片
    dropbox = $("#imgDocker")[0];       //拖拽功能
    dropbox.addEventListener("dragenter", clearDefault , false);
    dropbox.addEventListener("dragover", clearDefault , false);
    dropbox.addEventListener("drop", drop, false);

    //检测files列表是否标记删除
    function checkIsInList(idx){
        for(var i=0; i<no_upload_list.length; i++)
            if(no_upload_list[i] == idx)
                return true;
        return false;
    }

    //Ajax无刷新批量上传图片，支持删除
    $('#jQsubmit').on('click', function () {
        let formData = new FormData();
        let image_file = $("#image_file")[0].files;
        let image_name = $('#image_name').val();
        let imageType = /^image\//;

        if(image_name.length === 0) {
            alert('图片名称不能为空，批量上传图片名使用逗号分隔');
            return false
        }
        if(image_file.length == 0){
            alert('请选择需要上传的图片，支持拖拽批量上传');
            return false
        } 

        formData.append("image_name", image_name);
        for(var i=0; i<image_file.length; i++) {
            var file = image_file[i]
            if(checkIsInList(i))                    //去除双击删除的图片
                continue;
            else if(!imageType.test(file.type))     //去除非图片类型文件
                continue;
            else
                formData.append('image_file[]', file);
        }

        $.ajax({
            url: @@url,
            type: 'POST',
            data: formData,      
            processData: false,  //不处理发送数据
            contentType: false,  //不设置Content-Type
            success: function (data) {
                console.log(data);
                if(data.code !== 0 ){
                    alert(data.msg);
                    return
                }
                $('#image_name').val("");
                $('#imgDocker').html("");
                alert("上传成功")
            },
            error: function (data) {
                console.log(data);
            }
        });
    })

    //单击选择图片按钮触发file表单
    function selectImg(){
        $('#image_file').click();
    }

    //去除默认事件
    function clearDefault(e) {
      e.stopPropagation();
      e.preventDefault();
    }

    //拖拽核心功能
    function drop(e) {
        clearDefault(e);
        $('#image_file')[0].files = e.dataTransfer.files;
        showImages();
    }

    //在容器上面显示选择的图片，删除的图片隐藏并标记
    function showImages(){
        no_upload_list = [];
        $('#imgDocker').html("");
        let files = $('#image_file')[0].files;
        let imgDocker = $('#imgDocker')[0];

        for(var i=0; i<files.length; i++){

            var file = files[i];
            var imageType = /^image\//;
            if(!imageType.test(file.type)) 
                continue;
            
            var imgItem = document.createElement('img');
            imgItem.style.width = "150px";
            imgItem.style.height = "150px";
            imgItem.style.margin = "10px";
            imgItem.style.cursor = "pointer";
            imgItem.idx = i;
            imgItem.title = "双击删除图片";
            imgItem.ondblclick = function(){
                $(this).hide();
                no_upload_list.push(this.idx);
                console.log(no_upload_list);
            }
            imgDocker.appendChild(imgItem);

            var reader = new FileReader();
            reader.onload = (function(img){
                return function(e){
                    img.src = e.target.result;
                }
            })(imgItem);
            reader.readAsDataURL(file);
        }
    }


</script>



<?php
//图片上传
public function upload(){

        $allow_extd_list = ['bmp','png','jpg','jpeg','gif'];
        $image_name_list = explode(",", input('post.image_name'));

        $data = array();

        for($i=0; $i<count($_FILES['image_file']['name']); $i++){
            if($_FILES['image_file']['error'][$i] !== 0 )
                continue;
            $tmp_file = $_FILES['image_file']['tmp_name'][$i];
            $data['file_name'] = $_FILES['image_file']['name'][$i];
            $data['file_size'] = $_FILES['image_file']['size'][$i];

            //若用户发送的名字不够，使用上传文件名代替
            if($i < count($image_name_list))
                $data['image_name'] = $image_name_list[$i];
            else
                $data['image_name'] = $data['file_name'];

            //构造图片存储路径 -- 日期目录
            $save_dir = date('Ymd') . DS;
            $save_path = ROOT_PATH.'public'.DS.'uploads'.DS.$save_dir;
            if(!is_dir($save_path))
                mkdir($save_path, 0755) or die('文件夹创建失败');

            //构造图片存储路径 -- 文件后缀
            $ext = substr($data['file_name'], strrpos($data['file_name'], '.')+1);
            $ext = strtolower($ext);
            if(!in_array($ext, $allow_extd_list))
                $ext = "png";

            //构造图片存储路径 -- 哈希名称
            $randomSeed = time() . $data['file_name'].$i;
            $data['save_name'] = md5($randomSeed).".".$ext;

            $data['physics_path'] = $save_path . $data['save_name'];
            $data['network_path'] = "https://".$_SERVER['HTTP_HOST'].'/uploads/'.$save_dir.$data['save_name'];
            $data['create_time'] = $data['update_time'] = time();
            try{
                $this->connection->table('sxjx_wxapplet_image')->insert($data);
            }catch (\Exception  $e) {
                return $this->result(null, 4, $data['image_name'].'的数据插入失败, 图片名称必须唯一', 'json');
            }

            if(!move_uploaded_file($tmp_file, $data['physics_path'])){
                return $this->result(null, 6, '文件存储失败, 请检查文件路径是否正确', 'json');
            }

        }//for
        return $this->result(null, 0, '文件保存成功', 'json');
}
?>
