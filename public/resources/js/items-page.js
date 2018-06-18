function postGroupFunction(){

    document.getElementById("container-delete-group").style.display = "none";
    document.getElementById("container-delete-group").style.width = "0";
    document.getElementById("container-delete-group").style.height= "0";

    document.getElementById("container-get-group").style.display = "none";
    document.getElementById("container-get-group").style.width = "0";
    document.getElementById("container-get-group").style.height= "0";

    document.getElementById("container-post-group").style.display = "flex";
    document.getElementById("container-post-group").style.width = "100%";
    document.getElementById("container-post-group").style.height= "100%";

}

function getGroupFunction(){

    document.getElementById("container-delete-group").style.display = "none";
    document.getElementById("container-delete-group").style.width = "0";
    document.getElementById("container-delete-group").style.height= "0";

    document.getElementById("container-post-group").style.display = "none";
    document.getElementById("container-post-group").style.width = "0";
    document.getElementById("container-post-group").style.height= "0";

    document.getElementById("container-get-group").style.display = "grid";
    document.getElementById("container-get-group").style.width = "100%";
    document.getElementById("container-get-group").style.height= "100%";



}

function deleteGroupFunction(){
    document.getElementById("container-get-group").style.display = "none";
    document.getElementById("container-get-group").style.width = "0";
    document.getElementById("container-get-group").style.height= "0";

    document.getElementById("container-post-group").style.display = "none";
    document.getElementById("container-post-group").style.width = "0";
    document.getElementById("container-post-group").style.height= "0";

    document.getElementById("container-delete-group").style.display = "grid";
    document.getElementById("container-delete-group").style.width = "100%";
    document.getElementById("container-delete-group").style.height= "100%";

}