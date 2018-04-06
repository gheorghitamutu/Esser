const sidebarTrigger = document.querySelector('#sidebar-trigger')

/* --------------------------------- Make room for the sidebar to appear -------------------------------------------- */
function openLeftSideNavigationBar() {
    document.getElementById("offCanvasSidenav").style.marginLeft = "0%";
    document.getElementById("main").style.marginLeft = "20.2%";
    document.getElementById("footer-container").style.marginLeft = "20.2%";
    // keep this at 245px otherwise at 250px a line will appear
    document.getElementById("sidebar-trigger").style.marginLeft = "14%";
    document.getElementById("sidebar-trigger-container").style.marginLeft = "14%";
    document.getElementById("sidebar-trigger-container").style.width = "87%";

    // shadow the background
    document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
}

/* -------------------------------- Set back the initial width of the elements -------------------------------------- */
function closeLeftSideNavigationBar() {
    document.getElementById("offCanvasSidenav").style.marginLeft = "-20%";
    document.getElementById("main").style.marginLeft = "0.1%";
    document.getElementById("footer-container").style.marginLeft = "0";
    document.getElementById("sidebar-trigger").style.marginLeft = "0";
    document.getElementById("sidebar-trigger-container").style.marginLeft = "0";
    document.getElementById("sidebar-trigger-container").style.width = "100%";

    document.body.style.backgroundColor = "white";
}

/* -------------------------------- Control the sidebar trigger ----------------------------------------------------- */

sidebarTrigger.addEventListener('click', function() {
    if (this.classList.contains('active')) {
        this.classList.remove('active')
        closeLeftSideNavigationBar();
    }
    else
    {
        this.classList.add('active')
        openLeftSideNavigationBar();
    }
})

