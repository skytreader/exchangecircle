/**
This table will have one and only one record.
*/
CREATE TABLE event_settings(
	event_name VARCHAR(50) NOT NULL,
	confirm_deadline DATE NOT NULL
) ENGINE=INNODB;

CREATE TABLE invited(
	invitation_id INTEGER AUTO INCREMENT NOT NULL,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(50),
	confirmation_code CHAR(255) UNIQUE NOT NULL,
	date_confirmed TIMESTAMP DEFAULT NULL,
	PRIMARY KEY invitation_id
) ENGINE=INNODB;
