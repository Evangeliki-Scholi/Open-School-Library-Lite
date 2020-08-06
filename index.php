<?php
session_start();
include_once 'header.php';
include_once 'settings.php';
?>

            <div id="indexPage" style="visibility: visible;">
                <br/>
                <br/>
                <h1 style="text-align: center;">Hello and welcome</h1>
            </div>

<?php
if (!$elevated)
{
    echo '            <div id="loginPage" style="visibility: collapse;">
                <form autocomplete="off" onsubmit="Login(); return false;">
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

            <div id="accountPage" style="visibility: collapse;">
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
                            <span class="input-group-text" id="basic-addon1">New Password:</span>
                        </div>
                        <input type="password" class="form-control" id="AccountNewPassword" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <button type="button" class="btn btn-primary" id="AccountSaveBtn" onclick="SaveInfos();">Save</button>
                    <button type="button" class="btn btn-warning" id="AccountLogoutBtn" onclick="LogOut();">Log out</button>
                    <button type="button" class="btn btn-danger" id="AccountCloseAccount" disabled>Close Admin Account</button>
                </div>
            </div>
            <div id="borrowPage" style="visibility: collapse;">
                <div class="row">
                    <div class="col-10">
                        <input type="text" class="form-control" id="BorrowBookBarcode" placeholder="Barcode">
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
                        <input type="text" class="form-control" id="BorrowUserID">
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control" id="BorrowUserName" readonly>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-block" onclick="FindBorrowUser()">Find User</button>
                    </div>
                </div>
                <br />
                <button type="button" class="btn btn-primary btn-block" onclick="BorrowBook()">Borrow book</button>
            </div>
<?php
include_once 'footer.php';
?>