<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 5/30/2016
 * Time: 6:01 PM
 */
if(!class_exists('WPBooking_Comment_Model'))
{
	class WPBooking_Comment_Model extends WPBooking_Model
	{
		static $_inst;

		function __construct()
		{
			$this->table_name='comments';
			$this->ignore_create_table=TRUE;
			parent::__construct();
		}

		function get_avg_review($post_id=FALSE)
		{
			global $wpdb;
			if(!$post_id) return FALSE;

			$row=$this->select('avg('.$wpdb->commentmeta.'.meta_value) as avg_rate')
				->join('commentmeta','commentmeta.comment_id=comments.comment_ID')
				->where($wpdb->commentmeta.'.meta_key','wpbooking_review')
				->where('comment_post_ID',$post_id)
				->where('comment_approved',1)
				->get()->row();

			return !empty($row['avg_rate'])?$row['avg_rate']:FALSE;
		}
		static function inst()
		{
			if(!self::$_inst) self::$_inst=new self();
			return self::$_inst;
		}


	}

	WPBooking_Comment_Model::inst();

}