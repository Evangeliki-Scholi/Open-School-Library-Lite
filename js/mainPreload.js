var ClassesOfItemsToHide = [ 'Index', 'Account', 'Search', '404', 'Login', 'AddBook', 'BorrowBook', 'EditBook', 'ReturnBook', 'EditUser', 'AddUser' ];
var AllInputsToBeLocked = [ 'BookIdentifier', 'BookID', 'BookTitle', 'BookAuthor', 'BookDewey', 'BookISBN', 'BookMetadata', 'UserIdentifier', 'UserID', 'UserName', 'UserUsername', 'UserEmail', 'UserGrade', 'UserMetadata', 'UserPassword', 'UserSearch' ];

var ItemsToUnlock = {
    'AddBook' : [
        'BookID',
        'BookTitle',
        'BookAuthor',
        'BookDewey',
        'BookISBN',
        'BookMetadata'
    ],
    'BorrowBook' : [
        'BookIdentifier',
        'UserSearch',
        'UserIdentifier'
    ],
    'EditBook' : [
        'BookIdentifier',
        'BookTitle',
        'BookAuthor',
        'BookDewey',
        'BookISBN',
        'BookMetadata'
    ],
    'ReturnBook' : [
        'BookIdentifier'
    ],
    'EditUser' : [
        'UserSearch',
        'UserIdentifier',
        'UserName',
        'UserUsername',
        'UserEmail',
        'UserGrade',
        'UserMetadata'
    ],
    'AddUser' : [
        'UserID',
        'UserName',
        'UserUsername',
        'UserEmail',
        'UserGrade',
        'UserPassword'
    ]
};

var BookIdentifier;
var BookID;
var BookTitle;
var BookAuthor;
var BookDewey;
var BookISBN;
var BookMetadata;

var UserIdentifier;
var UserID;
var UserName;
var UserUsername;
var UserEmail;
var UserGrade;
var UserMetadata;
var UserPassword;

var UserSearch;
var UserSearchResults;

var BorrowBookBtn;
var ReturnBookBtn;
var SaveBookBtn;
var RemoveBookBtn;
var AddBookBtn;
var GetBookBtn;
var SearchUserBtn;
var GetUserBtn;

var SaveUserBtn;
var RemoveUserBtn;
var AddUserBtn;

function Show(name)
{
    for (var i = 0; i < ClassesOfItemsToHide.length; i++)
    {
        var element = document.getElementsByClassName(ClassesOfItemsToHide[i] + 'Page');
        for (var j = 0; j < element.length; j++)
        {
            element[j].style.visibility = 'collapse';
            element[j].style.height = '0px';
        }
    }
    var element = document.getElementsByClassName(name + 'Page');
    for (var i = 0; i < element.length; i++)
    {
        element[i].style.visibility = 'visible';
        element[i].style.height = 'auto';
        window.location.hash = '#' + name;
    }

    SetBook();
    SetUser();

    for (var i = 0; i < AllInputsToBeLocked.length; i++)
    {
        var element = document.getElementById(AllInputsToBeLocked[i]);
        if (element == null)
            continue;
        element.readOnly = true;
        element.disabled = true;
    }
    
    if (ItemsToUnlock.hasOwnProperty(name))
        for (var i = 0; i < ItemsToUnlock[name].length; i++)
        {
            document.getElementById(ItemsToUnlock[name][i]).readOnly = false;
            document.getElementById(ItemsToUnlock[name][i]).disabled = false;
        }
}

/**
 * @param {Object} data 
 * @returns True if data function execution should continue and False if not
 */
function CheckHandleError(data)
{
    if (data.hasOwnProperty('error'))
    {
        ShowError(data['error']);
        return false;
    }
    else if (data.hasOwnProperty('response') && data['response'] == false)
    {
        ShowError('Unexpected internal error<br />Please contact you Administrator<br />You could also open an issue on <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite/issues">GitHub</a>');
        return false;
    }
    else if (data.hasOwnProperty('message'))
        ShowInfo(data['message']);
    return true;
}

const BOOKAPI_V1 = '/API/V1/Book.php';
const USERSAPI_V1 = '/API/V1/Users.php';

/**
 * New AddBook Function
 */
function AddBook()
{
    if (BookID.value.trim() == '')
    {
        ShowError('Book Identifier is required');
        return;
    }
    if (BookTitle.value.trim() == '')
    {
        ShowError('Book Title is required');
        return;
    }
    if (BookAuthor.value.trim() == '')
    {
        ShowError('Book Author is required');
        return;
    }

    BookMetadata.value = '{}';
    
    var postData = { type : 'AddBook', Identifier : BookID.value, Title : BookTitle.value, Author : BookAuthor.value, Dewey : BookDewey.value, ISBN : BookISBN.value };
    $.post(BOOKAPI_V1, postData, function(data)
        {
            if (CheckHandleError(data))
            {
                SetBook();
                ShowSuccess('Book succesfully added');
            }
        })
        .fail(function()
        {
            ShowError('Interal server error<br />You can open an issue on <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite/issues">GitHub</a>');
        });
}

/**
 * New AddUser Function
 */
function AddUser()
{
    if (UserID.value.trim() == '')
    {
        ShowError('User Identifier is required');
        return;
    }
    if (UserName.value.trim() == '')
    {
        ShowError('User Name is required');
        return;
    }
    if (UserUsername.value.trim() == '')
    {
        ShowError('User Username is required');
        return;
    }
    if (UserGrade.value == 'Please choose education "grade"')
    {
        ShowError('User Grade is required');
        return;
    }
    if (UserPassword.value.trim() == '')
    {
        ShowError('User Password is required');
        return;
    }
    UserMetadata.value = '{}';
    var postData = { type : 'AddUser', Identifier : UserID.value, Name : UserName.value, Username : UserUsername.value, Email : UserEmail.value, Password : sha512_256(UserPassword.value), Algo : 'sha256', Grade : UserGrade.value, Metadata : UserMetadata.value };
    $.post(USERSAPI_V1, postData, function(data)
        {
            if (CheckHandleError(data))
            {
                SetUser();
                ShowSuccess('User succesfully added');
            }
        })
        .fail(function()
        {
            ShowError('Interal server error<br />You can open an issue on <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite/issues">GitHub</a>');
        });
}

/**
 * New BorrowBook Function
 */
function BorrowBook()
{
    if (BookID.value == '' || UserID.value == '')
    {
        ShowError('Book Identifier and User Identifier can not be empty');
        return;
    }

    $.post(BOOKAPI_V1, { type : 'BorrowBook', Identifier : BookID.value, UserID : UserID.value }, function(data)
    {
        if (CheckHandleError(data))
        {
            ShowSuccess('Book Successfully borrowed by ' + UserName.value);
            SetBook();
            SetUser();
        }
    }).fail(function()
    {
        ShowError('Interal server error');
    });
}

/**
 * New SetBook Function
 */
function SetBook(Identifier = '', Title = '', Author = '', Dewey = '', ISBN = '', Metadata = '')
{
    BookID.value = Identifier;
    BookTitle.value = Title;
    BookAuthor.value = Author;
    BookDewey.value = Dewey;
    BookISBN.value = ISBN;
    BookMetadata.value = Metadata;
}

/**
 * New FindBook Function
 */
function FindBook()
{
    if (BookIdentifier.value.trim() == '')
    {
        ShowInfo('Book Identifier can not be empty or whitespaces').
        return;
    }
    var postData = { type : 'GetBook', Identifier : BookIdentifier.value.trim() };
    $.post(BOOKAPI_V1, postData, function(data)
        {
            if (CheckHandleError(data))
                SetBook(data['Identifier'], data['Title'], data['Author'], data['Dewey'], data['ISBN'], data['Metadata']);
        })
        .fail(function()
        {
            ShowError('Interal server error<br />You can open an issue on <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite/issues">GitHub</a>');
        });
}

/**
 * New SetUser Function
 */
function SetUser(Identifier = '', Name = '', Username = '', Email = '', Grade = 'Please choose education "grade"', Metadata = '')
{
    UserID.value = Identifier;
    UserName.value = Name;
    UserUsername.value = Username;
    UserEmail.value = Email;
    UserGrade.value = Grade;
    UserMetadata.value = Metadata;
    UserPassword.value = '';
}

/**
 * New FindUser Function
 */
function FindUser()
{
    if (UserIdentifier.value.trim() == '')
    {
        ShowInfo('User Identifier can not be empty or whitespaces').
        return;
    }
    var postData = { type : 'GetUser', Identifier : UserIdentifier.value.trim() };
    $.post(USERSAPI_V1, postData, function(data)
        {
            if (CheckHandleError(data))
                SetUser(data['data']['Identifier'], data['data']['Name'], data['data']['Username'], data['data']['Email'], data['data']['Grade'], data['data']['Metadata']);
        })
        .fail(function()
        {
            ShowError('Interal server error<br />You can open an issue on <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite/issues">GitHub</a>');
        });
}

/**
 * New SearchUser Function
 */
function SearchUser()
{
    if (UserSearch.value.trim() < 2)
    {
        ShowInfo('Search item can not be empty, whitespace or less than two characters');
        return false;
    }
    UserSearchResults.innerHTML = '';
    var postData = { type : 'SearchUsers', SearchTag : UserSearch.value.trim() };
    $.post(USERSAPI_V1, postData, function(data)
        {
            if (!CheckHandleError(data))
                return;
            else if (data['data'].length == 0)
            {
                ShowInfo('No users were found');
                return;
            }

            const table = document.createElement('table');
            table.setAttribute('class', 'table table-bordered');
            
            var tr = table.insertRow(-1);

            var th = document.createElement('th');
            th.innerHTML = 'Identifier';
            tr.append(th);

            th = document.createElement('th');
            th.innerHTML = 'Name';
            tr.append(th);

            th = document.createElement('th');
            th.innerHTML = 'Grade';
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
                th.innerHTML = data['data'][i]['Grade'];
                tr.append(th);

                th = document.createElement('th');
                th.innerHTML = '<button class="btn btn-primary btn-block" onclick="UserIdentifier.value=\'' + data['data'][i]['Identifier'] + '\';FindUser();UserSearch.value=\'\';UserSearchResults.innerHTML=\'\';">Select</button>';
                tr.append(th);
            }

            UserSearchResults.appendChild(table);
        })
        .fail(function()
        {
            ShowError('Interal server error<br />You can open an issue on <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite/issues">GitHub</a>');
        })
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

/**
 * New LogOut Function
 */
function LogOut()
{
    $.post(USERSAPI_V1, { type : 'LogOut' }, function (data)
    {
        if (!CheckHandleError(data))
            return;
        window.location.href = 'index.php';
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
            Show('Search');
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

/**
 * New RemoveBook Function
 */
function RemoveBook()
{
    $.post(BOOKAPI_V1, { type : 'RemoveBook', Identifier : BookID.value }, function(data)
    {
        if (!CheckHandleError(data))
            return;
        SetBook();
        ShowSuccess('Book deleted sucessfully');
    });
}

/**
 * New RemoveUser Function
 */
function RemoveUser()
{
    if (UserID.value == '')
    {
        ShowError('User Identifier can not be empty');
        return;
    }

    $.post(USERSAPI_V1, { type : 'RemoveUser', Identifier : UserID.value }, function(data)
        {
            if (!CheckHandleError(data))
                return;
            ShowSuccess('User successfully removed');
            SetUser();
        })
        .fail(function()
        {
            ShowError('WebClient error');
        });
}

/**
 * New ReturnBook Function
 */
function ReturnBook()
{
    if (BookID.value == '')
    {
        ShowError('Book Identifier can not be empty');
        return;
    }
    $.post(BOOKAPI_V1, { type : 'ReturnBook', Identifier : BookID.value }, function(data)
    {
        if (CheckHandleError(data))
        {
            ShowSuccess('Book was successfully returned');
            SetBook();
        }
    }).fail(function()
    {
        ShowError('Internal Server Error');
        return false;
    });
}

/**
 * New SaveBookInfo Function
 */
function SaveBookInfo()
{
    if (BookID.value == '')
    {
        ShowError('Book Identifier can not be empty');
        return;
    }

    const initialJSON = BookMetadata.value;
    try
    {
        BookMetadata.value = JSON.stringify(JSON.parse(BookMetadata.value))
    }
    catch
    {
        ShowInfo('Metadata field is not a valid JSON data.<br />Visit <a href="https://www.json.org/json-en.html" target="_blank">JSON.org</a> for more info.');
        BookMetadata.value = initialJSON;
        return;
    }

    $.post(BOOKAPI_V1, { type : 'EditBook', Identifier : BookID.value, Title : BookTitle.value, Author : BookAuthor.value, Dewey : BookDewey.value, ISBN : BookISBN.value, Metadata : BookMetadata.value }, function(data)
        {
            if (CheckHandleError(data))
            {
                ShowSuccess('Book data stored successfully');
                SetBook();
                FindBook();
            }
        })
        .fail(function()
        {
            ShowError('Internal Server Error');
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

/**
 * New SaveUserInfo Function
 */
function SaveUserInfo()
{
    if (UserID.value == '')
    {
        ShowError('User Identifier can not be empty');
        return;
    }
    if (UserName.value == '')
    {
        ShowError('User Name can not be empty');
        return;
    }
    if (UserUsername.value == '')
    {
        ShowError('User Username can not be empty');
        return;
    }
    if (UserGrade.value == 'Please choose education "grade"')
    {
        ShowError('Please choose education "grade"');
        return;
    }

    try
    {
        UserMetadata.value = JSON.stringify(JSON.parse(UserMetadata.value));
    }
    catch
    {
        ShowInfo('Metadata field is not a valid JSON data.<br />Visit <a href="https://www.json.org/json-en.html" target="_blank">JSON.org</a> for more info.');
        return;
    }

    $.post(USERSAPI_V1, { type : 'EditUser', Name : UserName.value, Username : UserUsername.value, Email : UserEmail.value, Identifier : UserID.value, Grade : UserGrade.value, Metadata : UserMetadata.value }, function(data)
        {
            if (!CheckHandleError(data))
                return;
            ShowSuccess('User data successfully updated');
            FindUser();
        })
        .fail(function()
        {
            ShowError('Internal Server Error');
        });
}

$(function()
{
    document.getElementById('poweredBy').innerHTML = 'Powered By <a href="https://github.com/Evangeliki-Scholi/Open-School-Library-Lite">Open School Library Lite</a>';
    document.getElementById('tagBook').focus();

    BookIdentifier = document.getElementById('BookIdentifier');
    BookID = document.getElementById('BookID');
    BookTitle = document.getElementById('BookTitle');
    BookAuthor = document.getElementById('BookAuthor');
    BookDewey = document.getElementById('BookDewey');
    BookISBN = document.getElementById('BookISBN');
    BookMetadata = document.getElementById('BookMetadata');

    UserIdentifier = document.getElementById('UserIdentifier');
    UserID = document.getElementById('UserID');
    UserName = document.getElementById('UserName');
    UserUsername = document.getElementById('UserUsername');
    UserEmail = document.getElementById('UserEmail');
    UserGrade = document.getElementById('UserGrade');
    UserMetadata = document.getElementById('UserMetadata');
    UserPassword = document.getElementById('UserPassword');

    UserSearch = document.getElementById('UserSearch');
    UserSearchResults = document.getElementById('UserSearchResults');

    BorrowBookBtn = document.getElementById('BorrowBookBtn');
    ReturnBookBtn = document.getElementById('ReturnBookBtn');
    SaveBookBtn = document.getElementById('SaveBookBtn');
    RemoveBookBtn = document.getElementById('RemoveBookBtn');
    AddBookBtn = document.getElementById('AddBookBtn');
    GetBookBtn = document.getElementById('GetBookBtn');
    SearchUserBtn = document.getElementById('SearchUserBtn');
    GetUserBtn = document.getElementById('GetUserBtn');

    SaveUserBtn = document.getElementById('SaveUserBtn');
    RemoveUserBtn = document.getElementById('RemoveUserBtn');
    AddUserBtn = document.getElementById('AddUserBtn');

    if (window.location.hash)
        Show(window.location.hash.substring(1));

    document.getElementById('tagBook').addEventListener('keyup', function(event)
    {
        if (event.code == 'Enter')
        {
            event.preventDefault();
            PerformSearch();
        }
    });

    $.post('GAPIPAL.php', { Plugin : '', c : 'list' }, function(data)
    {
        if (!CheckHandleError(data))
            return;
        for (var i = 0; i < data['data'].length; i++)
        {
            var script = document.createElement('script');
            script.src = 'plugins/' + data['data'][i] + '/assets/' + data['data'][i] + '.js';
            document.head.appendChild(script);
        }
    });

    BookIdentifier.addEventListener('keyup', function(event)
        {
            if (event.code == 'Enter')
            {
                event.preventDefault();
                GetBookBtn.click();
            }
        });

    UserIdentifier.addEventListener('keyup', function(event)
        {
            if (event.code == 'Enter')
            {
                event.preventDefault();
                GetUserBtn.click();
            }
        });

    UserSearch.addEventListener('keyup', function(event)
        {
            if (event.code == 'Enter')
            {
                event.preventDefault();
                SearchUserBtn.click();
            }
        });

    AddBookBtn.addEventListener('click', AddBook);
    AddUserBtn.addEventListener('click', AddUser);
    BorrowBookBtn.addEventListener('click', BorrowBook);
    GetBookBtn.addEventListener('click', FindBook);
    GetUserBtn.addEventListener('click', FindUser);
    RemoveBookBtn.addEventListener('click', RemoveBook);
    RemoveUserBtn.addEventListener('click', RemoveUser);
    ReturnBookBtn.addEventListener('click', ReturnBook);
    SaveBookBtn.addEventListener('click', SaveBookInfo);
    SaveUserBtn.addEventListener('click', SaveUserInfo);
    SearchUserBtn.addEventListener('click', SearchUser);
});