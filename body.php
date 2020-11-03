<?php

function StartBODY()
{
    echo "\t".'<body class="layout-top-nav">
        <div class="wrapper">
';
}

function TopNavBar()
{
    echo "\t\t\t".'<!-- [Top NavBar] -->
            <nav class="main-header navbar navbar-expand-md navbar-dark">
                <!-- [Top Left NavBar] -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="index.php" class="nav-link">Home</a>
                    </li>
                </ul>
                <!-- [/Top Left NavBar] -->

                <!-- [Top Right NavBar] -->
                <form class="form-inline ml-auto" onsubmit="return SearchBooks() && false">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" id="SearchBookInput" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                &nbsp;&nbsp;
                <button class="btn btn-navbar" onclick="location.hash=\'#Login\'">Login</button>
                <!-- [/Top Right NavBar] -->
';

echo '
            </nav>
            <!-- [/Top NavBar] -->

';
}

function MainPage($LoggedIn = false)
{
    echo "\t\t\t".'<!-- [Main Page] -->
            <div class="content-wrapper">
                <!-- [Content Body] -->
                <section class="content">
                    <div class="container-fluid">
                        <br />
                        <br />
                        <div class="row">
                            <div class="col-md-10" id="ContentBody" style="margin: 0 auto;">
                            </div>
                        </div>
                    </div>
                </section>
                <!-- [/Content Body] -->
            </div>
            <!-- [/Main Page] -->

';
}

function EndBODY()
{
    echo "\t\t".'</div>
    </body>
';
}
?>