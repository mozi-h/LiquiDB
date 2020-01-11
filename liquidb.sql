-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.4.8-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win64
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
  `name_internal` enum('FRUEHSCHWIMMER','DSA_BRONZE','DSA_SILBER','DSA_GOLD','JUNIORRETTER','DRSA_BRONZE','DRSA_SILBER','DRSA_GOLD','DSTA') NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_short` varchar(50) NOT NULL,
  PRIMARY KEY (`name_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.badge_list: ~9 rows (ungefähr)
/*!40000 ALTER TABLE `badge_list` DISABLE KEYS */;
INSERT INTO `badge_list` (`name_internal`, `name`, `name_short`) VALUES
	('FRUEHSCHWIMMER', 'Frühschwimmer (Seepferdchen)', 'Frühschwimmer'),
	('DSA_BRONZE', 'Deutsches Schwimmabzeichen Bronze', 'DSA Bronze'),
	('DSA_SILBER', 'Deutsches Schwimmabzeichen Silber', 'DSA Silber'),
	('DSA_GOLD', 'Deutsches Schwimmabzeichen Gold', 'DSA Gold'),
	('JUNIORRETTER', 'Juniorretter', 'Juniorretter'),
	('DRSA_BRONZE', 'Deutsches Rettungsschwimmabzeichen Bronze', 'DRSA Bronze'),
	('DRSA_SILBER', 'Deutsches Rettungsschwimmabzeichen Silber', 'DRSA Silber'),
	('DRSA_GOLD', 'Deutsches Rettungsschwimmabzeichen Gold', 'DRSA Gold'),
	('DSTA', 'Deutsches Schnorcheltauchabzeichen', 'Dt. Schnorcheltauchabzeichen');
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
  `badge_name_internal` enum('FRUEHSCHWIMMER','DSA_BRONZE','DSA_SILBER','DSA_GOLD','JUNIORRETTER','DRSA_BRONZE','DRSA_SILBER','DRSA_GOLD','DSTA') NOT NULL,
  `type` enum('Voraussetzung','Praxis','Theorie') NOT NULL,
  `id` tinyint(3) unsigned NOT NULL,
  `name` varchar(25) NOT NULL,
  `auto_type` enum('NORMAL','TIME','AGE','BADGE','DOCUMENT') NOT NULL DEFAULT 'NORMAL' COMMENT 'Gibt Auskunft über den Automatisierungstypen',
  `auto_info` varchar(50) DEFAULT NULL COMMENT 'Informationen für die Automatisierung\r\nTIME: maximale Zeit in Sekunden\r\nAGE: Mindestalter in Jahren\r\nBADGE: badge_id ded benötigten Abzeichens\r\nDOCUMENT: maximale Gültigkeitsdauer in Jahren\r\nAlle anderen: NULL (keine Bedeutung)',
  `description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`badge_name_internal`,`id`,`type`),
  CONSTRAINT `FK_discipline_list_badge_list` FOREIGN KEY (`badge_name_internal`) REFERENCES `badge_list` (`name_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.discipline_list: ~83 rows (ungefähr)
/*!40000 ALTER TABLE `discipline_list` DISABLE KEYS */;
INSERT INTO `discipline_list` (`badge_name_internal`, `type`, `id`, `name`, `auto_type`, `auto_info`, `description`) VALUES
	('FRUEHSCHWIMMER', 'Praxis', 1, 'Sprung + Schwimmen', 'NORMAL', NULL, 'Sprung vom Beckenrand mit anschließendem 25 m Schwimmen in einer Schwimmart in Bauch- oder Rückenlage (Grobform, während des Schwimmens in Bauchlage erkennbar ins Wasser ausatmen)'),
	('FRUEHSCHWIMMER', 'Theorie', 1, 'Baderegeln', 'NORMAL', NULL, 'Kenntnis von Baderegeln'),
	('FRUEHSCHWIMMER', 'Praxis', 2, 'Eintauchen', 'NORMAL', NULL, 'Heraufholen eines Gegenstandes mit den Händen aus schultertiefem Wasser (Schultertiefe bezogen auf den Prüfling)'),
	('DSA_BRONZE', 'Praxis', 1, 'Tieftauchen', 'NORMAL', NULL, 'einmal ca. 2 m Tieftauchen von der Wasseroberfläche mit Heraufholen eines Gegenstandes (z.B.: kleiner Tauchring)'),
	('DSA_BRONZE', 'Theorie', 1, 'Baderegeln', 'NORMAL', NULL, 'Kenntnis von Baderegeln'),
	('DSA_BRONZE', 'Praxis', 2, 'Paketsprung', 'NORMAL', NULL, 'ein Paketsprung vom Startblock oder 1 m-Brett'),
	('DSA_BRONZE', 'Praxis', 3, 'Schwimmen', 'NORMAL', NULL, 'Kombi-Übung: Sprung kopfwärts vom Beckenrand und 15 Minuten Schwimmen. In dieser Zeit sind mindestens 200 m zurückzulegen, davon 150 m in Bauch- oder Rückenlage in einer erkennbaren Schwimmart und 50 m in der anderen Körperlage (Wechsel der Körperlage während des Schwimmens auf der Schwimmbahn ohne Festhalten)'),
	('DSA_SILBER', 'Praxis', 1, 'Schwimmen', 'NORMAL', NULL, 'Kombi-Übung: Sprung kopfwärts vom Beckenrand und 20 Minuten Schwimmen. In dieser Zeit sind mindestens 400 m zurückzulegen, davon 300 m in Bauch- oder Rückenlage in einer erkennbaren Schwimmart und 100 m in der anderen Körperlage (Wechsel der Körperlage während des Schwimmens auf der Schwimmbahn ohne Festhalten)'),
	('DSA_SILBER', 'Theorie', 1, 'Baderegeln', 'NORMAL', NULL, 'Kenntnisse von Baderegeln'),
	('DSA_SILBER', 'Praxis', 2, 'Streckentauchen', 'NORMAL', NULL, '10 m Streckentauchen mit Abstoßen vom Beckenrand im Wasser'),
	('DSA_SILBER', 'Theorie', 2, 'Selbstrettung', 'NORMAL', NULL, 'Verhalten zur Selbstrettung (z.B. Verhalten bei Erschöpfung, Lösen von Krämpfen)'),
	('DSA_SILBER', 'Praxis', 3, 'Tieftauchen', 'NORMAL', NULL, 'zweimal ca. 2 m Tieftauchen von der Wasseroberfläche mit Heraufholen je eines Gegenstandes (z.B.: kleiner Tauchring)'),
	('DSA_SILBER', 'Praxis', 4, 'Springen', 'NORMAL', NULL, 'Sprung aus 3 m Höhe oder zwei verschiedene Sprünge aus 1 m Höhe'),
	('DSA_GOLD', 'Praxis', 1, 'Schwimmen', 'NORMAL', NULL, 'Kombi-Übung: Sprung kopfwärts vom Beckenrand und 30 Minuten Schwimmen. In dieser Zeit sind mindestens 800 m zurückzulegen, davon 650 m in Bauch- oder Rückenlage in einer erkennbaren Schwimmart und 150 m in der anderen Körperlage (Wechsel der Körperlage während des Schwimmens auf der Schwimmbahn ohne Festhalten)'),
	('DSA_GOLD', 'Theorie', 1, 'Baderegeln', 'NORMAL', NULL, 'Kenntnisse von Baderegeln'),
	('DSA_GOLD', 'Praxis', 2, 'Kraulschwimmen', 'NORMAL', NULL, 'Startsprung und 25 m Kraulschwimmen'),
	('DSA_GOLD', 'Theorie', 2, 'Selbstrettung', 'NORMAL', NULL, 'Verhalten zur Selbstrettung (z.B. Verhalten bei Erschöpfung, Lösen von Krämpfen)'),
	('DSA_GOLD', 'Praxis', 3, 'Brustschwimmen', 'TIME', '75', 'Startsprung und 50 m Brustschwimmen in höchstens 1:15 Minuten'),
	('DSA_GOLD', 'Theorie', 3, 'Fremdrettung', 'NORMAL', NULL, 'Einfache Fremdrettung (Hilfe bei Bade-, Boots- und Eisunfällen)'),
	('DSA_GOLD', 'Praxis', 4, 'Rückenschwimmen', 'NORMAL', NULL, '50 m Rückenschwimmen mit Grätschschwung ohne Armtätigkeit oder Rückenkraulschwimmen'),
	('DSA_GOLD', 'Praxis', 5, 'Streckentauchen', 'NORMAL', NULL, '10 m Streckentauchen aus der Schwimmlage (ohne Abstoßen vom Beckenrand)'),
	('DSA_GOLD', 'Praxis', 6, 'Tieftauchen', 'TIME', '180', 'dreimal ca. 2 m Tieftauchen von der Wasseroberfläche mit Heraufholen je eines Gegenstandes (z.B.: kleiner Tauchring) innerhalb von 3 Minuten'),
	('DSA_GOLD', 'Praxis', 7, 'Springen', 'NORMAL', NULL, 'Sprung aus 3m Höhe oder 2 verschiedene Sprünge aus 1m Höhe'),
	('DSA_GOLD', 'Praxis', 8, 'Transportschwimmen', 'NORMAL', NULL, '50 m Transportschwimmen: Schieben oder Ziehen'),
	('JUNIORRETTER', 'Voraussetzung', 1, '10 Jahre', 'AGE', '10', 'Mindestalter 10 Jahre'),
	('JUNIORRETTER', 'Praxis', 1, 'Schwimmen', 'NORMAL', NULL, '100m Schwimmen ohne Unterbrechung, davon 25 m Kraulschwimmen, 25 m Rückenkraulschwimmen, 25 m Brustschwimmen und 25 m Rückenschwimmen mit Grätschschwung'),
	('JUNIORRETTER', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen\r\n'),
	('JUNIORRETTER', 'Voraussetzung', 2, 'DSA Gold', 'BADGE', 'DSA_GOLD', 'Deutsches Schwimmabzeichen Gold'),
	('JUNIORRETTER', 'Praxis', 2, 'Schleppen', 'NORMAL', NULL, '25 m Schleppen eines Partners mit Achselschleppgriff'),
	('JUNIORRETTER', 'Praxis', 3, 'Selbstretten', 'NORMAL', NULL, 'Selbstrettungsübung: Kombi-Übung in leichter Freizeitbekleidung, die ohne Pause in der angegebenen Reihenfolge zu erfüllen ist: fußwärts ins Wasser springen, danach Schwebelage einnehmen, 4 Minuten Schweben an der Wasseroberfläche in Rückenlage mit Paddelbewegungen, 6 Minuten langsames Schwimmen, jedoch mindestens viermal die Körperlage wechseln (Bauch-, Rücken-, Seitenlage), die Kleidungsstücke in tiefen Wasser ausziehen'),
	('JUNIORRETTER', 'Praxis', 4, 'Fremdretten', 'NORMAL', NULL, 'Fremdrettungsübung: Kombi-Übung, die in der angegebenen Reihenfolge zu erfüllen ist: 15 m zu einem Partner in Bauchlage anschwimmen, nach halber Strecke auf ca. 2 m Tiefe abtauchen und zwei kleine Tauchringe heraufholen, diese anschließend fallen lassen und das Anschwimmen fortsetzen, Rückweg: 15 m Schleppen eines Partners mit Achselschleppgriff, Sichern des Geretteten'),
	('DRSA_BRONZE', 'Voraussetzung', 1, '12 Jahre', 'AGE', '12', 'Mindestalter 12 Jahre'),
	('DRSA_BRONZE', 'Praxis', 1, 'Schwimmen', 'TIME', '600', '200 m Schwimmen in höchstens 10 Minuten, davon 100 m in Bauchlage und 100 m in Rückenlage mit Grätschschwung ohne Armtätigkeit'),
	('DRSA_BRONZE', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen'),
	('DRSA_BRONZE', 'Praxis', 2, 'Kleiderschwimmen', 'TIME', '240', '100 m Schwimmen in Kleidung in höchstens 4 Minuten, anschließend im Wasser entkleiden'),
	('DRSA_BRONZE', 'Praxis', 3, '3 Sprünge', 'NORMAL', NULL, 'Drei verschiedene Sprünge aus etwa 1 m Höhe (z.B. Paketsprung, Schrittsprung, Startsprung, Fußsprung, Kopfsprung)'),
	('DRSA_BRONZE', 'Praxis', 4, 'Streckentauchen', 'NORMAL', NULL, '15 m Streckentauchen'),
	('DRSA_BRONZE', 'Praxis', 5, 'Tieftauchen', 'TIME', '180', 'zweimal Tieftauchen von der Wasseroberfläche, einmal kopfwärts und einmal fußwärts, innerhalb von 3 Minuten mit zweimaligem Heraufholen eines 5-kg-Tauchrings oder eines gleichartigen Gegenstandes (Wassertiefe zwischen 2 und 3 m)'),
	('DRSA_BRONZE', 'Praxis', 6, 'Transportschwimmen', 'NORMAL', NULL, '50 m Transportschwimmen: Schieben oder Ziehen'),
	('DRSA_BRONZE', 'Praxis', 7, 'Befreiungsgriffe', 'NORMAL', NULL, 'Fertigkeiten zur Vermeidung von Umklammerungen sowie zur Befreiung aus Halsumklammerung von hinten und Halswürgegriff von hinten'),
	('DRSA_BRONZE', 'Praxis', 8, 'Schleppen', 'NORMAL', NULL, '50 m Schleppen, je eine Hälfte mit Kopf- oder Achselschleppgriff und dem Standard-Fesselschleppgriff'),
	('DRSA_BRONZE', 'Praxis', 9, 'Fremdretten', 'NORMAL', NULL, 'Kombi-Übung, die ohne Pause in der angegebenen Reihenfolge zu erfüllen ist: 20 m Anschwimmen in Bauchlage, hierbei etwa auf halber Strecke Abtauchen auf 2 bis 3 m Wassertiefe und Heraufholen eines 5 kg Tauchrings oder eines gleichartigen Gegenstandes, diesen anschließend fallen lassen und das Anschwimmen fortsetzen; 20 m Schleppen eines Partners'),
	('DRSA_BRONZE', 'Praxis', 10, 'Anlandbringen', 'NORMAL', NULL, 'Demonstration des Anlandbringens'),
	('DRSA_BRONZE', 'Praxis', 11, 'HLW', 'NORMAL', NULL, '3 Minuten Durchführung der Herz-Lungen-Wiederbelebung (HLW)'),
	('DRSA_SILBER', 'Voraussetzung', 1, '14 Jahre', 'AGE', '14', 'Mindestalter 14 Jahre'),
	('DRSA_SILBER', 'Praxis', 1, 'Schwimmen', 'TIME', '900', '400 m Schwimmen in höchstens 15 Minuten, davon 50 m Kraulschwimmen, 150 m Brustschwimmen und 200 m Schwimmen in Rückenlage mit Grätschschwung ohne Armtätigkeit'),
	('DRSA_SILBER', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen'),
	('DRSA_SILBER', 'Voraussetzung', 2, 'EH-Ausbildung', 'DOCUMENT', '2', 'Nachweis einer Erste Hilfe Ausbildung'),
	('DRSA_SILBER', 'Praxis', 2, 'Kleiderschwimmen', 'TIME', '720', '300 m Schwimmen in Kleidung in höchstens 12 Minuten, anschließend im Wasser entkleiden'),
	('DRSA_SILBER', 'Praxis', 3, '3-m-Sprung', 'NORMAL', NULL, 'Ein Sprung aus 3 m Höhe'),
	('DRSA_SILBER', 'Praxis', 4, 'Streckentauchen', 'NORMAL', NULL, '25 m Streckentauchen'),
	('DRSA_SILBER', 'Praxis', 5, 'Tieftauchen', 'TIME', '180', 'dreimal Tieftauchen von der Wasseroberfläche, zweimal kopfwärts und einmal fußwärts innerhalb von 3 Minuten, mit dreimaligem Heraufholen eines 5 kg Tauchrings oder eines gleichartigen Gegenstandes (Wassertiefe zwischen 3 und 5 m)'),
	('DRSA_SILBER', 'Praxis', 6, 'Transportschwimmen', 'TIME', '90', '50 m Transportschwimmen: Schieben oder Ziehen in höchstens 1:30 Minuten'),
	('DRSA_SILBER', 'Praxis', 7, 'Befreiungsgriffe', 'NORMAL', NULL, 'Fertigkeiten zur Vermeidung von Umklammerungen sowie zur Befreiung aus Halsumklammerung von hinten und Halswürgegriff von hinten'),
	('DRSA_SILBER', 'Praxis', 8, 'Kleiderschleppen', 'TIME', '240', '50 m Schleppen in höchstens 4 Minuten, beide Partner in Kleidung, je eine Hälfte der Strecke mit Kopf- oder Achsel- und einem Fesselschleppgriff (Standard-Fesselschleppgriff oder Seemannsgriff)'),
	('DRSA_SILBER', 'Praxis', 9, 'Fremdretten', 'NORMAL', NULL, 'Kombi-Übung, die ohne Pause in der angegebenen Reihenfolge zu erfüllen ist: Sprung kopfwärts ins Wasser; 20 m Anschwimmen in Bauchlage; Abtauchen auf 3 bis 5 m Tiefe, Heraufholen eines 5-kg-Tauchrings oder eines gleichartigen Gegenstandes, diesen anschließend fallen lassen; Lösen aus einer Umklammerung durch einen Befreiungsgriff; 25 m Schleppen; Sichern und Anlandbringen des Geretteten; 3 Minuten Durchführung der Herz-Lungen-Wiederbelebung (HLW)'),
	('DRSA_SILBER', 'Praxis', 10, 'Rettungsgeräte', 'NORMAL', NULL, 'Handhabung und praktischer Einsatz eines Rettungsgerätes (z.B. Gurtretter, Wurfleine oder Rettungsring)'),
	('DRSA_GOLD', 'Voraussetzung', 1, '16 Jahre', 'AGE', '16', 'Mindestalter 16 Jahre'),
	('DRSA_GOLD', 'Praxis', 1, 'Flossenschwimmen', 'TIME', '360', '300 m Flossenschwimmen in höchstens 6 Minuten, davon 250 m Bauch- oder Seitenlage und 50 m Schleppen , zu schleppender Partner in Kleidung (Kopf- und Achselgriff)'),
	('DRSA_GOLD', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen'),
	('DRSA_GOLD', 'Voraussetzung', 2, 'DRSA Silber', 'BADGE', 'DRSA_SILBER', 'Deutsches Rettungsschwimmabzeichen Silber'),
	('DRSA_GOLD', 'Praxis', 2, 'Kleiderschwimmen', 'TIME', '540', '300 m Schwimmen in Kleidung in höchstens 9 Minuten, anschließend im Wasser entkleiden'),
	('DRSA_GOLD', 'Voraussetzung', 3, 'Selbsterklärung', 'DOCUMENT', '2', 'Ärtzliche Tauglichkeit (Die Selbsterklärung zum Gesundheitszustand muss vor Beginn vorliegen)'),
	('DRSA_GOLD', 'Praxis', 3, 'Schnellschwimmen', 'TIME', '100', '100 m Schwimmen in höchstens 1:40 Minuten'),
	('DRSA_GOLD', 'Voraussetzung', 4, 'EH-Ausbildung', 'DOCUMENT', '2', 'Nachweis einer Erste Hilfe Ausbildung zur Ausstellung'),
	('DRSA_GOLD', 'Praxis', 4, 'Streckentauchen', 'NORMAL', NULL, '30 m Streckentauchen, dabei von 10 kleinen Ringen oder Tellern, die auf einer Strecke von 20 m in einer höchstens 2 m breiten Gasse verteilt sind, mindestens 8 Stück aufsammeln'),
	('DRSA_GOLD', 'Praxis', 5, 'K-Tieftauchen', 'TIME', '180', 'dreimal Tieftauchen in Kleidung innerhalb von 3 Minuten; das erste Mal mit einem Kopfsprung, anschließend je einmal kopf- und fußwärts von der Wasseroberfläche mit gleichzeitigem Heraufholen von jeweils zwei 5-kg-Tauchringen oder gleichartigen Gegenständen, die etwa 3 m voneinander entfernt liegen (Wassertiefe zwischen 3 und 5 m)'),
	('DRSA_GOLD', 'Praxis', 6, 'K-Transportschwimmen', 'TIME', '90', '50 m Transportschwimmen, beide Partner in Kleidung: Schieben oder Ziehen in höchstens 1:30 Minuten'),
	('DRSA_GOLD', 'Praxis', 7, 'Befreiungsgriffe', 'NORMAL', NULL, 'Fertigkeiten zur Vermeidung von Umklammerungen sowie zur Befreiung aus Halsumklammerung von hinten und Halswürgegriff von hinten'),
	('DRSA_GOLD', 'Praxis', 8, 'Fremdretten', 'NORMAL', NULL, 'Kombi-Übung (beide Partner in Kleidung), die ohne Pause in der angegebenen Reihenfolge zu erfüllen ist: Sprung kopfwärts ins Wasser; 25 m Schwimmen in höchstens 30 Sekunden; Abtauchen auf 3 bis 5 m Tiefe und Heraufholen eines 5 kg Tauchrings oder eines gleichartigen Gegenstandes, diesen anschließend fallen lassen; Lösen aus der Umklammerung durch einen Befreiungsgriff; 25 m Schleppen in höchstens 60 Sekunden mit einem Fesselschleppgriff; Sichern und Anlandbringen des Geretteten; 3 Minuten Durchf'),
	('DRSA_GOLD', 'Praxis', 9, 'Rettungsgeräte', 'NORMAL', NULL, 'Handhabung von Rettungsgeräten: Retten mit dem "Rettungsball mit Leine": Zielwerfen in einen Sektor mit 3-m-Öffnung in 12 m Entfernung: 6 Würfe innerhalb von 5 Minuten, davon 4 Treffer Retten mit einem anderen Rettungsgerät Retten mit Rettungsgurt Leine (als Schwimmer und Leinenführer)'),
	('DRSA_GOLD', 'Praxis', 10, 'Hilfsmittel', 'NORMAL', NULL, 'Handhabung gebräuchlicher Hilfsmittel zur Wiederbelebung'),
	('DSTA', 'Voraussetzung', 1, '12 Jahre', 'AGE', '12', 'Mindestalter 12 Jahre (bei Minderjährigen ist die Einverständniserklärung des Erziehungsberechtigten erforderlich)'),
	('DSTA', 'Praxis', 1, '2-Flossenschwimmen', 'NORMAL', NULL, '600 m Flossenschwimmen ohne Zeitbegrenzung (je 200m Bauch-, Rücken- und Seitenlage)'),
	('DSTA', 'Theorie', 1, 'Fragebogen', 'NORMAL', NULL, 'Bundeseinheitlicher Fragebogen'),
	('DSTA', 'Voraussetzung', 2, 'Selbsterklärung', 'DOCUMENT', '2', 'Ärtzliche Tauglichkeit ((oder Formblatt "Selbsterklärung zum Gesundheitszustand") Tauchtauglichkeit nicht älter als 4 Wochen)'),
	('DSTA', 'Praxis', 2, '1-Flossenschwimmen', 'NORMAL', NULL, '200 m Flossenschwimmen mit einer Flosse und Armbewegung'),
	('DSTA', 'Voraussetzung', 3, 'DRSA Bronze', 'BADGE', 'DRSA_BRONZE', 'Deutsches Rettungsschwimmabzeichen Bronze'),
	('DSTA', 'Praxis', 3, 'Streckentauchen', 'NORMAL', NULL, '30 m Streckentauchen ohne Startsprung'),
	('DSTA', 'Praxis', 4, 'Zeittauchen', 'NORMAL', NULL, '30 Sekunden Zeittauchen (Festhalten erlaubt)'),
	('DSTA', 'Praxis', 5, 'Ausblasen', 'NORMAL', NULL, 'in mindestens 3 m Tiefe Taucherbrille abnehmen, wieder aufsetzen und ausblasen'),
	('DSTA', 'Praxis', 6, 'Tieftauchen', 'TIME', '60', 'dreimal innerhalb von einer Minute 3 m Tieftauchen'),
	('DSTA', 'Praxis', 7, 'Fremdretten', 'NORMAL', NULL, 'Kombi-Übung: 50 m Flossenschwimmen in Bauchlage mit Armtätigkeit, einmal 3 bis 5 m Tieftauchen und Heraufholen eines 5 kg Tauchrings oder eines gleichartigen Gegenstandes, 50m Schleppen eines Partners, 3 Minuten Durchführen der Herz-Lungen-Wiederbelebung (HLW)');
/*!40000 ALTER TABLE `discipline_list` ENABLE KEYS */;

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
  PRIMARY KEY (`id`),
  KEY `FK_participant_user` (`added_by_user_id`),
  CONSTRAINT `FK_participant_user` FOREIGN KEY (`added_by_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.participant: ~3 rows (ungefähr)
/*!40000 ALTER TABLE `participant` DISABLE KEYS */;
INSERT INTO `participant` (`id`, `name`, `gender`, `birthday`, `birthplace`, `address`, `post_code`, `city`, `note`, `added_by_user_id`) VALUES
	(1, 'Tim Teilnehmer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(4, 'Susi Schwimmer', NULL, '2000-06-21', 'Samplecity', 'Am Weg 16', '32839', 'Steinheim', 'Tolle Notiz!\r\nNeue Zeile', 1),
	(6, 'Frauke Fröhlich', NULL, '2001-12-12', 'London', 'Bahnhofstrasse 1', '32825', 'Blomberg', 'Allergie gegen Chlor\r\nTel.: 05728 3985', 1);
/*!40000 ALTER TABLE `participant` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle liquidb.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL COMMENT 'Groß/Kleinschreibungs-Unique',
  `name` varchar(50) DEFAULT NULL,
  `display_name` varchar(50) GENERATED ALWAYS AS (if(`name` is not null,`name`,`username`)) VIRTUAL,
  `salt` varchar(32) NOT NULL,
  `pw_hash` varchar(64) NOT NULL DEFAULT '',
  `pw_changed` datetime DEFAULT NULL,
  `pw_must_change` tinyint(1) NOT NULL DEFAULT 1,
  `ist_trainer` tinyint(1) NOT NULL DEFAULT 0,
  `ist_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Exportiere Daten aus Tabelle liquidb.user: ~5 rows (ungefähr)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `name`, `salt`, `pw_hash`, `pw_changed`, `pw_must_change`, `ist_trainer`, `ist_admin`) VALUES
	(1, 'system', 'System', 'cGYF)ma?2aPQ0]}&kjBY:Opxw#nm$9i|', 'AFB3E303AD2F243ED3F720B87CAE4CFAC676DAC8C3162F22F2314C471C380D7E', '2020-01-09 18:44:59', 0, 1, 1),
	(2, 'mozi_h', NULL, 'UDru)YD8_WZ5hZY|:nAf,A%1hV0<s|~-', 'E3B623D2EF7A76E02AA37861D5541372177D10B4A1F0715FD1D93DA6D5DD1EFE', '2020-01-11 17:58:50', 0, 0, 1),
	(3, 'marv', 'Marvin', 'CmG?WU7$3O4n#P=N1762#g_FYD >(Oy-', '9ABF1B211FFF73B8D288DBCB32B06ED2B690D3181252252507803AA601CB8620', '2020-01-09 17:53:01', 0, 1, 0),
	(4, 'alex', 'Alex Muster', 'fu{q A5eQFE1FhSfLX;[XBFic(CAJ%Xf', '84BE7BA4B7F2A479A5272B4CFA237FD3C1DFFDAD2B0C8D01E52F7639EBAD59C3', '2020-01-09 17:28:33', 0, 0, 1),
	(5, 'nina', 'Nina Neu', 'b.7i_y/F cUFB}Tmb=wdE;897x|>a/xS', '47C6C9BC102DB52CA0AA11B473C37C6B47B04E33C49B57FF2DC6BD85BB13FBB7', '2020-01-09 17:55:42', 0, 1, 0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
