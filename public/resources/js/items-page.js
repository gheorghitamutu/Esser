var ids=[];
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

    for(var i=0;i<ids.length;i++){
        closeContainer(i);
    }
    ids=[];

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



    var nr=document.getElementsByClassName("container-row-group-items");
    for(var i=0;i < nr.length;i++){
        ids.push(0);
    }
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
    for(var i=0;i<ids.length;i++){
        closeContainer(i);
    }
    ids=[];

}

function openContainer(id){
    var container=document.getElementById(id+"'container'");
    container.style.width="100%";
    container.style.height="500px";
    container.style.display="flex";
    container.style.flexDirection="row";
}
function closeContainer(id){
    var container=document.getElementById(id+"'container'");
    container.style.width="0";
    container.style.height="0";
    container.style.display="none";

}

function showHiddenContainerItems(id){

    for(var i=0 ;i< ids.length;i++){
        if(id==i.toString()){
            if( ids[i] == 0){
                ids[i]=1;
                openContainer(id);
            }else{
                ids[i]=0;
                closeContainer(id);
            }

        }

    }
}

function deleteItemFunction(){
    document.getElementById("container-get-item").style.display = "none";
    document.getElementById("container-get-item").style.width = "0";
    document.getElementById("container-get-item").style.height= "0";

    document.getElementById("container-post-item").style.display = "none";
    document.getElementById("container-post-item").style.width = "0";
    document.getElementById("container-post-item").style.height= "0";

    document.getElementById("container-delete-item").style.display = "grid";
    document.getElementById("container-delete-item").style.width = "100%";
    document.getElementById("container-delete-item").style.height= "100%";

}

function postItemFunction(){
    document.getElementById("container-get-item").style.display = "none";
    document.getElementById("container-get-item").style.width = "0";
    document.getElementById("container-get-item").style.height= "0";

    document.getElementById("container-delete-item").style.display = "none";
    document.getElementById("container-delete-item").style.width = "0";
    document.getElementById("container-delete-item").style.height= "0";

    document.getElementById("container-post-item").style.display = "flex";
    document.getElementById("container-post-item").style.width = "100%";
    document.getElementById("container-post-item").style.height= "100%";


}
function showItemFunction(){

    document.getElementById("container-delete-item").style.display = "none";
    document.getElementById("container-delete-item").style.width = "0";
    document.getElementById("container-delete-item").style.height= "0";

    document.getElementById("container-post-item").style.display = "none";
    document.getElementById("container-post-item").style.width = "0";
    document.getElementById("container-post-item").style.height= "0";

    document.getElementById("container-get-item").style.display = "grid";
    document.getElementById("container-get-item").style.width = "100%";
    document.getElementById("container-get-item").style.height= "100%";
}