CREATE TABLE IF NOT EXISTS contentType(
	id int auto_increment primary key,
	contentTypeName varchar(10) not null,
	extension varchar(20) not null
);

CREATE TABLE IF NOT EXISTS content(
	id int auto_increment primary key,
	contentName varchar(100),
	contentContainer int default 0,
	contentTypeID int not null,
	foreign key (contentTypeID) references contentType(id)
)