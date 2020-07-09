<?php
session_start();
include_once "header.php";
if (!$elevated) header("Location: index.php");
?>

            <form onsubmit="return addUser();">
                <div class="form-group">
                    <input type="text" class="form-control" name="barcode" id="userID" placeholder="User ID">
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                    </div>
                </div>
                <br>
                <div class="form-group" style="margin:0px auto">
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>

            <script>
                $(function()
                {
                    $("#userID").focus();
                });

                function addUser()
                {
                    if ($("#name").val() == "")
                    {
                        showInfo("Name can not be empty");
                        return false;
                        exit();
                    }
                    else if ($("#userID").val() == "")
                    {
                        showInfo("User ID can not be empty");
                        return false;
                        exit();
                    }

                    var checkURL = "user.php?type=checkIfExists&userID=" + $("#userID").val();
                    $.getJSON(checkURL)
                        .done(function(data)
                        {
                            if (typeof data["error"] !== 'undefined' && !(typeof data["response"] !== 'undefined'))
                            {
                                showError("An unexpected Error occured");
                                console.log(data);
                            }
                            else if (data["response"] == true)
                            {
                                showInfo("A user with that User ID already exists");
                                return false;
                            }
                            else
                            {
                                var addURL = "user.php?type=addUser&userID=" + $("#userID").val() + "&name=" + $("#name").val();
                                $.getJSON(addURL)
                                    .done(function(data)
                                    {
                                        if (data["response"] == true)
                                        {
                                            showSuccess("User added successfully");
                                            $("#userID").val("");
                                            $("#name").val("");
                                        }
                                    });
                            }
                        });

                    return false;
                }
            </script>
<?php
include_once "footer.php";
?>