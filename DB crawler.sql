-- CREATE DATABASE `crawler`;
-- DROP TABLE ta_reviewpicture;
-- DROP TABLE ta_review;
-- DROP TABLE ta_hotel;
-- DROP TABLE ta_user;

CREATE TABLE `ta_hotel` (
  `HotelID` decimal(10,0) NOT NULL,
  `HotelName` varchar(255) DEFAULT NULL,
  `HotelStreet` varchar(255) DEFAULT NULL,
  `HotelPostalCode` varchar(255) DEFAULT NULL,
  `HotelLocation` varchar(255) DEFAULT NULL,
  `HotelRegion` varchar(255) DEFAULT NULL,
  `HotelCountry` varchar(255) DEFAULT NULL,
  `HotelRating` double DEFAULT NULL,
  `AmountofReviews` decimal(10,0) DEFAULT NULL,
  `HotelRank` decimal(10,0) DEFAULT NULL,
  `HotelCreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `HotelUpdateDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`HotelID`)
);

CREATE TABLE `ta_user` (
  `UserID` decimal(10,0) NOT NULL,
  `UserName` varchar(255) DEFAULT NULL,
  `Userrating` decimal(10,0) DEFAULT NULL,
  `Usergender` varchar(255) DEFAULT NULL,
  `UserCountry` varchar(255) DEFAULT NULL,
  `UserAGE` varchar(255) DEFAULT NULL,
  `UserCreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UserUpdateDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`UserID`)
);

CREATE TABLE `ta_review` (
  `ReviewID` decimal(10,0) NOT NULL,
  `ReviewTitel` varchar(255) DEFAULT NULL,
  `ReviewText` text,
  `UserStayDate` varchar(255) DEFAULT NULL,
  `HotelID` decimal(10,0) DEFAULT NULL,
  `UserID` decimal(10,0) DEFAULT NULL,
  `ReviewCreationDate` timestamp NULL DEFAULT NULL,
  `ReviewUpdateDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ReviewID`),
  KEY `HotelID` (`HotelID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `ta_review_ibfk_1` FOREIGN KEY (`HotelID`) REFERENCES `ta_hotel` (`HotelID`),
  CONSTRAINT `ta_review_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `ta_user` (`UserID`)
);

CREATE TABLE `ta_reviewpicture` (
  `ReviewPictureID` decimal(10,0) NOT NULL,
  `ReviewID` decimal(10,0) NOT NULL,
  `ReviewPictureURL` varchar(255) DEFAULT NULL,
  `ReviewPictureCreationDate` timestamp NULL DEFAULT NULL,
  `ReviewPictureUpdateDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ReviewPictureID`,`ReviewID`),
  KEY `ta_reviewpicture_ibfk_1` (`ReviewID`),
  CONSTRAINT `ta_reviewpicture_ibfk_1` FOREIGN KEY (`ReviewID`) REFERENCES `ta_review` (`ReviewID`) ON UPDATE CASCADE
);
