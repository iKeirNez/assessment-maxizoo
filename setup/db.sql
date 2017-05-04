/**
Erases all data and tables, allowing the site to be setup from scratch
A few demo values have been included to quickly get the site up and running
 */

-- DROP OLD TABLES
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS events;

-- SETUP TABLE 'users'
CREATE TABLE users
(
  id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  username VARCHAR(20) NOT NULL,
  first_name VARCHAR(20) NOT NULL,
  last_name VARCHAR(20) NOT NULL,
  password VARCHAR(25) NOT NULL,
  interest_area VARCHAR(20) NOT NULL,
  admin TINYINT(1) DEFAULT '0' NOT NULL
);
CREATE UNIQUE INDEX users_username_uindex ON users (username);

-- SETUP TABLE 'events'
CREATE TABLE events
(
  id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  name VARCHAR(40) NOT NULL,
  time DATETIME NOT NULL,
  venue VARCHAR(20) NOT NULL,
  cost DECIMAL(10) NOT NULL,
  locked TINYINT(1) DEFAULT '0' NOT NULL
);

-- SETUP TABLE 'bookings'
CREATE TABLE bookings
(
  userId INT(11) NOT NULL,
  eventId INT(11) NOT NULL,
  CONSTRAINT `PRIMARY` PRIMARY KEY (userId, eventId),
  CONSTRAINT bookings_users_id_fk FOREIGN KEY (userId) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT bookings_events_id_fk FOREIGN KEY (eventId) REFERENCES events (id) ON DELETE CASCADE
);

-- INSERT SAMPLE USERS
INSERT INTO keir05.users (username, first_name, last_name, password, interest_area, admin) VALUES ('admin', 'System', 'Administrator', 'admin', 'computing', 1);
INSERT INTO keir05.users (username, first_name, last_name, password, interest_area, admin) VALUES ('iKeirNez', 'Keir', 'Nellyer', 'asdf', 'cars', 1);
INSERT INTO keir05.users (username, first_name, last_name, password, interest_area, admin) VALUES ('jsmith', 'John', 'Smith', 'zxcv', 'music', 0);

-- INSERT SAMPLE EVENTS
INSERT INTO keir05.events (name, time, venue, cost, locked) VALUES ('Zoo Keeping 101', '2018-03-15 16:00:00', 'Edinburgh Zoo', 20, 0);
INSERT INTO keir05.events (name, time, venue, cost, locked) VALUES ('How To Raise A Gorilla', '2020-07-15 20:00:00', 'Cincinnati Zoo', 15, 0);
INSERT INTO keir05.events (name, time, venue, cost, locked) VALUES ('Picking A Suitable Zoo Enclosure', '2022-11-24 13:00:00', 'Cincinnati Zoo', 34, 0);
INSERT INTO keir05.events (name, time, venue, cost, locked) VALUES ('Small Children & Gorillas', '2016-11-25 20:00:00', 'Cincinnati Zoo', 50, 0);
INSERT INTO keir05.events (name, time, venue, cost, locked) VALUES ('Remember Remember Harambe', '2018-02-06 09:00:00', 'Cincinnati Zoo', 60, 1);
INSERT INTO keir05.events (name, time, venue, cost, locked) VALUES ('Harambe Dance Contest', '2017-01-12 18:00:00', 'Cincinnati Zoo', 5, 0);

-- INSERT SAMPLE BOOKINGS
INSERT INTO keir05.bookings (userId, eventId) VALUES ((SELECT id FROM users WHERE username = 'iKeirNez'), (SELECT id FROM events WHERE name = 'Zoo Keeping 101'));
INSERT INTO keir05.bookings (userId, eventId) VALUES ((SELECT id FROM users WHERE username = 'jsmith'), (SELECT id FROM events WHERE name = 'How To Raise A Gorilla'));
INSERT INTO keir05.bookings (userId, eventId) VALUES ((SELECT id FROM users WHERE username = 'iKeirNez'), (SELECT id FROM events WHERE name = 'Picking A Suitable Zoo Enclosure'));
INSERT INTO keir05.bookings (userId, eventId) VALUES ((SELECT id FROM users WHERE username = 'jsmith'), (SELECT id FROM events WHERE name = 'Small Children & Gorillas'));
INSERT INTO keir05.bookings (userId, eventId) VALUES ((SELECT id FROM users WHERE username = 'iKeirNez'), (SELECT id FROM events WHERE name = 'Harambe Dance Contest'));