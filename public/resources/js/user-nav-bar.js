const sidebarTrigger = document.querySelector('#sidebar-trigger');

/* --------------------------------- Make room for the sidebar to appear on left side width 21 % -------------------------------------------- */

function openLeftSideNavigationBar() {
    document.getElementById("offCanvasSidenav").style.width = "21%";
    document.getElementById("main").style.marginLeft = "21%";
    document.getElementById("main").style.width = "79%";
    document.getElementById("footer-container").style.marginLeft = "21%";
    document.getElementById("sidebar-trigger").style.marginLeft = "14%";
    document.getElementById("sidebar-trigger-container").style.marginLeft = "14%";
    document.getElementById("sidebar-trigger-container").style.width = "87%";

    // shadow the background
    document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
    /*
    if(document.getElementById("body").clientWidth >=600){
        closeLeftSideNavigationBar();
        openLeftSideNavigationBarDisplayAllWidth();
    }*/
}


/* --------------------------------- Close sidebar reset changes -------------------------------------------- */
function closeLeftSideNavigationBar() {

    document.getElementById("offCanvasSidenav").style.width = "0";
    document.getElementById("main").style.marginLeft = "0px";
    document.getElementById("main").style.width = "100%";
    document.getElementById("footer-container").style.marginLeft = "0";
    document.getElementById("sidebar-trigger").style.marginLeft = "0";
    document.getElementById("sidebar-trigger-container").style.marginLeft = "0";
    document.getElementById("sidebar-trigger-container").style.width = "100%";
    document.body.style.backgroundColor = "white";
}


/* --------------------------------- Make sidebar full scren , hide every thing else  -------------------------------------------- */

function openLeftSideNavigationBarDisplayAllWidth() {
    document.getElementById("main").style.display = "none";
    document.getElementById("offCanvasSidenav").style.width="100%";
    var sidenav_items=document.getElementsByClassName("sidenav-item");
    for(sidenav_item of sidenav_items){
        sidenav_item.style.marginLeft="15%";
        sidenav_item.style.marginRight="15%";
        sidenav_item.style.width="70%";
    }
    /*
    if(document.getElementById("body").clientWidth >=600){
        closeLeftSideNavigationBarDisplayAllWidth();
        openLeftSideNavigationBar();
    }*/
}

/* --------------------------------- Reset changes, close full screen sidebar -------------------------------------------- */
function closeLeftSideNavigationBarDisplayAllWidth() {
    document.getElementById("main").style.display = "flex";
    document.getElementById("main").style.width = "100%";
    document.getElementById("offCanvasSidenav").style.width = "0";
    document.getElementById("footer-container").style.marginLeft = "0";
    document.getElementById("sidebar-trigger").style.marginLeft = "0";
    document.getElementById("sidebar-trigger-container").style.marginLeft = "0";
    document.getElementById("sidebar-trigger-container").style.width = "100%";
    document.body.style.backgroundColor = "white";
}

/* -------------------------------- Control the sidebar trigger ----------------------------------------------------- */

sidebarTrigger.addEventListener('click', function() {
    if(document.getElementById("body").clientWidth >=600){
        if (this.classList.contains('active')){
            this.classList.remove('active')
            closeLeftSideNavigationBar();
        }else{
            this.classList.add('active')
            openLeftSideNavigationBar();
        }
    }else{
        if (this.classList.contains('active')) {
            this.classList.remove('active')
            closeLeftSideNavigationBarDisplayAllWidth();
        }else {
            this.classList.add('active')
            openLeftSideNavigationBarDisplayAllWidth();
        }
    }
})




