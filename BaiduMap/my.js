
//Init
var map = new BMap.Map("myMap", {enableMapClick: false}); //取消底图详情点击
var point = new BMap.Point(116.404, 39.915);
//map.centerAndZoom(point, 11);
map.centerAndZoom("深圳", 11);     //也可以这样定位


//事件开关
map.disableDoubleClickZoom(true);   //禁用双击放大
map.enableScrollWheelZoom();        //启用滚轮放大
map.enableContinuousZoom();         //启用惯性拖拽

//地图控件
controlScale = new BMap.ScaleControl({          //比例尺
    anchor: BMAP_ANCHOR_TOP_RIGHT
});

controlNavig = new BMap.NavigationControl({     //导航
    anchor:BMAP_ANCHOR_TOP_RIGHT,
    type: BMAP_NAVIGATION_CONTROL_LARGE,
    enableGeolocation: true
});

controlGeo = new BMap.GeolocationControl();     //定位

controlGeo.addEventListener('locationSuccess', function(e){
    var addr = '';
    addr += e.addressComponent.province;
    addr += e.addressComponent.city;
    addr += e.addressComponent.district;
    addr += e.addressComponent.street;
    addr += e.addressComponent.streetNumber;
    alert(addr);
})

controlGeo.addEventListener('locationError', function(e){
    alert(e.mssage);
})

//加入控件到地图当中
map.addControl(controlScale);
map.addControl(controlNavig);
map.addControl(controlGeo);


//逆地址解析器 坐标 -> 地址信息
var geoc = new BMap.Geocoder();


//创建红点
function addMarker(point, Name, addStr, markStr){
    var marker = new BMap.Marker(point);
    var label = new BMap.Label(Name, {offset: new BMap.Size(20, -10)})

    //创建右键删除菜单
    var delMarker = function(e, ee, marker){map.removeOverlay(marker);isCreateMarker=false}
    var menu = new BMap.ContextMenu();
    menu.addItem(new BMap.MenuItem('删除', delMarker.bind(marker)));
    marker.addContextMenu(menu);

    //创建双击备注信息
    var opts = {
        width: 200,
        height: 90,
        title: "地址：" +  addStr
    }

    var infoWin = new BMap.InfoWindow("备注：" + markStr, opts);
    marker.addEventListener("dblclick", function(){
        map.openInfoWindow(infoWin, point);
    })

    map.addOverlay(marker);
    marker.setLabel(label);
}




//移除红点
function delMarker(content){
    var allOverlay = map.getOverlays();
    for (var i = 0; i < allOverlay.length ; i++)
        if(allOverlay[i].getLabel().content == content)
            map.removeOverlay(allOverlay[i]);
}

//随机生成红点
function createRanMarker(num, isRand, arr){
	var bounds = map.getBounds();
	var sw = bounds.getSouthWest();
	var ne = bounds.getNorthEast();
	var lngSpan = Math.abs(sw.lng - ne.lng);
	var latSpan = Math.abs(ne.lat - sw.lat);

    if(isRand)
        for (var i = 0; i < num; i++) {
            var lng = sw.lng + lngSpan * (Math.random() * 0.7);
            var lat = ne.lat - latSpan * (Math.random() * 0.7);
            var point = new BMap.Point(lng, lat);
            var label = new BMap.Label("Lamp"+i,{offset:new BMap.Size(20,-10)});
            addMarker(point,label);
        }
    else
        for (var i in arr){
            var lng = sw.lng + lngSpan * (Math.random() * 0.7);
            var lat = ne.lat - latSpan * (Math.random() * 0.7);
            var point = new BMap.Point(lng, lat);
            addMarker(point,arr[i]);
        }
}

var showInfo = document.getElementById("showInfo");
var isCreateMarker = false;
var list = ['apple', 'banana', 'pear', 'danger', 'success'];

//createRanMarker(list.length, 0, list);
//delMarker("banana")

//单击地图创建marker
//右键移除marker
//一次只能创建一个marker
//双击marker查看备注信息




//移除先前创建的Marker 在新点击的地方添加Marker，双击查看地址信息和备注
function addOrmoveMarker(e){
    if(isCreateMarker){
        return false;
    }

    geoc.getLocation(e.point, function(rs){
        var addStr = "";

        //获取地址信息
        var a = rs.addressComponents;
        addStr += a.province + " ";
        addStr += a.city + " ";
        addStr += a.district + "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        addStr += a.street + "";
        addStr += a.streetNumber + " ";
        showInfo.innerHTML = addStr;        //显示地址信息


        //添加新的标记
        var point = new BMap.Point(e.point.lng, e.point.lat);
        var markStr = ""//prompt("输入路灯备注信息");
        if(markStr == "" || markStr == null) markStr = "无";
        addMarker(point, "新的路灯", addStr, markStr);

    })

    isCreateMarker = true;
}




//监听地图点击事件：创建标记，返回地址信息
map.addEventListener("click", addOrmoveMarker);


function inputCenterZoom(v){
    geoc.getPoint(v, function(p){
        if(p){
            map.centerAndZoom(p, 15);
        }else{
            alert("抱歉，没有查询到结果")
        }
    }, "")
}
