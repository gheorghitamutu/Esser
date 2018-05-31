

function showNewNotifications(){
    let button = document.getElementById("notification-button");
    let value=4;
    if(value<1){
        button.style.display="none";
    }else {
        button.innerHTML = 'You have ' + value + ' new Notifications !';
    }
}


function showHideAdminCpButton() {
    let credential = true;
    let buttonAdm = document.getElementById("admin-cp-container-user-landing-page");
    if (!credential) {
        buttonAdm.style.display = "none";
    }
}

function adjustTable(){
    var table = document.getElementsByClassName("items-content-user-landing-page");
    var item = document.getElementsByClassName("row-items");
    if( table[0].clientHeight % item.clientHeight != 0) {
        let result=table[0].clientHeight-(table[0].clientHeight % item.clientHeight);
        table[0].setAttribute("style", "height:"+result);
    }

    var cv= document.getElementById("admin-cp-container-user-landing-page");
    cv.setAttribute("style","text-color:red");
}
showHideAdminCpButton();
showNewNotifications();
adjustTable();