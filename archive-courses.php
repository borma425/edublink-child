<?php
/**
 * Template for displaying courses archive - Custom Raw HTML
 *
 * @package EduBlink_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if Tutor LMS is active
if ( ! function_exists( 'tutor_utils' ) ) {
	echo 'Tutor LMS is not active';
	exit;
}

use TUTOR\Input;

get_header();

$get = isset( $_GET['course_filter'] ) ? Input::sanitize_array( $_GET ) : array();//phpcs:ignore
if ( isset( $get['course_filter'] ) ) {
	$filter = ( new \Tutor\Course_Filter( false ) )->load_listing( $get, true );
	query_posts( $filter );
}

// Get archive title
$archive_title = get_the_archive_title();
if ( empty( $archive_title ) ) {
	$archive_title = __( 'Courses', 'tutor' );
}
?>

<div class="site-content">
	<div class="site-content-inner">
		
		<!-- Custom Archive Header -->
		<header class="custom-archive-header">
			<h1 class="custom-archive-title"><?php echo esc_html( $archive_title ); ?></h1>
		</header>

		<!-- Custom Course Archive Content - Raw HTML -->
		<div class="custom-course-archive-content">
			<?php if ( have_posts() ) : ?>
				<div class="custom-courses-list">
					<?php while ( have_posts() ) : the_post(); 
						$course_id = get_the_ID();
						$course_title = get_the_title();
						$course_excerpt = get_the_excerpt();
						$course_permalink = get_permalink();
						$course_image = get_the_post_thumbnail_url( $course_id, 'full' );
						$course_rating = tutor_utils()->get_course_rating( $course_id );
						$course_price = apply_filters( 'get_tutor_course_price', null, $course_id );
						$course_duration = get_tutor_course_duration_context( $course_id );
						$lesson_count = tutor_utils()->get_lesson_count_by_course( $course_id );
						$students_count = tutor_utils()->count_enrolled_users_by_course( $course_id );
						$instructors = tutor_utils()->get_instructors_by_course( $course_id );
						$categories = get_the_terms( $course_id, 'course-category' );
						$tags = get_the_terms( $course_id, 'course-tag' );
					?>
						<article class="custom-course-item">
							<?php if ( $course_image ) : ?>
								<div class="custom-course-image">
									<a href="<?php echo esc_url( $course_permalink ); ?>">
										<img src="<?php echo esc_url( $course_image ); ?>" alt="<?php echo esc_attr( $course_title ); ?>">
									</a>
								</div>
							<?php endif; ?>
							
							<div class="custom-course-info">
								<h2 class="custom-course-title">
									<a href="<?php echo esc_url( $course_permalink ); ?>">
										<?php echo esc_html( $course_title ); ?>
									</a>
								</h2>
								
								<?php if ( $course_excerpt ) : ?>
									<div class="custom-course-excerpt">
										<?php echo esc_html( $course_excerpt ); ?>
									</div>
								<?php endif; ?>
								
								<?php if ( $course_rating && isset( $course_rating->rating_avg ) ) : ?>
									<div class="custom-course-rating">
										<span class="custom-rating-value"><?php echo esc_html( number_format( $course_rating->rating_avg, 1 ) ); ?></span>
										<span class="custom-rating-count">(<?php echo esc_html( $course_rating->rating_count ); ?> reviews)</span>
									</div>
								<?php endif; ?>
								
								<?php if ( $course_price ) : ?>
									<div class="custom-course-price">
										<?php echo $course_price; ?>
									</div>
								<?php else : ?>
									<div class="custom-course-price">Free</div>
								<?php endif; ?>
								
								<div class="custom-course-meta">
									<?php if ( $course_duration ) : ?>
										<span class="custom-meta-item">Duration: <?php echo esc_html( $course_duration ); ?></span>
									<?php endif; ?>
									
									<?php if ( $lesson_count ) : ?>
										<span class="custom-meta-item">Lessons: <?php echo esc_html( $lesson_count ); ?></span>
									<?php endif; ?>
									
									<?php if ( $students_count ) : ?>
										<span class="custom-meta-item">Students: <?php echo esc_html( $students_count ); ?></span>
									<?php endif; ?>
								</div>
								
								<?php if ( ! empty( $instructors ) ) : ?>
									<div class="custom-course-instructors">
										<span class="custom-instructors-label">Instructors:</span>
										<?php foreach ( $instructors as $instructor ) : ?>
											<span class="custom-instructor-name"><?php echo esc_html( $instructor->display_name ); ?></span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								
								<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
									<div class="custom-course-categories">
										<span class="custom-categories-label">Categories:</span>
										<?php foreach ( $categories as $category ) : ?>
											<span class="custom-category-item"><?php echo esc_html( $category->name ); ?></span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								
								<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
									<div class="custom-course-tags">
										<span class="custom-tags-label">Tags:</span>
										<?php foreach ( $tags as $tag ) : ?>
											<span class="custom-tag-item"><?php echo esc_html( $tag->name ); ?></span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								
								<div class="custom-course-link">
									<a href="<?php echo esc_url( $course_permalink ); ?>" class="custom-view-course-btn">
										View Course
									</a>
								</div>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
				
				<!-- Custom Pagination -->
				<div class="custom-pagination">
					<?php
					the_posts_pagination( array(
						'mid_size'  => 2,
						'prev_text' => __( 'Previous', 'edublink-child' ),
						'next_text' => __( 'Next', 'edublink-child' ),
					) );
					?>
				</div>
			<?php else : ?>
				<div class="custom-no-courses">
					<p class="custom-no-courses-message">No courses found.</p>
				</div>
			<?php endif; ?>
		</div>

	</div><!-- .site-content-inner -->
</div><!-- .site-content -->

<?php
get_footer();

