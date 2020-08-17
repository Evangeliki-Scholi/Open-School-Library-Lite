<?php
session_start();
include_once 'header.php';
if (!$elevated) header('Location: index.php');
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
                var BOOKAPI = '/API/V1/Book.php';
                $.post(BOOKAPI, { type : 'AddBook', Identifier : $('#id').val(), Title : $('#title').val(), Author : $('#author').val(), Dewey : $('#dewey').val(), ISBN : $('#ISBN').val() })
                    .done(function(data)
                    {
                        if (data["response"] == true && data['error'] == undefined)
                            ShowSuccess("Book added successfully");
                        else
                            ShowError(data['error']);
                        return false;
                    });
                return false;
            }
            </script>
<?php
include_once "footer.php";
?>