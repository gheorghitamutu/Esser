
/*--------------------------------------------------  items pagination --------------------------------- */
let current_page_items = 1;
let records_per_page_items = 3*15;
let objJson_items = [];
let items= document.getElementById("container-hidden-items").childNodes;

for(let i = 0; i < items.length; i++)
{
    let items_elements = items[i].childNodes;
    for(let j = 0; j < items_elements.length; j++)
    {
        objJson_items.push
        (
            {
                adName: items_elements[j].innerHTML
            }
        );
    }
}

function prevPage_items()
{
    if (current_page_items > 1)
    {
        current_page_items--;
        changePage_items(current_page_items);
    }
}

function nextPage_items()
{
    if (current_page_items < numPages_items())
    {
        current_page_items++;
        changePage_items(current_page_items);
    }
}

function changePage_items(page_items)
{
    let btn_next = document.getElementById("btn_next_items");
    let btn_prev = document.getElementById("btn_prev_items");
    let listing_table = document.getElementsByClassName("items-content-user-landing-page")[0];
    let page_span = document.getElementById("page_items");

    // Validate page
    if (page_items < 1)
    {
        page_items = 1;
    }
    if (page_items > numPages_items())
    {
        page_items = numPages_items();
    }

    listing_table.innerHTML = "";

    let header1 = document.createElement('div');
    header1.className="header-items";
    listing_table.appendChild(header1);
    let content1 = document.createElement('div');
    content1.innerHTML="Name";
    header1.appendChild(content1);

    let header2 = document.createElement('div');
    header2.className="header-items";
    listing_table.appendChild(header2);
    let content2 = document.createElement('div');
    content2.innerHTML="Group";
    header2.appendChild(content2);

    let header3 = document.createElement('div');
    header3.className="header-items";
    listing_table.appendChild(header3);
    let content3 = document.createElement('div');
    content3.innerHTML="Quantity";
    header3.appendChild(content3);

    for (let i = (page_items-1) * records_per_page_items; i < (page_items * records_per_page_items) && i < objJson_items.length; i++)
    {
        let item = document.createElement('div');
        item.className="body-items";
        item.id=i.toString();
        listing_table.appendChild(item);

        let item_cont=document.getElementById(i.toString());
        let content = document.createElement('div');
        content.innerHTML=objJson_items[i].adName;
        item_cont.appendChild(content);
    }

    page_span.innerHTML = page_items + "/" + numPages_items();

    if (page_items === 1)
    {
        btn_prev.style.visibility = "hidden";
    }
    else
    {
        btn_prev.style.visibility = "visible";
    }

    if (page_items === numPages_items())
    {
        btn_next.style.visibility = "hidden";
    }
    else
    {
        btn_next.style.visibility = "visible";
    }
}

function numPages_items()
{
    return Math.ceil(objJson_items.length / records_per_page_items);
}

/*--------------------------------------------------  users pagination ---------------------------------------------- */
let current_page_users = 1;
let records_per_page_users = 3*15;
let objJson_users = [] ;
let users= document.getElementById("container-hidden-users").childNodes;

for(let i_users = 0; i_users < users.length; i_users++)
{
    let users_elements = users[i_users].childNodes;
    for(let j_users = 0; j_users < users_elements.length; j_users++)
    {
        objJson_users.push
        (
            {
                adName: users_elements[j_users].innerHTML
            }
        );
    }
}

function prevPage_users()
{
    if (current_page_users > 1)
    {
        current_page_users--;
        changePage_users(current_page_users);
    }
}

function nextPage_users()
{
    if (current_page_users < numPages_users())
    {
        current_page_users++;
        changePage_users(current_page_users);
    }
}

function changePage_users(page_users)
{
    let btn_next = document.getElementById("btn_next_users");
    let btn_prev = document.getElementById("btn_prev_users");
    let listing_table = document.getElementsByClassName("users-content-user-landing-page")[0];
    let page_span = document.getElementById("page_users");

    // Validate page
    if (page_users < 1)
    {
        page_users = 1;
    }

    if (page_users > numPages_users())
    {
        page_users = numPages_users();
    }

    listing_table.innerHTML = "";

    let header1 = document.createElement('div');
    header1.className="header-users";
    listing_table.appendChild(header1);
    let content1 = document.createElement('div');
    content1.innerHTML="Name";
    header1.appendChild(content1);

    let header2 = document.createElement('div');
    header2.className="header-users";
    listing_table.appendChild(header2);
    let content2 = document.createElement('div');
    content2.innerHTML="Group";
    header2.appendChild(content2);

    let header3 = document.createElement('div');
    header3.className="header-users";
    listing_table.appendChild(header3);
    let content3 = document.createElement('div');
    content3.innerHTML="Email";
    header3.appendChild(content3);

    for (let i = (page_users-1) * records_per_page_users; i < (page_users * records_per_page_users) && i < objJson_users.length; i++)
    {
        let user = document.createElement('div');
        user.className = "body-user";
        user.id=i.toString() + "user";
        listing_table.appendChild(user);

        let user_cont=document.getElementById(i.toString() + "user");
        let content = document.createElement('div');
        content.innerHTML=objJson_users[i].adName;
        user_cont.appendChild(content);
    }
    page_span.innerHTML = page_users + "/" + numPages_users();

    if (page_users === 1)
    {
        btn_prev.style.visibility = "hidden";
    }
    else
    {
        btn_prev.style.visibility = "visible";
    }

    if (page_users === numPages_users())
    {
        btn_next.style.visibility = "hidden";
    }
    else
    {
        btn_next.style.visibility = "visible";
    }
}

function numPages_users()
{
    return Math.ceil(objJson_users.length / records_per_page_users);
}

// fire when the entire page loads, including its content (images, css, scripts, etc.)
window.onload = function()
{
    changePage_items(1);
    changePage_users(1);
};






























