<?php
session_start();
include 'header.php';
include 'settings.php';
?>

            <div id="indexPage" style="visibility: visible;">
                <br/>
                <br/>
                <h1 style="text-align: center;"><?php echo GetSetting('Welcome Message'); ?></h1>
            </div>
            <div id="searchPage" style="visibility: collapse; height: 0px">

            </div>

<?php
if (2 < $elevated)
{
    echo '            <div id="loginPage" style="visibility: collapse; height: 0px">
                <form autocomplete="off" onsubmit="LogIn(); return false;">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Username</label>
                        <input type="text" class="form-control" id="LoginUsername" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" id="LoginPassword" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
';
    include_once 'footer.php';
    die();
}
?>

            <div id="accountPage" style="visibility: collapse; height=0px">
                <h1 style="text-align: center;"><?php echo $_SESSION['Name']; ?></h1>
                <br>
                <div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Name:</span>
                        </div>
                        <input type="text" class="form-control" id="AccountName" aria-label="Name" aria-describedby="basic-addon1" value="<?php echo $_SESSION['Name']; ?>">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Username:</span>
                        </div>
                        <input type="text" class="form-control" id="AccountUsername" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $_SESSION['Username']; ?>">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Email:</span>
                        </div>
                        <input type="text" class="form-control" id="AccountEmail" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $_SESSION['Email']; ?>">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">New Password:</span>
                        </div>
                        <input type="password" class="form-control" id="AccountNewPassword" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <button type="button" class="btn btn-primary" id="AccountSaveBtn" onclick="SaveInfos();">Save</button>
                    <button type="button" class="btn btn-warning" id="AccountLogoutBtn" onclick="LogOut();">Log out</button>
                    <button type="button" class="btn btn-danger" id="AccountCloseAccount" disabled>Close Admin Account</button>
                </div>
            </div>
            <div id="borrowBookPage" style="visibility: collapse; height: 0px">
                <div class="row">
                    <div class="col-10">
                        <input type="text" class="form-control" id="BorrowBookIdentifier" placeholder="Identifier">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-block" onclick="FindBorrowBook();">Search Book</button>
                    </div>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" id="BorrowBookID" readonly>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" id="BorrowBookTitle" readonly>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="BorrowBookAuthor" readonly>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-5">
                        <input type="text" class="form-control" id="BorrowUserID" placeholder='User Identification'>
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control" id="BorrowUserName" readonly>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-block" onclick="FindBorrowUser()">Find User</button>
                    </div>
                </div>
                <br />
                <br />
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-block" onclick="BorrowBook()">Borrow book</button>
                    </div>
                </div>
            </div>
            <div id="returnBookPage" style="visibility: collapse; height: 0px">
                <div class="row">
                    <div class="col-10">
                        <input type="text" class="form-control" id="ReturnBookIdentifier" placeholder="Identifier">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-block" onclick="FindReturnBook();">Search Book</button>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" id="ReturnBookID" readonly>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" id="ReturnBookTitle" readonly>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="ReturnBookAuthor" readonly>
                    </div>
                </div>
                <br />
                <br />
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-block" onclick="ReturnBook();">Return Book</button>
                    </div>
                </div>
            </div>
            <div id="editBookPage" style="visibility: collapse; height: 0px">
                <div class="row">
                    <div class="col-10">
                        <input type="text" class="form-control" id="EditBookIdentifier" placeholder="Identifier">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-block" onclick="FindEditBook();">Search Book</button>
                    </div>
                </div>
                <br />
                <br />
                <div class="row">
                    <div class="col-12">
                        <input type="text" class="form-control" id="EditBookID" readonly placeholder="ID">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-6">
                        <input type="text" class="form-control" id="EditBookTitle" placeholder="Title">
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control" id="EditBookAuthor" placeholder="Author">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-6">
                        <input type="text" class="form-control" id="EditBookDewey" placeholder="Dewey">
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control" id="EditBookISBN" placeholder="ISBN">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-11">
                        <textarea type="text" class="form-control" id="EditBookMetadata"></textarea>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-secondary" onclick="ShowInfo('Please provide valid JSON code')">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588z"/>
                                <circle cx="8" cy="4.5" r="1"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <br />
                <br />
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-primary btn-block" onclick="SaveBookInfo()">Save Book Info</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-danger btn-block" onclick="RemoveBook()">Delete Book</button>
                    </div>
                </div>
            </div>
            <div id="addBookPage" style="visibility: collapse; height: 0px;">
                <div class="row">
                    <div class="col-12">
                        <input type="text" class="form-control" id="AddBookID" placeholder="Identifier" required>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-4">
                        <input type="text" class="form-control" id="AddBookTitle" placeholder="Title" required>
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control" id="AddBookAuthor" placeholder="Author" required>
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control" id="AddBookDewey" placeholder="Dewey" required>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12">
                        <input type="text" class="form-control" id="AddBookISBN" placeholder="ISBN" required>
                    </div>
                </div>
                <br />
                <button type="button" class="btn btn-primary btn-block" onclick="AddBook()">Add book</button>
            </div>
            <div id="addUserPage" style="visibility: collapse; height: 0px;">
                <div class="row">
                    <div class="col-12">
                        <input type="text" class="form-control" id="AddUserIdentifier" placeholder="Identifier">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-6">
                        <input type="text" class="form-control" id="AddUserName" placeholder="Name">
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control" id="AddUserUsername" placeholder="Username">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-6">
                        <input type="text" class="form-control" id="AddUserEmail" placeholder="Email">
                    </div>
                    <div class="col-6">
                        <input type="password" class="form-control" id="AddUserPassword" placeholder="Password">
                    </div>
                </div>
                <br />
                <br />
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-block" onclick="AddUser()">Add User</button>
                    </div>
                </div>
            </div>
            <div id="editUserPage" style="visibility: collapse; height: 0px;">
                <div id="editPageSearchUser">
                    <div id="editPageSearchUserInput" class="row">
                        <div class="col-8">
                            <input type="text" class="form-control" id="EditUserSearchTag" placeholder="<?php echo GetSetting('Search'); ?>">
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-primary btn-block" onclick="PerformSearchUser()">Search Users</button>
                        </div>
                        <div class="col-1">
                            <button type="button" class="btn btn-secondary" onclick="ShowInfo('You can search for a user by Name, Username, Email and Identifier.<br />It is best advised to not search users by their Identifier')">
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588z"/>
                                    <circle cx="8" cy="4.5" r="1"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <br />
                    <br />
                    <div id="editPageSearchUserResults"> </div>
                </div>
                <br />
                <br />
                <div class="row">
                    <div class="col-10">
                        <input type="text" class="form-control" id="EditUserIdentifier" placeholder="<?php echo GetSetting('Search'); ?>">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-block" onclick="FindEditUser()">Get User</button>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-6">
                        <input type="text" class="form-control" id="EditUserName" placeholder="Name">
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control" id="EditUserUsername" placeholder="Username">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-12">
                        <input type="text" class="form-control" id="EditUserEmail" placeholder="Email">
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-11">
                        <textarea type="text" class="form-control" id="EditUserMetadata"></textarea>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-secondary" onclick="ShowInfo('Please provide valid JSON code')">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588z"/>
                                <circle cx="8" cy="4.5" r="1"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <br />
                <br />
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-primary btn-block" onclick="SaveUserInfo()">Save User Info</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-danger btn-block" onclick="RemoveUser()">Delete User</button>
                    </div>
                </div>
            </div>
<?php
include_once 'footer.php';
?>