#DROP TABLE TA_ReviewPicture;
#DROP TABLE TA_Review;
#DROP TABLE TA_User;
#DROP TABLE TA_Hotel;
#Drop Table TA_HotelPictures;

CREATE TABLE TA_User
(UserID numeric PRIMARY key, 
UserName varchar(255),
Userrating numeric,
Usergender varchar (255),
UserCountry varchar (255),
UserAGE numeric,
UserCreationDate timestamp,
UserUpdateDate timestamp);

CREATE TABLE TA_Hotel
(HotelID numeric PRIMARY key,
HotelName varchar (255),
HotelLocation varchar (255),
HotelRating numeric,
AmountofReviews numeric,
HotelRank numeric,
HotelCreationDate timestamp,
HotelUpdateDate timestamp);

CREATE TABLE TA_HotelPictures
(HotelPicturesID numeric PRIMARY key,
HotelID numeric,
Herkunft varchar (255),
HotelPictureAddress varchar(255),
 FOREIGN key (HotelID) REFERENCES TA_Hotel(HotelID));

CREATE TABLE TA_Review
(ReviewID numeric PRIMARY key,
 ReviewTitel varchar (255),
 ReviewText text,
UserStayDate varchar (255),
 HotelID numeric,
 UserID numeric,
 FOREIGN key (HotelID) REFERENCES TA_Hotel(HotelID),
 FOREIGN key (UserID) REFERENCES TA_User(UserID));

CREATE TABLE TA_ReviewPicture
(ReviewPictureID numeric PRIMARY key,
ReviewID numeric,
UserPictureNumber numeric,
UserPictureAddress varchar(255));