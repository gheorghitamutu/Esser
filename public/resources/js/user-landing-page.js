

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
    /*
    var table_products = document.getElementsByClassName("items-content-user-landing-page");
    var table_users = document.getElementsByClassName("users-content-user-landing-page");
    var height_table_products;
    var height_table_users;

    height_table_products=table_products[0].clientHeight;
    height_table_users=table_users[0].clientHeight;

    if(height_table_products> height_table_users ) {
        table_products[0].setAttribute("style", "height:" + height_table_products + "px");
    }else{
        table_users[0].setAttribute("style", "height:" + height_table_users + "px");
    }

    var items= document.getElementsByClassName("row-items");
    var users= document.getElementsByClassName("row-users");
    var max_height=0;
    for(let item of items){
        if(item.clientHeight >max_height){
            max_height=item.clientHeight;
        }
    }
    for(let user of users){
        if(user.clientHeight >max_height){
            max_height=user.clientHeight;
        }
    }
    for(let item of items){
        item.setAttribute("style","height:"+max_height);
    }
    for(let user of users){
        user.setAttribute("style","height:"+max_height);
    }
    */
}
showHideAdminCpButton();
showNewNotifications();
adjustTable();