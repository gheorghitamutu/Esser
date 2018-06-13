

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

showHideAdminCpButton();
showNewNotifications();