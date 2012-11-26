CREATE TABLE event_settings(
	event_name VARCHAR(50) NOT NULL,
	confirm_deadline DATE NOT NULL
) ENGINE=INNODB;

CREATE TABLE invited(
	invitation_id INTEGER AUTO INCREMENT NOT NULL,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(50),
	date_confirmed TIMESTAMP,
	PRIMARY KEY invitation_id
) ENGINE=INNODB;

/**
Security table:
For email for email confirmations.
*/
CREATE TABLE mail_confirmations(
	invitation_id INTEGER NOT NULL,
	confirmation_code CHAR(255),
	has_confirmed BOOLEAN DEFAULT FALSE,
	FOREIGN KEY invitation_id REFERENCES invited
) ENGINE=INNODB;
