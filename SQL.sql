

-------------------------------------------------使ってないけど参考に---------------------------------------------------------

create database ahpgtest_calendar default character set utf8 collate utf8_general_ci;
grant all on ahpgtest_calendar.* to 'ahpgtest_atsu'@'mysql1.php.starfree.ne.jp' identified by 'my2020';
-----------------------------------------------------------------------------------------------------------------------------




--------------------------------ログイン機能のテーブル作成(旧タイプと供用)-------------------------------------

use ahpgtest_calendar;

Create table login(
	username varchar(10),
	password varchar(10)
);

INSERT INTO login value('guest','abc123');

INSERT INTO login value('guest2','def456');

--後からカラム追加--

ALTER TABLE login ADD id int auto_increment primary key FIRST;
ALTER TABLE login ADD name varchar(90);


-------------------------------------テーブルを見直して新しく構築-----------------------------------------------

use ahpgtest_calendar;

Create table All_Memo(
	id int auto_increment primary key,
	username varchar(10),
	password varchar(10),
	year int(4),
	month int(2),
	day int(2),
	memo varchar(50),
	td_color varchar(10),
	text_color varchar(10)
);

INSERT INTO All_Memo value(Null,'guest','abc123',2020,8,21,'これはテストです',Null,Null);
INSERT INTO All_Memo value(Null,'guest','abc123',2020,8,11,'これはテスト2です',Null,Null);

-------------------------------------(ver.3)テーブル-----------------------------------------------
use ahpgtest_calendar;

Create table Memo_tags(
	id int auto_increment primary key,
	userid  varchar(15),
	year int(4),
	month int(2),
	day int(2),
	statt_time date,
	end_time date,
	title varchar(90),
	memo text,
	progress int(1),
	color varchar(10),
	logic_delete varchar(10)
);

INSERT INTO Memo_tags values(Null,'guest',2020,9,21,'9:00','10:00','予定のタイトル','予定の詳細な内容を記述',1,'#33FFFF','false');
INSERT INTO Memo_tags values(Null,'guest',2020,9,21,'9:00','10:00','予定のタイトル2','予定の詳細な内容を記述',1,'#33FFFF','false');
INSERT INTO Memo_tags values(Null,'guest',2020,9,21,'9:00','10:00','予定のタイトル3','予定の詳細な内容を記述',1,'#33FFFF','false');
INSERT INTO Memo_tags values(Null,'guest',2020,9,22,'9:00','10:00','予定のタイトル4','予定の詳細な内容を記述',1,'#33FFFF','false');