function LogIn()
{
	var Username = document.getElementById('UsernameInput').value;
	var Password = document.getElementById('PasswordInput').value;

	$.post(UserAPIV2, { type : 'GetAlgo', 'Username' : Username }, function (data)
	{
		console.log(data);
		if (!data['response']) { console.log('Error getting Hashing Algorythm'); return; }

		if (!data['data'].hasOwnProperty('Algo') || data['data']['Algo'] == null || data['data']['Algo'] == '') { console.log('User is not login capable.'); return; }

		switch (data['data']['Algo']) {
			case 'sha256':
				Password = sha256(Password);
				break;
			case 'sha512':
				Password = sha512(Password);
				break;
			default:
				console.log('User is not login capable');
				return;
		}

		$.post(UserAPIV2, { type : 'LogIn', 'Username' : Username, 'Password' : Password }, function (data)
		{
			if (!data['response']) { console.log('Wrong Credentials'); return; }
			else
				location.reload();
		});
	});
}

$(function()
{
    AddCard(CreateCard('LogInCard', 'Login', 'Log In', 'dark', '<div class="row"><div class="col-12"><input type="text" class="form-control" id="UsernameInput" placeholder="Username" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');"></div></div><br /><div class="row"><div class="col-12"><input type="password" class="form-control" id="PasswordInput" placeholder="Password" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');"></div></div>', '<button type="button" class="btn btn-block btn-dark" onclick="LogIn();" style="width: 100%">Login</button>'));
	ReloadView();
});
