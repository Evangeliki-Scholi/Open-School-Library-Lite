function getNavBarHeight()
{
    return document.getElementById("navbar").clientHeight;
}

function CreateBannerID() {
    var result = '';
    var allCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = allCharacters.length;
    for (var i = 0; i < 12; i++)
       result += allCharacters.charAt(Math.floor(Math.random() * charactersLength));
    return result;
 }

function DeleteBanner(bannerID)
{
    var banner = document.getElementById(bannerID);
    if (banner == null) return;
    banner.parentNode.removeChild(banner);
}

function CreateBanner(message, duration, type)
{
    var bannerID = CreateBannerID();
    var banner = document.createElement('div');
    banner.id = bannerID;
    banner.classList.add('alert');
    banner.classList.add('alert-' + type);
    banner.style.position = 'absolute';
    banner.style.right = '0px';
    banner.style.top = document.getElementById('navbar').offsetHeight + "px";

    banner.innerHTML = message + '&nbsp;&nbsp;<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg" onclick="DeleteBanner(\'' + bannerID + '\');"><path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.146-3.146a.5.5 0 0 0-.708-.708L8 7.293 4.854 4.146a.5.5 0 1 0-.708.708L7.293 8l-3.147 3.146a.5.5 0 0 0 .708.708L8 8.707l3.146 3.147a.5.5 0 0 0 .708-.708L8.707 8l3.147-3.146z"/></svg>';

    document.getElementById('content').insertBefore(banner, document.getElementById('content').firstChild);

    if (duration != -1)
    {
        setTimeout(function()
        {
            DeleteBanner(bannerID);
        }, duration);
    }
    return bannerID;
}

function ShowInfo(data)
{
    CreateBanner('<string>Info: </strong>' + data, 5000, 'info');
}

function ShowError(data)
{
    CreateBanner('<string>Error: </strong>' + data, 7000, 'danger');
}

function ShowSuccess(data)
{
    CreateBanner('<string>Success: </strong>' + data, 5000, 'success');
}
