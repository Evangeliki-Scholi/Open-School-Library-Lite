<?php
session_start();
include_once "header.php";
if (!$elevated) header("Location: index.php");
?>

            <form onsubmit="addBook(); return false;">
                <div class="form-group">
                    <input type="text" class="form-control" id="id" placeholder="identifier" required>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" id="title" placeholder="Title" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="author" placeholder="Author" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="dewey" placeholder="Dewey" required>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <input type="text" class="form-control" id="ISBN" placeholder="ISBN">
                </div>
                <div class="form-group" style="margin:0px auto">
                    <button type="submit" class="btn btn-primary">Add book</button>
                </div>
            </form>

            <script>
            $(function()
            {
                $("#bookBarcode").focus();
            });

            function addBook()
            {
                var url = "book.php?type=addBook&id=" + $("#id").val() + "&title=" + $("#title").val() + "&author=" + $("#author").val() + "&dewey=" + $("#dewey").val() + "&ISBN=" + $("#ISBN").val();                
                try
                {
                    $.getJSON(url)
                        .done(function(data)
                        {
                            if (data["response"] === true)
                                showSuccess("Book added successfully");
                            else if (data["response"] === false)
                                showInfo("Book might already exist in database");
                            else
                                showError("An unexpected error occured");
                            return false;
                        });
                }
                catch { }
                return false;
            }
            </script>
<?php
include_once "footer.php";
?>