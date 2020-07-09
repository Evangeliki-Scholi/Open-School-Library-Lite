<?php
if (!isset($_SESSION))
    session_start();

if (isset($_POST["username"]) && isset($_POST["password"]))
{
    require_once "sql_connection.php";

    $sql = $conn->prepare("SELECT `password`, `Name` FROM `admins` WHERE `username`=?");
    $sql->bind_param("s", $_POST["username"]);
    if ($sql->execute())
    {
        $result = $sql->get_result();
        $row = ($result->fetch_assoc());
        if (password_verify($_POST['password'], $row["password"]))
        {
            $_SESSION["logged in"] = true;
            $_SESSION["name"] = $row["Name"];
            echo "Welcome ".$_SESSION["name"];
        }
    }
}

include_once "header.php";
if ($elevated) header("Location: index.php");
?>
            <form autocomplete="off" method="POST">
                <div class="form-group">
                    <label for="exampleInputEmail1">Username</label>
                    <input type="text" class="form-control" id="Username" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="Password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

            <script>
                $(function()
                {
                    $("#Username").focus();
                });
            </script>
<?php
include_once "footer.php";
?>