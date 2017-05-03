#DROP TABLE TA_ReviewPicture;
#DROP TABLE TA_Review;
#DROP TABLE TA_User;
#DROP TABLE TA_Hotel;

CREATE TABLE TA_User
(UserID numeric PRIMARY key, 
UserName varchar(255),
Userrating numeric,
Usergender varchar (255),
UserCountry varchar (255),
UserAGE numeric,
UserStayDate varchar (255),
UserReviewTitel varchar (255),
UserReviewText varchar (1000),
UserCreationDate numeric,
UserUpdateDate numeric);

CREATE TABLE TA_Hotel
(HotelID numeric PRIMARY key,
HotelName varchar (255),
HotelLocation varchar (255),
HotelRank numeric);

CREATE TABLE TA_Review
(ReviewID numeric PRIMARY key,
 HotelRating numeric,
 AmountofReviews numeric,
 HotelID numeric,
 UserID numeric,
 FOREIGN key (HotelID) REFERENCES TA_Hotel(HotelID),
 FOREIGN key (UserID) REFERENCES TA_User(UserID));

CREATE TABLE TA_ReviewPicture
(ReviewPictureID numeric PRIMARY key,
ReviewID numeric,
UserPictureNumber numeric,
UserPictureAddress varchar(255));