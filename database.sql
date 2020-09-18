SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
    `ID` int NOT NULL,
    `Identifier` text COLLATE utf8_bin NOT NULL,
    `Title` text COLLATE utf8_bin NOT NULL,
    `Author` text COLLATE utf8_bin NOT NULL,
    `Dewey` text CHARACTER SET utf8 COLLATE utf8_bin,
    `ISBN` text CHARACTER SET utf8 COLLATE utf8_bin,
    `Availability` int NOT NULL,
    `BorrowedUntill` date DEFAULT NULL,
    `BorrowedByID` text COLLATE utf8_bin,
    `Found` tinyint(1) NOT NULL DEFAULT '0',
    `Metadata` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `ID` int NOT NULL,
    `Identifier` text COLLATE utf8_bin NOT NULL,
    `Name` text COLLATE utf8_bin NOT NULL,
    `Username` text COLLATE utf8_bin NOT NULL,
    `Email` text COLLATE utf8_bin,
    `Password` text COLLATE utf8_bin NOT NULL,
    `Algo` text COLLATE utf8_bin NOT NULL,
    `Level` tinyint NOT NULL DEFAULT '3',
    `Grade` text CHARACTER SET utf8 COLLATE utf8_bin,
    `Metadata` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `users` (`ID`, `Identifier`, `Name`, `Username`, `Email`, `Password`, `Algo`, `Level`, `Grade`, `Metadata`) VALUES
(1, '', 'Admin', 'root', '', '$2y$10$u8dSfD7oVovNtgKKvJ0V0u1m0XVVE8TX/fR52B2L4JSIoA4nF4FcK', 'sha256', 0, '', '{}');

ALTER TABLE `books`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `books`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

COMMIT;