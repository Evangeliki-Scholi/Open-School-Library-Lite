const ChargesAPIV2 = 'API/V2/Charge.php';

HideFunctions.push(EmptyBorrowBook);
HideFunctions.push(ResetCharges);
ShowFunctions.push(ShowCharges);
ShowFunctions.push(ShowAuthor);


var BorrowBookListNumber = 1;
function BorrowBookFindBook()
{
	if (BorrowBookBookIdentifier.val().length < 3)
		return;
	
	$.post(BookAPIV2, { 'type' : 'GetBook', 'Identifier' : BorrowBookBookIdentifier.val() }, function (data)
	{
		var table = document.getElementById('BorrowingTable').children[1];
		var row = table.insertRow(table.rows.length);
		row.insertCell(0).innerText = BorrowBookListNumber++;
		row.insertCell(1).innerText = data['data']['Identifier'];
		row.insertCell(2).innerText = data['data']['Title'];
		GetAuthors(JSON.parse(data['data']['AuthorIDs']), row.insertCell(3), true);
		row.insertCell(4).innerText = 'Cancel';

		BorrowBookBookIdentifier.val('');
	});
}

function BorrowBookFindUser()
{
	if (BorrowBookUserIdentifier.val() == '')
		return;

	$.post(UserAPIV2, { type : 'GetUser', Identifier : BorrowBookUserIdentifier.val() }, function (data)
	{
		if (data.hasOwnProperty('error'))
			return;

		BorrowBookUserName.val(data['data']['Name']);
		BorrowBookUserIdentifierLock.val(data['data']['Identifier']);
		BorrowBookUserIdentifier.val('');
	});
}

function EmptyBorrowBook()
{
	$('#BorrowingTable tbody tr').remove();
	BorrowBookListNumber = 1;
	if (document.getElementById('BorrowBookBookIdentifier'))
	{
		BorrowBookBookIdentifier.val('');
		BorrowBookUserIdentifierLock.val('');
		BorrowBookUserIdentifier.val('');
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

	const numberOfBooks = document.getElementById('BorrowingTable').children[1].children.length;
	for (var i = 0; i < numberOfBooks; i++)
	{
		var BookData = document.getElementById('BorrowingTable').children[1].children[i].innerText.split('\t');
		if (BookData.length < 2 || BookData[1] == '')
			continue;

		var Response = await $.ajax({
			url: ChargesAPIV2, 
			type: 'POST',
			data: { type : 'AddCharge', BookIdentifier : BookData[1], UserIdentifier : BorrowBookUserIdentifierLock.val() }
		});

		if (Response.hasOwnProperty('error'))
			console.error('An error has occured');
	}

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
					row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-danger" onclick="RemoveAuthor(' + authordata['ID'] + ');">Remove</button>';
				});
			}
		}
		catch { }
	});
}

function UpdateAuthors() {
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
			row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-danger" onclick="RemoveAuthor(' + data['ID'] + ');">Remove</button>';
		});
	}
}

function AddAuthor(authorID) {
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

	UpdateAuthors();
}

function RemoveAuthor(authorID) {
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

	UpdateAuthors();
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
				row.insertCell(3).innerHTML = '<buton class="btn btn-block btn-primary" onclick="AddAuthor(\'' + data[i]['ID'] + '\');">Add</button>';
			}
		}
	});
}

async function SubmitChanges() {
	if (!$('#EditBookId').val())
		return;

	$.post(BookAPIV2, { 'type' : 'EditBook', 'Identifier' : $('#EditBookId').val(), 'Title': $('#EditBookTitle').val(), 'AuthorIDs' : $('#EditBookAuthors').val(), 'Dewey' : $('#EditBookDewey').val(), 'ISBN' : $('#EditBookISBN').val(), 'Quantity' : $('#EditBookQuantity').val() }, function(data) {
		console.log(data);
		if (data['response'] == true) {
			$('#EditBookBookIdentifier').val('');
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

var BorrowBookBookIdentifier;
var BorrowBookUserIdentifier;
var BorrowBookUserIdentifierLock;
var BorrowBookUserName;

$(function()
{
	AddCard(CreateCard('ChargeBookCard', 'ChargeBook', 'Charge Book', 'dark', '<div class="row"><div class="col-9"> <input type="text" class="form-control" id="BorrowBookBookIdentifier" placeholder="Book Identifier" autocomplete="off"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark" onclick="BorrowBookFindBook()">Find Book</button></div></div> <br /><div class="row"><div class="col-12 table-responsive"><table id="BorrowingTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Action</th></tr></thead><tbody></tbody></table></div></div> <br /><div class="row"><div class="col-9"> <input type="text" class="form-control" id="BorrowBookUserIdentifier" placeholder="User Identifier" autocomplete="off"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark" onclick="BorrowBookFindUser()">Find User</button></div></div> <br /><div class="row"><div class="col-12" hidden><input type="text" class="form-control" id="BorrowBookUserIdentifierLock" readonly disabled></div></div><div class="row"><div class="col-12"> <input type="text" class="form-control" id="BorrowBookUserName" placeholder="User Name" readonly disabled></div></div>', '<button type="button" class="btn btn-block btn-dark" style="width: 100%" onclick="ChargeBooks()">Charge</button>'));
	AddCard(CreateCard('EditBookCard', 'EditBook', 'Edit Book', 'dark', '<div class="row"> <div class="col-9"> <input type="text" class="form-control" id="EditBookIdentifier" placeholder="Book Identifier" autocomplete="off"> </div><div class="col-3"> <button type="button" class="btn btn-block btn-dark" onclick="EditBookFindBook()">Find Book</button> </div></div><br/><div class="row"> <div class="col-12"> <form> <label for="EditBookIdentifier">Identifier</label> <input type="text" class="custom-form-control" id="EditBookId" placeholder="Book Identifier" readonly disabled> <label for="EditBookTitle">Title</label> <input type="text" class="custom-form-control" id="EditBookTitle" placeholder="Enter Book Title"> <label for="EditBookAuthors">Authors</label> <input type="text" class="custom-form-control" id="EditBookAuthors" placeholder="Enter Book Authors" hidden> <table id="EditBookAuthorsTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>ID</th> <th>Name</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> <label for="EditBookAddAuthor">Add Author</label> <div class="row"> <div class="col-9"> <input type="text" class="form-control" id="EditBookAddAuthor" placeholder="Add Author"> </div><div class="col-3"> <button type="button" class="btn btn-block btn-dark" onclick="EditBookFindAuthor()">Search</button> </div></div><br/> <table id="EditBookAuthorsResultsTable" class="table table-bordered table-striped"> <thead> <tr> <th>#</th> <th>ID</th> <th>Name</th> <th>Action</th> </tr></thead> <tbody></tbody> </table> <br/> <label for="EditBookDewey">Dewey</label> <input type="text" class="custom-form-control" id="EditBookDewey" placeholder="Enter Book Dewey"> <label for="EditBookISBN">ISBN</label> <input type="text" class="custom-form-control" id="EditBookISBN" placeholder="Enter Book ISBN"> <label for="EditBookQuantity">Quantity</label> <input type="number" class="custom-form-control" id="EditBookQuantity" placeholder="Enter Book Quantity"> </form> </div></div>', '<button type="button" class="btn btn-block btn-dark" style="width: 100%" onclick="SubmitChanges();">Submit Changes</button>'));
	AddCard(CreateCard('ReturnBookCard', 'ReturnBook', 'Return Book', 'dark', '<div class="row"><div class="col-9"> <input type="text" class="form-control" placeholder="Book Identifier" autocomplete="off"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark">Find Book</button></div></div> <br /><div class="row"><div class="col-12 table-responsive"><table id="BorrowingTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Action</th></tr></thead><tbody></tbody></table></div></div>', '<button type="button" class="btn btn-block btn-dark" style="width: 100%">Return Books</button>'));
	AddCard(CreateCard('ActiveChargesListCard','ActiveChargesList', 'Active Charges', 'dark', '<div class="col-12 table-responsive"><table id="ActiveChargesTable" class="table table-bordered table-striped"><thead><tr><th>Identifier</th><th>Title</th><th>User Name</th><th>Borrowing Date</th><th>Action</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipCharges >= 20) { SkipCharges = SkipCharges - 20; ShowCharges(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipCharges = SkipCharges + 20; ShowCharges();">Next Page</button></div></div>'));
	AddCard(CreateCard('AllChargesListCard','AllChargesList', 'All Charges', 'dark', '<div class="col-12 table-responsive"><table id="AllChargesTable" class="table table-bordered table-striped"><thead><tr><th>Identifier</th><th>Title</th><th>User Name</th><th>Borrowing Date</th><th>Return Date</th><th>Active</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipCharges >= 20) { SkipCharges = SkipCharges - 20; ShowCharges(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipCharges = SkipCharges + 20; ShowCharges();">Next Page</button></div></div>'));

	BorrowBookBookIdentifier = $('#BorrowBookBookIdentifier');
	BorrowBookUserIdentifier = $('#BorrowBookUserIdentifier');
	BorrowBookUserIdentifierLock = $('#BorrowBookUserIdentifierLock');
	BorrowBookUserName = $('#BorrowBookUserName');

	ReloadView();
});