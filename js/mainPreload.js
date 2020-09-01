var itemsToHide = [ 'index', 'search', 'login', 'account', 'borrowBook', 'returnBook', 'editBook', 'addBook', 'addUser', 'editUser' ];

function HideEverything()
{
    for (var i = 0; i < itemsToHide.length; i++)
    {
        $('#' + itemsToHide[i] + 'Page').css('visibility', 'collapse');
        $('#' + itemsToHide[i] + 'Page').css('height', '0');
    }
}

function ShowIndex()
{
    HideEverything();
    window.location.hash = '#Index';
    $('#indexPage').css('visibility', 'visible');
    $('#tagBook').focus();
}

function ShowSearch()
{
    HideEverything();
    $('#searchPage').css('visibility', 'visible');
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
    window.location.hash = '#Account';
    $('#accountPage').css('visibility', 'visible');
}

function ShowBorrowBook()
{
    HideEverything();
    window.location.hash = '#BorrowBook';
    $('#borrowBookPage').css('visibility', 'visible');
}

function ShowReturnBook()
{
    HideEverything();
    window.location.hash = '#ReturnBook';
    $('#returnBookPage').css('visibility', 'visible');
}

function ShowEditBook()
{
    HideEverything();
    window.location.hash = '#EditBook';
    $('#editBookPage').css('visibility', 'visible');
}

function ShowAddBook()
{
    HideEverything();
    window.location.hash = '#AddBook';
    $('#addBookPage').css('visibility', 'visible');
}

function ShowAddUser()
{
    HideEverything();
    window.location.hash = '#AddUser';
    $('#addUserPage').css('visibility', 'visible');
}

function ShowEditUser()
{
    HideEverything();
    window.location.hash = '#EditUser';
    $('#editUserPage').css('visibility', 'visible');
}

const BOOKAPI_V1 = '/API/V1/Book.php';
const USERSAPI_V1 = '/API/V1/Users.php';

function AddBook()
{
    $.post(BOOKAPI_V1, { type : 'AddBook', Identifier : $('#AddBookID').val(), Title : $('#AddBookTitle').val(), Author : $('#AddBookAuthor').val(), Dewey : $('#AddBookDewey').val(), ISBN : $('#AddBookISBN').val() })
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

function AddUser()
{
    var setValues = function(UserIdentifier, UserName, UserUsername, UserEmail, UserPassword)
    {
        $('#AddUserIdentifier').val(UserIdentifier);
        $('#AddUserName').val(UserName);
        $('#AddUserUsername').val(UserUsername);
        $('#AddUserEmail').val(UserEmail);
        $('#AddUserPassword').val(UserPassword);
    }

    var message = '';
    if ($('#AddUserIdentifier').val() == '')
        message += 'User Identifier can not be empty';
    if ($('#AddUserName').val() == '')
        message += ((message.length != 0) ? '<br />' : '') + 'User Name can not be empty';
    if ($('#AddUserUsername').val() == '')
        message += ((message.length != 0) ? '<br />' : '') + 'User Username can not be empty';
    if ($('#AddUserPassword').val() == '')
        message += ((message.length != 0) ? '<br />' : '') + 'User Password can not be empty';
    if ($('#AddUserGrade').val() == 'Please choose education "grade"')
        message += ((message.length != 0) ? '<br />' : '') + 'User Grade can not be empty';

    if (message.length != 0)
    {
        ShowError(message);
        return true;
    }

    var Identifier = $('#AddUserIdentifier').val();
    var Name = $('#AddUserName').val();
    var Username = $('#AddUserUsername').val();
    var Email = $('#AddUserEmail').val();
    var Password = sha512_256($('#AddUserPassword').val());
    var Grade = $('#AddUserGrade').val();

    $.post(USERSAPI_V1, { type : 'AddUser', Identifier : Identifier, Name : Name, Username : Username, Email : Email, Password : Password, Algo : "sha256", Grade : Grade })
        .done(function(data)
        {
            if (data.hasOwnProperty('error'))
                ShowError(data['error']);
            else if (data['response'] == false)
                ShowError('Unexpected error');
            else
            {
                setValues('', '', '', '', '');
                ShowInfo('User was added successfully');
            }
        })
        .fail(function()
        {
            ShowError('Webclient error');
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

    $.post(BOOKAPI_V1, { type : 'BorrowBook', Identifier : bookID, UserID : userID}, function(data)
    {
        try
        {
            if (data.hasOwnProperty('error'))
            {
                ShowError(data['error']);
                return;
            }
            if (data['response'] === true)
            {
                ShowSuccess('Book successfully booked');
                $('#BorrowBookID').val();
                $('#BoorowUserName').val('');
                $('#BorrowBookIdentifier').val('');
                $('#BorrorBookID').val('');
                $('#BorrowBookTitle').val('');
                $('#BorrowBookAuthor').val('');
            }
            else
            {
                ShowInfo('Book was not successfully booked');
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

function FindBorrowBook()
{
    var bookIdentifer = $('#BorrowBookIdentifier').val();
    if (bookIdentifer === '' || bookIdentifer === undefined)
    {
        $('#BorrowBookID').val('');
        $('#BorrowBookTitle').val('');
        $('#BorrowBookAuthor').val('');
        ShowError('Book barcode can not be empty');
        return;
    }
    $.post(BOOKAPI_V1, { type : 'GetBook', Identifier : bookIdentifer }, function(data)
    {
        try
        {
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
    $.post(USERSAPI_V1, { type : 'GetUser', Identifier : userID }, function(data)
    {
        try
        {
            if (!data.hasOwnProperty('data') || data.hasOwnProperty('error'))
            {
                $('#BorrowUserName').val('');
                ShowError('Webclient error');
                return;
            }
            $('#BorrowUserName').val(data['data']['Name']);
            $('#BorrowUserGrade').val(data['data']['Grade']);
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

async function FindEditBook()
{
    var setValues = function(BookID, BookTitle, BookAuthor, BookDewey, BookISBN, BookMetadata)
    {
        $('#EditBookID').val(BookID);
        $('#EditBookTitle').val(BookTitle);
        $('#EditBookAuthor').val(BookAuthor);
        $('#EditBookDewey').val(BookDewey);
        $('#EditBookISBN').val(BookISBN);
        $('#EditBookMetadata').val(BookMetadata);
    }
    var bookIdentifer = $('#EditBookIdentifier').val();
    if (bookIdentifer === '' || bookIdentifer === undefined)
    {
        setValues('', '', '', '', '', '');
        ShowError('Book Identifier can not be empty');
        return false;
    }
    return await $.post(BOOKAPI_V1, { type : 'GetBook', Identifier : bookIdentifer }, function(data)
    {
        try
        {
            if (data['response'] == true)
            {
                setValues(data['Identifier'], data['Title'], data['Author'], data['Dewey'], data['ISBN'], data['Metadata']);
                return true;
            }
            else
            {
                setValues('', '', '', '', '', '');
                ShowError('Internal server error');
                return false;
            }
        }
        catch
        {
            setValues('', '', '', '', '' , '');
            ShowError('Internal server error');
            return false;
        }
    }).fail(function()
    {
        setValues('', '', '', '', '' , '');
        ShowError('Webclient error');
        return false;
    });
}

function FindEditUser()
{
    var Identifier = $('#EditUserIdentifier').val();
    if (Identifier == '')
    {
        ShowError('User Identifier can not be empty');
        return;
    }

    $.post(USERSAPI_V1, { type : 'GetUser', Identifier : Identifier })
        .done(function(data)
        {
            if (data.hasOwnProperty('error'))
                ShowError(data['error']);
            else if (data['response'] == false)
                ShowError('Internal server error');
            else
            {
                $('#EditUserName').val(data['data']['Name']);
                $('#EditUserUsername').val(data['data']['Username']);
                $('#EditUserEmail').val(data['data']['Email']);
                $('#EditUserGrade').val(data['data']['Grade']);
                $('#EditUserMetadata').val(data['data']['Metadata']);
            }
        })
        .fail(function()
        {
            ShowError('Webclient error');
        });
}

async function FindReturnBook()
{
    var setValues = function(BookID, BookTitle, BookAuthor)
    {
        $('#ReturnBookID').val(BookID);
        $('#ReturnBookTitle').val(BookTitle);
        $('#ReturnBookAuthor').val(BookAuthor);
    }
    var bookIdentifer = $('#ReturnBookIdentifier').val();
    if (bookIdentifer === '' || bookIdentifer === undefined)
    {
        setValues('', '', '');
        ShowError('Book Identifier can not be empty');
        return false;
    }
    return await $.post(BOOKAPI_V1, { type : 'GetBook', Identifier : bookIdentifer }, function(data)
    {
        try
        {
            if (data['response'] == true)
            {
                setValues(data['Identifier'], data['Title'], data['Author']);
                return true;
            }
            else
            {
                setValues('', '', '');
                ShowError('Internal server error');
                return false;
            }
        }
        catch
        {
            setValues('', '', '');
            ShowError('Internal server error');
            return false;
        }
    }).fail(function()
    {
        setValues('', '', '');
        ShowError('Webclient error');
        return false;
    });
}

async function GetUserHashingAlgorithm(username)
{
    return await Promise.resolve($.post(USERSAPI_V1, { type : 'GetAlgo', Username : username }));
}

async function LogIn()
{
    var username = $('#LoginUsername').val();
    var password = $('#LoginPassword').val();

    var data = await GetUserHashingAlgorithm(username);
    if (data.hasOwnProperty('error'))
    {
        ShowError(data['error']);
        return false;
    }
    if (data['respone'] == false)
    {
        ShowError('An unexpected error occured');
        return false;
    }

    switch (data['data'])
    {
        case 'sha256':
            password = sha512_256(password);
            break;
        case 'sha512':
            password = sha512(password);
            break;
        default:
            ShowError(data['data'] + 'Unrecognizable hashing algorithm. Please contact server Admins.');
            return false;
    }

    $.post(USERSAPI_V1, { type : 'LogIn', Username : username, Password : password }, function(data)
    {
        if (data.hasOwnProperty('error'))
        {
            ShowError(data['error']);
            return false;
        }
        else if (data['response'] == true)
            window.location.href = 'index.php';
        else
            ShowInfo('Wrong credentials');
    }).fail(function()
    {
        ShowError('Webclient error');
        return false;
    });

    return false;
}

function LogOut()
{
    $.post(USERSAPI_V1, { type : 'LogOut' }, function (data)
    {
        if (data.hasOwnProperty('error'))
            ShowError(data['error']);
        else if (data['response'] == true)
            window.location.href = 'index.php';
        else
            ShowInfo('Something when wrong');
    })
}

function PerformSearch()
{
    if ($('#tagBook').val().trim().length < 2)
    {
        ShowInfo('Search item can not be empty, whitespace or less than two characters');
        return false;
    }

    const SearchTag = $('#tagBook').val();
    var AvailCol = -1;

    $.post(BOOKAPI_V1, { type : 'SearchBooks', SearchTag : SearchTag, Limit : 20, Skip : 0 }, function(data)
    {
        try
        {
            if (data['response'] != true)
            {
                ShowError('Internal server Error');
                return false;
            }
            if (data['data'].length == 0)
            {
                ShowInfo('No results were found');
                return false;
            }

            length = data['data'].length;

            var col = [];
            var j = 0;
            for (var i = 0; i < length; i++)
            {
                for (var key in data['data'][i])
                {
                    if (col.indexOf(key) === -1)
                    {
                        if (key === 'Availability' && AvailCol == -1)
                            AvailCol = j;
                        col.push(key);
                    }
                    j++
                }
            }

            var table = document.createElement('table');
            table.setAttribute('class', 'table table-bordered');

            var tr = table.insertRow(-1);

            for (var i = 0; i < col.length; i++)
            {
                var th = document.createElement('th');
                if (col[i] === 'Availability')
                    th.innerHTML = 'Available';
                else
                    th.innerHTML = col[i];
                
                tr.append(th);
            }

            for (var i = 0; i < length; i++)
            {
                tr = table.insertRow(-1);

                for (var j = 0; j < col.length; j++)
                {
                    var tableCell = tr.insertCell(-1);
                    if (j !== AvailCol)
                        tableCell.innerHTML = data['data'][i][col[j]];
                    else
                        tableCell.innerHTML = (data['data'][i][col[j]] != 0) ? 'Yes' : 'No';
                }
            }

            document.getElementById('searchPage').innerHTML = '';
            document.getElementById('searchPage').appendChild(table);
            ShowSearch();
        }
        catch
        {
            ShowIndex();
            ShowError('An unexpected error occured');
        }
        return false;
    }).fail(function()
    {
        ShowIndex();
        ShowError('Webclient Error');
    });

    return false;
}

function PerformSearchUser()
{
    const SearchTag = $('#EditUserSearchTag').val().trim();
    if (SearchTag.length < 2)
    {
        ShowInfo('Search item can not be empty, whitespace or less than two characters');
        return false;
    }

    document.getElementById('editPageSearchUserResults').innerHTML = '';

    $.post(USERSAPI_V1, { type : 'SearchUsers', SearchTag : SearchTag })
        .done(function(data)
        {
            if (data.hasOwnProperty('error'))
            {
                ShowError(data['error']);
                return;
            }
            else if (data['response'] == false)
            {
                ShowError('Internal server error');
                return;
            }

            if (data['data'].length == 0)
            {
                ShowInfo('No results found');
                return;
            }

            var table = document.createElement('table');
            table.setAttribute('class', 'table table-bordered');
            var tr = table.insertRow(-1);
            var th = document.createElement('th');
            th.innerHTML = 'Identifier';
            tr.append(th);
            th = document.createElement('th');
            th.innerHTML = 'Name';
            tr.append(th);
            th = document.createElement('th');
            th.innerHTML = '';
            tr.append(th);

            for (var i = 0; i < data['data'].length; i++)
            {
                tr = table.insertRow(-1);
                th = document.createElement('th');
                th.innerHTML = data['data'][i]['Identifier'];
                tr.append(th);
                th = document.createElement('th');
                th.innerHTML = data['data'][i]['Name'];
                tr.append(th);
                th = document.createElement('th');
                th.innerHTML = '<button class="btn btn-primary btn-block" onclick="$(\'#EditUserIdentifier\').val(\'' + data['data'][i]['Identifier'] + '\');FindEditUser();$(\'#EditUserSearchTag\').val(\'\');document.getElementById(\'editPageSearchUserResults\').innerHTML = \'\';">Select</button>';
                tr.append(th);
            }

            document.getElementById('editPageSearchUserResults').appendChild(table);
        })
        .fail(function()
        {
            ShowError('WebClient error');
        });
}

function RemoveBook()
{
    var setValues = function(BookID, BookTitle, BookAuthor, BookDewey, BookISBN, BookMetadata)
    {
        $('#EditBookID').val(BookID);
        $('#EditBookTitle').val(BookTitle);
        $('#EditBookAuthor').val(BookAuthor);
        $('#EditBookDewey').val(BookDewey);
        $('#EditBookISBN').val(BookISBN);
        $('#EditBookMetadata').val(BookMetadata);
    }
    $.post(BOOKAPI_V1, { type : 'RemoveBook', Identifier : $('#EditBookID').val() }, function(data)
    {
        if (data.hasOwnProperty('error'))
            ShowError(data['error']);
        else if (data['response'] == false)
            ShowInfo('Internal server error');
        else
        {
            setValues('');
            ShowSuccess('Book deleted sucessfully');
        }
    });
}

function RemoveUser()
{
    FindEditUser();
    var Identifier = $('#EditUserIdentifier').val();
    if (Identifier == '')
    {
        ShowError('Identifier can not be empty');
        return;
    }

    $.post(USERSAPI_V1, { type : 'RemoveUser', Identifier : Identifier})
        .done(function(data)
        {
            if (data.hasOwnProperty('error'))
                ShowError(data['error']);
            else if (data['response'] == false)
                ShowError('Internal server error');
            else
            {
                ShowSuccess('Successfully removed user');
                $('#EditUserIdentifier').val('');
                $('#EditUserName').val('');
                $('#EditUserUsername').val('');
                $('#EditUserEmail').val('');
                $('#EditUserMetadata').val('');
            }
        })
        .fail(function()
        {
            ShowError('WebClient error');
        });
}

async function ReturnBook()
{
    var bookIdentifer = $('#ReturnBookID').val();
    if (bookIdentifer === '' || bookIdentifer === undefined)
    {
        $('#ReturnBookID').val('');
        $('#ReturnBookTitle').val('');
        $('#ReturnBookAuthor').val('');
        ShowError('Book Identifier can not be empty');
        return false;
    }
    $.post(BOOKAPI_V1, { type : 'ReturnBook', Identifier : bookIdentifer }, function(data)
    {
        try
        {
            if (data.hasOwnProperty('error'))
            {
                ShowError(data['error']);
                return;
            }
            else if (data['response'] != true)
            {
                ShowError('Internal server error');
                return false;
            }
            else
            {
                ShowSuccess('Book Successfully returned');
                return true;
            }
        }
        catch
        {
            ShowError('Internal server error');
            return false;
        }
    }).fail(function()
    {
        ShowError('Webclient error');
        return false;
    });
}

async function SaveBookInfo()
{
    var setValues = function(BookID, BookTitle, BookAuthor, BookDewey, BookISBN, BookMetadata)
    {
        $('#EditBookID').val(BookID);
        $('#EditBookTitle').val(BookTitle);
        $('#EditBookAuthor').val(BookAuthor);
        $('#EditBookDewey').val(BookDewey);
        $('#EditBookISBN').val(BookISBN);
        $('#EditBookMetadata').val(BookMetadata);
    }
    const bookIdentifier = $('#EditBookID').val();
    if (bookIdentifier === '' || bookIdentifier === undefined)
    {
        setValues('', '', '', '', '', '');
        ShowError('Book Identifier can not be empty');
        return false;
    }

    const BookTitle = $('#EditBookTitle').val();
    const BookAuthor = $('#EditBookAuthor').val();
    const BookDewey = $('#EditBookDewey').val();
    const BookISBN = $('#EditBookISBN').val();
    var BookMetadata = $('#EditBookMetadata').val();

    try
    {
        BookMetadata = JSON.stringify(JSON.parse(BookMetadata));
    }
    catch
    {
        ShowInfo('Metadata field is not a valid JSON data.<br />Visit <a href="https://www.json.org/json-en.html" target="_blank">JSON.org</a> for more info.');
        return;
    }

    $.post(BOOKAPI_V1, { type : 'EditBook', Identifier : bookIdentifier, Title : BookTitle, Author : BookAuthor, Dewey : BookDewey, ISBN : BookISBN, Metadata : BookMetadata }, function(data)
    {
        try
        {
            if (data.hasOwnProperty('error'))
            {
                ShowError(data['error']);
                return;
            }
            if (data['response'] != true)
            {
                FindEditBook();
                ShowError('Internal server error');
                return false;
            }
            FindEditBook();
            if (BookTitle != $('#EditBookTitle').val() || BookAuthor != $('#EditBookAuthor').val() || BookDewey != $('#EditBookDewey').val() || BookISBN != $('#EditBookISBN').val() || BookMetadata != $('#EditBookMetadata').val())
            {
                setValues(bookIdentifier, BookTitle, BookAuthor, BookDewey, BookISBN, BookMetadata)
                ShowError('Internal server error');
                return false;
            }
        }
        catch
        {
            FindEditBook();
            ShowError('Internal server error');
            return false;
        }
    }).fail(function()
    {
        FindEditBook();
        ShowError('Webclient error');
        return false;
    });
}

function SaveInfos()
{
    var rdata = {};
    rdata['type'] = 'EditSelf';
    rdata['Name'] = $('#AccountName').val();
    rdata['Username'] = $('#AccountUsername').val();
    rdata['Email'] = $('#AccountEmail').val();
    var newPassword = $('#AccountNewPassword').val();
    if (newPassword === undefined)
        newPassword = '';
    if (newPassword != '')
    {
        rdata['Password'] = sha512_256(newPassword);
        rdata['Algo'] = 'sha256';
    }

    if (rdata['Name'] == '' || rdata['Username'] == '')
    {
        ShowError('Username and Name can not be empty');
        return;
    }


    $.post(USERSAPI_V1, rdata, function(data)
    {
        try
        {
            if (data.hasOwnProperty('error'))
                ShowError(data['error']);
            else if (data['response'] !== true)
                ShowError('There was a problem with changing your Informations');
            else
            {
                ShowSuccess('Your informations were updated successfully');
                window.location.href = 'index.php';
            }
            $('AccountNewPassword').val('');
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

function SaveUserInfo()
{
    var message = '';
    if ($('#EditUserIdentifier').val() == '')
        message += 'User Identifier can not be empty';
    if ($('#EditUserName').val() == '')
        message += ((message.length != 0) ? '<br />' : '') + 'User Name can not be empty';
    if ($('#EditUserUsername').val() == '')
        message += ((message.length != 0) ? '<br />' : '') + 'User Username can not be empty';
    if ($('#EditUserGrade').val() == 'Please choose education "grade"')
        message += ((message.length != 0) ? '<br />' : '') + 'User Grade can not be empty';

    if (message.length != 0)
    {
        ShowError(message);
        return true;
    }

    const Name = $('#EditUserName').val();
    const Username = $('#EditUserUsername').val();
    const Email = ($('#EditUserEmail').val()) ? $('#EditUserEmail').val() : ' ';
    const Identifier = $('#EditUserIdentifier').val();
    const Grade = $('#EditUserGrade').val();
    var UserMetadata = $('#EditUserMetadata').val();

    try
    {
        UserMetadata = JSON.stringify(JSON.parse(UserMetadata));
    }
    catch
    {
        ShowInfo('Metadata field is not a valid JSON data.<br />Visit <a href="https://www.json.org/json-en.html" target="_blank">JSON.org</a> for more info.');
        return;
    }

    $.post(USERSAPI_V1, { type : 'EditUser', Name : Name, Username : Username, Email : Email, Identifier : Identifier, Grade : Grade, Metadata : UserMetadata})
        .done(function(data)
        {
            if (data.hasOwnProperty('error'))
                ShowError(data['error']);
            else if (data['response'] == false)
                ShowError('Internal server error');
            else
            {
                ShowSuccess('User data successfully updated');
                FindEditUser();
            }
        })
        .fail(function()
        {
            ShowError('Webclient error');
        });
}

$(function()
{
    $('#poweredBy').html('Powered By <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite">Open School Library Lite</a>');
    $('#tagBook').focus();
    HideEverything();
    document.getElementById('tagBook').addEventListener('keyup', function(event)
    {
        if (event.keyCode === 13)
        {
            event.preventDefault();
            PerformSearch();
        }
    });

    $.post('GAPIPAL.php', { Plugin : '', c : 'list' }, function(data)
    {
        if (data.hasOwnProperty('error'))
            ShowError(data['error']);
        else
        {
            for (var i = 0; i < data['data'].length; i++)
            {
                var script = document.createElement('script');
                script.src = 'plugins/' + data['data'][i] + '/assets/' + data['data'][i] + '.js';
                document.head.appendChild(script);
            }
        }
        if (window.location.hash)
        {
            if (window.location.hash != 'Search' && eval('typeof Show' + window.location.hash.substring(1)) == 'function')
                window['Show' + window.location.hash.substring(1)]();
        }
    });

    document.getElementById('BorrowBookIdentifier').addEventListener('keyup', function(event)
    {
        if (event.keyCode === 13)
        {
            event.preventDefault();
            FindBorrowBook();
        }
    });

    document.getElementById('BorrowUserID').addEventListener('keyup', function(event)
    {
        if (event.keyCode === 13)
        {
            event.preventDefault();
            FindBorrowUser();
        }
    });

    document.getElementById('ReturnBookIdentifier').addEventListener('keyup', function(event)
    {
        if (event.keyCode === 13)
        {
            event.preventDefault();
            FindReturnBook();
        }
    });

    document.getElementById('EditBookIdentifier').addEventListener('keyup', function(event)
    {
        if (event.keyCode === 13)
        {
            event.preventDefault();
            FindEditBook();
        }
    });

    document.getElementById('EditUserSearchTag').addEventListener('keyup', function(event)
    {
        if (event.keyCode === 13)
        {
            event.preventDefault();
            PerformSearchUser();
        }
    });
});