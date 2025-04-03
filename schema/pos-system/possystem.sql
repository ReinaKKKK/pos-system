SET SESSION FOREIGN_KEY_CHECKS=0;


/* Create Tables */

CREATE TABLE admin_login
(
	-- 管理者ID
	id int NOT NULL AUTO_INCREMENT COMMENT '管理者ID',
	-- 管理者名
	username varchar(255) NOT NULL COMMENT '管理者名',
	-- メールアドレス
	email varchar(255) NOT NULL COMMENT 'メールアドレス',
	-- 登録日時
	password varchar(255) NOT NULL COMMENT '登録日時',
	PRIMARY KEY (id)
);


CREATE TABLE orders
(
	-- 注文明細ID
	id int NOT NULL AUTO_INCREMENT COMMENT '注文明細ID',
	-- 購入数
	quantity int NOT NULL COMMENT '購入数',
	-- 税抜き価格
	price decimal(10,2) NOT NULL COMMENT '税抜き価格',
	-- 税込み価格
	total_price decimal(10,2) NOT NULL COMMENT '税込み価格',
	-- 商品ID
	product_id int NOT NULL COMMENT '商品ID',
	-- ユーザーID
	user_id int NOT NULL COMMENT 'ユーザーID',
	PRIMARY KEY (id)
);


CREATE TABLE products
(
	-- 商品ID
	id int NOT NULL AUTO_INCREMENT COMMENT '商品ID',
	-- 商品名
	name varchar(255) NOT NULL COMMENT '商品名',
	-- 税抜き価格
	price decimal(10,2) NOT NULL COMMENT '税抜き価格',
	-- 在庫数
	stock int NOT NULL COMMENT '在庫数',
	-- 登録日時
	created_at timestamp NOT NULL COMMENT '登録日時',
	-- 更新日時
	updated_at timestamp NOT NULL COMMENT '更新日時',
	PRIMARY KEY (id)
);


CREATE TABLE users
(
	-- ユーザーID
	id int NOT NULL AUTO_INCREMENT COMMENT 'ユーザーID',
	-- ユーザー名
	name varchar(255) NOT NULL COMMENT 'ユーザー名',
	-- メールアドレス（ユニーク）
	email varchar(255) NOT NULL COMMENT 'メールアドレス（ユニーク）',
	-- ハッシュ化したパスワード
	password varchar(255) NOT NULL COMMENT 'ハッシュ化したパスワード',
	-- 登録日時
	created_at timestamp NOT NULL COMMENT '登録日時',
	-- 更新日時
	updated_at timestamp NOT NULL COMMENT '更新日時',
	PRIMARY KEY (id)
);



/* Create Foreign Keys */

ALTER TABLE orders
	ADD FOREIGN KEY (product_id)
	REFERENCES products (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE orders
	ADD FOREIGN KEY (user_id)
	REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;



