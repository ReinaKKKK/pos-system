SET SESSION FOREIGN_KEY_CHECKS=0;


/* Create Tables */

-- availabilities
CREATE TABLE availabilities
(
	id bigint NOT NULL COMMENT 'id',
	event_id bigint NOT NULL COMMENT 'event_id',
	date date NOT NULL COMMENT 'date',
	time_slot time COMMENT 'time_slot',
	created_at timestamp COMMENT 'created_at',
	updated_at timestamp COMMENT 'updated_at',
	PRIMARY KEY (id)
) COMMENT = 'availabilities';


-- イベント作成
CREATE TABLE events
(
	id bigint NOT NULL AUTO_INCREMENT COMMENT 'id',
	name varchar(255) NOT NULL COMMENT 'name',
	url varchar(255) NOT NULL COMMENT 'url',
	edit_password varchar(255) COMMENT 'edit_password',
	detail text COMMENT 'detail',
	created_at timestamp COMMENT 'created_at',
	updated_at timestamp COMMENT 'updated_at',
	PRIMARY KEY (id)
) COMMENT = 'イベント作成';


-- responses
CREATE TABLE responses
(
	id bigint NOT NULL COMMENT 'id',
	user_id bigint NOT NULL COMMENT 'user_id',
	availability_id bigint NOT NULL COMMENT 'availability_id',
	response tinyint NOT NULL COMMENT 'response',
	comment text COMMENT 'comment',
	created_at timestamp COMMENT 'created_at',
	updated_at timestamp COMMENT 'updated_at',
	PRIMARY KEY (id)
) COMMENT = 'responses';


-- 参加者
CREATE TABLE users
(
	id bigint NOT NULL COMMENT 'id',
	event_id bigint NOT NULL COMMENT 'event_id',
	name varchar(255) NOT NULL COMMENT 'name',
	response_id bigint NOT NULL COMMENT 'response_id',
	created_at timestamp COMMENT 'created_at',
	updated_at timestamp COMMENT 'updated_at',
	PRIMARY KEY (id)
) COMMENT = '参加者';



/* Create Foreign Keys */

ALTER TABLE responses
	ADD FOREIGN KEY (availability_id)
	REFERENCES availabilities (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE availabilities
	ADD FOREIGN KEY (event_id)
	REFERENCES events (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE users
	ADD FOREIGN KEY (event_id)
	REFERENCES events (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE responses
	ADD FOREIGN KEY (user_id)
	REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;



