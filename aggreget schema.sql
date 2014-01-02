-- --------------------------------------------------------
-- Host:                         mkgdb01.matthewkgroves.com
-- Server version:               5.1.56-log - MySQL Server
-- Server OS:                    pc-linux-gnu
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2014-01-02 16:20:43
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table aggroget.aggro_news
CREATE TABLE IF NOT EXISTS `aggro_news` (
  `news` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(250) NOT NULL DEFAULT '',
  `site` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(250) DEFAULT NULL,
  `dtscrape` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`news`),
  UNIQUE KEY `UniqueURLSite` (`site`,`url`),
  UNIQUE KEY `news` (`news`),
  KEY `news_2` (`news`),
  KEY `siteindex` (`site`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table aggroget.aggro_sites
CREATE TABLE IF NOT EXISTS `aggro_sites` (
  `site` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rssurl` varchar(250) DEFAULT NULL,
  `sitename` varchar(50) DEFAULT NULL,
  `besttitle` tinyint(3) unsigned DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`site`),
  UNIQUE KEY `site` (`site`),
  KEY `site_2` (`site`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

-- Dumping data for table aggroget.aggro_sites: 11 rows
/*!40000 ALTER TABLE `aggro_sites` DISABLE KEYS */;
INSERT INTO `aggro_sites` (`site`, `rssurl`, `sitename`, `besttitle`, `active`) VALUES
	(1, 'http://feeds.delicious.com/rss/popular/', 'del.icio.us', 49, 1),
	(2, 'http://rss.stumbleupon.com/buzz/', 'Stumble Buzz', 50, 1),
	(3, 'http://www.twitbuzz.com/rss.xml', 'TwitBuzz', 8, 0),
	(4, 'http://www.reddit.com/.rss', 'Reddit', 9, 1),
	(5, 'http://feeds.tailrank.com/TailrankTopStories', 'Tailrank', 4, 0),
	(6, 'http://d.yimg.com/ds/rss/V1/top10/all', 'Yahoo Buzz', 3, 0),
	(7, 'http://rss.furl.net/urls/popular.rss?days=1', 'Furl', 5, 1),
	(8, 'http://givemesomethingtoread.com/rss', 'Instapaper', 1, 1),
	(10, 'http://services.digg.com/stories/popular?count=20&appkey=http%3A%2F%2Faggregate.com', 'Digg', 2, 1),
	(11, 'http://feeds.mixx.com/MixxPopular', 'Mixx', 7, 1),
	(12, 'http://www.fark.com', 'Fark', 48, 0);
/*!40000 ALTER TABLE `aggro_sites` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
