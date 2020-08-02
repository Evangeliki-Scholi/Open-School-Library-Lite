<?php
session_start();
include_once 'header.php';
if (!$elevated) header('Location: index.php');
?>

            <form onsubmit="return findBook();">
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" name="barcode" id="bookBarcode" placeholder="Barcode">
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Search Book</button>
                    </div>
                </div>
            </form>
            <br>
            <br>
            <form onsubmit="return returnBook();">
                <div class="form-group">
                    <input type="text" class="form-control" name="id" id="id" placeholder="Id" readonly>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="" readonly>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="author" id="author" placeholder="Author" value="" readonly>
                    </div>
                </div>
                <br>
                <div class="form-group" style="margin:0px auto">
                    <button type="submit" class="btn btn-primary">Return book</button>
                </div>
            </form>

            <script>
                var canReturn = false;
                var lastID = "";

                $(function()
                {
                    $("#bookBarcode").focus();
                });

                function findBook()
                {
                    if ($("#bookBarcode").val() == "") return false;
                    console.log(lastID);
                    console.log($("#bookBarcode").val());
                    if (lastID == $("#bookBarcode").val())
                    {
                        console.log("Got Data");
                        returnBook();
                        return false;
                    }
                    else
                    {
                        console.log("Getting data");
                        var url = "book.php?type=getBook&format=json&id=" + $("#bookBarcode").val();
                        $.getJSON(url)
                            .done(function(data)
                            {
                                lastID = $("#bookBarcode").val();
                                $("#id").val(data["ID"]);
                                $("#title").val(data["Title"]);
                                $("#author").val(data["Author"]);
                                $("#bookBarcode").val("");
                                canReturn = true;
                                return false;
                            });
                    }
                    return false;
                }

                function returnBook()
                {
                    if (!canReturn) return false;
                    var url = "book.php?type=returnBook&id=" + $("#id").val();
                    console.log(url);
                    $.getJSON(url)
                        .done(function(data)
                        {
                            if (data["response"] === "undefined")
                                    return false;
                            if (data["response"] == true)
                            {
                                $("#bookBarcode").val("");
                                $("#id").val("");
                                $("#title").val("");
                                $("#author").val("");
                                return false;
                            }
                            else
                                return false;
                        });
                    return false;
                }
            </script>
<?php
include_once 'footer.php';
?>