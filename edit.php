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
            <form onsubmit="return editBook();">
                <div class="form-group">
                    <input type="text" class="form-control" name="id" id="id" placeholder="ID" readonly>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="author" id="author" placeholder="Author" value="">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="dewey" id="dewey" placeholder="Dewey" value="">
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <input type="text" class="form-control" name="ISBN" id="ISBN" placeholder="ISBN">
                </div>
                <br>
                <div class="form-group" style="margin:0px auto">
                    <button type="submit" class="btn btn-primary">Submit changes to book</button>
                    <button type="button" class="btn btn-danger" onclick="deleteBook();">Delete book</button>
                </div>
            </form>

            <script>
                var canUpdate = false;

                $(function()
                {
                    $("#bookBarcode").focus();
                });

                function findBook()
                {
                    if ($("#bookBarcode").val() == "") return false;
                    var url = "book.php";
                    console.log(url);
                    $.post(url, { type : "getBook", format : "json", id : $("#bookBarcode").val() })
                        .done(function(data)
                        {
                            $("#id").val(data["ID"]);
                            $("#title").val(data["Title"]);
                            $("#title").focus();
                            $("#author").val(data["Author"]);
                            $("#dewey").val(data["Dewey"]);
                            $("#ISBN").val(data["ISBN"]);
                            canUpdate = true;
                            return false;
                        });
                    return false;
                }

                function deleteBook()
                {
                    if (!canUpdate) return false;
                    var url = "book.php?";
                    $.post(url, { type : "deleteBook", id : $("#id").va() })
                        .done(function(data)
                        {
                            console.log(data);
                            if (data["response"] == true)
                                showSuccess("Book deletion was successful!");
                            else
                                showError("Book deletion was unseccessful!<br>Please contact the system admin."); 
                        });
                    return false;
                }

                function editBook()
                {
                    if (!canUpdate) return false;
                    var url = "book.php?type=editBook&id=" + $("#id").val() + "&title=" + $("#title").val() + "&author=" + $("#author").val() + "&dewey=" + $("#dewey").val() + "&ISBN=" + $("#ISBN").val();
                    console.log(url);
                    $.getJSON(url)
                        .done(function(data)
                        {
                            if (data["response"] == true)
                                showSuccess("Edit was successful!");
                            else
                                showError("Edit was unseccessful!<br>Please contact the system admin.");
                            return false;
                        });
                    return false;
                }
            </script>
<?php
include_once 'footer.php';
?>