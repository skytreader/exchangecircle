/*
event_organizer is the email address of the person who created the event.
*/
CREATE TABLE events(
    event_id INTEGER AUTO INCREMENT NOT NULL,
    event_name VARCHAR(50) NOT NULL,
    event_organizer VARCHAR(100) NOT NULL,
    PRIMARY KEY event_id
) ENGINE=INNODB;

CREATE TABLE event_settings(
    event_id INTEGER NOT NULL,
    confirm_deadline DATE NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events (event_id)
) ENGINE=INNODB;

CREATE TABLE invited(
    invitation_id INTEGER AUTO INCREMENT NOT NULL,
    event_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(50),
    confirmation_code CHAR(255) UNIQUE NOT NULL,
    date_confirmed TIMESTAMP DEFAULT NULL,
    FOREIGN KEY (event_id) REFERENCES events (event_id)
    PRIMARY KEY invitation_id
) ENGINE=INNODB;
