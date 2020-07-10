<?php
$_SESSION['Logged in'] = false;
session_unset('Logged in');
session_start();
session_destroy();
?>
<html>
    <head>
    </head>
    <body>
        <script>
            function deleteCookie(name)
            {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }
            deleteCookie("PHPSESSID");
            window.location.href = "index.php";
        </script>
    </body>
</html>