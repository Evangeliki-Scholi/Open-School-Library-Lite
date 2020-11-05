const UserAPIV2 = 'API/V2/User.php';
const BookAPIV2 = 'API/V2/Book.php';
const AuthorAPIV2 = 'API/V2/Author.php';

var HideFunctions = [];
var ShowFunctions = [];

/**
 * Add a Card created by the CreateCard function to main body;
 * @param {Element} Card 
 */
function AddCard(Card)
{
	document.getElementById('ContentBody').appendChild(Card);
}

/**
 * 
 * @param {String} ID
 * @param {String} Hash
 * @param {String} Color 
 * @param {String} Header
 * @param {String} BodyHTML
 * @param {String} FooterHTML
 * @returns {Element}
 */
function CreateCard(ID, Hash, Header, Color = 'dark', BodyHTML = '', FooterHTML = '')
{
	var Card = document.createElement('div');
	Card.classList += 'card card-' + Color + ' ' + Hash;
	Card.id = ID;
	Card.innerHTML = '<div class="card-header" id="Card-' + ID + 'Header"><h3 class="card-title">' + Header + '</h3></div><div class="card-body" id="' + ID + 'Body">' + BodyHTML + '</div><div class="card-footer" id="' + ID + 'Footer">' + FooterHTML + '</div>';
	return Card;
}

function ReloadView()
{
	var ElementsInContentBody = ContentBody.children;
	for (var i = 0; i < ElementsInContentBody.length; i++)
		ElementsInContentBody[i].style.display = 'none';

	for (var i = 0; i < HideFunctions.length; i++)
		HideFunctions[i]();

	const ElementsToShow = document.getElementsByClassName(location.hash.substring(1));

	for (var i = 0; i < ElementsToShow.length; i++)
		ElementsToShow[i].style.display = 'block';

	for (var i = 0; i < ShowFunctions.length; i++)
		ShowFunctions[i]();
}

/**
 * 
 * @param {Array} AuthorIDs
 * @param {Element} ElementToPutIn
 * @param {Boolean} GetHTMLWithLinks
 */
function GetAuthors(AuthorIDs, ElementToPutIn, GetHTMLWithLinks = false)
{
	for (var i = 0; i < AuthorIDs.length; i++)
	{
		$.post(AuthorAPIV2, { type : 'GetAuthor', Identifier : AuthorIDs[i]}, function(data)
		{
			if (GetHTMLWithLinks == false)
				ElementToPutIn.innerText += data['data']['Name'] + ' - ';
			else
				ElementToPutIn.innerHTML += '<a href="#' + data['data']['Name'] + '">' + data['data']['Name'] + '</a>';
		});
	}
}

function LogIn()
{
	var Username = document.getElementById('UsernameInput').value;
	var Password = document.getElementById('PasswordInput').value;

	$.post(UserAPIV2, { type : 'GetAlgo', 'Username' : Username }, function (data)
	{
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

var SkipSearch = 0;
function SearchBooks()
{
	var SearchTag = document.getElementById('SearchBookInput').value;

	$.post(BookAPIV2, { type : 'SearchBook', SearchTag : SearchTag, Skip : SkipSearch }, function (data)
	{
		if (!data['response'])
		{
			console.log('Could not perform the Search');
			return;
		}

		var table = document.getElementById('SearchResultTable').children[1];
		table.innerHTML = '';
		for (var i = 0; i < data['data'].length; i++)
		{
			var row = table.insertRow(table.rows.length);
			row.insertCell(0).innerText = SkipSearch + i + 1;
			row.insertCell(1).innerText = data['data'][i]['Identifier'];
			row.insertCell(2).innerText = data['data'][i]['Title'];
			GetAuthors(JSON.parse(data['data'][i]['AuthorIDs']), row.insertCell(3), false);
			row.insertCell(4).innerText = data['data'][i]['Dewey'];
			row.insertCell(5).innerText = data['data'][i]['ISBN'];
			row.insertCell(6).innerText = data['data'][i]['Quantity'] - data['data'][i]['QuantityBorrowed'];
		}

		location.hash = '#Search';
	});

	return false;
}

$(function()
{
	AddCard(CreateCard('LogInCard', 'Login', 'Log In', 'dark', '<div class="row"><div class="col-12"><input type="text" class="form-control" id="UsernameInput" placeholder="Username"></div></div><br /><div class="row"><div class="col-12"><input type="password" class="form-control" id="PasswordInput" placeholder="Password"></div></div>', '<button type="button" class="btn btn-block btn-dark" onclick="LogIn();" style="width: 100%">Login</button>'));
	AddCard(CreateCard('SearchResultsCard', 'Search', 'Search Results', 'dark', '<div class="col-12 table-responsive"><table id="SearchResultTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Dewey</th><th>ISBN</th><th>Quantity Available</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipSearch >= 20) { SkipSearch = SkipSearch - 20; SearchBooks(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipSearch = SkipSearch + 20; SearchBooks();">Next Page</button></div></div>'));
	window.addEventListener("hashchange", ReloadView, false);
	ReloadView();
});