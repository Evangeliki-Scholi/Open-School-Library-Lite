<?php

function Footer($Title = 'Open School Library Lite', $LoggedIn = false, $Level = 3, $Name = 'User')
{
	echo '			<!-- [Footer] -->
			<footer class="main-footer">
				<div class="float-right d-none d-sm-inline">
					Made with <a href="https://adminlte.io">AdminLTE 3</a>
				</div>

				<strong>Powered By <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite">Open School Library Lite</a> and a lot of ❤️ for <a href="https://en.wikipedia.org/wiki/Free_and_open-source_software">FLOSS</a></strong>
			</footer>

			<!-- [lib SHA] -->
			<script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha256/0.8.0/sha256.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha512/0.8.0/sha512.min.js"></script>
			<!-- [/lib SHA] -->

			<!-- [JQuery] -->
			<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
			<!-- [/JQuery] -->

			<!-- [Popper] -->
			<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
			<!-- [/Popper] -->

			<!-- [Bootstrap] -->
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
			<!-- [/Bootstrap] -->

			<!-- [Font Awesome] -->
			<script src="https://kit.fontawesome.com/17bc1b9547.js" crossorigin="anonymous"></script>
			<!-- [/Font Awesome] -->
';

if ($LoggedIn)
	echo '			<!-- [Admin LTE] -->
			<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.5/js/adminlte.min.js"></script>
			<!-- [/Admin LTE] -->

			<!-- [OSLL] -->
			<script src="js/OSLL.card.js"></script>
			<script src="js/OSLL.pub.js"></script>
			<script src="js/OSLL.js"></script>
			<!-- [/OSLL] -->
';
else
	echo '			<!-- [OSLL] -->
			<script src="js/OSLL.card.js"></script>
			<script src="js/OSLL.pub.js"></script>
			<script src="js/OSLL.login.js"></script>
			<!-- [/OSLL] -->
';

	echo '
			<!-- [/Footer] -->
';

}

function EndBody($Title = 'Open School Library Lite', $LoggedIn = false, $Level = 3, $Name = 'User')
{
	echo '		</div>
	</body>
';
}

function EndHTML($Title = 'Open School Library Lite', $LoggedIn = false, $Level = 3, $Name = 'User')
{
	echo '</html>';
}

?>