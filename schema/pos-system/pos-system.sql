SET SESSION FOREIGN_KEY_CHECKS=0;


/* Create Tables */

CREATE TABLE admin_users
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


CREATE TABLE cart
(
	-- カートID
	id int NOT NULL AUTO_INCREMENT COMMENT 'カートID',
	-- 購入数
	quantity int NOT NULL COMMENT '購入数',
	-- 追加日時
	created_at timestamp NOT NULL COMMENT '追加日時',
	-- ユーザーID
	user_id int NOT NULL COMMENT 'ユーザーID',
	-- 商品ID
	product_id int NOT NULL COMMENT '商品ID',
	PRIMARY KEY (id)
);


CREATE TABLE orders
(
	-- 注文ID
	id int NOT NULL AUTO_INCREMENT COMMENT '注文ID',
	-- 税込み合計金額
	total_price decimal(10,2) NOT NULL COMMENT '税込み合計金額',
	tax_amount decimal(10,2) NOT NULL,
	-- 注文日時
	created_at timestamp NOT NULL COMMENT '注文日時',
	-- ユーザーID
	user_id int NOT NULL COMMENT 'ユーザーID',
	PRIMARY KEY (id)
);


CREATE TABLE order_items
(
	-- 注文明細ID
	id int NOT NULL AUTO_INCREMENT COMMENT '注文明細ID',
	-- 購入数
	quantity int NOT NULL COMMENT '購入数',
	-- 税抜き価格
	price decimal(10,2) NOT NULL COMMENT '税抜き価格',
	-- 消費税率
	tax_rate decimal(5,2) NOT NULL COMMENT '消費税率',
	-- 消費税額
	tax_amount decimal(10,2) NOT NULL COMMENT '消費税額',
	-- 税込み価格
	total_price decimal(10,2) NOT NULL COMMENT '税込み価格',
	-- 商品ID
	product_id int NOT NULL COMMENT '商品ID',
	-- 注文ID
	order_id int NOT NULL COMMENT '注文ID',
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
	-- 消費税率（例: 0.10 = 10%）
	tax_rate decimal(5,2) NOT NULL COMMENT '消費税率（例: 0.10 = 10%）',
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

ALTER TABLE order_items
	ADD FOREIGN KEY (order_id)
	REFERENCES orders (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE cart
	ADD FOREIGN KEY (product_id)
	REFERENCES products (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE order_items
	ADD FOREIGN KEY (product_id)
	REFERENCES products (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE cart
	ADD FOREIGN KEY (user_id)
	REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE orders
	ADD FOREIGN KEY (user_id)
	REFERENCES users (id)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;



