<?php

function deleteDir($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? deleteDir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

function custom_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
  
    while( $file = readdir($dir) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ($file === 'sql_connection.php' || $file === 'settings.php')
                continue;
            if ( is_dir($src . '/' . $file) )
                custom_copy($src . '/' . $file, $dst . '/' . $file);
            else
                copy($src . '/' . $file, $dst . '/' . $file);
        }
    }
    closedir($dir);
}
  
function custom_version_compare($NewVersion, $OldVersion)
{
    $NewVersionSpliter = explode('.', $NewVersion);
    $OldVersionSpliter = explode('.', $OldVersion);
    
    $CommonLength = (count($NewVersionSpliter) < count($OldVersionSpliter) ? count($NewVersionSpliter) : count($OldVersionSpliter));
    $i = 0;
    
    while ($i < $CommonLength)
    {
        $NewSize = strlen($NewVersionSpliter[$i]);
        $OldSize = strlen($OldVersionSpliter[$i]);
        
        if (is_numeric($NewVersionSpliter[$i]) && is_numeric($OldVersionSpliter[$i]))
            if ($NewSize > $OldSize)
                return 1;
    
        if ($NewVersionSpliter[$i] === $OldVersionSpliter[$i])
        {
            $i++;
            continue;
        }
        else if ($NewVersionSpliter[$i] > $OldVersionSpliter[$i])
            return 1;
        else
            return -1;
    }
    return 0;
}

function copySettings($src)
{
    require_once $src.'/settings.php';
    require_once 'settings.php';
    $settingsStr = '<?php\n$_SETTINGS = '.var_export($_SETTINGS, true).';\n?>';
    unlink('settings.php');
    file_put_contents('settings.php', $settingsStr);
}

function upgradeDatabase($src)
{
    if (file_exists($src.'/databaseUpdate.php'))
    {
        include $src.'/databaseUpdate.php';
        return dbUpdate();
    }
    else return true;
}

if(!isset($_SESSION))
    session_start();
    
if(!isset($_SESSION['Logged in']))
    header('Location: index.php');

ob_start();
try
{
    $url = 'https://github.com/Evangeliki-Scholi/Open-School-Library-Lite/zipball/master';
    
    if (file_exists('update'))
        deleteDir('update');
    mkdir('update');

    if(!file_put_contents('update.zip', file_get_contents($url)))
        throw new Exception();

    $zip = new ZipArchive;
    $result = $zip->open('update.zip');
    if (!$result === true)
        throw new Exception();
    $zip->extractTo('update');
    $zip->close();
    $folder = scandir('update')[2];
    
    $NewVersion = file_get_contents('update/'.$folder.'/Version.txt');
    $OldVersion = file_get_contents('Version.txt');
    
    echo $NewVersion.'\n<br>\n'.$OldVersion.'\n<br>\n';
    $VersionComparison = custom_version_compare($NewVersion, $OldVersion);
    
    if ($VersionComparison <= 0)
    {
        deleteDir('update');
        unlink('update.zip');
        ob_end_clean();
        echo '{ "response": false }';
        die();
    }

    $files = scandir('update/'.$folder);
    array_shift($files);
    array_shift($files);
    custom_copy('update/'.$folder, '.');
    copySettings('update/'.$folder);
    if (!upgradeDatabase('update/'.$folder))
    {
        deleteDir('update');
        unlink('update.zip');
        ob_end_clean();
        echo '{ "response": false }';
        die();
    }
    deleteDir('update');
    unlink('update.zip');
    echo '{ "response": true }';
}
catch (Exception $e)
{
    ob_end_clean();
    echo $e;
    echo '{ "response": false }';
}