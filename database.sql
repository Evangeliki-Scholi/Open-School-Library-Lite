SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
    `ID` int NOT NULL,
    `Identifier` text COLLATE utf8_bin NOT NULL,
    `Title` text COLLATE utf8_bin NOT NULL,
    `AuthorIDs` text COLLATE utf8_bin NOT NULL,
    `Dewey` text CHARACTER SET utf8 COLLATE utf8_bin,
    `ISBN` text CHARACTER SET utf8 COLLATE utf8_bin,
    `Quantity` int DEFAULT 1 NOT NULL,
    `QuantityBorrowed` int DEFAULT 0 NOT NULL,
    `Metadata` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `charges`;
CREATE TABLE IF NOT EXISTS `charges` (
    `ID` int NOT NULL,
    `BookIdentifier` int NOT NULL,
    `UserIdentifier` int NOT NULL,
    `BorrowDate` date NOT NULL,
    `ReturnDate` date DEFAULT NULL,
    `Active` tinyint(1) DEFAULT 0,
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
    `Level` tinyint NOT NULL DEFAULT '2',
    `Grade` text CHARACTER SET utf8 COLLATE utf8_bin,
    `Metadata` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors` (
    `ID` int NOT NULL,
    `Name` text COLLATE utf8_bin NOT NULL,
    `PictureURL` text COLLATE utf8_bin,
    `Description` text COLLATE utf8_bin NOT NULL,
    `Metadata` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `users` (`ID`, `Identifier`, `Name`, `Username`, `Email`, `Password`, `Algo`, `Level`, `Grade`, `Metadata`) VALUES
(1, '', 'Admin', 'root', '', '$2y$10$u8dSfD7oVovNtgKKvJ0V0u1m0XVVE8TX/fR52B2L4JSIoA4nF4FcK', 'sha256', 0, '', '{}');


ALTER TABLE `books`
    ADD PRIMARY KEY (`ID`);

ALTER TABLE `charges`
    ADD PRIMARY KEY (`ID`);

ALTER TABLE `users`
    ADD PRIMARY KEY (`ID`);

ALTER TABLE `authors`
    ADD PRIMARY KEY (`ID`);


ALTER TABLE `books`
    MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `charges`
    MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
    MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `authors`
    MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

COMMIT;