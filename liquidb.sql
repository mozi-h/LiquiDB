-- --------------------------------------------------------
-- Host:                         192.168.178.43
-- Server Version:               10.3.17-MariaDB-0+deb10u1 - Raspbian 10
-- Server Betriebssystem:        debian-linux-gnueabihf
-- HeidiSQL Version:             10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportiere Datenbank Struktur für liquidb
CREATE DATABASE IF NOT EXISTS `liquidb` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `liquidb`;

-- Exportiere Struktur von Tabelle liquidb.attendance
CREATE TABLE IF NOT EXISTS `attendance` (
  `date` date NOT NULL DEFAULT cast(current_timestamp() as date),
  `participant_id` int(10) unsigned NOT NULL,
  `other_amount` tinyint(3) unsigned DEFAULT NULL,
  `paid` enum('Yes','No','Other') NOT NULL COMMENT 'Yes: gezahlt\r\nNo: nicht gezahlen / muss nicht zahlen\r\nOther: andere Regelung (z.B. Jahreskarte)',
  `author_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`date`,`participant_id`),
  KEY `FK_attendance_participant` (`participant_id`),
  KEY `FK_attendance_user` (`author_user_id`),
  CONSTRAINT `FK_attendance_participant` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id`),
  CONSTRAINT `FK_attendance_user` FOREIGN KEY (`author_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Pro Datum kann je Zeile entweder:\r\nparticipant_id und ob/wie gezahlt wurde\r\nODER\r\nother_amount -> nicht als participant vorhandene anzahl anderer personen, die ob/wie gezahlt haben';

-- Exportiere Daten aus Tabelle liquidb.attendance: ~8 rows (ungefähr)
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` (`date`, `participant_id`, `other_amount`, `paid`, `author_user_id`) VALUES
	('2020-01-20', 10, NULL, 'Yes', 1),
	('2020-01-21', 1, NULL, 'Other', 2),
	('2020-01-21', 4, NULL, 'Yes', 2),
	('2020-01-21', 6, NULL, 'Yes', 2),
	('2020-01-21', 8, NULL, 'No', 2),
	('2020-01-21', 9, NULL, 'No', 2),
	('2020-01-21', 10, NULL, 'Yes', 1),
	('2020-01-21', 22, NULL, 'Other', 2);
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;

-- Exportiere Struktur von View liquidb.attendance_today_not
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `attendance_today_not` (
	`id` INT(10) UNSIGNED NOT NULL,
	`name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;

-- Exportiere Struktur von Tabelle liquidb.badge
CREATE TABLE IF NOT EXISTS `badge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `participant_id` int(10) unsigned NOT NULL,
  `badge_name_internal` enum('FRUEHSCHWIMMER','DSA_BRONZE','DSA_SILBER','DSA_GOLD','JUNIORRETTER','DRSA_BRONZE','DRSA_SILBER','DRSA_GOLD','DSTA') NOT NULL,
  `issue_date` date DEFAULT NULL COMMENT 'Datum der Ausstellung',
  `issue_forced` binary(1) DEFAULT NULL COMMENT '0: Abzeichen mit LiquiDB begleitet\r\n1: Manuell Eingetragen, LiquiDB überschrieben',
  `issue_trainer` varchar(50) DEFAULT NULL COMMENT 'Trainer, der ausgestellt hat',
  `issue_user_id` int(10) unsigned DEFAULT NULL COMMENT 'Benutzer, der Abzeichen als ausgestellt markiert hat',
  PRIMARY KEY (`id`),
  KEY `FK_badge_participant` (`participant_id`),
  KEY `FK_badge_badge_list` (`badge_name_internal`),
  KEY `FK_badge_user` (`issue_user_id`),
  CONSTRAINT `FK_badge_badge_list` FOREIGN KEY (`badge_name_internal`) REFERENCES `badge_list` (`name_internal`),
  CONSTRAINT `FK_badge_participant` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`id`),
  CONSTRAINT `FK_badge_user` FOREIGN KEY (`issue_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.badge: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `badge` DISABLE KEYS */;
/*!40000 ALTER TABLE `badge` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle liquidb.badge_list
CREATE TABLE IF NOT EXISTS `badge_list` (
  `regulation` varchar(50) NOT NULL DEFAULT 'PO_01/01/2020',
  `name_internal` enum('FRUEHSCHWIMMER','DSA_BRONZE','DSA_SILBER','DSA_GOLD','JUNIORRETTER','DRSA_BRONZE','DRSA_SILBER','DRSA_GOLD','DSTA') NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_short` varchar(50) NOT NULL,
  PRIMARY KEY (`name_internal`,`regulation`),
  KEY `FK_badge_list_regulation` (`regulation`),
  CONSTRAINT `FK_badge_list_regulation` FOREIGN KEY (`regulation`) REFERENCES `regulation` (`name_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.badge_list: ~9 rows (ungefähr)
/*!40000 ALTER TABLE `badge_list` DISABLE KEYS */;
INSERT INTO `badge_list` (`regulation`, `name_internal`, `name`, `name_short`) VALUES
	('PO_01/01/2020', 'FRUEHSCHWIMMER', 'Frühschwimmer (Seepferdchen)', 'Frühschwimmer'),
	('PO_01/01/2020', 'DSA_BRONZE', 'Deutsches Schwimmabzeichen Bronze', 'DSA Bronze'),
	('PO_01/01/2020', 'DSA_SILBER', 'Deutsches Schwimmabzeichen Silber', 'DSA Silber'),
	('PO_01/01/2020', 'DSA_GOLD', 'Deutsches Schwimmabzeichen Gold', 'DSA Gold'),
	('PO_01/01/2020', 'JUNIORRETTER', 'Juniorretter', 'Juniorretter'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Deutsches Rettungsschwimmabzeichen Bronze', 'DRSA Bronze'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Deutsches Rettungsschwimmabzeichen Silber', 'DRSA Silber'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Deutsches Rettungsschwimmabzeichen Gold', 'DRSA Gold'),
	('PO_01/01/2020', 'DSTA', 'Deutsches Schnorcheltauchabzeichen', 'Dt. Schnorcheltauchabzeichen');
/*!40000 ALTER TABLE `badge_list` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle liquidb.discipline
CREATE TABLE IF NOT EXISTS `discipline` (
  `badge_id` int(10) unsigned NOT NULL,
  `type` enum('Voraussetzung','Praxis','Theorie') NOT NULL,
  `id` tinyint(3) unsigned NOT NULL,
  `done_date` date DEFAULT NULL COMMENT 'Datum des Bestehen',
  `done_time` smallint(5) unsigned DEFAULT NULL COMMENT 'Gebrauchte Zeit',
  PRIMARY KEY (`badge_id`),
  CONSTRAINT `FK_discipline_badge` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.discipline: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `discipline` DISABLE KEYS */;
/*!40000 ALTER TABLE `discipline` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle liquidb.discipline_list
CREATE TABLE IF NOT EXISTS `discipline_list` (
  `regulation` varchar(50) NOT NULL DEFAULT 'PO_01/01/2020',
  `badge_name_internal` enum('FRUEHSCHWIMMER','DSA_BRONZE','DSA_SILBER','DSA_GOLD','JUNIORRETTER','DRSA_BRONZE','DRSA_SILBER','DRSA_GOLD','DSTA') NOT NULL,
  `type` enum('Voraussetzung','Praxis','Theorie') NOT NULL,
  `id` tinyint(3) unsigned NOT NULL,
  `name` varchar(25) NOT NULL,
  `auto_type` enum('NORMAL','TIME','AGE','BADGE','DOCUMENT') NOT NULL DEFAULT 'NORMAL' COMMENT 'Gibt Auskunft über den Automatisierungstypen',
  `auto_info` varchar(50) DEFAULT NULL COMMENT 'Informationen für die Automatisierung\r\nTIME: maximale Zeit in Sekunden\r\nAGE: Mindestalter in Jahren\r\nBADGE: badge_id ded benötigten Abzeichens\r\nDOCUMENT: maximale Gültigkeitsdauer in Jahren\r\nAlle anderen: NULL (keine Bedeutung)',
  `description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`badge_name_internal`,`id`,`type`,`regulation`),
  KEY `FK_discipline_list_regulation` (`regulation`),
  CONSTRAINT `FK_discipline_list_badge_list` FOREIGN KEY (`badge_name_internal`) REFERENCES `badge_list` (`name_internal`),
  CONSTRAINT `FK_discipline_list_regulation` FOREIGN KEY (`regulation`) REFERENCES `regulation` (`name_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.discipline_list: ~83 rows (ungefähr)
/*!40000 ALTER TABLE `discipline_list` DISABLE KEYS */;
INSERT INTO `discipline_list` (`regulation`, `badge_name_internal`, `type`, `id`, `name`, `auto_type`, `auto_info`, `description`) VALUES
	('PO_01/01/2020', 'FRUEHSCHWIMMER', 'Praxis', 1, 'Sprung + Schwimmen', 'NORMAL', NULL, 'Sprung vom Beckenrand mit anschließendem 25 m Schwimmen in einer Schwimmart in Bauch- oder Rückenlage (Grobform, während des Schwimmens in Bauchlage erkennbar ins Wasser ausatmen)'),
	('PO_01/01/2020', 'FRUEHSCHWIMMER', 'Theorie', 1, 'Baderegeln', 'NORMAL', NULL, 'Kenntnis von Baderegeln'),
	('PO_01/01/2020', 'FRUEHSCHWIMMER', 'Praxis', 2, 'Eintauchen', 'NORMAL', NULL, 'Heraufholen eines Gegenstandes mit den Händen aus schultertiefem Wasser (Schultertiefe bezogen auf den Prüfling)'),
	('PO_01/01/2020', 'DSA_BRONZE', 'Praxis', 1, 'Tieftauchen', 'NORMAL', NULL, 'einmal ca. 2 m Tieftauchen von der Wasseroberfläche mit Heraufholen eines Gegenstandes (z.B.: kleiner Tauchring)'),
	('PO_01/01/2020', 'DSA_BRONZE', 'Theorie', 1, 'Baderegeln', 'NORMAL', NULL, 'Kenntnis von Baderegeln'),
	('PO_01/01/2020', 'DSA_BRONZE', 'Praxis', 2, 'Paketsprung', 'NORMAL', NULL, 'ein Paketsprung vom Startblock oder 1 m-Brett'),
	('PO_01/01/2020', 'DSA_BRONZE', 'Praxis', 3, 'Schwimmen', 'NORMAL', NULL, 'Kombi-Übung: Sprung kopfwärts vom Beckenrand und 15 Minuten Schwimmen. In dieser Zeit sind mindestens 200 m zurückzulegen, davon 150 m in Bauch- oder Rückenlage in einer erkennbaren Schwimmart und 50 m in der anderen Körperlage (Wechsel der Körperlage während des Schwimmens auf der Schwimmbahn ohne Festhalten)'),
	('PO_01/01/2020', 'DSA_SILBER', 'Praxis', 1, 'Schwimmen', 'NORMAL', NULL, 'Kombi-Übung: Sprung kopfwärts vom Beckenrand und 20 Minuten Schwimmen. In dieser Zeit sind mindestens 400 m zurückzulegen, davon 300 m in Bauch- oder Rückenlage in einer erkennbaren Schwimmart und 100 m in der anderen Körperlage (Wechsel der Körperlage während des Schwimmens auf der Schwimmbahn ohne Festhalten)'),
	('PO_01/01/2020', 'DSA_SILBER', 'Theorie', 1, 'Baderegeln', 'NORMAL', NULL, 'Kenntnisse von Baderegeln'),
	('PO_01/01/2020', 'DSA_SILBER', 'Praxis', 2, 'Streckentauchen', 'NORMAL', NULL, '10 m Streckentauchen mit Abstoßen vom Beckenrand im Wasser'),
	('PO_01/01/2020', 'DSA_SILBER', 'Theorie', 2, 'Selbstrettung', 'NORMAL', NULL, 'Verhalten zur Selbstrettung (z.B. Verhalten bei Erschöpfung, Lösen von Krämpfen)'),
	('PO_01/01/2020', 'DSA_SILBER', 'Praxis', 3, 'Tieftauchen', 'NORMAL', NULL, 'zweimal ca. 2 m Tieftauchen von der Wasseroberfläche mit Heraufholen je eines Gegenstandes (z.B.: kleiner Tauchring)'),
	('PO_01/01/2020', 'DSA_SILBER', 'Praxis', 4, 'Springen', 'NORMAL', NULL, 'Sprung aus 3 m Höhe oder zwei verschiedene Sprünge aus 1 m Höhe'),
	('PO_01/01/2020', 'DSA_GOLD', 'Praxis', 1, 'Schwimmen', 'NORMAL', NULL, 'Kombi-Übung: Sprung kopfwärts vom Beckenrand und 30 Minuten Schwimmen. In dieser Zeit sind mindestens 800 m zurückzulegen, davon 650 m in Bauch- oder Rückenlage in einer erkennbaren Schwimmart und 150 m in der anderen Körperlage (Wechsel der Körperlage während des Schwimmens auf der Schwimmbahn ohne Festhalten)'),
	('PO_01/01/2020', 'DSA_GOLD', 'Theorie', 1, 'Baderegeln', 'NORMAL', NULL, 'Kenntnisse von Baderegeln'),
	('PO_01/01/2020', 'DSA_GOLD', 'Praxis', 2, 'Kraulschwimmen', 'NORMAL', NULL, 'Startsprung und 25 m Kraulschwimmen'),
	('PO_01/01/2020', 'DSA_GOLD', 'Theorie', 2, 'Selbstrettung', 'NORMAL', NULL, 'Verhalten zur Selbstrettung (z.B. Verhalten bei Erschöpfung, Lösen von Krämpfen)'),
	('PO_01/01/2020', 'DSA_GOLD', 'Praxis', 3, 'Brustschwimmen', 'TIME', '75', 'Startsprung und 50 m Brustschwimmen in höchstens 1:15 Minuten'),
	('PO_01/01/2020', 'DSA_GOLD', 'Theorie', 3, 'Fremdrettung', 'NORMAL', NULL, 'Einfache Fremdrettung (Hilfe bei Bade-, Boots- und Eisunfällen)'),
	('PO_01/01/2020', 'DSA_GOLD', 'Praxis', 4, 'Rückenschwimmen', 'NORMAL', NULL, '50 m Rückenschwimmen mit Grätschschwung ohne Armtätigkeit oder Rückenkraulschwimmen'),
	('PO_01/01/2020', 'DSA_GOLD', 'Praxis', 5, 'Streckentauchen', 'NORMAL', NULL, '10 m Streckentauchen aus der Schwimmlage (ohne Abstoßen vom Beckenrand)'),
	('PO_01/01/2020', 'DSA_GOLD', 'Praxis', 6, 'Tieftauchen', 'TIME', '180', 'dreimal ca. 2 m Tieftauchen von der Wasseroberfläche mit Heraufholen je eines Gegenstandes (z.B.: kleiner Tauchring) innerhalb von 3 Minuten'),
	('PO_01/01/2020', 'DSA_GOLD', 'Praxis', 7, 'Springen', 'NORMAL', NULL, 'Sprung aus 3m Höhe oder 2 verschiedene Sprünge aus 1m Höhe'),
	('PO_01/01/2020', 'DSA_GOLD', 'Praxis', 8, 'Transportschwimmen', 'NORMAL', NULL, '50 m Transportschwimmen: Schieben oder Ziehen'),
	('PO_01/01/2020', 'JUNIORRETTER', 'Voraussetzung', 1, '10 Jahre', 'AGE', '10', 'Mindestalter 10 Jahre'),
	('PO_01/01/2020', 'JUNIORRETTER', 'Praxis', 1, 'Schwimmen', 'NORMAL', NULL, '100m Schwimmen ohne Unterbrechung, davon 25 m Kraulschwimmen, 25 m Rückenkraulschwimmen, 25 m Brustschwimmen und 25 m Rückenschwimmen mit Grätschschwung'),
	('PO_01/01/2020', 'JUNIORRETTER', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen\r\n'),
	('PO_01/01/2020', 'JUNIORRETTER', 'Voraussetzung', 2, 'DSA Gold', 'BADGE', 'DSA_GOLD', 'Deutsches Schwimmabzeichen Gold'),
	('PO_01/01/2020', 'JUNIORRETTER', 'Praxis', 2, 'Schleppen', 'NORMAL', NULL, '25 m Schleppen eines Partners mit Achselschleppgriff'),
	('PO_01/01/2020', 'JUNIORRETTER', 'Praxis', 3, 'Selbstretten', 'NORMAL', NULL, 'Selbstrettungsübung: Kombi-Übung in leichter Freizeitbekleidung, die ohne Pause in der angegebenen Reihenfolge zu erfüllen ist: fußwärts ins Wasser springen, danach Schwebelage einnehmen, 4 Minuten Schweben an der Wasseroberfläche in Rückenlage mit Paddelbewegungen, 6 Minuten langsames Schwimmen, jedoch mindestens viermal die Körperlage wechseln (Bauch-, Rücken-, Seitenlage), die Kleidungsstücke in tiefen Wasser ausziehen'),
	('PO_01/01/2020', 'JUNIORRETTER', 'Praxis', 4, 'Fremdretten', 'NORMAL', NULL, 'Fremdrettungsübung: Kombi-Übung, die in der angegebenen Reihenfolge zu erfüllen ist: 15 m zu einem Partner in Bauchlage anschwimmen, nach halber Strecke auf ca. 2 m Tiefe abtauchen und zwei kleine Tauchringe heraufholen, diese anschließend fallen lassen und das Anschwimmen fortsetzen, Rückweg: 15 m Schleppen eines Partners mit Achselschleppgriff, Sichern des Geretteten'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Voraussetzung', 1, '12 Jahre', 'AGE', '12', 'Mindestalter 12 Jahre'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 1, 'Schwimmen', 'TIME', '600', '200 m Schwimmen in höchstens 10 Minuten, davon 100 m in Bauchlage und 100 m in Rückenlage mit Grätschschwung ohne Armtätigkeit'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 2, 'Kleiderschwimmen', 'TIME', '240', '100 m Schwimmen in Kleidung in höchstens 4 Minuten, anschließend im Wasser entkleiden'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 3, '3 Sprünge', 'NORMAL', NULL, 'Drei verschiedene Sprünge aus etwa 1 m Höhe (z.B. Paketsprung, Schrittsprung, Startsprung, Fußsprung, Kopfsprung)'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 4, 'Streckentauchen', 'NORMAL', NULL, '15 m Streckentauchen'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 5, 'Tieftauchen', 'TIME', '180', 'zweimal Tieftauchen von der Wasseroberfläche, einmal kopfwärts und einmal fußwärts, innerhalb von 3 Minuten mit zweimaligem Heraufholen eines 5-kg-Tauchrings oder eines gleichartigen Gegenstandes (Wassertiefe zwischen 2 und 3 m)'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 6, 'Transportschwimmen', 'NORMAL', NULL, '50 m Transportschwimmen: Schieben oder Ziehen'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 7, 'Befreiungsgriffe', 'NORMAL', NULL, 'Fertigkeiten zur Vermeidung von Umklammerungen sowie zur Befreiung aus Halsumklammerung von hinten und Halswürgegriff von hinten'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 8, 'Schleppen', 'NORMAL', NULL, '50 m Schleppen, je eine Hälfte mit Kopf- oder Achselschleppgriff und dem Standard-Fesselschleppgriff'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 9, 'Fremdretten', 'NORMAL', NULL, 'Kombi-Übung, die ohne Pause in der angegebenen Reihenfolge zu erfüllen ist: 20 m Anschwimmen in Bauchlage, hierbei etwa auf halber Strecke Abtauchen auf 2 bis 3 m Wassertiefe und Heraufholen eines 5 kg Tauchrings oder eines gleichartigen Gegenstandes, diesen anschließend fallen lassen und das Anschwimmen fortsetzen; 20 m Schleppen eines Partners'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 10, 'Anlandbringen', 'NORMAL', NULL, 'Demonstration des Anlandbringens'),
	('PO_01/01/2020', 'DRSA_BRONZE', 'Praxis', 11, 'HLW', 'NORMAL', NULL, '3 Minuten Durchführung der Herz-Lungen-Wiederbelebung (HLW)'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Voraussetzung', 1, '14 Jahre', 'AGE', '14', 'Mindestalter 14 Jahre'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 1, 'Schwimmen', 'TIME', '900', '400 m Schwimmen in höchstens 15 Minuten, davon 50 m Kraulschwimmen, 150 m Brustschwimmen und 200 m Schwimmen in Rückenlage mit Grätschschwung ohne Armtätigkeit'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Voraussetzung', 2, 'EH-Ausbildung', 'DOCUMENT', '2', 'Nachweis einer Erste Hilfe Ausbildung'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 2, 'Kleiderschwimmen', 'TIME', '720', '300 m Schwimmen in Kleidung in höchstens 12 Minuten, anschließend im Wasser entkleiden'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 3, '3-m-Sprung', 'NORMAL', NULL, 'Ein Sprung aus 3 m Höhe'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 4, 'Streckentauchen', 'NORMAL', NULL, '25 m Streckentauchen'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 5, 'Tieftauchen', 'TIME', '180', 'dreimal Tieftauchen von der Wasseroberfläche, zweimal kopfwärts und einmal fußwärts innerhalb von 3 Minuten, mit dreimaligem Heraufholen eines 5 kg Tauchrings oder eines gleichartigen Gegenstandes (Wassertiefe zwischen 3 und 5 m)'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 6, 'Transportschwimmen', 'TIME', '90', '50 m Transportschwimmen: Schieben oder Ziehen in höchstens 1:30 Minuten'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 7, 'Befreiungsgriffe', 'NORMAL', NULL, 'Fertigkeiten zur Vermeidung von Umklammerungen sowie zur Befreiung aus Halsumklammerung von hinten und Halswürgegriff von hinten'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 8, 'Kleiderschleppen', 'TIME', '240', '50 m Schleppen in höchstens 4 Minuten, beide Partner in Kleidung, je eine Hälfte der Strecke mit Kopf- oder Achsel- und einem Fesselschleppgriff (Standard-Fesselschleppgriff oder Seemannsgriff)'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 9, 'Fremdretten', 'NORMAL', NULL, 'Kombi-Übung, die ohne Pause in der angegebenen Reihenfolge zu erfüllen ist: Sprung kopfwärts ins Wasser; 20 m Anschwimmen in Bauchlage; Abtauchen auf 3 bis 5 m Tiefe, Heraufholen eines 5-kg-Tauchrings oder eines gleichartigen Gegenstandes, diesen anschließend fallen lassen; Lösen aus einer Umklammerung durch einen Befreiungsgriff; 25 m Schleppen; Sichern und Anlandbringen des Geretteten; 3 Minuten Durchführung der Herz-Lungen-Wiederbelebung (HLW)'),
	('PO_01/01/2020', 'DRSA_SILBER', 'Praxis', 10, 'Rettungsgeräte', 'NORMAL', NULL, 'Handhabung und praktischer Einsatz eines Rettungsgerätes (z.B. Gurtretter, Wurfleine oder Rettungsring)'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Voraussetzung', 1, '16 Jahre', 'AGE', '16', 'Mindestalter 16 Jahre'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 1, 'Flossenschwimmen', 'TIME', '360', '300 m Flossenschwimmen in höchstens 6 Minuten, davon 250 m Bauch- oder Seitenlage und 50 m Schleppen , zu schleppender Partner in Kleidung (Kopf- und Achselgriff)'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Voraussetzung', 2, 'DRSA Silber', 'BADGE', 'DRSA_SILBER', 'Deutsches Rettungsschwimmabzeichen Silber'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 2, 'Kleiderschwimmen', 'TIME', '540', '300 m Schwimmen in Kleidung in höchstens 9 Minuten, anschließend im Wasser entkleiden'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Voraussetzung', 3, 'Selbsterklärung', 'DOCUMENT', '2', 'Ärtzliche Tauglichkeit (Die Selbsterklärung zum Gesundheitszustand muss vor Beginn vorliegen)'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 3, 'Schnellschwimmen', 'TIME', '100', '100 m Schwimmen in höchstens 1:40 Minuten'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Voraussetzung', 4, 'EH-Ausbildung', 'DOCUMENT', '2', 'Nachweis einer Erste Hilfe Ausbildung zur Ausstellung'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 4, 'Streckentauchen', 'NORMAL', NULL, '30 m Streckentauchen, dabei von 10 kleinen Ringen oder Tellern, die auf einer Strecke von 20 m in einer höchstens 2 m breiten Gasse verteilt sind, mindestens 8 Stück aufsammeln'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 5, 'K-Tieftauchen', 'TIME', '180', 'dreimal Tieftauchen in Kleidung innerhalb von 3 Minuten; das erste Mal mit einem Kopfsprung, anschließend je einmal kopf- und fußwärts von der Wasseroberfläche mit gleichzeitigem Heraufholen von jeweils zwei 5-kg-Tauchringen oder gleichartigen Gegenständen, die etwa 3 m voneinander entfernt liegen (Wassertiefe zwischen 3 und 5 m)'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 6, 'K-Transportschwimmen', 'TIME', '90', '50 m Transportschwimmen, beide Partner in Kleidung: Schieben oder Ziehen in höchstens 1:30 Minuten'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 7, 'Befreiungsgriffe', 'NORMAL', NULL, 'Fertigkeiten zur Vermeidung von Umklammerungen sowie zur Befreiung aus Halsumklammerung von hinten und Halswürgegriff von hinten'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 8, 'Fremdretten', 'NORMAL', NULL, 'Kombi-Übung (beide Partner in Kleidung), die ohne Pause in der angegebenen Reihenfolge zu erfüllen ist: Sprung kopfwärts ins Wasser; 25 m Schwimmen in höchstens 30 Sekunden; Abtauchen auf 3 bis 5 m Tiefe und Heraufholen eines 5 kg Tauchrings oder eines gleichartigen Gegenstandes, diesen anschließend fallen lassen; Lösen aus der Umklammerung durch einen Befreiungsgriff; 25 m Schleppen in höchstens 60 Sekunden mit einem Fesselschleppgriff; Sichern und Anlandbringen des Geretteten; 3 Minuten Durchf'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 9, 'Rettungsgeräte', 'NORMAL', NULL, 'Handhabung von Rettungsgeräten: Retten mit dem "Rettungsball mit Leine": Zielwerfen in einen Sektor mit 3-m-Öffnung in 12 m Entfernung: 6 Würfe innerhalb von 5 Minuten, davon 4 Treffer Retten mit einem anderen Rettungsgerät Retten mit Rettungsgurt Leine (als Schwimmer und Leinenführer)'),
	('PO_01/01/2020', 'DRSA_GOLD', 'Praxis', 10, 'Hilfsmittel', 'NORMAL', NULL, 'Handhabung gebräuchlicher Hilfsmittel zur Wiederbelebung'),
	('PO_01/01/2020', 'DSTA', 'Voraussetzung', 1, '12 Jahre', 'AGE', '12', 'Mindestalter 12 Jahre (bei Minderjährigen ist die Einverständniserklärung des Erziehungsberechtigten erforderlich)'),
	('PO_01/01/2020', 'DSTA', 'Praxis', 1, '2-Flossenschwimmen', 'NORMAL', NULL, '600 m Flossenschwimmen ohne Zeitbegrenzung (je 200m Bauch-, Rücken- und Seitenlage)'),
	('PO_01/01/2020', 'DSTA', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen'),
	('PO_01/01/2020', 'DSTA', 'Voraussetzung', 2, 'Selbsterklärung', 'DOCUMENT', '2', 'Ärtzliche Tauglichkeit ((oder Formblatt "Selbsterklärung zum Gesundheitszustand") Tauchtauglichkeit nicht älter als 4 Wochen)'),
	('PO_01/01/2020', 'DSTA', 'Praxis', 2, '1-Flossenschwimmen', 'NORMAL', NULL, '200 m Flossenschwimmen mit einer Flosse und Armbewegung'),
	('PO_01/01/2020', 'DSTA', 'Voraussetzung', 3, 'DRSA Bronze', 'BADGE', 'DRSA_BRONZE', 'Deutsches Rettungsschwimmabzeichen Bronze'),
	('PO_01/01/2020', 'DSTA', 'Praxis', 3, 'Streckentauchen', 'NORMAL', NULL, '30 m Streckentauchen ohne Startsprung'),
	('PO_01/01/2020', 'DSTA', 'Praxis', 4, 'Zeittauchen', 'NORMAL', NULL, '30 Sekunden Zeittauchen (Festhalten erlaubt)'),
	('PO_01/01/2020', 'DSTA', 'Praxis', 5, 'Ausblasen', 'NORMAL', NULL, 'in mindestens 3 m Tiefe Taucherbrille abnehmen, wieder aufsetzen und ausblasen'),
	('PO_01/01/2020', 'DSTA', 'Praxis', 6, 'Tieftauchen', 'TIME', '60', 'dreimal innerhalb von einer Minute 3 m Tieftauchen'),
	('PO_01/01/2020', 'DSTA', 'Praxis', 7, 'Fremdretten', 'NORMAL', NULL, 'Kombi-Übung: 50 m Flossenschwimmen in Bauchlage mit Armtätigkeit, einmal 3 bis 5 m Tieftauchen und Heraufholen eines 5 kg Tauchrings oder eines gleichartigen Gegenstandes, 50m Schleppen eines Partners, 3 Minuten Durchführen der Herz-Lungen-Wiederbelebung (HLW)');
/*!40000 ALTER TABLE `discipline_list` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle liquidb.group
CREATE TABLE IF NOT EXISTS `group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.group: ~1 rows (ungefähr)
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` (`id`, `name`, `description`) VALUES
	(1, 'Gruppe 1', 'Nichtschwimmer. Anfänger bis Frühschwimmer.'),
	(2, 'Gruppe 2', 'Fortgeschrittene. Jugendschwimmabzeichen.'),
	(3, 'Gruppe 3', '(Angehende) Rettungsschwimmer');
/*!40000 ALTER TABLE `group` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle liquidb.participant
CREATE TABLE IF NOT EXISTS `participant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `gender` enum('m','w','d') DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `age` tinyint(3) unsigned GENERATED ALWAYS AS (timestampdiff(YEAR,`birthday`,current_timestamp())) VIRTUAL,
  `birthplace` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL COMMENT 'Straße und Hausnummer',
  `post_code` varchar(6) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `note` varchar(500) DEFAULT NULL,
  `added_by_user_id` int(10) unsigned NOT NULL DEFAULT 1,
  `group_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_participant_user` (`added_by_user_id`),
  KEY `FK_participant_group` (`group_id`),
  CONSTRAINT `FK_participant_group` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`),
  CONSTRAINT `FK_participant_user` FOREIGN KEY (`added_by_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.participant: ~103 rows (ungefähr)
/*!40000 ALTER TABLE `participant` DISABLE KEYS */;
INSERT INTO `participant` (`id`, `name`, `gender`, `birthday`, `birthplace`, `address`, `post_code`, `city`, `note`, `added_by_user_id`, `group_id`) VALUES
	(1, 'Tim Teilnehmer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(4, 'Susi Schwiüßmmer', 'w', '2000-06-21', 'Sampläecity', 'Am Wäeg 16', '05849', 'Steiänberg', 'Tolle Notiz!\r\nNeue Zeileä', 1, NULL),
	(6, 'Frauke Fröhlich', NULL, '2005-01-17', 'London', 'Bahnhofstrasse 1', '52825', 'Münster', 'Allergie gegen Chlor\r\nTel.: 05728 3985', 1, NULL),
	(7, 'Eryn Chadwick', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(8, 'Aisha Lunt', 'w', NULL, NULL, NULL, NULL, NULL, 'Vitae id possimus quisquam ab sapiente molestias amet. Quia nihil praesentium quia voluptatem soluta ullam. Deserunt numquam beatae non consequatur velit. Beatae sequi repellat earum eveniet.\r\nSequi vel sit aut sunt consequatur sed. Dolore dolorem qui doloribus ut.', 1, NULL),
	(9, 'Owen Pratt', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(10, 'Abdul-Kai Forester', 'm', '2000-10-04', 'Ulster', 'Kaufstraße 12c', '74635', 'Orthausen', 'Erreichbar unter 02938 748564\r\nSchwierigkeiten mit Streckentauchen', 1, 3),
	(11, 'Ronald Warren', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(12, 'Julius Utterson', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(13, 'Sebastian Wills', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(14, 'Nick Doherty', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(15, 'Denny Edler', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(16, 'Isabel Osman', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(17, 'Maddison Power', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(18, 'Jacqueline Pratt', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(19, 'Cristal Jobson', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(20, 'Stephanie Willis', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(21, 'Chadwick Morrow', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(22, 'Maxwell Armstrong', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(23, 'Jack Tait', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(24, 'Gil Sanchez', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(25, 'Anthony Jordan', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(26, 'Chad Campbell', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(27, 'Noah Russell', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(28, 'Candace Bryson', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(29, 'Javier Daniells', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(30, 'Joseph Myatt', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(31, 'Maribel Robertson', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(32, 'Kurt Robinson', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(33, 'Raquel Vangness', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(34, 'Wendy Grey', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(35, 'Ivette Dubois', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(36, 'Elijah Nayler', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(37, 'Dasha Blackwall', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(38, 'Ember Walker', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(39, 'Erick Gavin', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(40, 'Madison Bradshaw', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(41, 'Johnathan Sinclair', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(42, 'Tom Anderson', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(43, 'Miley Nicholls', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(44, 'Rosemary Booth', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(45, 'Holly Herbert', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(46, 'Samantha Milner', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(47, 'Oliver Hobson', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(48, 'Rocco Donovan', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(49, 'Sasha Edmonds', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(50, 'Barry Khan', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(51, 'Blake Hunter', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(52, 'Barry Simpson', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(53, 'Lillian Saunders', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(54, 'Carolyn Lane', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(55, 'Danielle Overson', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(56, 'Kendra Rixon', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(57, 'Hayden Walsh', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(58, 'Oliver Asher', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(59, 'Boris Briggs', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(60, 'Henry Boden', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(61, 'Nicholas Ebden', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(62, 'Adeline Jarvis', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2),
	(63, 'Gabriel Larsen', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(64, 'Selena Whitmore', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(65, 'Deborah Shepherd', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(66, 'Luke Gilmour', 'd', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(67, 'Sienna Jobson', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(68, 'Jocelyn Selby', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(69, 'Johnathan Raven', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(70, 'Isabella Bristow', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(71, 'Percy Clarke', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(72, 'Peter Ventura', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(73, 'Tess Mcgee', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(74, 'Carolyn Blackwall', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(75, 'Jules Wright', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(76, 'Maribel Waterson', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(77, 'Rufus Allen', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(78, 'Gwen Ellery', 'w', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(79, 'Boris Owen', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(80, 'Iris Dobson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(81, 'Helen Johnson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
	(82, 'George Ulyatt', NULL, NULL, 'Hayward', NULL, NULL, NULL, NULL, 1, NULL),
	(83, 'Ronald Clark', NULL, NULL, 'Memphis', NULL, NULL, NULL, NULL, 1, NULL),
	(84, 'Jayden Lomax', 'm', NULL, 'Anaheim', NULL, NULL, NULL, NULL, 1, NULL),
	(85, 'Domenic James', 'm', NULL, 'Fullerton', NULL, NULL, NULL, NULL, 1, NULL),
	(86, 'Harry Bryant', 'm', NULL, 'Washington', NULL, NULL, NULL, NULL, 1, NULL),
	(87, 'Dalia Lane', 'w', NULL, 'Las Vegas', NULL, NULL, NULL, NULL, 1, NULL),
	(88, 'Boris Wood', 'm', NULL, 'Quebec', NULL, NULL, NULL, NULL, 1, NULL),
	(89, 'Nate Owen', 'm', NULL, 'Lancaster', NULL, NULL, NULL, NULL, 1, NULL),
	(90, 'Elijah Butler', 'm', NULL, 'Berlin', NULL, NULL, NULL, NULL, 1, NULL),
	(91, 'Valentina Lewis', 'w', NULL, 'Indianapolis', NULL, NULL, NULL, NULL, 1, NULL),
	(92, 'Makenzie Cobb', 'w', NULL, 'Bakersfield', NULL, NULL, NULL, NULL, 1, NULL),
	(93, 'Ramon Lynn', 'm', NULL, 'Tulsa', NULL, NULL, NULL, NULL, 1, NULL),
	(94, 'Kirsten Hill', 'w', NULL, 'Rochester', NULL, NULL, NULL, NULL, 1, NULL),
	(95, 'Bob Barrett', 'm', NULL, 'Richmond', NULL, NULL, NULL, NULL, 1, NULL),
	(96, 'Michael Uttridge', 'm', NULL, 'Baltimore', NULL, NULL, NULL, NULL, 1, NULL),
	(97, 'Luna Curtis', 'w', NULL, 'Berlin', NULL, NULL, NULL, NULL, 1, NULL),
	(98, 'Chester Grey', 'm', NULL, 'London', NULL, NULL, NULL, NULL, 1, NULL),
	(99, 'Maddison Poulton', 'w', NULL, 'London', NULL, NULL, NULL, NULL, 1, NULL),
	(100, 'Domenic Yang', 'm', NULL, 'El Paso', NULL, NULL, NULL, NULL, 1, NULL),
	(101, 'Mark Hall', 'm', NULL, 'Bridgeport', NULL, NULL, NULL, NULL, 1, NULL),
	(102, 'Clint Dallas', 'm', NULL, 'Colorado Springs', NULL, NULL, NULL, NULL, 1, NULL),
	(103, 'Camden Jarrett', 'w', NULL, 'Otawa', NULL, NULL, NULL, NULL, 1, NULL),
	(104, 'Rosemary Avery', 'w', NULL, 'New Orleans', NULL, NULL, NULL, NULL, 1, NULL),
	(105, 'Maxwell Norton', 'm', NULL, 'Bellevue', NULL, NULL, NULL, NULL, 1, NULL),
	(106, 'Nick Appleton', 'm', NULL, 'Portland', NULL, NULL, NULL, NULL, 1, NULL),
	(108, 'Tina Testing', NULL, NULL, NULL, NULL, NULL, NULL, 'Gruppen-\r\ntest!\r\n🎉', 1, NULL),
	(109, 'Tanja Test2', NULL, NULL, NULL, NULL, NULL, NULL, 'Mehr Tests!\r\n✨', 1, 3);
/*!40000 ALTER TABLE `participant` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle liquidb.regulation
CREATE TABLE IF NOT EXISTS `regulation` (
  `name_internal` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_short` varchar(50) NOT NULL,
  `description` int(11) DEFAULT NULL,
  PRIMARY KEY (`name_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.regulation: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `regulation` DISABLE KEYS */;
INSERT INTO `regulation` (`name_internal`, `date`, `name`, `name_short`, `description`) VALUES
	('PO_01/01/2020', '2020-01-01', 'Prüfungsordnung 2020 1. Auflage', 'PO 2020 1', NULL);
/*!40000 ALTER TABLE `regulation` ENABLE KEYS */;

-- Exportiere Struktur von View liquidb.regulation_current
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `regulation_current` (
	`name_internal` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;

-- Exportiere Struktur von Tabelle liquidb.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL COMMENT 'Groß/Kleinschreibungs-Unique',
  `name` varchar(50) DEFAULT NULL,
  `display_name` varchar(50) GENERATED ALWAYS AS (if(`name` is not null,`name`,`username`)) VIRTUAL,
  `salt` varchar(32) NOT NULL,
  `pw_hash_bin` varbinary(256) NOT NULL,
  `pw_hash` varchar(64) GENERATED ALWAYS AS (hex(`pw_hash_bin`)) VIRTUAL,
  `pw_changed` datetime DEFAULT NULL,
  `pw_must_change` tinyint(1) NOT NULL DEFAULT 1,
  `ist_trainer` tinyint(1) NOT NULL DEFAULT 0,
  `ist_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.user: ~5 rows (ungefähr)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `name`, `salt`, `pw_hash_bin`, `pw_changed`, `pw_must_change`, `ist_trainer`, `ist_admin`) VALUES
	(1, 'system', 'System', 'cGYF)ma?2aPQ0]}&kjBY:Opxw#nm$9i|', _binary 0xAFB3E303AD2F243ED3F720B87CAE4CFAC676DAC8C3162F22F2314C471C380D7E, '2020-01-09 18:44:59', 0, 1, 1),
	(2, 'mozi_h', NULL, 'S.;s%%1nd:*Xo/YUR$Jo-(ou&EUU.w8_', _binary 0x5ED266089B51128FD71E577B8D7792B338C83C82DB795E0AC756E8AF20A5C1A6, '2020-01-21 10:21:45', 0, 1, 1),
	(3, 'marv', 'Marvin', 'CmG?WU7$3O4n#P=N1762#g_FYD >(Oy-', _binary 0x9ABF1B211FFF73B8D288DBCB32B06ED2B690D3181252252507803AA601CB8620, '2020-01-09 17:53:01', 0, 1, 0),
	(4, 'alex', NULL, ':n:Ygfv&MmY6#,<jRMH%70nbL$gisgvw', _binary 0x5B606CC0258C65970DF3D52A0BB7CFFCD974C95336025DD3C2A7D3FB0C0AF958, '2020-01-17 12:30:18', 0, 0, 1),
	(5, 'nina', 'Nina Neu', 'b.7i_y/F cUFB}Tmb=wdE;897x|>a/xS', _binary 0x47C6C9BC102DB52CA0AA11B473C37C6B47B04E33C49B57FF2DC6BD85BB13FBB7, '2020-01-09 17:55:42', 0, 1, 0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Exportiere Struktur von View liquidb.attendance_today_not
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `attendance_today_not`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `attendance_today_not` AS select `p`.`id` AS `id`,`p`.`name` AS `name` from `participant` `p` where !(`p`.`id` in (select `p`.`id` from (`participant` `p` left join `attendance` `a` on(`p`.`id` = `a`.`participant_id`)) where `a`.`date` = cast(current_timestamp() as date)));

-- Exportiere Struktur von View liquidb.regulation_current
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `regulation_current`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `regulation_current` AS select `regulation`.`name_internal` AS `name_internal` from `regulation` where `regulation`.`date` = (select max(`regulation`.`date`) from `regulation`);

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
