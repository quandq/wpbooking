<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/4/2016
 * Time: 10:10 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$service=new WB_Service();
$reply_allow=wpbooking_review_allow_reply(get_comment_ID());
?>
<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<footer class="comment-meta">
			<div class="comment-author vcard">
				<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment->comment_author_email, $args['avatar_size'] ); ?>
				<?php printf( '<b class="review-author-name">%s</b>', get_comment_author_link( $comment->comment_ID ) ); ?>
				<?php $count=WPBooking_User::inst()->count_reviews();
				if($count){
					printf('<span class="review-count">'._n('1 review','%d reviews',$count,'wpbooking').'</span>',$count);
				}
				?>
			</div><!-- .comment-author -->
		</footer><!-- .comment-meta -->

		<div class="comment-content-wrap">
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'This review is waiting for approval.' ); ?></p>
			<?php else:
				$comment_title=get_comment_meta(get_comment_ID(),'wpbooking_title',true);
				if(!$comment_title)$comment_title='&nbsp;';
				printf('<span class="comment-title">%s</span>',$comment_title);

				if($wpbooking_review=get_comment_meta(get_comment_ID(),'wpbooking_review',true)){
					?>
					<div class="wpbooking-review-summary">
						<label class="wpbooking-rating-review-result">
							<span class="rating-stars">
								<a class="<?php if($wpbooking_review>=1) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
								<a class="<?php if($wpbooking_review>=2) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
								<a class="<?php if($wpbooking_review>=3) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
								<a class="<?php if($wpbooking_review>=4) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
								<a class="<?php if($wpbooking_review>=5) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
							</span>
						</label>
					</div>
					<?php
				}
				echo "<div class='comment-text'>";
					comment_text();
				echo "</div>";
				$wpbooking_review_details=get_comment_meta(get_comment_ID(),'wpbooking_review_detail',true);

				if(!empty($wpbooking_review_details)){
					echo "<div class='wpbooking-more-review-detail'>";
						echo "<div class='review-stats'>";
						foreach($wpbooking_review_details as $key=> $value){
							if(!isset($value['title'])) return;
							if(!isset($value['rate'])) return;
							?>
							<label class="wpbooking-rating-review-result">
								<span class="rating-title">
									<?php echo esc_attr($value['title'])?>
								</span>
								<span class="rating-stars">
								<a class="<?php if($value['rate']>=1) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
								<a class="<?php if($value['rate']>=2) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
								<a class="<?php if($value['rate']>=3) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
								<a class="<?php if($value['rate']>=4) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
								<a class="<?php if($value['rate']>=5) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
							</span>
							</label>
							<?php
						}
						echo "</div>";
					echo "<span class='wp-show-detail-review'><span class='more'>".esc_html__('More','wpbooking')." <i class='fa fa-angle-double-down'></i></span><span class='less'>".esc_html__('Less','wpbooking')." <i class='fa fa-angle-double-up'></i></span></span>";
					echo "</div>";
				}
				if($service->enable_vote_for_review(get_comment_ID()) and !$comment->comment_parent){
					$count=wpbooking_count_review_vote(get_comment_ID());
					$liked=wpbooking_user_liked_review(get_comment_ID())?'active':FALSE;

					printf('<div class="wpbooking-vote-for-review">%s <span class="review-vote-count">%s</span> <a data-review-id="%s" class="review-do-vote %s"><i class="fa fa-thumbs-o-up"></i></a></div>',esc_html__('Was this review helpful?','wpbooking'),($count)?sprintf(esc_html__('%d like this','wpbooking'),$count):FALSE,get_comment_ID(),$liked);
				}
				if($reply_allow){
					echo '<a href="#" onclick="return false" class="wb-btn-reply-comment">'.esc_html__('Reply').'</a>';
				}
				 endif; ?>
		</div><!-- .comment-content -->
	</div>
	<?php if($reply_allow) {?>
	<ul>
		<li class="reply-comment-form">
			<div class="comment_container">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php echo ($service->get_author('avatar')) ?>
						<?php printf( '<b class="review-author-name">%s</b>', $service->get_author('name') ); ?>
						<?php $count=WPBooking_User::inst()->count_reviews($service->get_author('email'));
						if($count){
							printf('<span class="review-count">'._n('1 review','%d reviews',$count,'wpbooking').'</span>',$count);
						}
						?>
					</div><!-- .comment-author -->
				</footer><!-- .comment-meta -->

				<div class="comment-content-wrap">
					<div class="wpbooking-add-reply">
						<div class="reply-input">
							<textarea name="reply_content" class="reply_content" id="" cols="30" rows="10"></textarea>
						</div>
						<div class="reply-submit">
							<a href="#" data-review-id="<?php comment_ID() ?>" onclick="return false" class="wb-btn wb-btn-primary"><?php esc_html_e('Send','wpbooking')?> <i class="fa fa fa-spinner fa-spin"></i></a>
						</div>
					</div>
				</div><!-- .comment-content -->
			</div>
		</li>
	</ul>
	<?php } ?>

