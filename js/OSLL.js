
const UserAPIV2 = 'API/V2/User.php';
const BookAPIV2 = 'API/V2/Book.php';
const AuthorAPIV2 = 'API/V2/Author.php';
const ChargesAPIV2 = 'API/V2/Charge.php';

var HideFunctions = [EmptyBorrowBook, ResetCharges];
var ShowFunctions = [ShowCharges, ShowAuthor];

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

/**
 * Add a Notification created by the CreateNotification function
 * @param {Element} Notification 
 */
function AddNotification(Notification)
{
	document.getElementById('NotificationNumber').innerText = parseInt(document.getElementById('NotificationNumber').innerText) + 1;
	document.getElementById('NotificationNumberNotifications').innerText = parseInt(document.getElementById('NotificationNumber').innerText) + ' ' + document.getElementById('NotificationNumberNotifications').innerText.split(' ', 2)[1];
	document.getElementById('NotificationDropDown').appendChild(Notification);
	var Divider = document.createElement('div');
	Divider.classList = 'dropdown-divider';
	document.getElementById('NotificationDropDown').appendChild(Divider);
}

/**
 * Create a custom notification for the addition to the Notification Bar in the top right corner
 * @param {String} ID 
 * @param {String} Link 
 * @param {String} FontAwesomeIcon 
 * @param {String} Message 
 */
function CreateNotification(ID, Link, FontAwesomeIcon, Message)
{
	var Notification = document.createElement('a');
	Notification.href = Link;
	Notification.id = 'Notification-' + ID;
	Notification.classList = 'dropdown-item';
	Notification.innerHTML = '<i class="fas ' + FontAwesomeIcon + ' mr-2"></i>' + Message;
	return Notification;
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
				ElementToPutIn.innerHTML += '<a href="#Author" onclick="AuthorID=' + data['data']['ID'] + '">' + data['data']['Name'] + '</a>';
		});
	}
}

var BorrowBookListNumber = 1;
function BorrowBookFindBook()
{
	if (BorrowBookBookIdentifier.value.length < 3)
		return;
	
	$.post(BookAPIV2, { 'type' : 'GetBook', 'Identifier' : BorrowBookBookIdentifier.value }, function (data)
	{
		var table = document.getElementById('BorrowingTable').children[1];
		var row = table.insertRow(table.rows.length);
		row.insertCell(0).innerText = BorrowBookListNumber++;
		row.insertCell(1).innerText = data['data']['Identifier'];
		row.insertCell(2).innerText = data['data']['Title'];
		GetAuthors(JSON.parse(data['data']['AuthorIDs']), row.insertCell(3), false);
		row.insertCell(4).innerText = 'Cancel';

		BorrowBookBookIdentifier.value = '';
	});
}

function BorrowBookFindUser()
{
	if (BorrowBookUserIdentifier.value == '')
		return;

	$.post(UserAPIV2, { type : 'GetUser', Identifier : BorrowBookUserIdentifier.value }, function (data)
	{
		if (data.hasOwnProperty('error'))
			return;

		BorrowBookUserName.value = data['data']['Name'];
		BorrowBookUserIdentifierLock.value = data['data']['Identifier'];
		BorrowBookUserIdentifier.value = '';
	});
}

function EmptyBorrowBook()
{
	$('#BorrowingTable tbody tr').remove();
	BorrowBookListNumber = 1;
	BorrowBookBookIdentifier.value = '';
	BorrowBookUserIdentifierLock.value = '';
	BorrowBookUserIdentifier.value = '';
	BorrowBookUserName.value = '';
}


var SkipCharges = 0;
function ResetCharges()
{
	SkipCharges = 0;
}

function ShowCharges()
{
	if (location.hash != '#ActiveChargesList' && location.hash != '#AllChargesList')
		return;

	var table = (location.hash == '#ActiveChargesList') ? document.getElementById('ActiveChargesTable') : document.getElementById('AllChargesTable');
	table = table.children[1];
	table.innerHTML = '';
	$.post(ChargesAPIV2, { type : (location.hash == '#ActiveChargesList') ? 'ListActiveCharges' : 'ListCharges', Skip : SkipCharges }, function (data)
	{
		if (data.hasOwnProperty('error'))
			return;
		
		for (var i = 0; i < data['data'].length; i++)
		{
			var row = table.insertRow(table.rows.length);
			row.insertCell(0).innerText = data['data'][i]['ID'];
			row.insertCell(1).innerText = data['data'][i]['Title'];
			row.insertCell(2).innerText = data['data'][i]['Name'];
			row.insertCell(3).innerText = data['data'][i]['BorrowDate'];
			if (location.hash == '#ActiveChargesList')
				continue;
			row.insertCell(4).innerText = data['data'][i]['ReturnDate'];
			row.insertCell(5).innerText = (data['data'][i]['Active'] == 1) ? 'Yes' : 'No';
		}
	});
}


function LogOut()
{
	$.post(UserAPIV2, { type : "LogOut" }, function (data) { location.reload(); });
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

var BorrowBookBookIdentifier;
var BorrowBookUserIdentifier;
var BorrowBookUserIdentifierLock;
var BorrowBookUserName;

$(function()
{
	AddCard(CreateCard('BorrowBookCard', 'BorrowBook', 'Borrow Book', 'dark', '<div class="row"><div class="col-9"> <input type="text" class="form-control" id="BorrowBookBookIdentifier" placeholder="Book Identifier" autocomplete="off"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark" onclick="BorrowBookFindBook()">Find Book</button></div></div> <br /><div class="row"><div class="col-12 table-responsive"><table id="BorrowingTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Action</th></tr></thead><tbody></tbody></table></div></div> <br /><div class="row"><div class="col-9"> <input type="text" class="form-control" id="BorrowBookUserIdentifier" placeholder="User Identifier" autocomplete="off"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark" onclick="BorrowBookFindUser()">Find User</button></div></div> <br /><div class="row"><div class="col-12" hidden><input type="text" class="form-control" id="BorrowBookUserIdentifierLock" readonly disabled></div></div><div class="row"><div class="col-12"> <input type="text" class="form-control" id="BorrowBookUserName" placeholder="User Name" readonly disabled></div></div>', '<button type="button" class="btn btn-block btn-dark" id="BorrowBookBtn" style="width: 100%">Charge</button>'));
	AddCard(CreateCard('ReturnBookCard', 'ReturnBook', 'Return Book', 'dark', '<div class="row"><div class="col-9"> <input type="text" class="form-control" placeholder="Book Identifier" autocomplete="off"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark">Find Book</button></div></div> <br /><div class="row"><div class="col-12 table-responsive"><table id="BorrowingTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Action</th></tr></thead><tbody></tbody></table></div></div>', '<button type="button" class="btn btn-block btn-dark" style="width: 100%">Return Books</button>'));
	AddCard(CreateCard('SearchResultsCard', 'Search', 'Search Results', 'dark', '<div class="col-12 table-responsive"><table id="SearchResultTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Dewey</th><th>ISBN</th><th>Quantity Available</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipSearch >= 20) { SkipSearch = SkipSearch - 20; SearchBooks(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipSearch = SkipSearch + 20; SearchBooks();">Next Page</button></div></div>'));
	AddCard(CreateCard('AuthorCard', 'Author', 'Author\'s Page', 'dark', '<div class="row"><div class="col-12"><h1 id="AuthorName" style="text-align: center;"></h1></div></div><div class="row"><div class="col-12"><img src="" id="AuthorPictureImg" width="30%" style="margin-left: auto; margin-right: auto;"></div></div><br /><div class="row"><div class="col-12"><p id="AuthorDescription" class="text-justify"></p></div></div>', ''));
	AddCard(CreateCard('ActiveChargesListCard','ActiveChargesList', 'Active Charges', 'dark', '<div class="col-12 table-responsive"><table id="ActiveChargesTable" class="table table-bordered table-striped"><thead><tr><th>Identifier</th><th>Title</th><th>User Name</th><th>Borrowing Date</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipCharges >= 20) { SkipCharges = SkipCharges - 20; ShowCharges(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipCharges = SkipCharges + 20; ShowCharges();">Next Page</button></div></div>'));
	AddCard(CreateCard('AllChargesListCard','AllChargesList', 'All Charges', 'dark', '<div class="col-12 table-responsive"><table id="AllChargesTable" class="table table-bordered table-striped"><thead><tr><th>Identifier</th><th>Title</th><th>User Name</th><th>Borrowing Date</th><th>Return Date</th><th>Active</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipCharges >= 20) { SkipCharges = SkipCharges - 20; ShowCharges(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipCharges = SkipCharges + 20; ShowCharges();">Next Page</button></div></div>'));

	BorrowBookBookIdentifier = document.getElementById('BorrowBookBookIdentifier');
	BorrowBookUserIdentifier = document.getElementById('BorrowBookUserIdentifier');
	BorrowBookUserIdentifierLock = document.getElementById('BorrowBookUserIdentifierLock');
	BorrowBookUserName = document.getElementById('BorrowBookUserName');

	window.addEventListener("hashchange", ReloadView, false);
	ReloadView();
});