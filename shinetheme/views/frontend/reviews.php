<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/30/2016
 * Time: 3:36 PM
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="wb-reviews-section">
	
	<?php if ( have_comments() ) : ?>
		<h5 class="service-info-title">
			<?php
			printf(esc_html__("%s's Reviews",'wpbooking'),get_the_title());
			?>
		</h5>

		
		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 56,
				'callback'=>'wpbooking_comment_item'
			) );
			?>
		</ol><!-- .comment-list -->
		
		<?php wpbooking_comment_nav(); ?>

		<?php
		$count=wpbooking_count_review(get_the_ID());
		$limit=get_option('comments_per_page');
		if($count){
			$page = get_query_var('cpage');
			if ( !$page )
				$page = 1;

			$page--;

			$to=($page+1)*$limit;

			if($count<$to)
			{
				$to=$count;
			}
			printf('<div class="wpbooking-review-total"><span class="count-total">%s</span><span class="show-from">%s</span></div>',sprintf(_n('1 review on this room','%d reviews on this room',$count,'wpbooking'),$count),sprintf(esc_html__('Showing %d to %d','wpbooking'),($limit*$page)+1,$to));
		}
		?>
	<?php endif; // have_comments() ?>


	<?php
	$field_review = apply_filters('wpbooking_review_field', wpbooking_load_view('single/review/review-field'));


	if ( ! isset( $args['format'] ) )
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5' === $args['format'];
	comment_form(array(
		'comment_notes_before'=>FALSE,
		'fields'=>array(
			'author' => '<p class="comment-form-author">' . '<label for="author"><strong>' . __( 'Name','wpbooking' ) . ( $req ? ' </strong><span class="required">*</span>' : '' ) . '</label> ' .
				'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>',
			'email'  => '<p class="comment-form-email"><label for="email"><strong>' . __( 'Your Email' ) . ( $req ? ' </strong><span class="required">*</span>' : '' ) . '</label> ' .
				'<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req  . ' /></p>',
		),
		'comment_field'        => '<div class="wpbooking-comment-form-content">
						<p class="comment-form-title">
							<label for="wpbooking_title"><strong>'.esc_html__('Review Title','wpbooking').'</strong> <span class="required">*</span></label>
							<input type="text" name="wpbooking_title" >
						</p>
						<p class="comment-form-comment"><label for="comment"><strong>' . esc_html__( 'Review Text', 'wpbooking' ) . '</strong> <span class="required">*</span></label> <textarea id="comment" name="comment" cols="45" rows="8"  aria-required="true" required="required"></textarea></p></div>'.$field_review,
		'label_submit'=>esc_html__('SEND','wpbooking')
	));
	if(!comments_open(get_the_ID()) and !is_user_logged_in()){
		printf('<p class="alert alert-danger">%s</p>',esc_html__('Please Login To Write Review','wpbooking'));
	}
	?>

</div><!-- .comments-area -->
