<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;


/**
 * Flattens a multidimensional array into a single-dimensional array.
 *
 * @param array $array The array to flatten.
 * @return array The flattened array.
 */
function flatten_array( array $array ): array { // phpcs:ignore
	$result = array();

	array_walk_recursive(
		$array,
		function ( $value ) use ( &$result ) {
			$result[] = $value;
		}
	);

	return $result;
}
