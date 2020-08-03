<?php
if (!isset($_SESSION))
    session_start();

function GetAlgo($lusername)
{
    try
    {
        require 'sql_connection.php';
        $sql = $conn->prepare('SELECT `Algo` FROM `admins` WHERE `Username` = ?;');
        $sql->bind_param('s', $lusername);
        if (!$sql->execute()) return '{}';
        $result = $sql->get_result();
        if ($result->num_rows != 1) return '{}';
        $row = $result->fetch_assoc();
        return '{ "response" : "'.$row['Algo'].'" }';
    }
    catch (Exception $e)
    {
        return '{}';
    }
}

function LogIn($lusername, $lpassword)
{
    try
    {
        require_once 'sql_connection.php';
        $sql = $conn->prepare('SELECT `ID`, `Name`, `Password` FROM `admins` WHERE `Username` = ?');
        $sql->bind_param('s', $lusername);
        if (!$sql->execute()) return '{}';
        $result = $sql->get_result();
        if ($result->num_rows === 0) return '{}';
        $row = $result->fetch_assoc();
        if (password_verify($lpassword, $row['Password']))
        {
            $_SESSION['Logged in'] = true;
            $_SESSION['Name'] = $row['Name'];
            $_SESSION['Admin ID'] = $row['ID'];
            $_SESSION['Username'] = $lusername;
            return '{ "response": true }';
        }
        else
            return '{ "response": false }';
    }
    catch (Exception $e)
    {
        return '{ "response": false }';
    }
}

if (isset($_POST['type']))
{
    switch ($_POST['type'])
    {
        case 'GetAlgo':
            echo (isset($_POST['username'])) ? GetAlgo($_POST['username']) : '{}';
            break;

        case 'LogIn':
            echo (isset($_POST['username']) && isset($_POST['password'])) ? LogIn($_POST['username'], $_POST['password']) : '{}';
            break;

        default:
            return '{}';
            break;
    }

    exit();
}
/*if (isset($_POST['username']) && isset($_POST['password']))
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
}*/

include_once 'header.php';
if ($elevated) header('Location: index.php');
?>
            <form autocomplete="off" method="POST" onsubmit="login(); return false;">
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

                async function login()
                {
                    $.post('login.php', { type : 'GetAlgo', username : $('#Username').val() }, async function (data)
                    {
                        if (data !== '{}')
                        {
                            console.log(data);
                            data = JSON.parse(data);
                            var lpassword = $('#Password').val();
                            if (data['response'] == 'sha256')
                            {
                                console.log('sha256');
                                lpassword = sha256(lpassword);
                            }
                            else if (data['response'] == 'sha512')
                            {
                                console.log('sha512');
                                lpassword = sha512(lpassword);
                            }

                            console.log(lpassword);
                            
                            $.post('login.php', { type : 'LogIn', username : $('#Username').val(), password : lpassword }, function(data)
                            {
                                console.log(data);
                                if (data !== '{}')
                                {
                                    data = JSON.parse(data);
                                    if (data['response'] === true)
                                        window.location.href = 'index.php';
                                    else
                                        showInfo('Wrong credentials');
                                }
                                else
                                    showError('Internal Server Error.<br>Contact Server Admins');
                            });
                        }
                        else
                            showError('Internal Server Error.<br>Contact Server Admins');
                    }).fail(console.log);
                    return false;
                }

                $(function()
                {
                    $("#Username").focus();
                });
            </script>
<?php
include_once 'footer.php';
?>