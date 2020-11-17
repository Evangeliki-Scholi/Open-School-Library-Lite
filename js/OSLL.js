const ChargesAPIV2 = 'API/V2/Charge.php';

HideFunctions.push(EmptyBorrowBook);
HideFunctions.push(ResetCharges);
ShowFunctions.push(ShowCharges);
ShowFunctions.push(ShowAuthor);


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
		GetAuthors(JSON.parse(data['data']['AuthorIDs']), row.insertCell(3), true);
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
	if (BorrowBookBookIdentifier)
	{
		BorrowBookBookIdentifier.value = '';
		BorrowBookUserIdentifierLock.value = '';
		BorrowBookUserIdentifier.value = '';
	}
	if (BorrowBookUserName)
		BorrowBookUserName.value = '';
}

async function ChargeBooks()
{
	if (BorrowBookUserName.value == '')
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
			data: { type : 'AddCharge', BookIdentifier : BookData[1], UserIdentifier : BorrowBookUserIdentifierLock.value }
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
	AddCard(CreateCard('ReturnBookCard', 'ReturnBook', 'Return Book', 'dark', '<div class="row"><div class="col-9"> <input type="text" class="form-control" placeholder="Book Identifier" autocomplete="off"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark">Find Book</button></div></div> <br /><div class="row"><div class="col-12 table-responsive"><table id="BorrowingTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Action</th></tr></thead><tbody></tbody></table></div></div>', '<button type="button" class="btn btn-block btn-dark" style="width: 100%">Return Books</button>'));
	AddCard(CreateCard('ActiveChargesListCard','ActiveChargesList', 'Active Charges', 'dark', '<div class="col-12 table-responsive"><table id="ActiveChargesTable" class="table table-bordered table-striped"><thead><tr><th>Identifier</th><th>Title</th><th>User Name</th><th>Borrowing Date</th><th>Action</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipCharges >= 20) { SkipCharges = SkipCharges - 20; ShowCharges(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipCharges = SkipCharges + 20; ShowCharges();">Next Page</button></div></div>'));
	AddCard(CreateCard('AllChargesListCard','AllChargesList', 'All Charges', 'dark', '<div class="col-12 table-responsive"><table id="AllChargesTable" class="table table-bordered table-striped"><thead><tr><th>Identifier</th><th>Title</th><th>User Name</th><th>Borrowing Date</th><th>Return Date</th><th>Active</th></tr></thead><tbody></tbody></table></div>', '<div class="row"><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="if (SkipCharges >= 20) { SkipCharges = SkipCharges - 20; ShowCharges(); }">Previous Page</button></div><div class="col-6"><button type="button" class="btn btn-block btn-primary" onclick="SkipCharges = SkipCharges + 20; ShowCharges();">Next Page</button></div></div>'));

	BorrowBookBookIdentifier = document.getElementById('BorrowBookBookIdentifier');
	BorrowBookUserIdentifier = document.getElementById('BorrowBookUserIdentifier');
	BorrowBookUserIdentifierLock = document.getElementById('BorrowBookUserIdentifierLock');
	BorrowBookUserName = document.getElementById('BorrowBookUserName');

	ReloadView();
});