function loadJS(){
   var sc = document.createElement("script");
   sc.type = "text/javascript";
   sc.src = "http://api.map.baidu.com/api?v=2.0&ak=pMFfCI92eZVChOWyVNGQeOcWP4BYLxty&callback=init";
   document.body.appendChild(sc);
}

function init(){
    //map = new BMap.Map("allmap",{minZoom:11, maxZoon:0});
    map = new BMap.Map("allmap");

    point = new BMap.Point(114.260, 22.705);
    //map.centerAndZoom(point, 11);
    map.centerAndZoom("深圳", 15);

    //功能开关
    map.enableScrollWheelZoom();

    //控件
    TRcontrol = new BMap.ScaleControl({anchor:BMAP_ANCHOR_TOP_RIGHT});

    TRnavigation = new BMap.NavigationControl({
        anchor:BMAP_ANCHOR_TOP_RIGHT,
        type: BMAP_NAVIGATION_CONTROL_LARGE,
        enableGeolocation: true
    })

    var geoControl = new BMap.GeolocationControl();

    geoControl.addEventListener('locationSuccess', function(e){
        var addr = '';
        addr += e.addressComponent.province;
        addr += e.addressComponent.city;
        addr += e.addressComponent.district;
        addr += e.addressComponent.street;
        addr += e.addressComponent.streetNumber;
        alert(addr);
    })

    //定位控件
    geoControl.addEventListener('locationError', function(e){
        alert(e.mssage);
    })

    map.addControl(TRcontrol);
    map.addControl(TRnavigation);
    map.addControl(geoControl);



    function addMarker(point, label){
        var m = new BMap.Marker(point);
        m.setLabel(label);
        map.addOverlay(m);
        m.disableDragging();
        m.addEventListener("click", function(){
            var p = m.getPosition();
            alert(p.lng + "," + p.lat);
        })
    }

    function delMarker(c){
        var allOverlay = map.getOverlays();
        for (var i = 0; i < allOverlay.length -1; i++){
            if(allOverlay[i].getLabel().content == "kali"){
                map.removeOverlay(allOverlay[i]);
                return false;
            }
        }
    }

    var label = new BMap.Label("Kali",{offset:new BMap.Size(20,-10)});
    addMarker(point , label);
    delMarker("")

}

window.onload = loadJS;

function addControl(){
    map.addControl(TRcontrol);
    map.addControl(TRnavigation);
}

function delControl(){
    map.removeControl(TRcontrol);
    map.removeControl(TRnavigation);
}


