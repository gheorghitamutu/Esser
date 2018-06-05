

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
    var table_products = document.getElementsByClassName("items-content-user-landing-page");
    var table_users = document.getElementsByClassName("users-content-user-landing-page");
    var height_table_products;
    var height_table_users;
    /*
    var item = document.getElementsByClassName("row-items");
    if( table_products[0].clientHeight % item.clientHeight != 0) {
        height_table_products=table_products[0].clientHeight-(table_products[0].clientHeight % item.clientHeight);
    }

    var user = document.getElementsByClassName("row-users");
    if( table_userss[0].clientHeight % users.clientHeight != 0) {
        height_table_users=table_users[0].clientHeight-(table_users[0].clientHeight % user.clientHeight);
    }
    */
    height_table_products=table_products[0].clientHeight;
    height_table_users=table_users[0].clientHeight;

    if(height_table_products> height_table_users ) {
        table_products[0].setAttribute("style", "height:" + height_table_products + "px");
    }else{
        table_users[0].setAttribute("style", "height:" + height_table_users + "px");
    }

    /*
    var main_cointainer=document.getElementById("main");
    if()*/
}
showHideAdminCpButton();
showNewNotifications();
adjustTable();