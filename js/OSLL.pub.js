const UserAPIV2 = 'API/V2/User.php';
const BookAPIV2 = 'API/V2/Book.php';
const AuthorAPIV2 = 'API/V2/Author.php';

var HideFunctions = [];
var ShowFunctions = [ShowAuthor];



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
			if (ElementToPutIn)
			{
				if (GetHTMLWithLinks == false)
					ElementToPutIn.innerText += data['data']['Name'] + ' - ';
				else
					ElementToPutIn.innerHTML += '<a href="#Author" onclick="AuthorID=' + data['data']['ID'] + '">' + data['data']['Name'] + '</a>';
			}
			else
				return data['data'];
		});
	}
}

var SkipSearch = 0;
function SearchBooks()
{
	var SearchTag = $('#SearchBookInput').val();

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
			GetAuthors(JSON.parse(data['data'][i]['AuthorIDs']), row.insertCell(3), true);
			row.insertCell(4).innerText = data['data'][i]['Dewey'];
			row.insertCell(5).innerText = data['data'][i]['ISBN'];
			row.insertCell(6).innerText = data['data'][i]['Quantity'] - data['data'][i]['QuantityBorrowed'];
		}

		location.hash = '#Search';
	});

	return false;
}

var AuthorID = -1;
function ShowAuthor()
{
	if (location.hash != '#Author')
		return;

	if (AuthorID == -1)
		location.hash = '';

	$.post(AuthorAPIV2, { type : 'GetAuthor', Identifier : AuthorID }, function(data)
	{
		document.getElementById('AuthorName').innerText = data['data']['Name'];
		if (data['data']['PictureURL'] != null)
		{
			document.getElementById('AuthorPictureImg').style.display = 'block';
			document.getElementById('AuthorPictureImg').src = data['data']['PictureURL'];
		}
		else
			document.getElementById('AuthorPictureImg').style.display = 'none';
			document.getElementById('AuthorDescription').innerText = (data['data']['Description'] != '') ? data['data']['Description'] : 'No Author Description';
	});
}

$(function()
{
	AddCard(CreateCard('SearchResultsCard', 'Search', 'Search Results', 'dark', '<div class="col-12 table-responsive"><table id="SearchResultTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Dewey</th><th>ISBN</th><th>Quantity Available</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipSearch >= 20) { SkipSearch = SkipSearch - 20; SearchBooks(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipSearch = SkipSearch + 20; SearchBooks();">Next Page</button></div></div>'));
	AddCard(CreateCard('AuthorCard', 'Author', 'Author\'s Page', 'dark', '<div class="row"><div class="col-12"><h1 id="AuthorName" style="text-align: center;"></h1></div></div><div class="row"><div class="col-12"><img src="" id="AuthorPictureImg" width="30%" style="margin-left: auto; margin-right: auto;"></div></div><br /><div class="row"><div class="col-12"><p id="AuthorDescription" class="text-justify"></p></div></div>', ''));

	window.addEventListener("hashchange", ReloadView, false);
	ReloadView();
});