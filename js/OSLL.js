
const UserAPIV2 = 'API/V2/User.php';
const BookAPIV2 = 'API/V2/Book.php';
const AuthorAPIV2 = 'API/V2/Author.php';

var HideFunctions = [EmptyBorrowBook];
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
    console.log(AuthorIDs);

    for (var i = 0; i < AuthorIDs.length; i++)
    {
        $.post(AuthorAPIV2, { type : 'GetAuthor', Identifier : AuthorIDs[i]}, function(data)
        {
            console.log(data);
            if (GetHTMLWithLinks == false)
                ElementToPutIn.innerText += data['data']['Name'] + ' - ';
            else
                ElementToPutIn.innerHTML += '<a href="#' + data['data']['Name'] + '">' + data['data']['Name'] + '</a>';
        });
    }
}

var BorrowBookListNumber = 1;
function BorrowBookFindBook()
{
    if (BorrowBookIdentifier.value.length < 3)
        return
    
    $.post(BookAPIV2, { 'type' : 'GetBook', 'Identifier' : BorrowBookIdentifier.value }, function(data)
    {
        var table = document.getElementById('BorrowingTable').children[1];
        var row = table.insertRow(table.rows.length);
        row.insertCell(0).innerText = BorrowBookListNumber++;
        row.insertCell(1).innerText = data['data']['Identifier'];
        row.insertCell(2).innerText = data['data']['Title'];
        GetAuthors(JSON.parse(data['data']['AuthorIDs']), row.insertCell(3), false);
        row.insertCell(4).innerText = 'Cancel';

        BorrowBookIdentifier.value = '';
    });
}

function EmptyBorrowBook()
{
    $('#BorrowingTable tbody tr').remove();
    BorrowBookIdentifier.value = '';

}

var BorrowBookIdentifier;
var BorrowBookFindBookBtn;

$(function()
{
    AddCard(CreateCard('BorrowBookCard', 'BorrowBook', 'Borrow Book', 'dark', '<div class="row"><div class="col-9"> <input type="text" class="form-control" id="BorrowBookIdentifier" placeholder="Book Identifier"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark" id="BorrowBookFindBookBtn">Find Book</button></div></div> <br /><div class="row"><div class="col-12 table-responsive"><table id="BorrowingTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Action</th></tr></thead><tbody></tbody></table></div></div> <br /><div class="row"><div class="col-9"> <input type="text" class="form-control" placeholder="User Identifier"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark">Find User</button></div></div> <br /><div class="row"><div class="col-12"> <input type="text" class="form-control" placeholder="User Name" readonly disabled></div></div>', '<button type="button" class="btn btn-block btn-dark" id="BorrowBookBtn" style="width: 100%">Charge</button>'));
    AddCard(CreateCard('ReturnBookCard', 'ReturnBook', 'Return Book', 'dark', '<div class="row"><div class="col-9"> <input type="text" class="form-control" placeholder="Book Identifier"></div><div class="col-3"> <button type="button" class="btn btn-block btn-dark">Find Book</button></div></div> <br /><div class="row"><div class="col-12 table-responsive"><table id="BorrowingTable" class="table table-bordered table-striped"><thead><tr><th>#</th><th>Identifier</th><th>Title</th><th>Author</th><th>Action</th></tr></thead><tbody></tbody></table></div></div>', '<button type="button" class="btn btn-block btn-dark" style="width: 100%">Return Books</button>'));

    BorrowBookIdentifier = document.getElementById('BorrowBookIdentifier');
    BorrowBookFindBookBtn = document.getElementById('BorrowBookFindBookBtn');

    window.addEventListener("hashchange", ReloadView, false);
    ReloadView();

    BorrowBookFindBookBtn.onclick = BorrowBookFindBook;
});