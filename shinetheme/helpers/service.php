<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/20/2016
 * Time: 6:00 PM
 */
if(!function_exists('wpbooking_service_price'))
{
	function wpbooking_service_price($post_id=FALSE)
	{
		if(!$post_id) $post_id=get_the_ID();

		$base_price= get_post_meta($post_id,'price',true);
		$service_type= get_post_meta($post_id,'service_type',true);

		$base_price= apply_filters('wpbooking_service_base_price',$base_price,$post_id,$service_type);
		$base_price= apply_filters('wpbooking_service_base_price_'.$service_type,$base_price,$post_id,$service_type);

		return $base_price;
	}
}
if(!function_exists('wpbooking_service_price_html'))
{
	function wpbooking_service_price_html($post_id=FALSE)
	{
		if(!$post_id) $post_id=get_the_ID();

		$price=wpbooking_service_price($post_id);
		//$currency=get_post_meta($post_id,'currency',TRUE);
		$service_type= get_post_meta($post_id,'service_type',true);

		$price_html=WPBooking_Currency::format_money($price);
		$price_html=sprintf(__('from %s/night','wpbooking'),'<br><span class="price">'.$price_html.'</span>');

		$price_html= apply_filters('wpbooking_service_base_price_html',$price_html,$price,$post_id,$service_type);
		$price_html= apply_filters('wpbooking_service_base_price_html_'.$service_type,$price_html,$price,$post_id,$service_type);

		return $price_html;
	}
}
if(!function_exists('wpbooking_service_rate_to_html'))
{
	function wpbooking_service_rate_to_html($post_id=FALSE)
	{
		if(!$post_id) $post_id=get_the_ID();
		$rate=WPBooking_Comment_Model::inst()->get_avg_review($post_id);

		$html= '
		<span class="rating-stars">';
		for($i=1;$i<=5;$i++){
			$active=FALSE;
			if($rate>=$i) $active='active';
			$html.=sprintf('<a class="%s"><i class="fa fa-star-o icon-star"></i></a>',$active);
		}
		$html.='</span>';

		$res=WPBooking_Comment_Model::inst()->select('count(comment_ID) as total')->where(array(
			'comment_post_ID'=>$post_id,
			'comment_parent'=>0,
			'comment_approved'=>1
		))->get()->row();
		$count=!empty($res['total'])?$res['total']:0;

		$html.='<span class="rating-count">';
		if($count==0){
			//$html.=esc_html__('0 review','wpbooking');
		}elseif($count>1){
			$html.=sprintf(esc_html__('%d reviews','wpbooking'),$count);
		}else{
			$html.=esc_html__('1 review','wpbooking');
		}
		$html.='</span>';

		return $html;

	}
}

if(!function_exists('wpbooking_service_review_score_html')){
    function wpbooking_service_review_score_html($post_id = false){
        if(!$post_id) $post_id = get_the_ID();

        $score = WPBooking_Comment_Model::inst()->get_avg_review($post_id);
        $score = number_format($score,1,'.',' ');

        if($score > 4){
            $rating = __('Excellent ','wpbooking').$score;
        }elseif($score > 3){
            $rating = __('Very Good ','wpbooking').$score;
        }elseif($score > 2){
            $rating = __('Average ','wpbooking').$score;
        }elseif($score > 1){
            $rating = __('Poor ','wpbooking').$score;
        }else{
            $rating = __('Terrible ','wpbooking').$score;
        }

        $res=WPBooking_Comment_Model::inst()->select('count(comment_ID) as total')->where(array(
            'comment_post_ID'=>$post_id,
            'comment_parent'=>0,
            'comment_approved'=>1
        ))->get()->row();
        $count=!empty($res['total'])?$res['total']:0;
        $rating .= ' <span>('.sprintf(_n('%d review','%d reviews',$count,'wpbooking'), $count).')</span>';

        if($score == 0) $rating = '';

        return $rating;
    }
}


/**
 * @return string
 */
if(!function_exists('wpbooking_service_star_rating')){
    function wpbooking_service_star_rating($post_id){
        if(empty($post_id)) $post_id = get_the_ID();

        $hotel_star = get_post_meta($post_id, 'star_rating', true);
        for($i=1; $i<=5; $i++){
            $active=FALSE;
            if($hotel_star >= $i) $active='active';
            echo sprintf('<span class="%s"><i class="fa fa-star-o icon-star"></i></span>',$active);
        }
        $star_rating = '<span>'.$hotel_star.' '._n('star','stars',(int)$hotel_star,'wpbooking').'</span>';

        return $star_rating;
    }
}

if(!function_exists('wpbooking_order_item_status_html')){
	function wpbooking_order_item_status_html($status){
		$all_status=WPBooking_Config::inst()->item('order_status');
		if(array_key_exists($status,$all_status)){
			switch($status){
				case "on_hold":
					return sprintf('<label class="label label-warning">%s</label>',$all_status[$status]['label']);
				break;
				case "completed":
					return sprintf('<label class="label label-success">%s</label>',$all_status[$status]['label']);
				break;
				case "cancelled":
				case "refunded":
					return sprintf('<label class="label label-danger">%s</label>',$all_status[$status]['label']);
				break;

				default:
					return sprintf('<label class="label label-default">%s</label>',$all_status[$status]['label']);
					break;
			}
		}else{
			return sprintf('<label class="label label-default">%s</label>',esc_html__('Unknown','wpbooking'));
		}
	}
}
if(!function_exists('wpbooking_order_item_status_color')){
	function wpbooking_order_item_status_color($status){
		$all_status=WPBooking_Config::inst()->item('order_status');
		if(array_key_exists($status,$all_status)){
			switch($status){
				case "on_hold":
					return '#f0ad4e';
				break;
				case "completed":
					return '#5cb85c';
				break;
				case "cancelled":
				case "refunded":
					return '#d9534f';
				break;

				default:
					return '#5e5e5e';
					break;
			}
		}else{
			return '#5e5e5e';
		}
	}
}
if(!function_exists('wpbooking_payment_status_html')){
	function wpbooking_payment_status_html($status){

		// Pre-handle for old
		if($status=='on-paying') $status='processing';

		$all_status=WPBooking_Config::inst()->item('order_status');
		if(array_key_exists($status,$all_status)){
			switch($status){
				case "processing":
					return sprintf('<label class="label label-info">%s</label>',$all_status[$status]['label']);
				break;
				case "completed":
					return sprintf('<label class="label label-success">%s</label>',$all_status[$status]['label']);
				break;
				case "failed":
					return sprintf('<label class="label label-danger">%s</label>',$all_status[$status]['label']);
				break;
			}
		}else{
			return sprintf('<label class="label label-default">%s</label>',esc_html__('Unknown','wpbooking'));
		}
	}
}
if(!function_exists('wpbooking_get_order_item_used_gateway')){
	function wpbooking_get_order_item_used_gateway($payment_id=FALSE){

		$payment=WPBooking_Payment_Model::inst()->find($payment_id);
		if($payment and !empty($payment['gateway'])){
			$gateway=WPBooking_Payment_Gateways::inst()->get_gateway($payment['gateway']);
			if($gateway){
				return $gateway->get_info('label');
			}else{
				return esc_html__('Unknown Gateway','wpbooking');
			}

		}
	}
}

if(!function_exists('wpbooking_post_query_desc'))
{
	function wpbooking_post_query_desc($input=FALSE)
	{
		if(!$input) $input=WPBooking_Input::get();

		$q=array();
		if(!empty($input['location_id']) and $location_id=$input['location_id']){
			$location=get_term($location_id,'wpbooking_location');
			if(!is_wp_error($location) and $location)
			$q[]=sprintf(esc_html__('in %s','wpbooking'),'<span>'.$location->name.'</span>');
		}
		if(!empty($input['check_in']) and $check_in=$input['check_in']){
			$q[]=sprintf(esc_html__('from %s','wpbooking'),'<span>'.$check_in.'</span>');

			if(!empty($input['check_out']) and $check_out=$input['check_out']){
				$q[]=sprintf(esc_html__('to %s','wpbooking'),'<span>'.$check_out.'</span>');
			}
		}

		if(!empty($input['guest']) and $guest=$input['guest']){
			$q[]=sprintf(esc_html__('%s guest(s)','wpbooking'),'<span>'.$guest.'</span>');
		}
		$query_desc=FALSE;
		if(!empty($q)){
			foreach($q as $key=>$val){
				if($key==count($q)-1 && count($q)>1){
					$query_desc.=' ';
				}
				$query_desc.=$val.' ';
			}
		}

		return  apply_filters('wpbooking_service_post_query_desc',$query_desc,$q,$input);
	}
}
if(!function_exists('wpbooking_comment_nav')){
	function wpbooking_comment_nav() {
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			echo '<nav class="wb-reviews-pagination">';
			paginate_comments_links( apply_filters( 'wpbooking_comment_pagination_args', array(
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type'      => 'list',
			) ) );
			echo '</nav>';
		endif;
	}
}
if(!function_exists('wpbooking_comment_item')){
	function wpbooking_comment_item( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		echo wpbooking_load_view( 'single/review/item', array( 'comment' => $comment, 'args' => $args, 'depth' => $depth ) );
	}
}

if(!function_exists('wpbooking_review_allow_reply')){
	function wpbooking_review_allow_reply($review_id){

		$allow=FALSE;
		$review = get_comment($review_id);
		if($review){
			$post_id = $review->comment_post_ID;
			$service = new WB_Service($post_id);
			$count_child=WPBooking_Comment_Model::inst()->count_child($review_id);
			if(!$count_child and !$review->comment_parent and $service->get_author('id') == get_current_user_id() and $review->user_id!=get_current_user_id()) $allow=true;
		}

		return apply_filters('wpbooking_review_allow_reply',$allow);
	}
}
if(!function_exists('wpbooking_count_review')){
	function wpbooking_count_review($post_id){
		$model=WPBooking_Comment_Model::inst();

		$res= $model->select('count(comment_ID) as total')->where(array(
			'comment_post_ID'=>$post_id,
			'comment_parent'=>0,
			'comment_approved'=>1
		))->get()->row();

		return !empty($res['total'])?$res['total']:0;
	}
}
if(!function_exists('wpbooking_get_service')){
	function wpbooking_get_service($post_id=false){
		return WPBooking_Service_Controller::inst()->get_service_instance($post_id);
	}
}