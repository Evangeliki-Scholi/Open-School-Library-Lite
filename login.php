<?php
if (!isset($_SESSION))
    session_start();

if (isset($_POST['username']) && isset($_POST['password']))
{
    require_once 'sql_connection.php';

    $sql = $conn->prepare('SELECT `id`, `password`, `Name` FROM `admins` WHERE `username`=?');
    $sql->bind_param('s', $_POST['username']);
    if ($sql->execute())
    {
        $result = $sql->get_result();
        $row = ($result->fetch_assoc());
        if (!isset($_POST['hashed']) && password_verify($_POST['password'], $row['password']))
        {
            echo "Needs update";
            $newPassword = password_hash(md5($_POST['password']), PASSWORD_BCRYPT);
            $sql = $conn->prepare('UPDATE `admins` SET `password` = ? WHERE `admins`.`id` = ?');
            $sql->bind_param('sd', $newPassword, $row['id']);
            if ($sql->execute())
            {
                $_SESSION['Logged in'] = true;
                $_SESSION['Name'] = $row['Name'];
                $_SESSION['Admin ID'] = $row['id'];
                $_SESSION['Username'] = $_POST['username'];
            }
        }
        else if (password_verify($_POST['password'], $row['password']) || password_verify(hash('sha256', $_POST['password']), $row['password']))
        {
            $_SESSION['Logged in'] = true;
            $_SESSION['Name'] = $row['Name'];
            $_SESSION['Admin ID'] = $row['id'];
            $_SESSION['Username'] = $_POST['username'];
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