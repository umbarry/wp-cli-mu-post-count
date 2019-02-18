<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Allow post count operation across multisite blogs
 *
 * ## OPTIONS
 *
 * --author=<author>
 * : Author id
 *
 * [--year=<year>]
 * : Filter by year
 *
 * [--monthnum=<month>]
 * : Filter by month
 *
 * @when after_wp_load
 */
$count_command = function($args, $assoc_args)
{
	$blogs = get_blogs_of_user($assoc_args['author']);
	$count = array();
	$command_arguments = '--author=' . $assoc_args['author'];

	if(!empty($assoc_args['year'])) {
		$command_arguments .= '--year=' . $assoc_args['year'] . ' ';
	}

	if(!empty($assoc_args['monthnum'])) {
		$command_arguments .= '--monthnum=' . $assoc_args['monthnum'] . ' ';
	}

	foreach ($blogs as $blog) {

		$count[] = array(
			'blog_id' 	=> $blog->userblog_id,
			'url'		=> $blog->siteurl,
			'count'		=> WP_CLI::runcommand('post list --format=count ' . $command_arguments . '--url=' . $blog->siteurl, array(
				'return' => true,
			))
		);
	}

	WP_CLI\Utils\format_items("csv", $count, array('url', 'count'));
};

WP_CLI::add_command('mu-post-count', $count_command);
