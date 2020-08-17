$(function()
{
    $('#poweredBy').html('Powered By <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite">Open School Library Lite</a>');
    HideEverything();
    ShowIndex();
});

function checkUpdate()
{
    window.location.href = 'upgrade.php';
}

function onSearch()
{
    var url = "book.php";
    var AvailCol = 0;
    var FoundAvailCol = false;
    try
    {
        $.post(url, { type : "searchBook", tag : $("#tagBook").val() })
            .done(function(data)
            {
                if (data.length == 0)
                {
                    document.getElementById("content").innerHTML = "";
                    ShowInfo('No results were found');
                }

                length = data.length;
                if (length == 0) return;

                var col = [];
                for (var i = 0; i < data.length; i++)
                {
                    for (var key in data[i])
                    {
                        if (col.indexOf(key) === -1)
                        {
                            if (key !== "Availability" && !FoundAvailCol)
                                AvailCol++;
                            else
                                FoundAvailCol = true;
                            col.push(key);
                        }
                    }
                }

                var table = document.createElement("table");
                table.setAttribute("class", "table table-bordered");

                var tr = table.insertRow(-1);

                for (var i = 0; i < col.length; i++)
                {
                    var th = document.createElement("th")
                    if (col[i] === "Availability")
                        th.innerHTML = "Available";
                    else
                        th.innerHTML = col[i];
                    tr.appendChild(th);
                }

                for (var i = 0; i < length; i++)
                {
                    tr = table.insertRow(-1);

                    for (var j = 0; j < col.length; j++)
                    {
                        var tabCell = tr.insertCell(-1);
                        if (j !== AvailCol)
                            tabCell.innerHTML = data[i][col[j]];
                        else
                        {
                            tabCell.innerHTML = (data[i][col[j]] > 0) ? "Yes" : "No";
                            console.log(data[i][col[j]]);
                        }
                    }
                }
                
                var divContainer = document.getElementById("content");
                divContainer.innerHTML = "";
                divContainer.appendChild(table);
            });
    }
    catch
    {

    }
    return false;
}