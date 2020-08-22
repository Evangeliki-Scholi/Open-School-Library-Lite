function HideEverything()
{
    $('#indexPage').css('visibility', 'collapse');
    $('#indexPage').css('height', '0');
    $('#loginPage').css('visibility', 'collapse');
    $('#loginPage').css('height', '0');
    $('#accountPage').css('visibility', 'collapse');
    $('#accountPage').css('height', '0');
    $('#borrowBookPage').css('visibility', 'collapse');
    $('#borrowBookPage').css('height', '0');
    $('#addBookPage').css('visibility', 'collapse');
    $('#addBookPage').css('height', '0');
}

function ShowIndex()
{
    HideEverything();
    $('#indexPage').css('visibility', 'visible');
    $('#tagBook').focus();
}

function ShowLogin()
{
    HideEverything();
    $('#loginPage').css('visibility', 'visible');
    $('#LoginUsername').focus();
}

function ShowAccount()
{
    HideEverything();
    $('#accountPage').css('visibility', 'visible');
}

function ShowBorrow()
{
    HideEverything();
    $('#borrowBookPage').css('visibility', 'visible');
}

function ShowAddBook()
{
    HideEverything();
    $('#addBookPage').css('visibility', 'visible');
}

const loginAPIURL = 'login.php';
const bookAPIURL = 'book.php';
const userAPIURL = 'user.php';

const BOOKAPI_V1 = '/API/V1/Book.php';

async function GetUserHashingAlgorithm(username)
{
    return await Promise.resolve($.post(loginAPIURL, { type : 'GetAlgo', username : username }));
}

async function Login()
{
    var username = $('#LoginUsername').val();
    var password = $('#LoginPassword').val();

    var hashingAlgorithm = null;
    var data = await GetUserHashingAlgorithm(username);
    if ( data === '{}') hashingAlgorithm = -1;
    else if (data === '') hashingAlgorithm = -2;
    else
    {
        try
        {
            data = JSON.parse(data);
            if (!data.hasOwnProperty('response') || data.hasOwnProperty('error')) return -2;
            hashingAlgorithm = data['response'];
        }
        catch
        {
            hashingAlgorithm = -2;
        }
    }

    if (hashingAlgorithm === -1)
    {
        ShowError('User does not exist');
        return false;
    }
    else if (hashingAlgorithm === -2)
    {
        ShowError('Internal server error');
        return false;
    }
    else if (hashingAlgorithm === -3 || hashingAlgorithm === null)
    {
        ShowError('Webclient error');
        return false;
    }

    switch (hashingAlgorithm)
    {
        case 'sha256':
            password = sha512_256(password);
            break;
        case 'sha512':
            password = sha512(password);
            break;
        default:
            ShowError('Unrecognizable hashing algorithm. Please contact server Admins.');
            return false;
    }

    $.post(loginAPIURL, { type : 'LogIn', username : username, password : password }, function(data)
    {
        if (data === '{}' || data === '')
        {
            ShowError('Internal server error');
            return false;
        }
        try
        {
            data = JSON.parse(data);
            if (!data.hasOwnProperty('response') || data.hasOwnProperty('error'))
            {
                ShowError('Internal server error');
                return false;
            }
            if (data['response'] === '')
            {
                ShowError('Internal server error');
                return false;
            }
            else if (data['response'] === true)
                window.location.href = 'index.php';
            else if (data['response'] === false)
                ShowInfo('Wrong credentials');
        }
        catch
        {

        }
    }).fail(function()
    {
        ShowError('Webclient error');
        return false;
    });

    return false;
}

function LogOut()
{
    window.location.href = 'logout.php';
}

function SaveInfos()
{
    var name = $('#AccountName').val();
    var username = $('#AccountUsername').val();
    var newPassword = $('#AccountNewPassword').val();
    if (newPassword === undefined)
        newPassword = '';
    if (newPassword != '')
        newPassword = sha512_256(newPassword);

    if (name == '' || username == '')
    {
        ShowError('Username and Name can not be empty');
        return;
    }

    $.post('account.php', { Name : name, Username : username, New_Password : newPassword, Algo : 'sha256' }, function(data)
    {
        try
        {
            data = JSON.parse(data);
            if (!data.hasOwnProperty('response') || data.hasOwnProperty('error'))
            {
                ShowError('Internal server error 1');
                return;
            }
            else if (data['response'] !== true)
            {
                ShowError('There was a problem with changing your Informations');
                return;
            }
            else
            {
                ShowSuccess('Your informations were updated successfully');
                return;
            }
            return;
        }
        catch
        {
            ShowError('Internal server error');
            return;
        }
    }).fail(function()
    {
        ShowError('Webclient error');
        return;
    });
}

function FindBorrowBook()
{
    var bookBarcode = $('#BorrowBookBarcode').val();
    if (bookBarcode === '' || bookBarcode === undefined)
    {
        $('#BorrowBookID').val('');
        $('#BorrowBookTitle').val('');
        $('#BorrowBookAuthor').val('');
        ShowError('Book barcode can not be empty');
        return;
    }
    $.post(BOOKAPI_V1, { type : 'GetBook', Identifier : bookBarcode }, function(data)
    {
        console.log(data);
        try
        {
            data = JSON.parse(data);
            if (data['response'] == true)
            {
                $('#BorrowBookID').val(data['Identifier']);
                $('#BorrowBookTitle').val(data['Title']);
                $('#BorrowBookAuthor').val(data['Author']);
            }
            else
            {
                $('#BorrowBookID').val('');
                $('#BorrowBookTitle').val('');
                $('#BorrowBookAuthor').val('');
                ShowError('Internal server error');
                return;
            }
            return;
        }
        catch
        {
            $('#BorrowBookID').val('');
            $('#BorrowBookTitle').val('');
            $('#BorrowBookAuthor').val('');
            ShowError('Internal server error');
            return;
        }
    }).fail(function()
    {
        $('#BorrowBookID').val('');
        $('#BorrowBookTitle').val('');
        $('#BorrowBookAuthor').val('');
        ShowError('Webclient error');
        return;
    });
}

function FindBorrowUser()
{
    var userID = $('#BorrowUserID').val();
    if (userID === '' || userID === undefined)
    {
        $('#BorrowUserName').val('');
        ShowError('User ID can not be empty');
        return;
    }
    $.post(userAPIURL, { type : 'getUser', format : 'json', userID : userID }, function(data)
    {
        try
        {
            if (!data.hasOwnProperty('Name') || data.hasOwnProperty('error'))
            {
                $('#BorrowUserName').val('');
                ShowError('Webclient error');
                return;
            }
            $('#BorrowUserName').val(data['Name']);
        }
        catch
        {
            $('#BorrowUserName').val('');
            ShowError('Webclient error');
            return;
        }
    }).fail(function()
    {
        $('#BorrowUserName').val('');
        ShowError('Webclient error');
        return;
    });

}

function BorrowBook()
{
    var bookID = $('#BorrowBookID').val();
    var userID = $('#BorrowUserID').val();

    if (bookID === '' || bookID === undefined || userID === '' || userID === undefined)
    {
        ShowInfo('A valid book barcode and a valid user ID need to be filled');
        return;
    }

    $.post(bookAPIURL, { type : 'borrowBook', bookID : bookID, userID : userID}, function(data)
    {
        try
        {
            if (!data.hasOwnProperty('response') || data.hasOwnProperty('error'))
            {
                ShowError('Internal server error');
                return;
            }
            if (data['response'] === true)
            {
                ShowSuccess('Book successfully booked');
                $('#BorrowBookID').val();
                $('#BoorowUserName').val('');
                $('#BorrowBookBarcode').val('');
                $('#BorrorBookID').val('');
                $('#BorrowBookTitle').val('');
                $('#BorrowBookAuthor').val('');
            }
            else if (data['response'] === false)
            {
                ShowInfo('Book was not successfully booked');
            }
            else
            {
                ShowError('Internal server error');
                return;
            }
        }
        catch
        {
            ShowError('Internal server error');
            return;
        }
    }).fail(function()
    {
        ShowError('Webclient error 1');
        return;
    });
}

function AddBook()
{
    $.post(BOOKAPI_V1, { type : 'AddBook', Identifier : $('#id').val(), Title : $('#title').val(), Author : $('#author').val(), Dewey : $('#dewey').val(), ISBN : $('#ISBN').val() })
        .done(function(data)
        {
            if (data["response"] == true && data['error'] == undefined)
                ShowSuccess("Book added successfully");
            else
                ShowError(data['error']);
            return false;
        });
    return false;
}