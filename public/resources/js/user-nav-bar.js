const sidebarTrigger = document.querySelector('#sidebar-trigger');

/* --------------------------------- Make room for the sidebar to appear on left side width 21 % -------------------------------------------- */

function openLeftSideNavigationBar() {
        document.getElementById("offCanvasSidenav").style.display = "flex";
        document.getElementById("offCanvasSidenav").style.width = "21%";

        document.getElementById("main").style.display = "flex";
        document.getElementById("main").style.marginLeft = "21%";
        document.getElementById("main").style.width = "79%";

        document.getElementById("footer-container").style.marginLeft = "21%";
        document.getElementById("sidebar-trigger").style.marginLeft = "14%";
        document.getElementById("sidebar-trigger-container").style.marginLeft = "14%";
        document.getElementById("sidebar-trigger-container").style.width = "87%";

        // shadow the background
        document.body.style.backgroundColor = "rgba(0,0,0,0.4)";

}


/* --------------------------------- Close sidebar reset changes -------------------------------------------- */
function closeLeftSideNavigationBar() {

        document.getElementById("offCanvasSidenav").style.width = "0";
        document.getElementById("offCanvasSidenav").style.display = "none";

        document.getElementById("main").style.display = "flex";
        document.getElementById("main").style.width = "100%";
        document.getElementById("main").style.marginLeft = "0";

        document.getElementById("footer-container").style.marginLeft = "0";
        document.getElementById("sidebar-trigger").style.marginLeft = "0";
        document.getElementById("sidebar-trigger-container").style.marginLeft = "0";
        document.getElementById("sidebar-trigger-container").style.width = "100%";
        document.body.style.backgroundColor = "white";



    if(document.getElementById("body").clientWidth <=900) {
        var sidenav_items = document.getElementsByClassName("sidenav-item");
        for (let sidenav_item of sidenav_items) {
            sidenav_item.style.marginLeft = "2%";
            sidenav_item.style.marginRight = "2%";
            sidenav_item.style.width = "96%";
        }
    }
    if(document.getElementById("body").clientWidth >=900) {
        var sidenav_items = document.getElementsByClassName("sidenav-item");
        for (let sidenav_item of sidenav_items) {
            sidenav_item.style.marginLeft = "15%";
            sidenav_item.style.marginRight = "15%";
            sidenav_item.style.width = "70%";
        }
    }

}


/* --------------------------------- Make sidebar full scren , hide every thing else  -------------------------------------------- */

function openLeftSideNavigationBarDisplayAllWidth() {
    if(document.getElementById("body").clientWidth <600) {

        document.getElementById("main").style.width = "0";
        document.getElementById("main").style.display = "none";

        document.getElementById("offCanvasSidenav").style.display = "flex";
        document.getElementById("offCanvasSidenav").style.width = "100%";

        var sidenav_items = document.getElementsByClassName("sidenav-item");
        for (let sidenav_item of sidenav_items) {
            sidenav_item.style.marginLeft = "15%";
            sidenav_item.style.marginRight = "15%";
            sidenav_item.style.width = "70%";
        }
    }
}


/* -------------------------------- Control the sidebar trigger ----------------------------------------------------- */

var active=0;
sidebarTrigger.addEventListener('click', function() {

        if (this.classList.contains('active')){
            this.classList.remove('active');
            active=0;
            closeLeftSideNavigationBar();
        }else{
            this.classList.add('active');
            active=1;
            if(document.getElementById("body").clientWidth >=600) {
                openLeftSideNavigationBar();
            }else{
                openLeftSideNavigationBarDisplayAllWidth();
            }
        }
});

/*----------------------------------    while sidebar is active this check for width to change from fullscreen to left side bar--------------*/
var body =document.getElementById("body");
window.addEventListener('resize',function(){
    if( active == 1 ){
        if (body.clientWidth >= 600) {
            closeLeftSideNavigationBar();
            openLeftSideNavigationBar();
        } else {
            closeLeftSideNavigationBar();
            openLeftSideNavigationBarDisplayAllWidth();
        }
    }
});