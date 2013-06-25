-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- ホスト: 127.0.0.1
-- 生成日時: 2013 年 6 月 23 日 16:53
-- サーバのバージョン: 5.5.27
-- PHP のバージョン: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- データベース: `feegle`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `author_table`
--

CREATE TABLE IF NOT EXISTS `author_table` (
  `AUTHOR_ID` int(11) NOT NULL AUTO_INCREMENT,
  `AUTHOR_NAME` varchar(100) NOT NULL,
  `USER_ID` char(100) NOT NULL,
  `RDATE` datetime DEFAULT NULL,
  `UDATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`AUTHOR_ID`),
  UNIQUE KEY `AUTHOR_NAME` (`AUTHOR_NAME`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- テーブルのデータのダンプ `author_table`
--

INSERT INTO `author_table` (`AUTHOR_ID`, `AUTHOR_NAME`, `USER_ID`, `RDATE`, `UDATE`) VALUES
(6, '西原 理恵子', 'kahi556@gmail.com', '2013-06-23 11:04:05', '2013-06-23 09:04:06');

-- --------------------------------------------------------

--
-- テーブルの構造 `book_review_table`
--

CREATE TABLE IF NOT EXISTS `book_review_table` (
  `BOOK_REVIEW_NO` int(11) NOT NULL AUTO_INCREMENT,
  `BOOK_REVIEW` longtext,
  `BOOK_ID` int(11) NOT NULL,
  `TAG` varchar(1000) NOT NULL COMMENT 'カンマ区切りで複数指定可',
  `FEELING` varchar(100) NOT NULL,
  `USER_ID` char(100) NOT NULL,
  `RDATE` datetime DEFAULT NULL,
  `UDATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`BOOK_REVIEW_NO`),
  KEY `BOOK_ID` (`BOOK_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- テーブルのデータのダンプ `book_review_table`
--

INSERT INTO `book_review_table` (`BOOK_REVIEW_NO`, `BOOK_REVIEW`, `BOOK_ID`, `TAG`, `FEELING`, `USER_ID`, `RDATE`, `UDATE`) VALUES
(1, 'いいい', 6, 'ううう', 'normal', 'kahi556@gmail.com', '2013-06-23 11:04:05', '2013-06-23 09:04:06');

-- --------------------------------------------------------

--
-- テーブルの構造 `book_table`
--

CREATE TABLE IF NOT EXISTS `book_table` (
  `BOOK_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BOOK_NAME` varchar(255) NOT NULL,
  `ISBN` char(50) DEFAULT NULL COMMENT '国際標準図書番号（日本図書コード・書籍JANコード）',
  `AUTHOR_ID` int(11) NOT NULL,
  `USER_ID` char(100) NOT NULL,
  `RDATE` datetime DEFAULT NULL,
  `UDATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`BOOK_ID`),
  KEY `AUTHOR_ID` (`AUTHOR_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- テーブルのデータのダンプ `book_table`
--

INSERT INTO `book_table` (`BOOK_ID`, `BOOK_NAME`, `ISBN`, `AUTHOR_ID`, `USER_ID`, `RDATE`, `UDATE`) VALUES
(6, 'ああ息子', '4620317470', 6, 'kahi556@gmail.com', '2013-06-23 11:04:05', '2013-06-23 09:04:06');

-- --------------------------------------------------------

--
-- テーブルの構造 `ljob_table`
--

CREATE TABLE IF NOT EXISTS `ljob_table` (
  `LJOB_CD` char(5) NOT NULL,
  `LJOB_NAME` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`LJOB_CD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `ljob_table`
--

INSERT INTO `ljob_table` (`LJOB_CD`, `LJOB_NAME`) VALUES
('A', '管理的公務員'),
('B', '専門的・技術的職業'),
('C', '事務'),
('D', '販売'),
('E', 'サービス職業'),
('F', '保安職業'),
('G', '農林漁業'),
('H', '生産工程'),
('I', '輸送・機械運転'),
('J', '建設・採掘'),
('K', '運搬・清掃・包装等');

-- --------------------------------------------------------

--
-- テーブルの構造 `mjob_table`
--

CREATE TABLE IF NOT EXISTS `mjob_table` (
  `MJOB_CD` char(5) NOT NULL,
  `LJOB_CD` char(5) NOT NULL,
  `MJOB_NAME` varchar(255) NOT NULL,
  PRIMARY KEY (`MJOB_CD`,`LJOB_CD`),
  KEY `LJOB_CD` (`LJOB_CD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mjob_table`
--

INSERT INTO `mjob_table` (`MJOB_CD`, `LJOB_CD`, `MJOB_NAME`) VALUES
('01', 'A', '管理的公務員'),
('02', 'A', '法人・団体の役員'),
('03', 'A', '法人・団体管理職員'),
('04', 'A', 'その他の管理的職業従事者'),
('05', 'B', '研究者'),
('06', 'B', '農林水産技術者'),
('07', 'B', '製造技術者（開発）'),
('08', 'B', '製造技術者'),
('09', 'B', '建築・土木・測量技術者'),
('10', 'B', '情報処理・通信技術者'),
('11', 'B', 'その他の技術者'),
('12', 'B', '医師、歯科医師、獣医師、薬剤師'),
('13', 'B', '保健師、助産師、看護師'),
('14', 'B', '医療技術者'),
('15', 'B', 'その他の保健医療'),
('16', 'B', '社会福祉の専門的職業'),
('17', 'B', '法務'),
('18', 'B', '経営・金融・保険の専門的職業'),
('19', 'B', '教育'),
('20', 'B', '宗教家'),
('21', 'B', '著述家、記者、編集者'),
('22', 'B', '美術家、デザイナー、写真家、映像撮影者'),
('23', 'B', '音楽家、舞台芸術家'),
('24', 'B', 'その他の専門的職業'),
('25', 'C', '一般事務'),
('26', 'C', '会計事務'),
('27', 'C', '生産関連事務従事者'),
('28', 'C', '営業・販売関連事務'),
('29', 'C', '外勤事務'),
('30', 'C', '運輸・郵便事務'),
('31', 'C', '事務用機器操作'),
('32', 'D', '商品販売'),
('33', 'D', '販売類似'),
('34', 'D', '営業'),
('35', 'E', '家庭生活支援サービス'),
('36', 'E', '介護サービス'),
('37', 'E', '保健医療サービス'),
('38', 'E', '生活衛生サービス '),
('39', 'E', '飲食物調理'),
('40', 'E', '接客・給仕'),
('41', 'E', '居住施設・ビル等の管理'),
('42', 'E', 'その他のサービス'),
('43', 'F', '自衛官'),
('44', 'F', '司法警察職員'),
('45', 'F', 'その他の保安'),
('46', 'G', '農業'),
('47', 'G', '林業'),
('48', 'G', '漁業'),
('49', 'H', '生産設備制御・監視（金属材料製造、金属加工、金属溶接・溶断）'),
('50', 'H', '生産設備制御・監視（金属材料製造、金属加工、金属溶接・溶断を除く）'),
('51', 'H', '生産設備制御・監視（機械組立）'),
('52', 'H', '金属材料製造、金属加工、金属溶接・溶断'),
('54', 'H', '製品製造・加工処理（金属材料製造、金属加工、金属溶接・溶断を除く）'),
('57', 'H', '機械組立'),
('60', 'H', '機械整備・修理'),
('61', 'H', '製品検査（金属材料製造、金属加工、金属溶接・溶断）'),
('62', 'H', '製品検査（金属材料製造、金属加工、金属溶接・溶断を除く）'),
('63', 'H', '機械検査'),
('64', 'H', '生産関連・生産類似'),
('65', 'I', '鉄道運転'),
('66', 'I', '自動車運転'),
('67', 'I', '船舶・航空機運転'),
('68', 'I', 'その他の輸送'),
('69', 'I', '定置・建設機械運転'),
('70', 'J', '建設躯体工事'),
('71', 'J', '建設（建設躯体工事を除く）'),
('72', 'J', '電気工事'),
('73', 'J', '土木'),
('74', 'J', '採掘'),
('75', 'K', '運搬'),
('76', 'K', '清掃'),
('78', 'K', 'その他の運搬・清掃・包装等');

-- --------------------------------------------------------

--
-- テーブルの構造 `recommend_table`
--

CREATE TABLE IF NOT EXISTS `recommend_table` (
  `RECO_NO` int(11) NOT NULL AUTO_INCREMENT,
  `USER_ID` char(24) NOT NULL,
  `BOOK_ID` int(11) NOT NULL,
  `RDATE` datetime DEFAULT NULL,
  `UDATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RECO_NO`,`USER_ID`),
  KEY `BOOK_ID` (`BOOK_ID`),
  KEY `USER_ID` (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `user_reg_table`
--

CREATE TABLE IF NOT EXISTS `user_reg_table` (
  `USER_ID` char(24) NOT NULL,
  `PASSWORD` char(24) DEFAULT NULL,
  `NICKNAME` varchar(50) DEFAULT NULL,
  `BIRTH` date DEFAULT NULL,
  `GENDER` char(1) DEFAULT NULL COMMENT 'M:男性、F:女性',
  `MJOB_CD` char(5) NOT NULL,
  `LJOB_CD` char(5) NOT NULL,
  `RKEY` varchar(50) DEFAULT NULL,
  `RDATE` datetime DEFAULT NULL,
  `STATUS` int(11) DEFAULT NULL COMMENT '1:本登録済',
  PRIMARY KEY (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `user_table`
--

CREATE TABLE IF NOT EXISTS `user_table` (
  `USER_ID` char(100) NOT NULL,
  `PASSWORD` char(24) DEFAULT NULL,
  `NICKNAME` varchar(100) DEFAULT NULL,
  `BIRTH` date DEFAULT NULL,
  `GENDER` char(1) DEFAULT NULL COMMENT 'M:男性、F:女性',
  `MJOB_CD` char(5) DEFAULT NULL,
  `LJOB_CD` char(5) DEFAULT NULL,
  `PREMIUM_DEGREE` int(11) DEFAULT NULL,
  `REVIEW_POSTS_CNT` int(11) DEFAULT NULL COMMENT '書評投稿数',
  `THANKS_CNT` int(11) DEFAULT NULL COMMENT '「ありがとう」獲得数',
  `FOLLOW_CNT` int(11) DEFAULT NULL,
  `FOLLOWER_CNT` int(11) DEFAULT NULL,
  `KEYWORD` varchar(1000) DEFAULT NULL,
  `RDATE` datetime DEFAULT NULL,
  `UDATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `user_table`
--

INSERT INTO `user_table` (`USER_ID`, `PASSWORD`, `NICKNAME`, `BIRTH`, `GENDER`, `MJOB_CD`, `LJOB_CD`, `PREMIUM_DEGREE`, `REVIEW_POSTS_CNT`, `THANKS_CNT`, `FOLLOW_CNT`, `FOLLOWER_CNT`, `RDATE`, `UDATE`) VALUES
('kahi556@gmail.com', 'JyT1/rFnpBqD2', 'test', '1998-09-30', 'M', '11', 'B', NULL, NULL, NULL, NULL, NULL, '2013-05-25 11:29:04', '2013-05-25 09:29:05');

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `book_review_table`
--
ALTER TABLE `book_review_table`
  ADD CONSTRAINT `book_review_table_ibfk_1` FOREIGN KEY (`BOOK_ID`) REFERENCES `book_table` (`BOOK_ID`);

--
-- テーブルの制約 `book_table`
--
ALTER TABLE `book_table`
  ADD CONSTRAINT `book_table_ibfk_1` FOREIGN KEY (`AUTHOR_ID`) REFERENCES `author_table` (`AUTHOR_ID`);

--
-- テーブルの制約 `mjob_table`
--
ALTER TABLE `mjob_table`
  ADD CONSTRAINT `mjob_table_ibfk_1` FOREIGN KEY (`LJOB_CD`) REFERENCES `ljob_table` (`LJOB_CD`);

--
-- テーブルの制約 `recommend_table`
--
ALTER TABLE `recommend_table`
  ADD CONSTRAINT `recommend_table_ibfk_1` FOREIGN KEY (`BOOK_ID`) REFERENCES `book_table` (`BOOK_ID`),
  ADD CONSTRAINT `recommend_table_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `user_table` (`USER_ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
