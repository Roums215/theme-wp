<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

return array(
	'Hooks'           => 'includes/hooks.php',
	'Scripts'         => 'includes/scripts.php',
	'Template_Loader' => 'includes/template-loader.php',
	'Video_Btn'       => 'includes/video-btn.php',
	'Admin_Menu'      => 'includes/admin-menu.php',
	'Save_Video'      => 'includes/save-video.php',
	'Display_Video'   => 'includes/display-video.php',
	'Helpers'         => 'includes/helpers.php',
	'Generate_CSS'    => 'includes/generate-css.php',
	'Email_Reminders' => 'includes/email-reminders.php',
	'Email_Settings'  => 'includes/email-settings.php',
	'Reminders_List'  => 'includes/reminders-list.php',
	'Database'            => 'includes/database.php',
	'Migration'           => 'includes/migration.php',
	'Video_Reviews_List'  => 'includes/video-reviews-list.php',
	'Video_Reviews_Table' => 'includes/video-reviews-table.php',
);
