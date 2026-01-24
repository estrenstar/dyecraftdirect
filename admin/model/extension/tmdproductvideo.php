<?php
class ModelExtensiontmdproductvideo extends Model {
	public function install() {
$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."product_video` (
  `product_video_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `video_thumbimage` varchar(255) NOT NULL,
  `video_type` varchar(255) NOT NULL,
  `upload_video` varchar(255) NOT NULL,
  `youtube_video` text NOT NULL,
  `vimeo_video` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`product_video_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
	public function uninstall() {
	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."product_video`");
	}
}
