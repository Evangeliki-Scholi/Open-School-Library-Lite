<?php

function TopNavBar($LoggedIn = false, $Level = 3)
{
	echo '			<!--[Top NavBar]-->
			<nav class="main-header navbar navbar-expand navbar-dark">
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
				
				<!-- [Top '.(($LoggedIn) ? 'Center' : 'Right').' NavBar] -->
				<form class="form-inline ml-auto" onsubmit="return SearchBooks() && false" autocomplete="off">
					<div class="input-group input-group-sm">
					<input class="form-control form-control-navbar" id="SearchBookInput" type="search" placeholder="Search" aria-label="Search" style="color: #FFFFFF;" onkeypress="javascript: if(event.keyCode == 13) SearchBooks();">
					<div class="input-group-append">
						<button class="btn btn-navbar" type="submit">
						<i class="fas fa-search"></i>
						</button>
					</div>
					</div>
				</form>'.((!$LoggedIn)? '
				&nbsp;
				&nbsp;
				<button class="btn btn-navbar" onclick="location.hash=\'#Login\'">Login</button>' : '').'
				<!-- [/Top '.(($LoggedIn) ? 'Center' : 'Right').' NavBar] -->
';
	if ($LoggedIn)
		echo '
				<!-- [Top Right NavBar] -->
				<ul class="navbar-nav ml-auto">
					<!-- [Notification Center] -->
					<li class="nav-item dropdown">
						<a class="nav-link" data-toggle="dropdown" href="#">
							<i class="far fa-bell"></i>
							<span class="badge badge-warning navbar-badge" id="NotificationNumber">0</span>
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
							<span class="dropdown-header" id="NotificationNumberNotifications">0 Notifications</span>
							<div class="dropdown-divider"></div>

							<!-- [Notifications] -->
							<div id="NotificationDropDown"></div>
							<!-- [/Notifications] -->

							<!-- <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>-->
						</div>
					</li>

					<!--<li class="nav-item">
					<a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button"><i
						class="fas fa-th-large"></i></a>
					</li>-->
				</ul>
';
	echo '			</nav>
			<!-- [/Top NavBar] -->

';
}

function SideNavBar($Title = 'Open School Library Lite', $LoggedIn = false, $Level = 3, $Name = 'User', $Email = '00000000000000000000000000000000')
{
	if (!$LoggedIn)
		return;
	
		echo '			<!-- [SideBar] -->
		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<!-- [OSLL Title] -->
			<a href="index.html" class="brand-link">
				<span class="brand-text font-weight-light">'.$Title.'</span>
			</a>
			<!-- [/OSSL Title] -->

			<div class="sidebar">
				<!-- [Profile] -->
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="https://www.gravatar.com/avatar/'.md5($Email).'" class="img-circle elevation-2">
					</div>
					<div class="info">
						<a href="#" class="d-block">'.$Name.'</a>
					</div>
				</div>
				<!-- [/Profile] -->

				<!-- [Menu] -->
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
						<!-- [Book Menu] -->
						<li class="nav-item has-treeview">
							<a href="#" class="nav-link">
								<i class="nav-icon fas fa-book-open"></i>
								<p>Book<i class="right fas fa-angle-left"></i></p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="#ChargeBook" class="nav-link">
										<i class="fas fa-arrow-circle-up"></i>
										<p>Charge Book</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="#EditBook" class="nav-link">
										<i class="fas fa-edit"></i>
										<p>Edit Book</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="#NewBook" class="nav-link">
										<i class="fas fa-plus-square"></i>
										<p>New Book</p>
									</a>
								</li>
							</ul>
						</li>
						<!-- [/Book Menu] -->

						<!-- [User Menu] -->
						<li class="nav-item has-treeview">
							<a href="#" class="nav-link">
								<i class="nav-icon fas fa-user"></i>
								<p>User<i class="right fas fa-angle-left"></i></p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="#EditUser" class="nav-link">
										<i class="fas fa-user-edit"></i>
										<p>Edit User</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="#NewUser" class="nav-link">
										<i class="fas fa-user-plus"></i>
										<p>New User</p>
									</a>
								</li>
							</ul>
						</li>
						<!-- [/User Menu] -->

						<li class="nav-item">
							<a href="#ActiveChargesList" class="nav-link">
								<i class="nav-icon far fa-circle text-danger"></i>
								<p class="text">List Active Charges</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="#AllChargesList" class="nav-link">
								<i class="nav-icon far fa-circle text-success"></i>
								<p class="text">List All Charges</p>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" onclick="LogOut();" >
								<i class="nav-icon far fa-circle text-info"></i>
								<p class="text">Log Out</p>
							</a>
						</li>
					</ul>
				</nav>
				<!-- [/Menu] -->
			</div>
		</aside>
		<!-- [/SideBar] -->

';
}

function StartBody($Title = 'Open School Library Lite', $LoggedIn = false, $Level = 3, $Name = 'User', $Email = '00000000000000000000000000000000')
{
	echo '	<body class="'.(($LoggedIn) ? 'hold-transition sidebar-mini' : 'layout-top-nav').'">
		<div class="wrapper">
';
}

function MainPageLoad($Title = 'Open School Library Lite', $LoggedIn = false, $Level = 3, $Name = 'User', $Email = '00000000000000000000000000000000')
{
	TopNavBar($LoggedIn, $Level);
	
	if ($LoggedIn)
		SideNavBar($Title, $LoggedIn, $Level, $Name, $Email);

	echo '			<!-- [Main Page] -->
			<div class="content-wrapper">

				<!-- [New Notification Panel] -->
				<div aria-live="polite" aria-atomic="true" style="position: relative; height: 0px; z-index: 99 !important;">
					<!-- Position it -->
					<div style="position: absolute; top: 0; right: 0;" id="InstantNotification">
						<br />
					</div>
				</div>
				<!-- [/New Notification Panel] -->

				<!-- [Content Header] -->
				<section class="content-header">
					<div class="container-fluid">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h2>'.$Title.(($LoggedIn) ? ' Admin Panel' : '').'</h2>
							</div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-right">
									<li class="breadcrumb-item"><a href="#">Home</a></li>
									'.((0 <= $Level && $Level <= 1) ? '<li class="breadcrumb-item active">Admin Panel</li>' : '').'
								</ol>
							</div>
						</div>
					</div>
				</section>
				<!-- [/Content Header] -->

				<!-- [Content Body] -->
				<section class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12" id="ContentBody">
							</div>
						</div>
					</div>
				</section>
				<!-- [/Content Body] -->
			</div>
			<!-- [/Main Page] -->

';
}

?>