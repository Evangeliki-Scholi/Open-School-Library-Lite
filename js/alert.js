function getNavBarHeight()
{
    return document.getElementById("navbar").clientHeight;
}

var isThereAnAlert = false;
function showInfo(data)
{
    if (!isThereAnAlert)
        document.getElementById("content").innerHTML+="<div class=\"alert alert-info\" id=\"alert\" style=\"position:absolute;right:0px;top:" + getNavBarHeight() + "px\"><strong>Info: </strong>" + data + "</div>";
    else
    {
        if (document.getElementById("alert").classList.contains("alert-success"))
            document.getElementById("alert").classList.remove("alert-success");
        
            if (document.getElementById("alert").classList.contains("alert-danger"))
            document.getElementById("alert").classList.remove("alert-danger");

        if (!document.getElementById("alert").classList.contains("alert-info"))
            document.getElementById("alert").classList.add("alert-info");
        document.getElementById("alert").innerHTML="<strong>Info: </strong>" + data;
    }
    isThereAnAlert = true;
}

function showError(data)
{
    if (!isThereAnAlert)
        document.getElementById("content").innerHTML+="<div class=\"alert alert-danger\" id=\"alert\" style=\"position:absolute;right:0px;top:" + getNavBarHeight() + "px\"><strong>Error: </strong>" + data + "</div>";
    else
    {
        if (document.getElementById("alert").classList.contains("alert-success"))
            document.getElementById("alert").classList.remove("alert-success");
        
            if (document.getElementById("alert").classList.contains("alert-info"))
            document.getElementById("alert").classList.remove("alert-info");

        if (!document.getElementById("alert").classList.contains("alert-danger"))
            document.getElementById("alert").classList.add("alert-danger");
        document.getElementById("alert").innerHTML="<strong>Error: </strong>" + data;
    }
    isThereAnAlert = true;
}

function showSuccess(data)
{
    if (!isThereAnAlert)
        document.getElementById("content").innerHTML+="<div class=\"alert alert-success\" id=\"alert\" style=\"position:absolute;right:0px;top:" + getNavBarHeight() + "px\"><strong>Success: </strong>" + data + "</div>";
    else
    {
        if (document.getElementById("alert").classList.contains("alert-info"))
            document.getElementById("alert").classList.remove("alert-info");
        
            if (document.getElementById("alert").classList.contains("alert-danger"))
            document.getElementById("alert").classList.remove("alert-danger");

        if (!document.getElementById("alert").classList.contains("alert-success"))
            document.getElementById("alert").classList.add("alert-success");
        document.getElementById("alert").innerHTML="<strong>Success: </strong>" + data;
    }
    isThereAnAlert = true;
}