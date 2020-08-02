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
            <form onsubmit="return borrowBook();">
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
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" name="userID" id="userID" placeholder="User Identification">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="User Name" id="userName" placeholder="Name" readonly>
                    </div>
                </div>
                <br>
                <div class="form-group" style="margin:0px auto">
                    <button type="submit" class="btn btn-primary">Borrow book</button>
                </div>
            </form>

            <script>
                var canBorrow = false;
                var lastName = "";
                var id = -1;

                $(function()
                {
                    $("#bookBarcode").focus();
                });

                function findBook()
                {
                    if ($("#bookBarcode").val() == "") return false;
                    var url = "book.php?type=getBook&format=json&id=" + $("#bookBarcode").val();
                    $.getJSON(url)
                        .done(function(data)
                        {
                            $("#id").val(data["Identifier"]);
                            $("#title").val(data["Title"]);
                            $("#author").val(data["Author"]);
                            canBorrow = true;
                            $("#userID").focus();
                            return false;
                        });
                    return false;
                }

                function borrowBook()
                {
                    if (!canBorrow) return false;
                    if ($("#userID").val() != lastName)
                    {
                        var url = "user.php?type=getUser&format=json&id="+$("#userID").val();
                        lastName = $("#userID").val();
                        $.getJSON(url)
                            .done(function(data)
                            {
                                $("#userName").val(data["Name"]);
                                id = data["ID"];
                            });
                        $("#userID").val("");
                    }
                    else
                    {
                        if (id == -1) return false;
                        var url = "book.php?type=borrowBook&id=" + $("#id").val() + "&userID=" + id;
                        console.log(url);
                        $.getJSON(url)
                            .done(function(data)
                            {
                                try
                                {
                                    if (data["response"] === "undefined")
                                        return false;
                                        $("#bookBarcode").val("");
                                        $("#id").val("");
                                        $("#title").val("");
                                        $("#author").val("");
                                        $("#userID").val("");
                                        $("#userName").val("");
                                }
                                catch { }
                            });
                    }
                    return false;
                }
            </script>
<?php
include_once 'footer.php';
?>