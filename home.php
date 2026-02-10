<?php
/**
 * Template Name: Front Page
 * 
 * Front page template for the home page
 * Uses Timber Twig template
 * 
 * @package EduBlink_Child
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if Timber is available
if ( ! class_exists( 'Timber\Timber' ) ) {
	echo 'Timber plugin is not installed.';
	return;
}

// Get Timber context
$context = Timber::context();

// Add theme directory URI to context
$context['theme_uri'] = get_stylesheet_directory_uri();

// Capture wp_head and wp_footer output
ob_start();
wp_head();
$context['wp_head'] = ob_get_clean();

ob_start();
wp_footer();
$context['wp_footer'] = ob_get_clean();

// Get featured courses from Tutor LMS
$context['courses'] = array();
if ( function_exists( 'tutor_utils' ) ) {
	$course_post_type = tutor()->course_post_type;
	
	// Get featured courses (limit to 6)
	$args = array(
		'post_type'      => $course_post_type,
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);
	
	$courses_query = new WP_Query( $args );
	
	if ( $courses_query->have_posts() ) {
		while ( $courses_query->have_posts() ) {
			$courses_query->the_post();
			$course_id = get_the_ID();
			
			// Get course data using Timber::get_post()
			$course = Timber::get_post( $course_id );
			
			if ( $course ) {
				// Get course rating
				$course_rating = tutor_utils()->get_course_rating( $course_id );
				$course->rating_avg = $course_rating ? number_format( $course_rating->rating_avg, 1 ) : 0;
				$course->rating_count = $course_rating ? $course_rating->rating_count : 0;
				
				// Get course price
				$course->price = apply_filters( 'get_tutor_course_price', null, $course_id );
				
				// Get course duration
				$course->duration = get_tutor_course_duration_context( $course_id );
				
				// Get lesson count
				$course->lesson_count = tutor_utils()->get_lesson_count_by_course( $course_id );
				
				// Get students count
				$course->students_count = tutor_utils()->count_enrolled_users_by_course( $course_id );
				
				// Get instructors
				$instructors = tutor_utils()->get_instructors_by_course( $course_id );
				if ( ! empty( $instructors ) && isset( $instructors[0]->ID ) ) {
					$course->instructor = Timber::get_user( $instructors[0]->ID );
				} else {
					$course->instructor = null;
				}
				
				// Get course level
				$course->level = get_post_meta( $course_id, '_tutor_course_level', true );
				if ( empty( $course->level ) ) {
					$course->level = 'مبتدئ';
				}
				
				// Get course image
				$course->thumbnail = get_the_post_thumbnail_url( $course_id, 'full' );
				if ( ! $course->thumbnail ) {
					$course->thumbnail = tutor()->url . 'assets/images/placeholder-course.jpg';
				}
				
				$context['courses'][] = $course;
			}
		}
		wp_reset_postdata();
	}
}

// Render Twig template
Timber::render( 'front-page.twig', $context );

