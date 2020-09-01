<?php

session_start();
$elevated = (isset($_SESSION['Logged in']) && isset($_SESSION['Level'])) ? $_SESSION['Level'] : 3;

function copyFile($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
  
    while($file = readdir($dir)) {
        if (($file != '.') && ($file != '..'))
            copy($src.'/'.$file, $dst.'/'.$file);
    }
    closedir($dir);
}

function deleteDir($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? deleteDir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

function installPlugin($elevated)
{
    if ($elevated != 0)
        return array('response' => false, 'error' => 'You do not have the right permissions');

    if (!isset($_POST['name']) || $_POST['name'] == '')
        return array('response' => false, 'error' => 'No name for the plugin to install was given');

    if (!isset($_POST['from']) || $_POST['from'] == '')
        return array('response' => false, 'error' => 'No location for the plugin to install was given');

    if ($_POST['from'] != 'url' && $_POST['from'] != '$_FILE')
        return array('response' => false, 'error' => 'Wrong location for the plugin to install was given');
    
    if (!file_exists('plugins/'))
        if (!mkdir('plugins'))
            return array('response' => false, 'error' => 'Could not create folder');
    if (!chdir('plugins'))
        return array('response' => false, 'error' => 'Could not change folder');
    if (!file_exists($_POST['name']))
        if (!mkdir($_POST['name']))
            return array('response' => false, 'error' => 'Could not create plugin data folder');
    if (!chdir($_POST['name']))
        return array('response' => false, 'error' => 'Could not change to plugin folder');
    if ($_POST['from'] == '$_FILE')
    {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name']))
            return array('response' => false, 'error' => 'Could not save file');
    }

    $zip = new ZipArchive();
    if (!$zip->open($_POST['name'].'.zip'))
    {
        chdir('..');
        deleteDir($_POST['name']);
        return array('response' => false, 'error' => 'Could not unzip folder');
    }

    $zip->extractTo('.');
    $zip->close();

    if (!file_exists($_POST['name'].'.php'))
    {
        chdir('..');
        deleteDir($_POST['name']);
        return array('response' => false, 'error' => 'Plugin had malformated file structure');
    }

    if (file_exists($_POST['name'].'_install.php'))
    {
        require $_POST['name'].'_install.php';
        if (function_exists('install'))
        {
            $output = install();
            unlink($_POST['name'].'_install.php');
            if (!isset($output['response']) || $output['response'] != true)
            {
                chdir('..');
                deleteDir($_POST['name']);
                return array('response' => false, 'error' => (isset($output['error']) ? $output['error'] : 'Plugin "'.$_POST['name'].'" did not install correctly'));
            }
        }
    }
    unlink($_POST['name'].'.zip');
    return array('response' => true);
}

function listPlugin($elevated)
{
    if (!file_exists('plugins/'))
        return array('response' => false, 'error' => 'No plugin have been installed');

    chdir('plugins');
    return array('response' => true, 'data' => array_filter(glob('*'), 'is_dir'));
}

function loadPlugin($elevated)
{
    if (!isset($_POST['name']) || $_POST['name'] == '')
        return array('response' => false, 'error' => 'No name for the plugin to load was given');

    if (!file_exists('plugins/'))
        return array('response' => false, 'error' => 'No plugin have been installed');
    chdir('plugins');

    if (!file_exists($_POST['name'].'/'))
        return array('response' => false, 'error' => 'The plugin "'.$_POST['name'].'" has not been installed');
    chdir($_POST['name']);

    if (!file_exists($_POST['name'].'.php'))
        return array('response' => false, 'error' => 'The plugin "'.$_POST['name'].'" has not been properly installed');

    require $_POST['name'].'.php';
    if (!function_exists($_POST['name']))
        return array('response' => false, 'error' => 'The plugin "'.$_POST['name'].'" is malformated');
    
    return $_POST['name']();
}

function loadAssetPlugin($elevated)
{
    if (!isset($_POST['name']) || $_POST['name'] == '')
        return array('response' => false, 'error' => 'No name for the plugin to load was given');

    if (!isset($_POST['asset']) || $_POST['asset'] == '')
        return array('response' => false, 'error' => 'No name for the asset to load was given');

    if (!file_exists('plugins/'))
        return array('response' => false, 'error' => 'No plugin have been installed');

    chdir('plugins');
    if (!file_exists($_POST['name'].'/'))
        return array('response' => false, 'error' => 'The plugin "'.$_POST['name'].'" has not been installed');
    chdir($_POST['name']);

    if (!file_exists('assets'))
        return array('response' => false, 'error' => 'The plugin "'.$_POST['name'].'" has not been properly installed');

    require chdir('assets');
    if (!function_exists($_POST['name']))
        return array('response' => false, 'error' => 'The asset "'.$_POST['name'].'" does not exist');
    
    return array('response' => true, 'data' => file_get_contents($_POST['name']));
}

function removePlugin($elevated)
{
    if ($elevated != 0)
        return array('response' => false, 'error' => 'You do not have the right permissions');

    if (!isset($_POST['name']) || $_POST['name'] == '')
        return array('response' => false, 'error' => 'No name for the plugin to install was given');

    if (!file_exists('plugins/'))
        return array('response' => false, 'error' => 'No plugin have been installed');

    chdir('plugins');
    if (!file_exists($_POST['name'].'/'))
        return array('response' => false, 'error' => 'The plugin "'.$_POST['name'].'" has not been installed');
    deleteDir($_POST['name']);
    return array('response' => true);
}

$isAPI = isset($_POST['API']);
$isPlugin = isset($_POST['Plugin']);

header('Content-Type: application/json');

if ($isAPI && !$isPlugin && isset($_POST['Version']))
{
    if ($_POST['API'] == '' || $_POST['Version'] == '')
        echo '{ "response" : false, "error" : "API and or Version can not be empty" }';
    
    if (!file_exists('API/V'.$_POST['Version'].'/'.$_POST['API'].'.php'))
        echo '{ "response" : false, "error" : "API/Version does not exist" }';

    require 'API/V'.$_POST['Version'].'/'.$_POST['API'].'.php';
}
else if ($isPlugin && !$isAPI && isset($_POST['c']))
{
    $typeOfExec = $_POST['c'];
    if ($typeOfExec == '')
        echo '{ "response" : false, "error" : "Type of execution can not be empty" }';
    
    if (!function_exists($typeOfExec.'Plugin'))
        echo '{ "response" : false, "error" : "The given type of execution does not exist" }';
    
    echo json_encode(($typeOfExec.'Plugin')($elevated));
}
else if (isset($_POST['help']))
{
    echo '{ "response" : true, "verion" : 1 }';
}
else
    echo '{ "response" : false, "error" : "Incorrect POST Data." }';
?>