<?php
session_start();
$elevated = (isset($_SESSION['Logged in'])) ? $_SESSION['Logged in'] === true : false;
if (!$elevated || !isset($_SESSION['Admin ID']))
{
    header('Location: index.php');
    die();
}

if(isset($_POST['Name']) && isset($_POST['Username']) && isset($_POST['New_Password']) && isset($_POST['Algo']))
{
    require_once 'sql_connection.php';
    $id = $_SESSION['Admin ID'];
    $sql;
    if ($_POST['New_Password'] !== '')
    {
        $lpassword = password_hash($_POST['New_Password'], PASSWORD_BCRYPT);
        $sql = $conn->prepare('UPDATE `admins` SET `Name` = ?, `Username` = ?, `Password` = ?, `Algo` = ? WHERE `admins`.`ID` = ?');
        $sql->bind_param('ssssd', $_POST['Name'], $_POST['Username'], $lpassword, $_POST['Algo'], $id);
    }
    else
    {
        $sql = $conn->prepare('UPDATE `admins` SET `Name` = ?, `Username` = ? WHERE `admins`.`ID` = ?');
        $sql->bind_param('ssd', $_POST['Name'], $_POST['Username'], $id);
    }
    $executed = $sql->execute();
    if ($executed)
    {
        $_SESSION['Name'] = $_POST['Name'];
        $_SESSION['Username'] = $_POST['Username'];
    }
    echo ('{"response":'.(($executed == true) ? 'true' : 'false').'}');
    die();
}

require_once 'settings.php';
require_once 'header.php';
?>

<h1 style="text-align: center">Welcome <?php echo $_SESSION['Name']; ?></h1>
<br>
<div>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1">Name:</span>
        </div>
        <input type="text" class="form-control" id="Name" aria-label="Name" aria-describedby="basic-addon1" value="<?php echo $_SESSION['Name']; ?>">
    </div>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1">Username:</span>
        </div>
        <input type="text" class="form-control" id="Username" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $_SESSION['Username']; ?>">
    </div>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1">New Password:</span>
        </div>
        <input type="password" class="form-control" id="New Password" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveInfos();">Save</button>
    <button type="button" class="btn btn-warning" id="logoutBtn" onclick="logout();">Log out</button>
    <button type="button" class="btn btn-danger" id="closeAccount" disabled>Close Admin Account</button>
</div>

<script>
    function logout()
    {
        window.location = 'logout.php';
    }

    async function saveInfos()
    {
        if (document.getElementById('Name').value == '' || document.getElementById('Username').value == '')
        {
            showError('Username and Name can not be empty');
            return;
        }
        var name = document.getElementById('Name').value;
        var username = document.getElementById('Username').value;
        var newPassword = (document.getElementById('New Password').value !== '') ? await Hash.SHA512(document.getElementById('New Password').value) : '';
        
        $.post('account.php', {Name : name, Username : username, New_Password : newPassword, Algo : 'sha512' }, function(data)
        {
            console.log(data);
            data = JSON.parse(data);
            if (data['response'] != true)
                showError('There was a problem with changing your Informations');
        });
    }
</script>

<?php
require_once 'footer.php';
?>