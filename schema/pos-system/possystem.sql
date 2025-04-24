SET SESSION FOREIGN_KEY_CHECKS=0;

-- 商品テーブル
CREATE TABLE items (
    id INT NOT NULL AUTO_INCREMENT COMMENT '商品ID',
    name VARCHAR(255) NOT NULL COMMENT '商品名',
    price DECIMAL(10,2) NOT NULL COMMENT '税抜き価格',
    created_at TIMESTAMP NOT NULL COMMENT '登録日時',
    updated_at TIMESTAMP NOT NULL COMMENT '更新日時',
    PRIMARY KEY (id)
) COMMENT='商品マスターテーブル';

-- 売上（注文明細）テーブル
CREATE TABLE sales (
    id INT NOT NULL AUTO_INCREMENT COMMENT '注文明細ID',
    quantity INT NOT NULL COMMENT '購入数',
    total_price DECIMAL(10,2) NOT NULL COMMENT '税込み価格（数量×単価＋消費税）',
    tax DECIMAL(10,2) NOT NULL COMMENT '消費税',
    created_at TIMESTAMP NOT NULL COMMENT '売上日時',
    item_id INT NOT NULL COMMENT '対象商品のID（items.id）',
    PRIMARY KEY (id)
) COMMENT='売上・注文データ';

-- ユーザーテーブル
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'ユーザーID',
    username VARCHAR(50) NOT NULL COMMENT 'ユーザー名（ログイン用）',
    password VARCHAR(255) NOT NULL COMMENT 'ハッシュ化されたログインパスワード',
    created_at TIMESTAMP NOT NULL COMMENT '登録日時',
    updated_at TIMESTAMP NOT NULL COMMENT '更新日時',
    PRIMARY KEY (id),
    UNIQUE (username)
) COMMENT='ユーザー管理テーブル（管理者のみ）';

-- 外部キー制約
ALTER TABLE sales
    ADD FOREIGN KEY (item_id)
    REFERENCES items (id)
    ON UPDATE RESTRICT
    ON DELETE RESTRICT;
