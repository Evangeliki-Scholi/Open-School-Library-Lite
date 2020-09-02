<?php
session_start();
include 'header.php';
include 'settings.php';
?>

            <div class="IndexPage" style="visibility: visible;">
                <br/>
                <br/>
                <h1 style="text-align: center;"><?php echo GetSetting('Welcome Message'); ?></h1>
            </div>
            <div class="SearchPage" id="searchPage" style="visibility: collapse; height: 0px">

            </div>
            <div class="404Page" id="404Page" style="visibility:collapse;height:0px">
                <svg data-name="mainImage"id="mainImage_create"viewBox="0 0 171.2 81.5"xmlns="http://www.w3.org/2000/svg"><style id="svgStyle">.changeColor{fill:#4f86ed}#title{font-size:50%;font-family:'Times New Roman',serif}.cls-1{opacity:.3}.cls-7{opacity:.8}.cls-2{fill:#fff}.cls-10,.cls-11,.cls-12,.cls-14,.cls-16,.cls-3{fill:none}.cls-3{stroke:#5c7690}.cls-10,.cls-11,.cls-12,.cls-3{stroke-miterlimit:10}.cls-14,.cls-15,.cls-16,.cls-3{stroke-width:.5px}.cls-4{fill:#ffe1d9}.cls-5{fill:#ffcfbf}.cls-6{fill:#fecbb6}.cls-9{fill:#fecb02}.cls-10,.cls-12{stroke:#d26f51}.cls-10,.cls-11{stroke-width:.38px}.cls-11{stroke:#000}.cls-12{stroke-width:.19px}.cls-13{opacity:.45}.cls-14,.cls-15,.cls-16{stroke:#b0bec5;stroke-linejoin:round}.cls-15{fill:#edf0f2}.cls-16{stroke-linecap:round}.cls-17{font-family:'PT Sans',sans-serif;font-size:49.87px;font-weight:700}.cls-18{fill:#fffdbb;opacity:.5}.earMove{transition:all ease-in-out 2s;transform-origin:50% 50%;animation:earmove 1.5s linear infinite alternate}.faceMove{transition:all ease-in-out 2s;transform-origin:50% 50%;animation:move 1.5s linear infinite alternate}.neckMove{transition:all ease-in-out 2s;transform-origin:50% 50%;animation:neck 1.5s linear infinite alternate}@keyframes earmove{0%{transform:translateX(-.3px) translateY(.6px)}30%{transform:translateX(-.3px) translateY(.6px)}60%{transform:translateX(-.7px) translateY(0)}70%{transform:translateX(-.7px) translateY(-.3px)}100%{transform:translateX(-.7px) translateY(-.3px)}}@keyframes move{0%{transform:translateX(-.3px) translateY(.6px)}30%{transform:translateX(-.3px) translateY(.6px)}60%{transform:translateX(2px) translateY(0)}70%{transform:translateX(2px) translateY(-.3px)}100%{transform:translateX(2px) translateY(-.3px)}}@keyframes neck{0%{transform:translateY(.7px)}50%{transform:translateY(.7px)}100%{transform:translateY(0)}}</style><path class="changeColor cls-1"d="M46.62,52.5c5.78,4.9,21.14,8.4,39.19,8.4s33.41-3.5,39.19-8.4c-5.78-4.9-21.14-8.4-39.19-8.4S52.41,47.6,46.62,52.5Z"id="c-1"style="fill:#00ed63"></path><path class="cls-2"d="M99.73,47.71H68.65a7.13,7.13,0,0,0-7.13,7.13V60a152.58,152.58,0,0,0,24.3,1.83,157.87,157.87,0,0,0,21.05-1.35V54.84A7.13,7.13,0,0,0,99.73,47.71Z"></path><path class="cls-3"d="M123.56,55.81C115,58.94,101.27,61,85.81,61c-26,0-47-5.71-47-12.76,0-3.45,5.05-6.58,13.25-8.88"></path><path class="cls-3"d="M55.37,38.47a140,140,0,0,1,30.44-3c26,0,47,5.71,47,12.76,0,2.4-2.44,4.65-6.69,6.57"></path><path class="cls-3"d="M53.41,38.95l.94-.24"></path><path class="cls-4"d="M91.68,47.71l-.75-11.2L79.15,43.84l-1.69,3.87H75.79c0,3.36,3.76,6.08,8.4,6.08s8.4-2.72,8.4-6.08Z"></path><path class="cls-5 neckMove"d="M78,46.53a27.19,27.19,0,0,0,6.41.82c3.1,0,7.11-2.19,7.11-2.19l-.42-6.2L79.15,43.84Z"></path><polygon class="earMove"points="92.59 32.22 92.59 28.5 76.77 27.71 76.77 32.22 92.59 32.22"></polygon><circle class="earMove cls-6"cx="78.06"cy="34.04"r="2.47"></circle><path class="cls-4"d="M81.74,57.06,60.63,49.72h0A6.72,6.72,0,1,0,57.7,62.49H93.25C93.25,56.78,81.74,57.06,81.74,57.06Z"></path><path class="cls-4"d="M77.46,25H90.92a0,0,0,0,1,0,0V39.38a6.73,6.73,0,0,1-6.73,6.73h0a6.73,6.73,0,0,1-6.73-6.73V25A0,0,0,0,1,77.46,25Z"></path><rect class="changeColor cls-7"height="2.45"width="19.14"x="74.82"y="26.48"id="c-2"style="fill:#00ed63"transform="translate(1.29 -3.65) rotate(2.49)"></rect><path class="changeColor cls-7"d="M84.36,18.69h.5a7.8,7.8,0,0,1,7.8,7.8v0a0,0,0,0,1,0,0H76.56a0,0,0,0,1,0,0v0A7.8,7.8,0,0,1,84.36,18.69Z"id="c-3"style="fill:#00ed63"transform="translate(1.06 -3.66) rotate(2.49)"></path><polygon class="changeColor cls-8"points="82.44 23.89 92.18 24.32 92.59 24.34 92.48 26.84 80.96 26.33 82.44 23.89"id="c-4"style="fill:#00ed63"></polygon><circle class="faceMove cls-9"cx="78.72"cy="23.73"r="3.73"transform="translate(51.58 101.34) rotate(-87.51)"></circle><circle class="faceMove cls-2"cx="78.72"cy="23.73"r="2.36"transform="translate(51.58 101.34) rotate(-87.51)"></circle><circle class="cls-4 earMove"cx="90.92"cy="34.04"r="2.47"></circle><path class="cls-4"d="M112.2,53l-9.87-21.92-3-5.48-11.86-.22,7.42,3.35H91.55l5.82,4.58,2,22.26h0A6.72,6.72,0,1,0,112.2,53Z"></path><ellipse class="faceMove"cx="80.09"cy="33.12"rx="0.53"ry="0.59"></ellipse><ellipse class="faceMove"cx="86.34"cy="33.12"rx="0.53"ry="0.59"></ellipse><polyline class="faceMove cls-10"points="84.19 31.08 81.74 37.01 84.39 37.01"></polyline><path class="faceMove cls-10"d="M83.06,40.36a4,4,0,0,1,2.75-1"></path><line class="faceMove cls-11"x1="81.07"x2="78.47"y1="30.33"y2="30.58"></line><line class="faceMove cls-11"x1="86.34"x2="88.15"y1="30.45"y2="31.08"></line><line class="cls-12"x1="106.86"x2="110.99"y1="47.82"y2="46.11"></line><line class="cls-12"x1="107.43"x2="111.55"y1="49.9"y2="48.19"></line><line class="cls-12"x1="107.99"x2="112.11"y1="51.98"y2="50.27"></line><g class="cls-13"><rect class="cls-14"height="3.5"width="10.77"x="85.81"y="2.46"></rect><rect class="cls-15"height="3.5"width="10.77"x="96.58"y="2.46"></rect><rect class="cls-14"height="3.5"width="10.77"x="92.19"y="5.95"></rect><line class="cls-16"x1="107.36"x2="109.63"y1="5.95"y2="5.95"></line><line class="cls-16"x1="110.68"x2="111.57"y1="5.95"y2="5.95"></line></g><g class="cls-13"><rect class="cls-16"height="3.5"width="10.77"x="125"y="23.12"></rect><rect class="cls-15"height="3.5"width="10.77"x="130.39"y="26.62"></rect><rect class="cls-16"height="3.5"width="10.77"x="119.62"y="26.62"></rect><line class="cls-16"x1="141.16"x2="145.73"y1="26.62"y2="26.62"></line><line class="cls-16"x1="125"x2="115.4"y1="23.12"y2="23.12"></line><line class="cls-16"x1="117.95"x2="115.4"y1="26.62"y2="26.62"></line></g><g class="cls-13"><rect class="cls-16"height="3.5"width="10.77"x="39.34"y="16.12"></rect><rect class="cls-16"height="3.5"width="10.77"x="39.34"y="23.11"></rect><rect class="cls-16"height="3.5"width="10.77"x="50.11"y="23.11"></rect><rect class="cls-16"height="3.5"width="10.77"x="50.11"y="16.12"></rect><rect class="cls-15"height="3.5"width="10.77"x="44"y="19.61"></rect><rect class="cls-16"height="3.5"width="10.77"x="33.23"y="19.61"></rect><line class="cls-16"x1="60.89"x2="65.51"y1="19.61"y2="19.61"></line><line class="cls-16"x1="39.34"x2="35.46"y1="16.12"y2="16.12"></line><line class="cls-16"x1="36.45"x2="33.23"y1="26.61"y2="26.61"></line><line class="cls-16"x1="63.2"x2="65.51"y1="23.11"y2="23.11"></line></g><polyline class="cls-3"points="115.4 58.12 115.4 38.27 120.2 37.01"></polyline><polyline class="cls-3"points="129.01 53.21 129.01 43.14 131.74 42.13"></polyline><path class="cls-3"d="M115.4,42.13a53.27,53.27,0,0,1,8,2A42,42,0,0,1,129,47"></path><path class="cls-3"d="M115.4,47.34a53.27,53.27,0,0,1,8,2A42,42,0,0,1,129,52.22"></path><path class="cls-3"d="M115.4,52.56a53.27,53.27,0,0,1,8,2l1,.42"></path><path class="faceMove cls-18"d="M78.84,26.09l0-4.71L68.05,18.32a.91.91,0,0,0-.45-.13c-1.17,0-2.11,2.46-2.11,5.5s.95,5.5,2.11,5.5a.9.9,0,0,0,.44-.12Z"></path><path class="cls-5"d="M57.7,62.49H93.25A3.67,3.67,0,0,0,92.92,61H53.43A6.69,6.69,0,0,0,57.7,62.49Z"></path><path class="cls-12"d="M88.15,60.27s1.7.95,1.7,2.22"></path><path class="cls-5"d="M101.81,61a6.68,6.68,0,0,0,8.51,0Z"></path><polygon class="cls-5"points="90.92 30.25 77.46 29.69 77.46 28.64 90.92 29.22 90.92 30.25"></polygon><text id="title"transform="matrix(1 0 0 1 44.7249 78)"><?php echo GetSetting('PageNotFound'); ?></text></svg>
            </div>

<?php
if (2 < $elevated)
{
    echo '            <div class="LoginPage" id="loginPage" style="visibility: collapse; height: 0px">
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
    echo '            <div id="passwordResetPage" style="visibility: collapse; height: 0px">
                <div class="row">
                    <div class="col-10">
                        <input type="text" class="form-control" id="passwordResetUsername" placeholder="Username">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-block" onclick="SendResetPasswordCode()">Send Reset Password Code</button>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-6">
                        <input type="text" class="form-control" id="passwordResetCode" placeholder="Code from email">
                    </div>
                    <div class="col-6">
                        <input type="password" class="form-control" id="passwordResetPassword" placeholder="New password">
                    </div>
                </div>
                <br />
                <br />
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-block" onclick="ResetPasssword()">Reset password</button>
                    </div>
                </div>
            </div>
';
    include_once 'footer.php';
    die();
}
?>
            <div class="AccountPage" style="visibility: collapse;">
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
            <div id="BookPage" style="visibility:collapse;">
                <div class="BorrowBookPage EditBookPage ReturnBookPage">
                    <br />
                    <div class="row">
                        <div class="col-10">
                            <input type="text" class="form-control" id="BookIdentifier" placeholder="Identifier">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-primary btn-block" id="GetBookBtn">Get Book</button>
                        </div>
                    </div>
                    <br />
                </div>
                <div class="AddBookPage BorrowBookPage EditBookPage ReturnBookPage">
                    <br />
                    <div class="row">
                        <div class="col-12">
                            <input type="text" class="form-control" id="BookID" placeholder="Book Identifier">
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="form-control" id="BookTitle" placeholder="Book Title">
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" id="BookAuthor" placeholder="Book Author">
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="form-control" id="BookDewey" placeholder="Book Dewey">
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" id="BookISBN" placeholder="Book ISBN">
                        </div>
                    </div>
                    <br />
                </div>
                <div class="EditBookPage">
                    <br />
                    <div class="row">
                        <div class="col-11">
                            <textarea type="text" class="form-control" id="BookMetadata"></textarea>
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
                </div>
            </div>
            <div id="UserPage" style="visibility:collapse;">
                <div class="BorrowBookPage EditUserPage">
                    <br />
                    <div class="row">
                        <div class="col-10">
                            <input type="text" class="form-control" id="UserSearch" placeholder="User Search">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-primary btn-block" id="SearchUserBtn">Search Users</button>
                        </div>
                    </div>
                    <br />
                    <div id="UserSearchResults"></div>
                    <br />
                    <div class="row">
                        <div class="col-10">
                            <input type="text" class="form-control" id="UserIdentifier" placeholder="Identifier">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-primary btn-block" id="GetUserBtn">Get User</button>
                        </div>
                    </div>
                    <br />
                </div>
                <div class="BorrowBookPage EditUserPage AddUserPage">
                    <br />
                    <div class="row">
                        <div class="col-12">
                            <input type="text" class="form-control" id="UserID" placeholder="User Identifier">
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="form-control" id="UserName" placeholder="User Name">
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" id="UserUsername" placeholder="User Username">
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="form-control" id="UserEmail" placeholder="User Email">
                        </div>
                        <div class="col-6">
                            <select class="form-control" id="UserGrade">
                                <option selected>Please choose education "grade"</option>
                                <option>Teacher - Professor</option>
                                <option>Primary: 1st Grade</option>
                                <option>Primary: 2nd Grade</option>
                                <option>Primary: 3rd Grade</option>
                                <option>Primary: 4th Grade</option>
                                <option>Primary: 5th Grade</option>
                                <option>Primary: 6th Grade</option>
                                <option>Middle School: 1st Grade</option>
                                <option>Middle School: 2nd Grade</option>
                                <option>Middle School: 3rd Grade</option>
                                <option>High School: 1st Grade</option>
                                <option>High School: 2nd Grade</option>
                                <option>High School: 3rd Grade</option>
                                <option>University</option>
                                <option>Does not attend any educational program</option>
                            </select>
                        </div>
                    </div>
                    <br />
                </div>
                <div class="EditUserPage">
                    <br />
                    <div class="row">
                        <div class="col-11">
                            <textarea type="text" class="form-control" id="UserMetadata"></textarea>
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
                </div>
                <div class="AddUserPage">
                    <div class="row">
                        <div class="col-12">
                            <input type="password" class="form-control" id="UserPassword" placeholder="User Password">
                        </div>
                    </div>
                </div>
            </div>
            <div id="ActionButtons" style="visibility:collapse;">
                <br />
                <div class="BorrowBookPage">
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-block" id="BorrowBookBtn">Borrow Book</button>
                        </div>
                    </div>
                </div>
                <div class="ReturnBookPage">
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-block" id="ReturnBookBtn">Return Book</button>
                        </div>
                    </div>
                </div>
                <div class="EditBookPage">
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary btn-block" id="SaveBookBtn">Save Book Infos</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger btn-block" id="RemoveBookBtn">Remove Book</button>
                        </div>
                    </div>
                </div>
                <div class="AddBookPage">
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-block" id="AddBookBtn">Add Book</button>
                        </div>
                    </div>
                </div>
                <div class="EditUserPage">
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary btn-block" id="SaveUserBtn">Save User Infos</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger btn-block" id="RemoveUserBtn">Remove User</button>
                        </div>
                    </div>
                </div>
                <div class="AddUserPage">
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-block" id="AddUserBtn">Add User</button>
                        </div>
                    </div>
                </div>
                <br />
            </div>
<?php
include_once 'footer.php';
?>