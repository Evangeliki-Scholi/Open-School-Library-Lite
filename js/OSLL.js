const ChargesAPIV2 = 'API/V2/Charge.php';

HideFunctions.push(EmptyBorrowBook);
HideFunctions.push(ResetCharges);
ShowFunctions.push(ShowCharges);
ShowFunctions.push(ShowAuthor);


function CleanUpTable(tableID) {
	let table = document.getElementById(tableID);
	table = table.children[1];
	table.innerHTML = '';
}


/*******************************************************************************************************************************/

var BorrowBookListNumber = 1;
function BorrowBookFindBook()
{
	if ($('#BorrowBookBookIdentifier').val().length < 3)
		return;
	
	$.post(BookAPIV2, { 'type' : 'GetBook', 'Identifier' : $('#BorrowBookBookIdentifier').val() }, function (data)
	{
		if (!data['response'])
			return;

		var table = document.getElementById('BorrowingTable').children[1];
		var row = table.insertRow(table.rows.length);
		row.insertCell(0).innerText = BorrowBookListNumber++;
		row.insertCell(1).innerText = data['data']['Identifier'];
		row.insertCell(2).innerText = data['data']['Title'];
		GetAuthors(JSON.parse(data['data']['AuthorIDs']), row.insertCell(3), true);
		row.insertCell(4).innerText = 'Cancel';

		$('#BorrowBookBookIdentifier').val('');
	});
}

function BorrowBookFindUser()
{

	if ($('#BorrowBookUserSearch').val().length < 3)
		return;

	$.post(UserAPIV2, { 'type': 'SearchUser', 'SearchTag' : $('#BorrowBookUserSearch').val() }, function(data)
	{
		if (!data['response'])
			return;

		data = data['data'];
		
		let table = document.getElementById('BorrowBookSearchUserResultsTable');
		table = table.children[1];
		CleanUpTable('BorrowBookSearchUserResultsTable');

		for (let i = 0; i < data.length; i++)
		{
			let row = table.insertRow(table.rows.length);
			row.insertCell(0).innerText = table.rows.length;
			row.insertCell(1).innerText = data[i]['Identifier'];
			row.insertCell(2).innerHTML = data[i]['Name'];
			row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-primary" onclick=\'BorrowBookUserName.val("' + data[i]['Name'] + '");BorrowBookUserIdentifierLock.val(' + data[i]['Identifier'] + ');CleanUpTable("BorrowBookSearchUserResultsTable");$("#BorrowBookUserSearch").val("")\'>Select User</button>';
		}
	});
}

function EmptyBorrowBook()
{
	$('#BorrowingTable tbody tr').remove();
	BorrowBookListNumber = 1;
	if (document.getElementById('BorrowBookBookIdentifier'))
	{
		$('#BorrowBookBookIdentifier').val('');
		BorrowBookUserIdentifierLock.val('');
		$('#BorrowBookUserIdentifier').val('');
	}
	if (document.getElementById('BorrowBookUserName'))
		BorrowBookUserName.val('');
}

async function ChargeBooks()
{
	if (BorrowBookUserName.val() == '')
		return;
	if (document.getElementById('BorrowingTable').children[1].children.length == 0)
		return;

	let booksToBorrow = [];
	const numberOfBooks = document.getElementById('BorrowingTable').children[1].children.length;
	for (var i = 0; i < numberOfBooks; i++)
	{
		let BookData = document.getElementById('BorrowingTable').children[1].children[i].innerText.split('\t');
		if (BookData.length < 2 || BookData[1] == '')
			continue;
		booksToBorrow.push(parseInt(BookData[1]));
	}

	var Response = await $.ajax({
		url: ChargesAPIV2, 
		type: 'POST',
		data: { type : 'AddCharge', BookIdentifier : JSON.stringify(booksToBorrow), UserIdentifier : BorrowBookUserIdentifierLock.val() }
	});

	if (Response.hasOwnProperty('error'))
		console.error('An error has occured');

	EmptyBorrowBook();
}

async function RemoveCharge(Identifier)
{
	var Response = await $.ajax({
		url: ChargesAPIV2,
		type: 'POST',
		data: { type: 'ClearCharge', Identifier: Identifier}
	});

	if (Response.hasOwnProperty('error'))
		console.error('An error has occured');
}


/*******************************************************************************************************************************/


async function EditBookFindBook() {
	if ($('#EditBookIdentifier').val().length < 3)
		return;
	
	$.post(BookAPIV2, { 'type' : 'GetBook', 'Identifier' : $('#EditBookIdentifier').val() }, function (data)
	{
		data = data['data'];

		$('#EditBookIdentifier').val('');
		$('#EditBookId').val(data['Identifier']);
		$('#EditBookTitle').val(data['Title']);
		$('#EditBookAuthors').val(data['AuthorIDs']);
		$('#EditBookDewey').val(data['Dewey']);
		$('#EditBookISBN').val(data['ISBN']);
		$('#EditBookQuantity').val(data['Quantity']);

		try
		{
			let table = document.getElementById('EditBookAuthorsTable');
			table = table.children[1];
			table.innerHTML = '';

			let authors = JSON.parse(data['AuthorIDs']);
			for (var i = 0; i < authors.length; i++)
			{
				$.post(AuthorAPIV2, { type : 'GetAuthor', Identifier : authors[i]}, function(authordata)
				{
					authordata = authordata['data'];
					let row = table.insertRow(table.rows.length);
					row.insertCell(0).innerText = table.rows.length;
					row.insertCell(1).innerText = authordata['ID'];
					row.insertCell(2).innerHTML = '<a href="#Author" onclick="AuthorID=' + authordata['ID'] + '">' + authordata['Name'] + '</a>';
					row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-danger" onclick="EditBookRemoveAuthor(' + authordata['ID'] + ');">Remove</button>';
				});
			}
		}
		catch { }
	});
}

function EditBookUpdateAuthors() {
	let authorsAlreadyAdded = [];
	try
	{
		authorsAlreadyAdded = JSON.parse($('#EditBookAuthors').val());
	}
	catch { }

	let table = document.getElementById('EditBookAuthorsTable');
	table = table.children[1];
	table.innerHTML = '';

	for (let i = 0; i < authorsAlreadyAdded.length; i++)
	{
		$.post(AuthorAPIV2, { 'type': 'GetAuthor', Identifier : authorsAlreadyAdded[i] }, function(data) {
			data = data['data'];

			let row = table.insertRow(table.rows.length);
			row.insertCell(0).innerText = table.rows.length;
			row.insertCell(1).innerText = data['ID'];
			row.insertCell(2).innerHTML = '<a href="#Author" onclick="AuthorID=' + data['ID'] + '">' + data['Name'] + '</a>';
			row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-danger" onclick="EditBookRemoveAuthor(' + data['ID'] + ');">Remove</button>';
		});
	}
}

function EditBookAddAuthor(authorID) {
	let authorsAlreadyAdded = [];
	try
	{
		authorsAlreadyAdded = JSON.parse($('#EditBookAuthors').val());
	}
	catch { }

	if (authorsAlreadyAdded.includes(authorID))
		return;
	
	authorsAlreadyAdded.push(parseInt(authorID));

	$('#EditBookAuthors').val(JSON.stringify(authorsAlreadyAdded));

	let table = document.getElementById('EditBookAuthorsResultsTable');
	table = table.children[1];
	table.innerHTML = '';

	$('#EditBookAddAuthor').val('');

	EditBookUpdateAuthors();
}

function EditBookRemoveAuthor(authorID) {
	let authorsAlreadyAdded = [];
	try
	{
		authorsAlreadyAdded = JSON.parse($('#EditBookAuthors').val());
	}
	catch { }

	if (!authorsAlreadyAdded.includes(authorID))
		return;

	for(let i = 0; i < authorsAlreadyAdded.length; i++)
	{ 
		if (authorsAlreadyAdded[i] == parseInt(authorID))
			authorsAlreadyAdded.splice(i, 1); 
	}

	$('#EditBookAuthors').val(JSON.stringify(authorsAlreadyAdded));

	let table = document.getElementById('EditBookAuthorsResultsTable');
	table = table.children[1];
	table.innerHTML = '';

	EditBookUpdateAuthors();
}

async function EditBookFindAuthor() {
	if ($('#EditBookAddAuthor').val().length < 3)
		return;

	$.post(AuthorAPIV2, { 'type': 'SearchAuthor', 'SearchTag' : $('#EditBookAddAuthor').val() }, function(data)
	{
		data = data['data'];
		
		let table = document.getElementById('EditBookAuthorsResultsTable');
		table = table.children[1];
		table.innerHTML = '';
	
		let authorsAlreadyAdded = [];
		try
		{
			authorsAlreadyAdded = JSON.parse($('#EditBookAuthors').val());
		}
		catch { }

		for (let i = 0; i < data.length; i++)
		{
			if (!authorsAlreadyAdded.includes(data[i]['ID']))
			{
				let row = table.insertRow(table.rows.length);
				row.insertCell(0).innerText = table.rows.length;
				row.insertCell(1).innerText = data[i]['ID'];
				row.insertCell(2).innerHTML = '<a href="#Author" onclick="AuthorID=' + data[i]['ID'] + '">' + data[i]['Name'] + '</a>';
				row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-primary" onclick="EditBookAddAuthor(\'' + data[i]['ID'] + '\');">Add</button>';
			}
		}
	});
}

async function SubmitChanges() {
	if (!$('#EditBookId').val())
		return;

	$.post(BookAPIV2, { 'type' : 'EditBook', 'Identifier' : $('#EditBookId').val(), 'Title': $('#EditBookTitle').val(), 'AuthorIDs' : $('#EditBookAuthors').val(), 'Dewey' : $('#EditBookDewey').val(), 'ISBN' : $('#EditBookISBN').val(), 'Quantity' : $('#EditBookQuantity').val() }, function(data) {
		if (data['response'] == true) {
			$('#EditBookIdentifier').val('');
			$('#EditBookId').val('');
			$('#EditBookTitle').val('');
			$('#EditBookAuthors').val('');
			$('#EditBookDewey').val('');
			$('#EditBookISBN').val('');
			$('#EditBookQuantity').val('');

			let table = document.getElementById('EditBookAuthorsResultsTable');
			table = table.children[1];
			table.innerHTML = '';

			table = document.getElementById('EditBookAuthorsTable');
			table = table.children[1];
			table.innerHTML = '';
		}
	});
}


/*******************************************************************************************************************************/


function NewBookUpdateAuthors() {
	let authorsAlreadyAdded = [];
	try
	{
		authorsAlreadyAdded = JSON.parse($('#NewBookAuthors').val());
	}
	catch { }

	let table = document.getElementById('NewBookAuthorsTable');
	table = table.children[1];
	table.innerHTML = '';

	for (let i = 0; i < authorsAlreadyAdded.length; i++)
	{
		$.post(AuthorAPIV2, { 'type': 'GetAuthor', Identifier : authorsAlreadyAdded[i] }, function(data) {
			data = data['data'];

			let row = table.insertRow(table.rows.length);
			row.insertCell(0).innerText = table.rows.length;
			row.insertCell(1).innerText = data['ID'];
			row.insertCell(2).innerHTML = '<a href="#Author" onclick="AuthorID=' + data['ID'] + '">' + data['Name'] + '</a>';
			row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-danger" onclick="NewBookRemoveAuthor(' + data['ID'] + ');">Remove</button>';
		});
	}
}

function NewBookAddAuthor(authorID) {
	let authorsAlreadyAdded = [];
	try
	{
		authorsAlreadyAdded = JSON.parse($('#NewBookAuthors').val());
	}
	catch { }

	if (authorsAlreadyAdded.includes(authorID))
		return;
	
	authorsAlreadyAdded.push(parseInt(authorID));

	$('#NewBookAuthors').val(JSON.stringify(authorsAlreadyAdded));

	let table = document.getElementById('NewBookAuthorsResultsTable');
	table = table.children[1];
	table.innerHTML = '';

	$('#NewBookAddAuthor').val('');

	NewBookUpdateAuthors();
}

function NewBookRemoveAuthor(authorID) {
	let authorsAlreadyAdded = [];
	try
	{
		authorsAlreadyAdded = JSON.parse($('#NewBookAuthors').val());
	}
	catch { }

	if (!authorsAlreadyAdded.includes(authorID))
		return;

	for(let i = 0; i < authorsAlreadyAdded.length; i++)
	{ 
		if (authorsAlreadyAdded[i] == parseInt(authorID))
			authorsAlreadyAdded.splice(i, 1); 
	}

	$('#NewBookAuthors').val(JSON.stringify(authorsAlreadyAdded));

	let table = document.getElementById('NewBookAuthorsResultsTable');
	table = table.children[1];
	table.innerHTML = '';

	NewBookUpdateAuthors();
}

async function NewBookFindAuthor() {
	if ($('#NewBookAddAuthor').val().length < 3)
		return;

	$.post(AuthorAPIV2, { 'type': 'SearchAuthor', 'SearchTag' : $('#NewBookAddAuthor').val() }, function(data)
	{
		data = data['data'];
		
		let table = document.getElementById('NewBookAuthorsResultsTable');
		table = table.children[1];
		table.innerHTML = '';

		let authorsAlreadyAdded = [];
		try
		{
			authorsAlreadyAdded = JSON.parse($('#NewBookAuthors').val());
		}
		catch { }

		for (let i = 0; i < data.length; i++)
		{
			if (!authorsAlreadyAdded.includes(data[i]['ID']))
			{
				let row = table.insertRow(table.rows.length);
				row.insertCell(0).innerText = table.rows.length;
				row.insertCell(1).innerText = data[i]['ID'];
				row.insertCell(2).innerHTML = '<a href="#Author" onclick="AuthorID=' + data[i]['ID'] + '">' + data[i]['Name'] + '</a>';
				row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-primary" onclick="NewBookAddAuthor(\'' + data[i]['ID'] + '\');">Add</button>';
			}
		}
	});
}

async function AddBook() {
	if (!$('#NewBookIdentifier').val())
		return;

	$.post(BookAPIV2, { 'type' : 'AddBook', 'Identifier' : $('#NewBookIdentifier').val(), 'Title': $('#NewBookTitle').val(), 'AuthorIDs' : $('#NewBookAuthors').val(), 'Dewey' : $('#NewBookDewey').val(), 'ISBN' : $('#NewBookISBN').val(), 'Quantity' : $('#NewBookQuantity').val() }, function(data) {
		if (data['response'] == true) {
			$('#NewBookIdentifier').val('');
			$('#NewBookTitle').val('');
			$('#NewBookAuthors').val('');
			$('#NewBookDewey').val('');
			$('#NewBookISBN').val('');
			$('#NewBookQuantity').val('');

			CleanUpTable('NewBookAuthorsResultsTable');
			CleanUpTable('NewBookAuthorsTable');
		}
		else if (data['response'] == false && data.hasOwnProperty('error'))
			AddNotification(CreateNotification('Error', '', 'exclamation', data['error'], 'Error', true, false));
	});
}


/*******************************************************************************************************************************/


function EditUserSetValues(Identifier = '', Name = '', Username = '', Email = '', Level = '2', Grade = 'No education status')
{
	$('#EditUserIdentifier').val(Identifier);
	$('#EditUserName').val(Name);
	$('#EditUserUsername').val(Username);
	$('#EditUserEmail').val(Email);
	$('#EditUserLevel').val(Level);
	$('#EditUserGrade').val(Grade);
}

async function EditUserLoadUser(UserData)
{
	UserData = JSON.parse(UserData);

	EditUserSetValues(UserData['Identifier'], UserData['Name'], UserData['Username'], UserData['Email'], UserData['Level'], UserData['Grade']);
}

async function EditUserFindUser()
{
	if ($('#EditUserSearch').val().length < 3)
		return;

	$.post(UserAPIV2, { 'type': 'SearchUser', 'SearchTag' : $('#EditUserSearch').val() }, function(data)
	{
		if (!data['response'])
			return;

		data = data['data'];
		
		let table = document.getElementById('EditUserSearchUserResultsTable');
		table = table.children[1];
		CleanUpTable('EditUserSearchUserResultsTable');

		for (let i = 0; i < data.length; i++)
		{
			let row = table.insertRow(table.rows.length);
			row.insertCell(0).innerText = table.rows.length;
			row.insertCell(1).innerText = data[i]['Identifier'];
			row.insertCell(2).innerHTML = data[i]['Name'];
			row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-primary" onclick=\'EditUserLoadUser("' + JSON.stringify(data[i]).replace(/\"/g, '\\"') + '");CleanUpTable("EditUserSearchUserResultsTable");$("#EditUserSearch").val("")\'>Edit User</button>';
		}
	});
}

async function EditUserRemoveUser()
{
	if ($('#EditUserIdentifier').val().length < 1)
		return;

	$.post(UserAPIV2, { 'type' : 'RemoveUser', 'Identifier' : $('#EditUserIdentifier').val() }, function(data)
	{
		if (!data['response'])
			AddNotification(CreateNotification('Error', '', 'exclamation', data['error'], 'Error', true, false));
	});
}

async function EditUserSaveUser()
{
	if ($('#EditUserIdentifier').val().length < 1)
		return;

	$.post(UserAPIV2, { 'type' : 'EditUser', 'Identifier' : $('#EditUserIdentifier').val(), 'Name' : $('#EditUserName').val(), 'Username' : $('#EditUserUsername').val(), 'Email' : $('#EditUserEmail').val(), 'Level' : parseInt($('#EditUserLevel').val()), 'Grade' : $('#EditUserGrade').val() }, function(data)
	{
		if (!data['response'])
			AddNotification(CreateNotification('Error', '', 'exclamation', data['error'], 'Error', true, false));
	});
}


/*******************************************************************************************************************************/


function NewUserSetValues(Identifier = '', Name = '', Username = '', Email = '', Level = '2', Grade = 'No education status')
{
	$('#NewUserIdentifier').val(Identifier);
	$('#NewUserName').val(Name);
	$('#NewUserUsername').val(Username);
	$('#NewUserPassword').val('');
	$('#NewUserPasswordDup').val('');
	$('#NewUserEmail').val(Email);
	$('#NewUserLevel').val(Level);
	$('#NewUserGrade').val(Grade);
}

async function NewUserCreateUser()
{
	let emptyFields = false;
	if ($('#NewUserIdentifier').val().length < 1)
	{
		emptyFields = true;
		AddNotification(CreateNotification('Error', '', 'exclamation', 'User Identifier can not be empty', 'Error', true, false));
	}
	if ($('#NewUserName').val().length < 1)
	{
		emptyFields = true;
		AddNotification(CreateNotification('Error', '', 'exclamation', 'User Name can not be empty', 'Error', true, false));
	}
	if ($('#NewUserUsername').val().length < 5)
	{
		emptyFields = true;
		AddNotification(CreateNotification('Error', '', 'exclamation', 'User Username can not be less than 5 characters', 'Error', true, false));
	}
	if ($('#NewUserGrade').val().length < 1)
	{
		emptyFields = true;
		AddNotification(CreateNotification('Error', '', 'exclamation', 'User Grade can not be empty', 'Error', true, false));
	}
	if ($('#NewUserPassword').val().length < 8)
	{
		emptyFields = true;
		AddNotification(CreateNotification('Error', '', 'exclamation', 'User Password can not be less than 8 characters', 'Error', true, false));
	}
	if ($('#NewUserPassword').val() != $('#NewUserPasswordDup').val())
	{
		emptyFields = true;
		AddNotification(CreateNotification('Error', '', 'exclamation', 'User Passwords do not match', 'Error', true, false));
	}
	if (emptyFields)
		return;

	let hashedPassword = sha256($('#NewUserPassword').val());

	$.post(UserAPIV2, { 'type' : 'AddUser', 'Identifier' : $('#NewUserIdentifier').val(), 'Name' : $('#NewUserName').val(), 'Username' : $('#NewUserUsername').val(), 'Email' : $('#NewUserEmail').val(), 'Password' : $('#NewUserPassword').val(), 'Algo' : 'sha256', 'Level' : parseInt($('#NewUserLevel').val()), 'Grade' : $('#NewUserGrade').val() }, function(data)
	{
		if (!data['response'])
			AddNotification(CreateNotification('Error', '', 'exclamation', data['error'], 'Error', true, false));
	});
}

/*******************************************************************************************************************************/


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
			{
				let cell = row.insertCell(1);
				cell.innerHTML = '';

				let booksID = JSON.parse(data['data'][i]['BookIdentifier']);
				for (let j = 0; j < booksID.length - 1; j++)
				{
					$.post(BookAPIV2, { 'type' : 'GetBook', 'Identifier' : booksID[j] }, function(data)
					{
						if (!data['response'])
							return;

						data = data['data'];
						cell.innerText += data['Title'];
						cell.innerHTML += '<br /><br />';
					});
				}

				$.post(BookAPIV2, { 'type' : 'GetBook', 'Identifier' : booksID[booksID.length - 1] }, function(data)
				{
					if (!data['response'])
						return;

					data = data['data'];
					cell.innerText += data['Title'];
				});
			}
			row.insertCell(2).innerText = data['data'][i]['Name'];
			row.insertCell(3).innerText = data['data'][i]['BorrowDate'];
			if (location.hash == '#ActiveChargesList')
			{
				row.insertCell(4).innerHTML = '<button type="button" class="btn btn-block btn-primary" style="width: 100%" onclick="RemoveCharge(\'' + data['data'][i]['ID'] + '\'); ResetCharges(); ShowCharges();">Clear Charge</button>';
				continue;
			}
			row.insertCell(4).innerText = data['data'][i]['ReturnDate'];
			row.insertCell(5).innerText = (data['data'][i]['Active'] == 1) ? 'Yes' : 'No';
		}
	});
}


function LogOut()
{
	$.post(UserAPIV2, { type : "LogOut" }, function (data) { location.reload(); });
}

var BorrowBookUserIdentifierLock;
var BorrowBookUserName;

$(function()
{
	AddCard(CreateCard('ChargeBookCard', 'ChargeBook', 'Charge Book', 'dark', '<div class="row"> <div class="col-9"> <input type="text" class="form-control" id="BorrowBookBookIdentifier" placeholder="Book Identifier" autocomplete="off" onkeypress="javascript: if(event.keyCode==13) BorrowBookFindBook();"> </div><div class="col-3"> <button type="button" class="btn btn-block btn-primary" onclick="BorrowBookFindBook()">Find Book</button> </div></div><br/><div class="row"> <div class="col-12 table-responsive"> <table id="BorrowingTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>Identifier</th> <th>Title</th> <th>Author</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> </div></div><br/><div class="row"> <div class="col-9"> <input type="text" class="form-control" id="BorrowBookUserSearch" placeholder="Search User" autocomplete="off" onkeypress="javascript: if(event.keyCode==13) BorrowBookFindUser();"> </div><div class="col-3"> <button type="button" class="btn btn-block btn-primary" onclick="BorrowBookFindUser()">Find User</button> </div></div><br/><div class="row"> <div class="col-12"> <table id="BorrowBookSearchUserResultsTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>ID</th> <th>Name</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> </div></div><br/><div class="row"> <div class="col-12" hidden> <input type="text" class="form-control" id="BorrowBookUserIdentifierLock" readonly disabled> </div></div><div class="row"> <div class="col-12"> <input type="text" class="form-control" id="BorrowBookUserName" placeholder="User Name" readonly disabled> </div></div>', '<button type="button" class="btn btn-block btn-primary" style="width: 100%" onclick="ChargeBooks()">Charge</button>'));
	AddCard(CreateCard('EditBookCard', 'EditBook', 'Edit Book', 'dark', '<div class="row"> <div class="col-9"> <input type="text" class="form-control" id="EditBookIdentifier" placeholder="Book Identifier" autocomplete="off"> </div><div class="col-3"> <button type="button" class="btn btn-block btn-primary" onclick="EditBookFindBook()">Find Book</button> </div></div><br/><div class="row"> <div class="col-12"> <form> <label for="EditBookIdentifier">Identifier</label> <input type="text" class="custom-form-control" id="EditBookId" placeholder="Book Identifier" readonly disabled> <label for="EditBookTitle">Title</label> <input type="text" class="custom-form-control" id="EditBookTitle" placeholder="Enter Book Title"> <label for="EditBookAuthors">Authors</label> <input type="text" class="custom-form-control" id="EditBookAuthors" placeholder="Enter Book Authors" hidden> <table id="EditBookAuthorsTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>ID</th> <th>Name</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> <label for="EditBookAddAuthor">Add Author</label> <div class="row"> <div class="col-9"> <input type="text" class="form-control" id="EditBookAddAuthor" placeholder="Add Author"> </div><div class="col-3"> <button type="button" class="btn btn-block btn-primary" onclick="EditBookFindAuthor()">Search</button> </div></div><br/> <table id="EditBookAuthorsResultsTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>ID</th> <th>Name</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> <br/> <label for="EditBookDewey">Dewey</label> <input type="text" class="custom-form-control" id="EditBookDewey" placeholder="Enter Book Dewey"> <label for="EditBookISBN">ISBN</label> <input type="text" class="custom-form-control" id="EditBookISBN" placeholder="Enter Book ISBN"> <label for="EditBookQuantity">Quantity</label> <input type="number" class="custom-form-control" id="EditBookQuantity" placeholder="Enter Book Quantity"> </form> </div></div>', '<button type="button" class="btn btn-block btn-primary" style="width: 100%" onclick="SubmitChanges();">Submit Changes</button>'));
	AddCard(CreateCard('NewBookCard', 'NewBook', 'New Book', 'dark', '<div class="row"> <div class="col-12"> <form> <label for="NewBookIdentifier">Identifier</label> <input type="text" class="custom-form-control" id="NewBookIdentifier" placeholder="Book Identifier"> <label for="NewBookTitle">Title</label> <input type="text" class="custom-form-control" id="NewBookTitle" placeholder="Enter Book Title"> <label for="NewBookAuthors">Authors</label> <input type="text" class="custom-form-control" id="NewBookAuthors" placeholder="Enter Book Authors" hidden> <table id="NewBookAuthorsTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>ID</th> <th>Name</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> <label for="NewBookAddAuthor">Add Author</label> <div class="row"> <div class="col-9"> <input type="text" class="form-control" id="NewBookAddAuthor" placeholder="Add Author"> </div><div class="col-3"> <button type="button" class="btn btn-block btn-primary" onclick="NewBookFindAuthor()">Search</button> </div></div><br/> <table id="NewBookAuthorsResultsTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>ID</th> <th>Name</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> <br/> <label for="NewBookDewey">Dewey</label> <input type="text" class="custom-form-control" id="NewBookDewey" placeholder="Enter Book Dewey"> <label for="NewBookISBN">ISBN</label> <input type="text" class="custom-form-control" id="NewBookISBN" placeholder="Enter Book ISBN"> <label for="NewBookQuantity">Quantity</label> <input type="number" class="custom-form-control" id="NewBookQuantity" placeholder="Enter Book Quantity"> </form> </div></div>', '<button type="button" class="btn btn-block btn-primary" style="width: 100%" onclick="AddBook();">Add Book</button>'));
	AddCard(CreateCard('EditUserCard', 'EditUser', 'Edit User', 'dark', '<div class="row"> <div class="col-9"> <input type="text" class="form-control" id="EditUserSearch" placeholder="Search User" autocomplete="off" onkeypress="javascript: if(event.keyCode==13) EditUserFindUser();"> </div><div class="col-3"> <button type="button" class="btn btn-block btn-primary" onclick="EditUserFindUser()">Find User</button> </div></div><br/><div class="row"> <div class="col-12"> <table id="EditUserSearchUserResultsTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>ID</th> <th>Name</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> </div></div><br/><div class="row"> <div class="col-12"> <form> <label for="EditUserIdentifier">Identifier</label> <input type="text" class="custom-form-control" id="EditUserIdentifier" placeholder="User Identifier" readonly disabled> <label for="EditUserName">Name</label> <input type="text" class="custom-form-control" id="EditUserName" placeholder="Enter User Name"> <label for="EditUserUsername">Username</label> <input type="text" class="custom-form-control" id="EditUserUsername" placeholder="Enter Username"> <label for="EditUserEmail">Email</label> <input type="text" class="custom-form-control" id="EditUserEmail" placeholder="Enter User Email"> <label for="EditUserLevel">Level</label> <select class="custom-form-control" id="EditUserLevel"> <option value="0">Admin</option> <option value="1">Super User</option> <option value="2">User</option> </select> <label for="EditUserGrade">Level</label> <select class="custom-form-control" id="EditUserGrade"> <option value="No education status">No education status</option> <option value="1st Grade">1st Grade</option> <option value="2nd Grade">2nd Grade</option> <option value="3rd Grade">3rd Grade</option> <option value="4th Grade">4th Grade</option> <option value="5th Grade">5th Grade</option> <option value="6th Grade">6th Grade</option> <option value="7th Grade">7th Grade</option> <option value="8th Grade">8th Grade</option> <option value="9th Grade">9th Grade</option> <option value="10th Grade">10th Grade</option> <option value="11th Grade">11th Grade</option> <option value="12th Grade">12th Grade</option> <option value="Teacher-Professor">Teacher-Professor</option> <option value="University">University</option> </select> </form> </div></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-danger" style="width: 100%" onclick="EditUserRemoveUser();EditUserSetValues();">Remove User</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" style="width: 100%" onclick="EditUserSaveUser();EditUserSetValues();">Save User</button></div></div>'));
	AddCard(CreateCard('NewUserCard', 'NewUser', 'New User', 'dark', '<div class="row"> <div class="col-12"> <form> <label for="NewUserIdentifier">Identifier</label> <input type="text" class="custom-form-control" id="NewUserIdentifier" placeholder="User Identifier"> <label for="NewUserName">Name</label> <input type="text" class="custom-form-control" id="NewUserName" placeholder="Enter User Name"> <label for="NewUserUsername">Username</label> <input type="text" class="custom-form-control" id="NewUserUsername" placeholder="Enter Username"> <label for="NewUserEmail">Email</label> <input type="text" class="custom-form-control" id="NewUserEmail" placeholder="Enter User Email"> <label for="NewUserPassword">Password</label> <input type="text" class="custom-form-control" id="NewUserPassword" placeholder="Enter User Password"></input> <label for="NewUserPasswordDup">Re-enter password</label> <input type="text" class="custom-form-control" id="NewUserPasswordDup" placeholder="Re-enter User Password"></input> <label for="NewUserLevel">Level</label> <select class="custom-form-control" id="NewUserLevel"> <option value="0">Admin</option> <option value="1">Super User</option> <option value="2">User</option> </select> <label for="NewUserGrade">Level</label> <select class="custom-form-control" id="NewUserGrade"> <option value="No education status">No education status</option> <option value="1st Grade">1st Grade</option> <option value="2nd Grade">2nd Grade</option> <option value="3rd Grade">3rd Grade</option> <option value="4th Grade">4th Grade</option> <option value="5th Grade">5th Grade</option> <option value="6th Grade">6th Grade</option> <option value="7th Grade">7th Grade</option> <option value="8th Grade">8th Grade</option> <option value="9th Grade">9th Grade</option> <option value="10th Grade">10th Grade</option> <option value="11th Grade">11th Grade</option> <option value="12th Grade">12th Grade</option> <option value="Teacher-Professor">Teacher-Professor</option> <option value="University">University</option> </select> </form> </div></div>', '<button type="button" class="btn btn-block btn-primary" style="width: 100%" onclick="NewUserCreateUser();NewUserSetValues();">Create User</button>'));
	AddCard(CreateCard('ActiveChargesListCard','ActiveChargesList', 'Active Charges', 'dark', '<div class="col-12 table-responsive"><table id="ActiveChargesTable" class="table table-bordered table-striped"><thead><tr><th>Identifier</th><th>Title</th><th>User Name</th><th>Borrowing Date</th><th>Action</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipCharges >= 20) { SkipCharges = SkipCharges - 20; ShowCharges(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipCharges = SkipCharges + 20; ShowCharges();">Next Page</button></div></div>'));
	AddCard(CreateCard('AllChargesListCard','AllChargesList', 'All Charges', 'dark', '<div class="col-12 table-responsive"><table id="AllChargesTable" class="table table-bordered table-striped"><thead><tr><th>Identifier</th><th>Title</th><th>User Name</th><th>Borrowing Date</th><th>Return Date</th><th>Active</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipCharges >= 20) { SkipCharges = SkipCharges - 20; ShowCharges(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipCharges = SkipCharges + 20; ShowCharges();">Next Page</button></div></div>'));


	
	{
		let element = document.getElementById('NewUserLevel');
		for (let i = 0; i < element.length; i++)
		{
			if (parseInt(element.options[i].value) <= level)
				element.remove(i--);
		}
	}

	{
		let element = document.getElementById('EditUserLevel');
		for (let i = 0; i < element.length; i++)
		{
			if (parseInt(element.options[i].value) <= level)
				element.remove(i--);
		}
	}

	BorrowBookUserIdentifierLock = $('#BorrowBookUserIdentifierLock');
	BorrowBookUserName = $('#BorrowBookUserName');

	ReloadView();
});