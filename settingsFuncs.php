<?php

if (!isset($_SESSION))
    session_start();
$elevated = (isset($_SESSION['Logged in']));

function changeSetting($functionName, $value)
{
    try
    {
        require 'settings.php';
        $SETTINGS = $_SETTINGS;
        unlink('settings.php');
        $SETTINGS[$functionName] = $value;

        $settingsStr = '<?php\n$_SETTINGS = '.var_export($SETTINGS, true).';\n?>';
        unlink('settings.php');
        file_put_contents('settings.php', $settingsStr);
        return '{ "response": true }';
    }
    catch (Exception $e)
    {
        return '{ "response": false }';
    }
}

function getAllSettingsJSON()
{
    try
    {
        require 'settings.php';
        return json_encode($_SETTINGS);
    }
    catch (Exception $e)
    {
        return '{}';
    }
}

if (isset($_POST['type']) && $elevated)
{

    $type = $_POST['type'];

    switch ($type)
    {
        case 'changeAll':
            if (!isset($_POST['names']) || !isset($_POST['values']))
                echo '{ "response": false }';
            else
            {
                $KEYS = json_decode($_POST['names']);
                $VALUES = json_decode($_POST['values']);

                require 'settings.php';
                
                for ($i = 0; $i < count($KEYS); $i++)
                    $_SETTINGS[$KEYS[$i]] = $VALUES[$i];

                $settingsStr = '<?php $_SETTINGS = '.var_export($_SETTINGS, true).'; ?>';

                unlink('settings.php');
                file_put_contents('settings.php', $settingsStr);

                echo '{ "response": true }';
            }
            break;

        case 'change':
            if (!isset($_POST['name']) || !isset($_POST['value']))
                echo '{ "response": false }';
            else
                echo changeSetting($_POST['name'], $_POST['value']);
            break;

        case 'getAll':
            if (!isset($_POST['format']) || $_POST['format'] !== 'json')
                echo '{ "response": false }';
            else
                echo getAllSettingsJSON();
            break;

        default:
            echo '{ "response": false }';
    }

    exit();
}
else
{
    require 'header.php';
}
?>

<form onsubmit="return editFuncs();" id='form'>
    <div id="innerform">
    
    </div>
    <button type="submit" class="btn btn-primary">Submit Changes</button>
    <button type="button" class="btn btn-info" onclick="addField();">Add Settings Field</button>
</form>

<script>

    var keys = [];
    var needsRefresh = false;

    $(function()
    {
        $.post('settingsFuncs.php', { type: 'getAll', format: 'json'}, function(data)
        {
            data = JSON.parse(data);
            for (var key in data)
                keys.push(key);

            var innerHTMLofForm = '';

            for (var i = 0; i < keys.length; i++)
                innerHTMLofForm += '<div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text" id="basic-addon1">' + keys[i] + '</span></div><input type="text" class="form-control" id="' + keys[i] + '" aria-describedby="basic-addon1" value="' + data[keys[i]] + '"></div>';
            
            $('#innerform').html(innerHTMLofForm);
        });
    });

    function addField()
    {
        var newField = prompt("Enter new Field name", "");
        if (newField == "") return;
        keys.push(newField);
        var innerHTMLofForm = '<div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text" id="basic-addon1">' + newField + '</span></div><input type="text" class="form-control" id="' + newField + '" aria-describedby="basic-addon1" value="' + newField + '"></div>';
        $('#innerform').html($('#innerform').html() + innerHTMLofForm);
        needsRefresh = true;
    }

    function editFuncs()
    {

        try
        {
            var keyValues = [];
            for (var i = 0; i < keys.length; i++)
            {
                keyValues.push(document.getElementById(keys[i]).value);
            }

            $.post('settingsFuncs.php', { type: 'changeAll', names : JSON.stringify(keys), values : JSON.stringify(keyValues) }, function(data)
            {
                console.log(data);
                data = JSON.parse(data);
                if (data['response'] !== true)
                {
                    showError('Field ' + keys[i] + ' and above where not updated');
                    throw keys[i];
                }

                if (needsRefresh) location.reload();
            });
        }
        catch (err) { console.log(err); }

        return false;
    }

</script>
<?php
require 'footer.php';
?>