<?php
/**
 * Deprecated class
 *
 * @package Webcomic
 */

/**
 * Deprecated class.
 *
 * @deprecated
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) - Required for compatibility.
 * @SuppressWarnings(PHPMD.ExcessiveClassLength) - Required for compatibility.
 * @SuppressWarnings(PHPMD.TooManyMethods) - Required for compatibility.
 */
class WebcomicTag {
	/**
	 * Whether to trigger a deprecated error.
	 *
	 * @var bool
	 */
	protected static $error = true;

	/**
	 * Invoke a static method.
	 *
	 * @param string $method The method to invoke.
	 * @param array  $args The method arguments.
	 * @return mixed
	 */
	public static function __callStatic( string $method, array $args ) {
		static::$error = ( defined( 'WP_DEBUG' ) && WP_DEBUG && webcomic( 'option.debug' ) );

		preg_match( '/_$/', $method, $match );

		if ( $match ) {
			$method = rtrim( $method, '_' );

			static::$error = false;
		}

		if ( ! method_exists( 'WebcomicTag', $method ) ) {
			return;
		}

		return call_user_func_array( [ 'WebcomicTag', $method ], $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param int $id Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_attachments( $id = 0 ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_attachments() is deprecated.', 'webcomic' ) );

		return get_children(
			[
				'order'            => 'asc',
				'fields'           => 'ids',
				'orderby'          => 'menu_order',
				'post_type'        => 'attachment',
				'post_parent'      => $id,
				'post_mime_type'   => 'image',
				'suppress_filters' => false,
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param bool $config Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_webcomic_collection( $config = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_webcomic_collection() is deprecated; use get_webcomic_collection() instead.', 'webcomic' ) );

		if ( func_num_args() && func_get_arg( 0 ) ) {
			return get_webcomic_collection_options();
		}

		return get_webcomic_collection();
	}

	/**
	 * Deprecated method.
	 *
	 * @param bool $config Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_webcomic_collections( $config = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_webcomic_collections() is deprecated; use get_webcomic_collection() instead.', 'webcomic' ) );

		$args = [
			'fields'     => 'ids',
			'hide_empty' => false,
		];

		if ( $config ) {
			$args['fields'] = 'options';
		}

		return get_webcomic_collections( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $collection_one Deprecated parameter.
	 * @param mixed $collection_two Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 * @suppress PhanAccessMethodInternal - Required for compatibility.
	 */
	protected static function sort_webcomic_collections_name( $collection_one, $collection_two ) {
		static::$error && webcomic_error( __( 'WebcomicTag::sort_webcomic_collections_name() is deprecated; use sort_webcomic_collections_name() instead.', 'webcomic' ) );

		return sort_webcomic_collections_name( $collection_one['id'], $collection_two['id'] );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $collection_one Deprecated parameter.
	 * @param mixed $collection_two Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 * @suppress PhanAccessMethodInternal - Required for compatibility.
	 */
	protected static function sort_webcomic_collections_slug( $collection_one, $collection_two ) {
		static::$error && webcomic_error( __( 'WebcomicTag::sort_webcomic_collections_slug() is deprecated; use sort_webcomic_collections_slug() instead.', 'webcomic' ) );

		return sort_webcomic_collections_slug( $collection_one['id'], $collection_two['id'] );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $collection_one Deprecated parameter.
	 * @param mixed $collection_two Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 * @suppress PhanAccessMethodInternal - Required for compatibility.
	 */
	protected static function sort_webcomic_collections_count( $collection_one, $collection_two ) {
		static::$error && webcomic_error( __( 'WebcomicTag::sort_webcomic_collections_count() is deprecated; use sort_webcomic_collections_count() instead.', 'webcomic' ) );

		return sort_webcomic_collections_count( $collection_one['id'], $collection_two['id'] );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $collection_one Deprecated parameter.
	 * @param mixed $collection_two Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 * @suppress PhanAccessMethodInternal - Required for compatibility.
	 */
	protected static function sort_webcomic_collections_updated( $collection_one, $collection_two ) {
		static::$error && webcomic_error( __( 'WebcomicTag::sort_webcomic_collections_updated() is deprecated; use sort_webcomic_collections_updated() instead.', 'webcomic' ) );

		return sort_webcomic_collections_updated( $collection_one['id'], $collection_two['id'] );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $version Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic( $version = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic() is deprecated.', 'webcomic' ) );

		if ( ! $version ) {
			$directory = get_stylesheet_directory();
			$theme     = new WP_Theme( basename( $directory ), dirname( $directory ) );
			$version   = $theme->get( 'Webcomic' );
		}

		if ( ! $version ) {
			return true;
		}

		return version_compare( webcomic( 'option.version' ), $version, '>=' );
	}

	/**
	 * Deprecated method.
	 *
	 * @param bool $dynamic Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function is_webcomic( $dynamic = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::is_webcomic() is deprecated; use is_webcomic() instead.', 'webcomic' ) );

		if ( $dynamic ) {
			return is_webcomic() && wp_doing_ajax() && 'webcomic_dynamic' === webcomic( 'GLOBALS._REQUEST.action' );
		}

		return is_webcomic();
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $relation Deprecated parameter.
	 * @param bool   $in_same_term Deprecated parameter.
	 * @param bool   $excluded_terms Deprecated parameter.
	 * @param array  $taxonomies Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function is_relative_webcomic( $relation = 'first', $in_same_term = null, $excluded_terms = null, $taxonomies = [ 'storyline' ], $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::is_relative_webcomic() is deprecated; use is_webcomic() instead.', 'webcomic' ) );

		$comic = static::get_relative_webcomic_( $relation, $in_same_term, $excluded_terms, $taxonomies, $collection );

		if ( ! $comic ) {
			return false;
		}

		return is_webcomic( $collection, $comic );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $collection Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function is_webcomic_attachment( $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::is_webcomic_attachment() is deprecated; use is_webcomic_media() instead.', 'webcomic' ) );

		return is_webcomic_media( [ $collection ] );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $page Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function is_webcomic_page( $page = null, $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::is_webcomic_page() is deprecated; use is_webcomic_page() instead.', 'webcomic' ) );

		if ( is_object( $page ) ) {
			$page = $page->ID;
		}

		return is_webcomic_page( $collection, $page );
	}

	/**
	 * Deprecated method.
	 *
	 * @return bool
	 * @deprecated
	 */
	protected static function is_webcomic_archive() {
		static::$error && webcomic_error( __( 'WebcomicTag::is_webcomic_archive() is deprecated; use is_webcomic_collection() instead.', 'webcomic' ) );

		return is_webcomic_collection();
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $taxonomy Deprecated parameter.
	 * @param string $term Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function is_webcomic_tax( $taxonomy = '', $term = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::is_webcomic_tax() is deprecated; use is_webcomic_tax() instead.', 'webcomic' ) );

		$func = 'tax';

		if ( $taxonomy && function_exists( "is_webcomic_{$taxonomy}" ) ) {
			$func = $taxonomy;
		}

		$callback = "is_webcomic_{$taxonomy}";

		return $callback( null, $term );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $collection Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function is_webcomic_crossover( $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::is_webcomic_crossover() is deprecated; use is_webcomic_tax() instead.', 'webcomic' ) );

		if ( ! $collection ) {
			$collection = true;
		}

		return is_webcomic_tax(
			null, null, null, [
				'crossover' => $collection,
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $post Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function is_a_webcomic( $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::is_a_webcomic() is deprecated; use is_a_webcomic() instead.', 'webcomic' ) );

		return is_a_webcomic( $post );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $post Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function is_a_webcomic_attachment( $post = null, $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::is_a_webcomic_attachment() is deprecated; use is_a_webcomic_media() instead.', 'webcomic' ) );

		return is_a_webcomic_media( $post, [ $collection ] );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $post Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function has_webcomic_attachments( $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::has_webcomic_attachments() is deprecated; use has_webcomic_media() instead.', 'webcomic' ) );

		return has_webcomic_media( $post );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $scope Deprecated parameter.
	 * @param string $term Deprecated parameter.
	 * @param mixed  $post Deprecated parameter.
	 * @return bool
	 * @deprecated
	 */
	protected static function has_webcomic_crossover( $scope = '', $term = '', $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::has_webcomic_crossover() is deprecated; use has_webcomic_term() instead.', 'webcomic' ) );

		return has_webcomic_term( "crossover_{$scope}", $term, $post );
	}

	/**
	 * Deprecated method.
	 *
	 * @param bool   $pending Deprecated parameter.
	 * @param string $language Deprecated parameter.
	 * @param mixed  $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function have_webcomic_transcripts( $pending = null, $language = '', $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::have_webcomic_transcripts() is deprecated; use has_webcomic_transcripts() instead.', 'webcomic' ) );

		$args = [
			'post_status' => 'publish',
		];

		if ( $pending ) {
			$args['post_status'] = 'pending';
		}

		if ( $language ) {
			$args['tax_query'][] = [
				'field'    => 'slug',
				'taxonomy' => 'webcomic_transcript_language',
				'terms'    => $language,
			];
		}

		return has_webcomic_transcripts( $post, $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_transcripts_open( $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_transcripts_open() is deprecated; use webcomic_transcripts_open() instead.', 'webcomic' ) );

		return webcomic_transcripts_open( $post );
	}

	/**
	 * Deprecated method.
	 *
	 * @param bool  $original Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_prints_available( $original = null, $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_prints_available() is deprecated; use has_webcomic_print() instead.', 'webcomic' ) );

		$type = '';

		if ( $original ) {
			$type = 'original';
		}

		return has_webcomic_print( $type, $post );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $collection Deprecated parameter.
	 * @param mixed  $user Deprecated parameter.
	 * @param int    $age Deprecated parameter.
	 * @return mixed|null
	 * @deprecated
	 */
	protected static function verify_webcomic_age( $collection = '', $user = null, $age = 0 ) {
		static::$error && webcomic_error( __( 'WebcomicTag::verify_webcomic_age() is deprecated; use webcomic_age_required() instead.', 'webcomic' ) );

		if ( ! $collection ) {
			$collection = get_webcomic_collection();
		}

		if ( ! $age ) {
			$age = webcomic( "option.{$collection}.restrict_age" );
		}

		if ( ! $collection || ! $age ) {
			return true;
		}

		$user_age = (int) webcomic( "GLOBALS._COOKIE.{$collection}_age_" . COOKIEHASH );

		if ( $user_age ) {
			return $user_age >= $age;
		}
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_verify_webcomic_age( $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_verify_webcomic_age() is deprecated; use get_webcomic_age() instead.', 'webcomic' ) );

		if ( ! $collection ) {
			$collection = get_webcomic_collection();
		}

		return (int) webcomic( "option.{$collection}.restrict_age" );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $collection Deprecated parameter.
	 * @param mixed  $user Deprecated parameter.
	 * @param array  $roles Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function verify_webcomic_role( $collection = '', $user = null, $roles = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::verify_webcomic_role() is deprecated; use webcomic_roles_required() instead.', 'webcomic' ) );

		if ( ! $collection ) {
			$collection = get_webcomic_collection();
		}

		if ( ! $roles || ! is_array( $roles ) ) {
			$roles = webcomic( "option.{$collection}.restrict_roles" );
		}

		if ( ! $collection || ! $roles || ( is_user_logged_in() && in_array( '~loggedin~', $roles, true ) ) ) {
			return true;
		}

		$user_roles = wp_get_current_user()->roles;

		return ! ! array_intersect( $user_roles, $roles );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $size Deprecated parameter.
	 * @param string $relation Deprecated parameter.
	 * @param bool   $in_same_term Deprecated parameter.
	 * @param bool   $excluded_terms Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @param mixed  $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function the_webcomic( $size = 'full', $relation = '', $in_same_term = null, $excluded_terms = null, $taxonomy = 'storyline', $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::the_webcomic() is deprecated; use get_webcomic_media() instead.', 'webcomic' ) );

		$comic = get_webcomic( $post );

		if ( ! $comic ) {
			return '';
		}

		$output = get_webcomic_media( $size, $post );

		if ( ! $relation ) {
			$output = get_webcomic_link( $output, $comic );
		} elseif ( $relation ) {
			$output = static::relative_webcomic_link_( '%link', $output, $relation, $in_same_term, $excluded_terms, $taxonomy, $comic->post_type );
		}

		return $output;
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_count( $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_count() is deprecated.', 'webcomic' ) );

		return count( get_post_meta( $post, 'webcomic_media' ) );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $storylines Deprecated parameter.
	 * @param mixed $characters Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return mixed
	 * @deprecated Use get_webcomics() instead.
	 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - Required for compatibility.
	 */
	protected static function get_related_webcomics( $storylines = 'true', $characters = 'true', $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_related_webcomics() is deprecated; use get_webcomics() instead.', 'webcomic' ) );

		$args = [
			'fields'     => 'ids',
			'orderby'    => 'rand',
			'related_to' => get_webcomic( $post ),
			'related_by' => [],
		];

		if ( ! $args['related_to'] ) {
			return [];
		}

		$args['post__not_in'] = [ $args['related_to']->ID ];

		foreach ( get_object_taxonomies( $args['related_to'] ) as $taxonomy ) {
			if ( ! preg_match( '/^webcomic\d+_(character|storyline)/', $taxonomy ) ) {
				continue;
			} elseif ( ! $characters && false !== strpos( $taxonomy, '_character' ) ) {
				continue;
			} elseif ( ! $storylines && false !== strpos( $taxonomy, '_storyline' ) ) {
				continue;
			} elseif ( '!' === $characters && "{$args['related_to']->post_type}_character" !== $taxonomy ) {
				continue;
			} elseif ( '!' === $storylines && "{$args['related_to']->post_type}_storyline" !== $taxonomy ) {
				continue;
			} elseif ( 'x' === $characters && "{$args['related_to']->post_type}_character" === $taxonomy ) {
				continue;
			} elseif ( 'x' === $storylines && "{$args['related_to']->post_type}_storyline" === $taxonomy ) {
				continue;
			}

			$args['related_by'][] = $taxonomy;
		}

		return get_webcomics( $args );
	} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

	/**
	 * Deprecated method.
	 *
	 * @param string $before Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $after Deprecated parameter.
	 * @param string $image Deprecated parameter.
	 * @param int    $limit Deprecated parameter.
	 * @param mixed  $storylines Deprecated parameter.
	 * @param mixed  $characters Deprecated parameter.
	 * @param mixed  $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 * @SuppressWarnings(PHPMD.ExcessiveParameterList) - Required for compatibility.
	 */
	protected static function the_related_webcomics( $before = '', $join = ', ', $after = '', $image = '', $limit = 5, $storylines = 'true', $characters = 'true', $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::the_related_webcomics() is deprecated; use webcomics_list() instead.', 'webcomic' ) );

		$args = [
			'format'         => "{$before}{{{$join}}}{$after}",
			'link'           => '%title',
			'orderby'        => 'rand',
			'posts_per_page' => $limit,
			'post__in'       => static::get_related_webcomics_( $storylines, $characters, $post ),
		];

		if ( ! $args['post__in'] ) {
			return '';
		}

		return get_webcomics_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $relation Deprecated parameter.
	 * @param bool   $in_same_term Deprecated parameter.
	 * @param array  $excluded_terms Deprecated parameter.
	 * @param array  $taxonomies Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength) - Required for compatibility.
	 * @SuppressWarnings(PHPMD.NPathComplexity) - Required for compatibility.
	 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.TooHigh - Required for compatibility.
	 */
	protected static function get_relative_webcomic( $relation = 'random', $in_same_term = null, $excluded_terms = [], $taxonomies = [ 'storyline' ], $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_relative_webcomic() is deprecated; use get_webcomic() instead.', 'webcomic' ) );

		$comic     = get_webcomic();
		$tax_query = [];
		$args      = [
			'post_type' => get_webcomic_collection(),
			'tax_query' => [],
		];

		if ( $collection ) {
			$args['post_type'] = $collection;
		} elseif ( ! $args['post_type'] ) {
			unset( $args['post_type'] );
		}

		if ( false !== strpos( $relation, '-nocache' ) ) {
			$relation = str_replace( '-nocache', '', $relation );
		}

		$args['relation'] = $relation;

		if ( isset( $args['post_type'] ) && ( $in_same_term || $excluded_terms ) ) {
			foreach ( (array) $taxonomies as $taxonomy ) {
				if ( preg_match( '/^!?(character|storyline)$/', $taxonomy ) ) {
					$tax_query[] = "{$args['post_type']}_" . substr( $taxonomy, 1 );

					continue;
				} elseif ( preg_match( '/^x(character|storyline)$/', $taxonomy, $match ) ) {
					foreach ( webcomic( "option.{$args['post_type']}.taxonomies" ) as $tax ) {
						if ( ! preg_match( "/^(?!{$args['post_type']})webcomic\d+_{$match[1]}/", $tax ) ) {
							continue;
						}

						$tax_query[] = $tax;
					}

					continue;
				}

				$tax_query[] = $taxonomy;
			}

			if ( true === $in_same_term ) {
				$in_same_term = wp_get_object_terms(
					$comic->ID, $tax_query, [
						'fields' => 'ids',
					]
				);

				if ( is_wp_error( $in_same_term ) ) {
					$in_same_term = [];
				}
			} elseif ( is_string( $in_same_term ) ) {
				$in_same_term = explode( ',', $in_same_term );
			}

			$in_same_term = array_filter( array_map( 'intval', (array) $in_same_term ) );

			if ( is_string( $excluded_terms ) ) {
				$excluded_terms = explode( ',', $excluded_terms );
			}

			$excluded_terms = array_filter( array_map( 'intval', (array) $excluded_terms ) );

			foreach ( $tax_query as $taxonomy ) {
				if ( $in_same_term ) {
					$args['tax_query'][] = [
						'taxonomy' => $taxonomy,
						'terms'    => $in_same_term,
					];
				}

				if ( $excluded_terms ) {
					$args['tax_query'][] = [
						'taxonomy' => $taxonomy,
						'terms'    => $excluded_terms,
						'operator' => 'NOT IN',
					];
				}
			}
		} // End if().

		return get_webcomic( $comic, $args );
	} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.TooHigh

	/**
	 * Deprecated method.
	 *
	 * @param string $relation Deprecated parameter.
	 * @param mixed  $in_same_term Deprecated parameter.
	 * @param array  $excluded_terms Deprecated parameter.
	 * @param array  $taxonomy Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_relative_webcomic_link( $relation = 'random', $in_same_term = null, $excluded_terms = [], $taxonomy = [ 'storyline' ], $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_relative_webcomic_link() is deprecated; use get_webcomic_url() instead.', 'webcomic' ) );

		if ( false !== strpos( $relation, '-nocache' ) ) {
			$key = str_replace( '-nocache', '', $relation );

			return esc_url(
				add_query_arg(
					[
						"{$key}_webcomic"     => rawurlencode( $collection ),
						'in_same_story'       => rawurlencode( maybe_serialize( $in_same_term ) ),
						'excluded_storylines' => rawurlencode( maybe_serialize( $excluded_terms ) ),
						'taxonomy'            => rawurlencode( maybe_serialize( $taxonomy ) ),
					], home_url( '/' )
				)
			);
		}

		$comic = static::get_relative_webcomic_( $relation, $in_same_term, $excluded_terms, $taxonomy, $collection );

		if ( ! $comic ) {
			return '';
		}

		return get_webcomic_url( $comic );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $format Deprecated parameter.
	 * @param string $link Deprecated parameter.
	 * @param string $relation Deprecated parameter.
	 * @param bool   $in_same_term Deprecated parameter.
	 * @param array  $excluded_terms Deprecated parameter.
	 * @param array  $taxonomy Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function relative_webcomic_link( $format = '%link', $link = '%title', $relation = 'random', $in_same_term = null, $excluded_terms = [], $taxonomy = [ 'storyline' ], $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::relative_webcomic_link() is deprecated; use get_webcomic_link() instead.', 'webcomic' ) );

		$url   = '';
		$comic = static::get_relative_webcomic_( $relation, $in_same_term, $excluded_terms, $taxonomy, $collection );

		if ( ! $comic ) {
			return '';
		} elseif ( false !== strpos( $relation, '-nocache' ) ) {
			$url = static::get_relative_webcomic_link_( $relation, $in_same_term, $excluded_terms, $taxonomy, $collection );
		}

		$link = get_webcomic_link( str_replace( '%link', "{{{$link}}}", $format ), $comic );

		if ( $url ) {
			$link = preg_replace( "/href='.+?'/", "href='{$url}'", $link );
		}

		return str_replace( 'self-webcomic', "{$relation}-webcomic", $link );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_purchase_webcomic_link( $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_purchase_webcomic_link() is deprecated; use get_webcomic_url() instead.', 'webcomic' ) );

		$args = [
			'prints' => true,
		];

		return get_webcomic_url( $post, $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $format Deprecated parameter.
	 * @param string $link Deprecated parameter.
	 * @param mixed  $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function purchase_webcomic_link( $format, $link = '', $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::purchase_webcomic_link() is deprecated; use get_webcomic_link() instead.', 'webcomic' ) );

		return get_webcomic_link(
			str_replace( '%link', "{{{$link}}}", $format ), $post, [
				'prints' => '',
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $format Deprecated parameter.
	 * @param string $link Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_collection_link( $format = '%link', $link = '%title', $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_collection_link() is deprecated; use get_webcomic_collection_link() instead.', 'webcomic' ) );

		return get_webcomic_collection_link( str_replace( '%link', "{{{$link}}}", $format ), $collection );
	}

	/**
	 * Deprecated method.
	 *
	 * @param int    $id Deprecated parameter.
	 * @param string $before Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $after Deprecated parameter.
	 * @param string $target Deprecated parameter.
	 * @param string $image Deprecated parameter.
	 * @param mixed  $crossover Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_the_webcomic_collection_list( $id = 0, $before = '', $join = ', ', $after = '', $target = 'archive', $image = '', $crossover = 'true' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_the_webcomic_collection_list() is deprecated; use get_webcomic_collections_list() instead.', 'webcomic' ) );

		$post = get_webcomic( $id );
		$args = [
			'related_to' => $post,
			'format'     => "{$before}{{{$join}}}{$after}",
			'link'       => '%title',
		];

		if ( 'archive' !== $target ) {
			$args['link_args'] = [
				'relation' => $target,
			];
		}

		if ( $image ) {
			$args['link'] = "%{$image}";
		}

		if ( ! $crossover ) {
			$args['related_by'] = $post->post_type;
		} elseif ( 'only' === $crossover ) {
			$args['not_related_by'] = $post->post_type;
		}

		return get_webcomic_collections_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param int    $id Deprecated parameter.
	 * @param mixed  $taxonomy Deprecated parameter.
	 * @param string $before Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $after Deprecated parameter.
	 * @param string $target Deprecated parameter.
	 * @param string $image Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_the_webcomic_term_list( $id = 0, $taxonomy = '', $before = '', $join = ', ', $after = '', $target = 'archive', $image = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_the_webcomic_term_list() is deprecated; use get_webcomic_terms_list() instead.', 'webcomic' ) );

		$comic = get_webcomic( $id );

		if ( ! $comic ) {
			return '';
		}

		$args = [
			'link'         => '%title',
			'format'       => "{$before}{{{$join}}}{$after}",
			'taxonomy'     => $taxonomy,
			'object_ids'   => $comic->ID,
			'hierarchical' => false,
		];

		if ( 0 === strpos( $taxonomy, '!' ) ) {
			$args['type']       = substr( $taxonomy, 1 );
			$args['collection'] = 'own';
		} elseif ( 0 === strpos( $taxonomy, 'x' ) ) {
			$args['type']       = substr( $taxonomy, 1 );
			$args['collection'] = 'crossover';
		} elseif ( in_array( $taxonomy, [ 'character', 'storyline' ], true ) ) {
			$args['type'] = $taxonomy;
		}

		if ( 'archive' !== $target ) {
			$args['post_args']['relation'] = $target;
		}

		if ( $image ) {
			$args['link'] = "%{$image}";
		}

		return get_webcomic_terms_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $relation Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @param array  $args Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_relative_webcomic_term( $relation = 'random', $taxonomy = '', $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_relative_webcomic_term() is deprecated; use get_webcomic_term() instead.', 'webcomic' ) );

		$args['relation'] = $relation;
		$args['taxonomy'] = $taxonomy;

		return get_webcomic_term( null, $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $target Deprecated parameter.
	 * @param string $relation Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @param array  $args Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_relative_webcomic_term_link( $target = 'archive', $relation = 'random', $taxonomy = '', $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_relative_webcomic_term_link() is deprecated; use get_webcomic_term_url() instead.', 'webcomic' ) );

		if ( false !== strpos( $relation, '-nocache' ) ) {
			$key = str_replace( '-nocache', '', $relation );

			return esc_url(
				add_query_arg(
					[
						"{$key}_webcomic_term" => rawurlencode( $taxonomy ),
						'target'               => rawurlencode( $target ),
						'args'                 => rawurlencode( maybe_serialize( $args ) ),
					], home_url( '/' )
				)
			);
		}

		$comic_term = static::get_relative_webcomic_term_( $relation, $taxonomy, $args );

		if ( ! $comic_term ) {
			return '';
		}

		return get_webcomic_term_url( $comic_term );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $format Deprecated parameter.
	 * @param string $link Deprecated parameter.
	 * @param string $target Deprecated parameter.
	 * @param string $relation Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @param array  $args Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function relative_webcomic_term_link( $format, $link = '', $target = 'archive', $relation = 'random', $taxonomy = '', $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::relative_webcomic_term_link() is deprecated; use get_webcomic_term_link() instead.', 'webcomic' ) );

		$url        = '';
		$comic_term = static::get_relative_webcomic_term_( $relation, $taxonomy, $args );

		if ( ! $comic_term ) {
			return '';
		} elseif ( false !== strpos( $relation, '-nocache' ) ) {
			$url = static::get_relative_webcomic_term_link_( $target, $relation, $taxonomy, $args );
		}

		$link = get_webcomic_term_link( str_replace( '%link', "{{{$link}}}", $format ), $comic_term );

		if ( $url ) {
			$link = preg_replace( "/href='.+?'/", "href='{$url}'", $link );
		}

		return str_replace( 'self-webcomic', "{$relation}-webcomic", $link );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $prefix Deprecated parameter.
	 * @param int    $term Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_term_title( $prefix = '', $term = 0, $taxonomy = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_term_title() is deprecated; use get_webcomic_term_title() instead.', 'webcomic' ) );

		$title = get_webcomic_term_title(
			$term, [
				'taxonomy' => $taxonomy,
			]
		);

		if ( ! $title ) {
			return '';
		}

		return $prefix . $title;
	}

	/**
	 * Deprecated method.
	 *
	 * @param int    $term Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_term_description( $term = 0, $taxonomy = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_term_title() is deprecated; use get_webcomic_term_title() instead.', 'webcomic' ) );

		return get_webcomic_term_description(
			$term, [
				'taxonomy' => $taxonomy,
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $size Deprecated parameter.
	 * @param int    $term Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_term_media( $size = 'full', $term = 0, $taxonomy = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_term_media() is deprecated; use get_webcomic_term_media() instead.', 'webcomic' ) );

		return get_webcomic_term_media(
			$size, $term, [
				'taxonomy' => $taxonomy,
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $collection Deprecated parameter.
	 * @param int    $term Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_webcomic_term_crossover_link( $collection = '', $term = 0, $taxonomy = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_webcomic_term_crossover_link() is deprecated; use get_webcomic_term_url() instead.', 'webcomic' ) );

		$comic_term = get_webcomic_term(
			$term, [
				'taxonomy' => $taxonomy,
			]
		);

		if ( ! $comic_term || ( $taxonomy && $taxonomy !== $comic_term->taxonomy ) ) {
			return '';
		} elseif ( ! $collection ) {
			$collection = true;
		}

		return get_webcomic_term_url(
			$comic_term, [
				'crossover' => $collection,
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param int    $term Deprecated parameter.
	 * @param string $taxonomy Deprecated parameter.
	 * @param string $before Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $after Deprecated parameter.
	 * @param string $target Deprecated parameter.
	 * @param string $image Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_term_crossovers( $term = 0, $taxonomy = '', $before = '', $join = ', ', $after = '', $target = 'archive', $image = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_term_crossovers() is deprecated; use get_webcomic_collections_list() instead.', 'webcomic' ) );

		$comic_term = get_webcomic_term( $term );

		if ( ! $comic_term || ( $taxonomy && $taxonomy !== $comic_term->taxonomy ) ) {
			return '';
		}

		$args = [
			'link'           => '%title',
			'format'         => "{$before}{{{$join}}}{$after}",
			'related_to'     => $comic_term,
			'not_related_by' => get_webcomic_collection(),
		];

		if ( $image ) {
			$args['link'] = "%{$image}";
		}

		if ( 'archive' !== $target ) {
			$args['link_args']['relation'] = $target;
		}

		return get_webcomic_collections_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $prefix Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_crossover_title( $prefix = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_crossover_title() is deprecated; use get_webcomic_collection_title( "crossover" ) instead.', 'webcomic' ) );

		$title = get_webcomic_collection_title( 'crossover' );

		if ( ! $title ) {
			return '';
		}

		return $prefix . $title;
	}

	/**
	 * Deprecated method.
	 *
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_crossover_description() {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_crossover_description() is deprecated; use get_webcomic_collection_description( "crossover" ) instead.', 'webcomic' ) );

		return get_webcomic_collection_description( 'crossover' );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $size Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_crossover_media( $size = 'full' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_crossover_media() is deprecated; use get_webcomic_collection_media( $size, "crossover" ) instead.', 'webcomic' ) );

		return get_webcomic_collection_media( $size, 'crossover' );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $prefix Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_collection_title( $prefix = '', $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_collection_title() is deprecated; use get_webcomic_collection_title( "crossover" ) instead.', 'webcomic' ) );

		$title = get_webcomic_collection_title( $collection );

		if ( ! $title ) {
			return '';
		}

		return $prefix . $title;
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_collection_description( $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_collection_description() is deprecated; use get_webcomic_collection_description() instead.', 'webcomic' ) );

		return get_webcomic_collection_description( $collection );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $size Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_collection_media( $size = 'full', $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_collection_media() is deprecated; use get_webcomic_collection_media() instead.', 'webcomic' ) );

		return get_webcomic_collection_media( $size, $collection );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $type Deprecated parameter.
	 * @param string $dec Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_collection_print_amount( $type, $dec = '.', $join = ',', $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_collection_print_amount() is deprecated; use get_webcomic_collection_print_price() instead.', 'webcomic' ) );

		$type = str_replace( [ '-price', '-shipping' ], '', $type );

		return get_webcomic_collection_print_price( $type, $collection );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $before Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $after Deprecated parameter.
	 * @param string $target Deprecated parameter.
	 * @param string $image Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_collection_crossovers( $before = '', $join = ', ', $after = '', $target = 'archive', $image = '', $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_collection_crossovers() is deprecated; use get_webcomic_collections_list() instead.', 'webcomic' ) );

		$args = [
			'format'         => "{$before}{{{$join}}}{$after}",
			'not_related_by' => $collection,
			'related_to'     => $collection,
		];

		if ( ! $args['related_to'] ) {
			$args['related_to']     = get_webcomic_collection();
			$args['not_related_by'] = get_webcomic_collection();
		}

		if ( 'archive' !== $target ) {
			$args['link_args'] = [
				'relation' => $target,
			];
		}

		if ( $image ) {
			$args['link'] = "%{$image}";
		}

		return get_webcomic_collections_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $dec Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_donation_amount( $dec = '.', $join = ',', $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_donation_amount() is deprecated; use get_webcomic_collection_donation() instead.', 'webcomic' ) );

		return get_webcomic_collection_donation( $collection );
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_donation_fields( $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_donation_fields() is deprecated; use get_webcomic_collection_donation_url() instead.', 'webcomic' ) );

		return '';
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $label Deprecated parameter.
	 * @param string $collection Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_donation_form( $label = '', $collection = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_donation_form() is deprecated; use get_webcomic_collection_donation_link() instead.', 'webcomic' ) );

		$url = get_webcomic_collection_donation_url( $collection );

		if ( ! $url ) {
			return '';
		} elseif ( ! $label ) {
			// Translators: Post type name.
			$label = sprintf( esc_html__( 'Support %s', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
		}

		return "<form action='{$url}' class='webcomic-donation-form {$collection}-donation-form'><button type='submit'>{$label}</button></form>";
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $type Deprecated parameter.
	 * @param string $dec Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_print_amount( $type, $dec = '.', $join = ',', $post = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_print_amount() is deprecated; use get_webcomic_print_price() instead.', 'webcomic' ) );

		$type = str_replace( [ '-price', '-shipping' ], '', $type );

		return get_webcomic_print_price( $type, $post );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $type Deprecated parameter.
	 * @param string $post Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_print_adjustment( $type, $post = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_print_adjustment() is deprecated; use get_webcomic_print_adjust() instead.', 'webcomic' ) );

		$type = str_replace( [ '-price', '-shipping' ], '', $type );

		return get_webcomic_print_adjust( $type, $post ) . '%';
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $type Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_print_fields( $type, $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_print_fields() is deprecated; use get_webcomic_url() instead.', 'webcomic' ) );

		return '';
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $type Deprecated parameter.
	 * @param string $label Deprecated parameter.
	 * @param mixed  $post Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_print_form( $type, $label = '', $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_print_fields() is deprecated; use get_webcomic_link() instead.', 'webcomic' ) );

		$url = get_webcomic_url(
			$post, [
				'print' => $type,
			]
		);

		if ( 'cart' === $type ) {
			$url = get_webcomic_collection_cart_url( $post );
		} elseif ( ! $type ) {
			return '';
		}

		if ( ! $url ) {
			return '';
		} elseif ( ! $label ) {
			$label     = esc_html__( 'Buy Now', 'webcomic' );
			$post_type = get_post_type( $post );

			if ( 'cart' === $type ) {
				$label = esc_html__( 'View Cart', 'webcomic' );
			} elseif ( webcomic( "option.{$post_type}.commerce_cart" ) ) {
				$label = esc_html__( 'Add to Cart', 'webcomic' );
			}
		}

		$tokens    = [
			'%total'               => get_webcomic_print_price( $type, $post ),
			'%shipping'            => 0,
			'%price'               => get_webcomic_print_price( $type, $post ),
			'%collection-total'    => get_webcomic_collection_print_price( $type ),
			'%collection-shipping' => 0,
			'%collection-price'    => get_webcomic_collection_print_price( $type ),
		];
		$label     = str_replace( array_keys( $tokens ), $tokens, $label );
		$post_type = get_post_type( $post );

		return "<form action='{$url}' class='webcomic-print-form webcomic-{$type}-print-form {$post_type}-print-form {$post_type}-{$type}-print-form'><button type='submit'>{$label}</button></form>";
	}

	/**
	 * Deprecated method.
	 *
	 * @param string $template Deprecated parameter.
	 * @return void
	 * @deprecated
	 */
	protected static function webcomic_transcripts_template( $template = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_transcripts_template() is deprecated; use webcomic_transcripts_list() instead.', 'webcomic' ) );

		$comic = get_webcomic();

		if ( ! $comic ) {
			return;
		}

		locate_template( [ $template, "webcomic/transcripts-{$comic->post_type}.php", 'webcomic/transcripts.php' ], true, false );
	}

	/**
	 * Deprecated method.
	 *
	 * @param bool  $language Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function get_webcomic_transcripts_link( $language = null, $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_webcomic_transcripts_link() is deprecated; use get_webcomic_url() instead.', 'webcomic' ) );

		return get_webcomic_url(
			$post, [
				'transcribe' => true,
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed  $format Deprecated parameter.
	 * @param string $none Deprecated parameter.
	 * @param string $some Deprecated parameter.
	 * @param string $off Deprecated parameter.
	 * @param bool   $language Deprecated parameter.
	 * @param mixed  $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_transcripts_link( $format, $none = '', $some = '', $off = '', $language = null, $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_transcripts_link() is deprecated; use get_webcomic_link() instead.', 'webcomic' ) );

		$link = $off;

		if ( has_webcomic_transcripts( $post ) ) {
			$link = $some;
		} elseif ( webcomic_transcripts_open( $post ) ) {
			$link = $none;
		}

		return get_webcomic_link(
			str_replace( '%link', "{{{$link}}}", $format ), $post, [
				'transcribe' => true,
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param bool  $pending Deprecated parameter.
	 * @param array $args Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_webcomic_transcripts( $pending = null, $args = [], $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_webcomic_transcripts() is deprecated; use get_webcomic_transcripts() instead.', 'webcomic' ) );

		if ( $pending ) {
			$args['post_status'] = 'pending';
		}

		if ( $post ) {
			if ( is_object( $post ) ) {
				$post = $post->ID;
			}

			$args['post_parent'] = $post;
		}

		return get_webcomic_transcripts( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param int    $id Deprecated parameter.
	 * @param mixed  $post_author Deprecated parameter.
	 * @param string $before Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $after Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_webcomic_transcript_authors( $id = 0, $post_author = 'true', $before = '', $join = ', ', $after = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_webcomic_transcript_authors() is deprecated; use get_webcomic_transcript_authors_list() instead.', 'webcomic' ) );

		$args = [
			'format'   => "{$before}{{{$join}}}{$after}",
			'link_rel' => '',
			'post'     => $id,
		];

		return get_webcomic_transcript_authors_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param int    $id Deprecated parameter.
	 * @param mixed  $taxonomy Deprecated parameter.
	 * @param string $before Deprecated parameter.
	 * @param string $join Deprecated parameter.
	 * @param string $after Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function get_the_webcomic_transcript_term_list( $id = 0, $taxonomy = '', $before = '', $join = ', ', $after = '' ) {
		static::$error && webcomic_error( __( 'WebcomicTag::get_the_webcomic_transcript_term_list() is deprecated; use get_webcomic_transcript_terms_list() instead.', 'webcomic' ) );

		$transcript = get_webcomic_transcript( $id );

		if ( ! $transcript ) {
			return '';
		}

		$args = [
			'format'       => "{$before}{{{$join}}}{$after}",
			'hierarchical' => false,
			'object_ids'   => $transcript->ID,
			'taxonomy'     => str_replace( 'webcomic_language', 'webcomic_transcript_language', $taxonomy ),
		];

		return get_webcomic_transcript_terms_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $transcript Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return mixed
	 * @deprecated
	 */
	protected static function webcomic_transcript_fields( $transcript = null, $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_transcript_fields() is deprecated; use get_webcomic_transcript_form() instead.', 'webcomic' ) );

		$comic = get_webcomic( $post );

		if ( ! $comic ) {
			return '';
		}

		$transcript = get_webcomic_transcript( $transcript );

		if ( ! $transcript ) {
			$transcript = (object) [
				'ID' => 0,
			];
		}

		$output  = "<input type='hidden' name='webcomic_transcript_id' value='" . esc_attr( $transcript->ID ) . "'>";
		$output .= "<input type='hidden' name='webcomic_transcript_parent' value='" . esc_attr( $comic->ID ) . "'>";
		$output .= wp_nonce_field( 'Mgsisk\Webcomic\TranscribeNonce', 'Mgsisk\Webcomic\TranscribeNonce', true, false );

		return $output;
	}

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @param mixed $transcript Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return void
	 * @deprecated
	 * @suppress PhanDeprecatedFunction - Required for compatibility.
	 */
	protected static function webcomic_transcript_form( $args = [], $transcript = null, $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_transcript_form() is deprecated; use webcomic_transcript_form() instead.', 'webcomic' ) );

		$args       = wp_parse_args(
			$args, [
				'fields'                   => [],
				'language_field'           => '',
				'transcript_field'         => '',
				'must_log_in'              => '',
				'logged_in_as'             => '',
				'transcript_notes_before'  => '',
				'transcript_notes_after'   => '',
				'transcript_notes_success' => '',
				'transcript_notes_failure' => '',
				'id_form'                  => '',
				'title_submit'             => '',
				'label_submit'             => '',
				'wysiwyg_editor'           => false,
			]
		);
		$post       = get_webcomic( $post );
		$transcript = get_webcomic_transcript( $transcript );

		if ( $post ) {
			$args['parent'] = $post->ID;
		}

		if ( $transcript ) {
			$args['ID'] = $transcript->ID;
		}

		if ( isset( $args['id_form'] ) ) {
			$args['form_id'] = $args['id_form'];
		}

		if ( isset( $args['title_submit'] ) ) {
			$args['title'] = $args['title_submit'];
		}

		if ( isset( $args['label_submit'] ) ) {
			$args['submit_label'] = $args['label_submit'];
		}

		if ( isset( $args['transcript_field'] ) ) {
			$args['fields']['transcript'] = $args['transcript_field'];
		}

		if ( isset( $args['language_field'] ) ) {
			$args['fields']['languages'] = $args['language_field'];
		}

		unset( $args['id_form'], $args['title_submit'], $args['label_submit'], $args['transcript_field'], $args['language_field'], $args['transcript_notes_error'], $args['transcript_notes_success'], $args['wysiwyg_editor'] );

		webcomic_transcript_fields( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_dropdown_transcript_terms( $args = [], $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_dropdown_transcript_terms() is deprecated; use get_webcomic_transcript_terms_list() instead.', 'webcomic' ) );

		$args           = wp_parse_args(
			$args, [
				'after'            => '',
				'before'           => '',
				'class'            => '',
				'depth'            => 0,
				'hide_empty'       => ! webcomic_transcripts_open( $post ),
				'hide_if_empty'    => true,
				'hierarchical'     => true,
				'id'               => '',
				'object_ids'       => $post,
				'orderby'          => 'name',
				'select_name'      => 'webcomic_terms',
				'selected'         => 0,
				'show_option_all'  => '',
				'show_option_none' => '',
				'taxonomy'         => [],
				'walker'           => '',
			]
		);
		$args['format'] = "{$args['before']}<select id='{$args['id']}' class='{$args['class']}' name='{$args['select_name']}'>";
		$args['link']   = '%title';

		if ( $args['show_option_all'] ) {
			$args['format'] .= "<option value='0'>{$args['show_option_all']}</option>";
		}

		if ( $args['show_option_none'] ) {
			$args['format'] .= "<option value='-1'>{$args['show_option_none']}</option>";
		}

		if ( 'webcomic_language' === $args['taxonomy'] ) {
			$args['taxonomy'] = 'webcomic_transcript_language';
		}

		$args['format'] .= "{{}}</select>{$args['after']}";

		unset( $args['after'], $args['before'], $args['class'], $args['id'], $args['select_name'], $args['selected'], $args['show_option_all'], $args['show_option_none'] );

		return get_webcomic_transcript_terms_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @param mixed $post Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_list_transcript_terms( $args = [], $post = null ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_list_terms() is deprecated; use get_webcomic_terms_list() instead.', 'webcomic' ) );

		$args              = wp_parse_args(
			$args, [
				'after'        => '',
				'before'       => '',
				'class'        => '',
				'depth'        => 0,
				'hide_empty'   => ! webcomic_transcripts_open( $post ),
				'hierarchical' => true,
				'id'           => '',
				'orderby'      => 'name',
				'ordered'      => false,
				'selected'     => 0,
				'taxonomy'     => [],
				'walker'       => '',
			]
		);
		$args['format']    = "<ul id='{$args['id']}' class='{$args['class']}'><li>{{</li><li>}}</li></ul>";
		$args['start']     = "<ul id='{$args['id']}' class='{$args['class']}'>";
		$args['start_lvl'] = '<ul>';
		$args['start_el']  = '<li>';
		$args['end_el']    = '</li>';
		$args['end_lvl']   = '</ul>';
		$args['end']       = '</ul>';

		if ( $args['ordered'] ) {
			$args['format']    = str_replace( 'ul', 'ol', $args['format'] );
			$args['start']     = str_replace( 'ul', 'ol', $args['start'] );
			$args['start_lvl'] = '<ol>';
			$args['end_lvl']   = '</ol>';
			$args['end']       = '</ol>';
		}

		$args['format'] = $args['before'] . $args['format'] . $args['after'];

		if ( 'webcomic_language' === $args['taxonomy'] ) {
			$args['taxonomy'] = 'webcomic_transcript_language';
		}

		unset( $args['after'], $args['before'], $args['class'], $args['id'], $args['selected'] );

		return get_webcomic_transcript_terms_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_dropdown_terms( $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_dropdown_terms() is deprecated; use get_webcomic_terms_list() instead.', 'webcomic' ) );

		$args            = wp_parse_args(
			$args, [
				'after'            => '',
				'before'           => '',
				'class'            => '',
				'depth'            => 0,
				'hierarchical'     => true,
				'id'               => '',
				'orderby'          => '',
				'select_name'      => 'webcomic_terms',
				'selected'         => 0,
				'show_count'       => false,
				'show_option_all'  => '',
				'show_option_none' => '',
				'target'           => 'archive',
				'taxonomy'         => '',
				'walker'           => '',
				'webcomic_order'   => 'asc',
				'webcomic_orderby' => 'date',
				'webcomics'        => [],
			]
		);
		$args['current'] = $args['selected'];
		$args['format']  = "{$args['before']}<select id='{$args['id']}' class='{$args['class']}' name='{$args['select_name']}'>";
		$args['link']    = '%title';

		if ( $args['show_option_all'] ) {
			$args['format'] .= "<option value='0'>{$args['show_option_all']}</option>";
		}

		if ( $args['show_option_none'] ) {
			$args['format'] .= "<option value='-1'>{$args['show_option_none']}</option>";
		}

		$args['format'] .= "{{}}</select>{$args['after']}";

		if ( $args['show_count'] ) {
			$args['link'] = str_replace( '%title', '%title (%count)', $args['link'] );
		}

		if ( 'archive' !== $args['target'] ) {
			$args['link_post_args'] = [
				'relation' => $args['target'],
			];
		}

		if ( $args['webcomics'] ) {
			$args['format']    = str_replace( '{{}}', '{{webcomics_optgroup}}', $args['format'] );
			$args['webcomics'] = [
				'order'   => $args['webcomic_order'],
				'orderby' => $args['webcomic_orderby'],
			];
		}

		unset( $args['after'], $args['before'], $args['class'], $args['id'], $args['select_name'], $args['selected'], $args['show_count'], $args['show_option_all'], $args['show_option_none'], $args['target'], $args['webcomic_order'], $args['webcomic_order_by'] );

		return get_webcomic_terms_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_dropdown_collections( $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_dropdown_collections() is deprecated; use get_webcomic_collections_list() instead.', 'webcomic' ) );

		$old_args = wp_parse_args(
			$args, [
				'after'            => '',
				'before'           => '',
				'class'            => '',
				'collection'       => '',
				'hide_empty'       => true,
				'id'               => '',
				'order'            => 'asc',
				'orderby'          => '',
				'select_name'      => 'webcomic_collections',
				'selected'         => '',
				'show_count'       => false,
				'show_option_all'  => '',
				'show_option_none' => '',
				'target'           => 'archive',
				'webcomic_order'   => 'asc',
				'webcomic_orderby' => 'date',
				'webcomics'        => false,
			]
		);
		$args     = [
			'current' => $old_args['selected'],
			'format'  => "{$old_args['before']}<select id='{$old_args['id']}' class='{$old_args['class']}' name='{$old_args['select_name']}'>",
			'link'    => '%title',
			'order'   => $old_args['order'],
			'orderby' => $old_args['orderby'],
		];

		if ( $old_args['show_option_all'] ) {
			$args['format'] .= "<option value='0'>{$old_args['show_option_all']}</option>";
		}

		if ( $old_args['show_option_none'] ) {
			$args['format'] .= "<option value='-1'>{$old_args['show_option_none']}</option>";
		}

		$args['format'] .= "{{}}</select>{$old_args['after']}";

		if ( 'archive' !== $old_args['target'] ) {
			$args['link_args'] = [
				'relation' => $old_args['target'],
			];
		}

		if ( $old_args['collection'] ) {
			$args['related_to'] = $old_args['collection'];
			$args['related_by'] = [ $old_args['collection'] ];
		}

		if ( $old_args['show_count'] ) {
			$args['link'] = str_replace( '%title', '%title (%count)', $args['link'] );
		}

		if ( $old_args['webcomics'] ) {
			$args['format']    = str_replace( '{{}}', '{{webcomics_optgroup}}', $args['format'] );
			$args['webcomics'] = [
				'order'   => $old_args['webcomic_order'],
				'orderby' => $old_args['webcomic_orderby'],
			];
		}

		return get_webcomic_collections_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @return string
	 * @deprecated
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength) - Required for compatibility.
	 * @SuppressWarnings(PHPMD.NPathComplexity) - Required for compatibility.
	 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - Required for compatibility.
	 */
	protected static function webcomic_list_terms( $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_list_terms() is deprecated; use get_webcomic_terms_list() instead.', 'webcomic' ) );

		$args            = wp_parse_args(
			$args, [
				'after'            => '',
				'before'           => '',
				'class'            => '',
				'depth'            => 0,
				'feed_type'        => 'rss2',
				'feed'             => '',
				'hide_empty'       => true,
				'hierarchical'     => true,
				'id'               => '',
				'orderby'          => '',
				'ordered'          => false,
				'selected'         => 0,
				'show_count'       => false,
				'show_description' => false,
				'show_image'       => '',
				'target'           => 'archive',
				'taxonomy'         => '',
				'walker'           => '',
				'webcomic_image'   => '',
				'webcomic_order'   => 'asc',
				'webcomic_orderby' => 'date',
				'webcomics'        => [],
			]
		);
		$args['current']   = $args['selected'];
		$args['format']    = "<ul id='{$args['id']}' class='{$args['class']}'><li>{{</li><li>}}</li></ul>";
		$args['start']     = "<ul id='{$args['id']}' class='{$args['class']}'>";
		$args['start_lvl'] = '<ul>';
		$args['start_el']  = '<li>';
		$args['end_el']    = '</li>';
		$args['end_lvl']   = '</ul>';
		$args['end']       = '</ul>';
		$args['link']      = '<div class="webcomic-term-name">%title</div>';

		if ( $args['ordered'] ) {
			$args['format']    = str_replace( 'ul', 'ol', $args['format'] );
			$args['start']     = str_replace( 'ul', 'ol', $args['start'] );
			$args['start_lvl'] = '<ol>';
			$args['end_lvl']   = '</ol>';
			$args['end']       = '</ol>';
		}

		$args['format'] = $args['before'] . $args['format'] . $args['after'];

		if ( 'archive' !== $args['target'] ) {
			$args['link_post_args'] = [
				'relation' => $args['target'],
			];
		}

		if ( $args['show_count'] ) {
			$args['link'] = str_replace( '%title', '%title (%count)', $args['link'] );
		}

		if ( $args['show_image'] ) {
			$args['link'] .= "<div class='webcomic-term-image'>%{$args['show_image']}</div>";
		}

		if ( $args['show_description'] ) {
			$args['link'] .= "<div class='webcomic-term-description'>%description</div>";
		}

		if ( filter_var( $args['feed'], FILTER_VALIDATE_URL ) ) {
			$args['feed'] = "<img src='{$args['feed']}' alt=''>";
		}

		if ( $args['webcomics'] ) {
			$args['webcomics'] = [
				'order'     => $args['webcomic_order'],
				'format'    => "<ul class='webcomics'><li>{{</li><li>}}</li></ul>",
				'orderby'   => $args['webcomic_orderby'],
				'post_type' => substr( $args['taxonomy'], 0, strpos( $args['taxonomy'], '_' ) ),
			];

			if ( $args['ordered'] ) {
				$args['webcomics']['format'] = str_replace( 'ul', 'ol', $args['webcomics']['format'] );
			}

			if ( $args['webcomic_image'] ) {
				$args['webcomics']['link'] = "%{$args['webcomic_image']}";
			}
		}

		unset( $args['after'], $args['before'], $args['class'], $args['id'], $args['selected'], $args['show_count'], $args['show_description'], $args['show_image'], $args['target'], $args['webcomic_image'], $args['webcomic_order'], $args['webcomic_order_by'] );

		return get_webcomic_terms_list( $args );
	} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @return string
	 * @deprecated
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength) - Required for compatibility.
	 * @SuppressWarnings(PHPMD.NPathComplexity) - Required for compatibility.
	 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - Required for compatibility.
	 */
	protected static function webcomic_list_collections( $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_list_collections() is deprecated; use get_webcomic_collections_list() instead.', 'webcomic' ) );

		$old_args = wp_parse_args(
			$args, [
				'after'            => '',
				'before'           => '',
				'class'            => '',
				'collection'       => '',
				'feed_type'        => 'rss2',
				'feed'             => '',
				'hide_empty'       => true,
				'id'               => '',
				'order'            => 'asc',
				'orderby'          => '',
				'ordered'          => false,
				'selected'         => '',
				'show_count'       => false,
				'show_description' => false,
				'show_image'       => '',
				'target'           => 'archive',
				'webcomic_image'   => '',
				'webcomic_order'   => 'asc',
				'webcomic_orderby' => 'date',
				'webcomics'        => false,
			]
		);
		$args = [
			'current'    => $old_args['selected'],
			'feed_type'  => $old_args['feed_type'],
			'format'     => "<ul id='{$old_args['id']}' class='{$old_args['class']}'><li>{{</li><li>}}</li></ul>",
			'hide_empty' => $old_args['hide_empty'],
			'link'       => '<div class="webcomic-collection-name">%title</div>',
			'order'      => $old_args['order'],
			'orderby'    => $old_args['orderby'],
		];

		if ( 'archive' !== $old_args['target'] ) {
			$args['link_args'] = [
				'relation' => $old_args['target'],
			];
		}

		if ( $old_args['ordered'] ) {
			$args['format'] = str_replace( 'ul', 'ol', $args['format'] );
		}

		$args['format'] = $old_args['before'] . $args['format'] . $old_args['after'];

		if ( $old_args['show_count'] ) {
			$args['link'] = str_replace( '%title', '%title (%count)', $args['link'] );
		}

		if ( $old_args['show_image'] ) {
			$args['link'] .= "<div class='webcomic-collection-image'>%{$old_args['show_image']}</div>";
		}

		if ( $old_args['show_description'] ) {
			$args['link'] .= "<div class='webcomic-collection-description'>%description</div>";
		}

		if ( filter_var( $old_args['feed'], FILTER_VALIDATE_URL ) ) {
			$args['feed'] = "<img src='{$old_args['feed']}' alt=''>";
		}

		if ( $old_args['collection'] ) {
			$args['related_to'] = $old_args['collection'];
			$args['related_by'] = [ $old_args['collection'] ];
		}

		if ( $old_args['webcomics'] ) {
			$args['webcomics'] = [
				'format'  => "<ul class='webcomics'><li>{{</li><li>}}</li></ul>",
				'order'   => $old_args['webcomic_order'],
				'orderby' => $old_args['webcomic_orderby'],
			];

			if ( $old_args['ordered'] ) {
				$args['webcomics']['format'] = str_replace( 'ul', 'ol', $args['webcomics']['format'] );
			}

			if ( $old_args['webcomic_image'] ) {
				$args['webcomics']['link'] = "%{$old_args['webcomic_image']}";
			}
		}

		return get_webcomic_collections_list( $args );
	} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_term_cloud( $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_term_cloud() is deprecated; use get_webcomic_terms_list() instead.', 'webcomic' ) );

		$args              = wp_parse_args(
			$args, [
				'after'      => '',
				'before'     => '',
				'class'      => '',
				'id'         => '',
				'image'      => '',
				'largest'    => 150,
				'order'      => '',
				'orderby'    => 'rand',
				'selected'   => 0,
				'sep'        => ' ',
				'show_count' => false,
				'smallest'   => 75,
				'target'     => 'archive',
				'taxonomy'   => '',
			]
		);
		$args['format']    = "<ul id='{$args['id']}' class='{$args['class']}'><li>{{</li><li>}}</li></ul>";
		$args['start']     = "<ul id='{$args['id']}' class='{$args['class']}'>";
		$args['start_lvl'] = '<ul>';
		$args['start_el']  = '<li>';
		$args['end_el']    = '</li>';
		$args['end_lvl']   = '</ul>';
		$args['end']       = '</ul>';
		$args['link']      = '%title';
		$args['current']   = $args['selected'];
		$args['cloud_max'] = $args['largest'];
		$args['cloud_min'] = $args['smallest'];

		if ( $args['image'] ) {
			$args['link'] = "%{$args['image']}";
		} elseif ( $args['show_count'] ) {
			$args['link'] = str_replace( '%title', '%title (%count)', $args['link'] );
		}

		if ( $args['sep'] ) {
			$args['format']       = str_replace( [ '<ul id', '<li>{{', '{{</li><li>}}', '}}</li></ul>' ], [ '<div id', '{{', "{{{$args['sep']}}}", '}}</div>' ], $args['format'] );
			$args['hierarchical'] = false;
		}

		$args['format'] = $args['before'] . $args['format'] . $args['after'];

		if ( 'archive' !== $args['target'] ) {
			$args['link_post_args'] = [
				'relation' => $args['target'],
			];
		}

		unset( $args['after'], $args['before'], $args['class'], $args['id'], $args['image'], $args['largest'], $args['order'], $args['selected'], $args['sep'], $args['show_count'], $args['smallest'], $args['target'] );

		return get_webcomic_terms_list( $args );
	}

	/**
	 * Deprecated method.
	 *
	 * @param array $args Deprecated parameter.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_collection_cloud( $args = [] ) {
		static::$error && webcomic_error( __( 'WebcomicTag::webcomic_collection_cloud() is deprecated; use get_webcomic_collections_list() instead.', 'webcomic' ) );

		$old_args = wp_parse_args(
			$args, [
				'after'      => '',
				'before'     => '',
				'class'      => '',
				'id'         => '',
				'image'      => '',
				'largest'    => 150,
				'order'      => '',
				'orderby'    => 'rand',
				'selected'   => '',
				'sep'        => ' ',
				'show_count' => false,
				'smallest'   => 75,
				'target'     => 'archive',
			]
		);
		$args     = [
			'cloud_max' => $old_args['largest'],
			'cloud_min' => $old_args['smallest'],
			'current'   => $old_args['selected'],
			'format'    => "<ul id='{$old_args['id']}' class='{$old_args['class']}'><li>{{</li><li>}}</li></ul>",
			'link'      => '%title',
			'order'     => $old_args['order'],
			'orderby'   => $old_args['orderby'],
		];

		if ( 'archive' !== $old_args['target'] ) {
			$args['link_args'] = [
				'relation' => $old_args['target'],
			];
		}

		if ( $old_args['sep'] ) {
			$args['format'] = str_replace( [ '<ul id', '<li>{{', '{{</li><li>}}', '}}</li></ul>' ], [ '<div id', '{{', "{{{$old_args['sep']}}}", '}}</div>' ], $args['format'] );
		}

		$args['format'] = $old_args['before'] . $args['format'] . $old_args['after'];

		if ( $old_args['image'] ) {
			$args['link'] = "%{$old_args['image']}";
		} elseif ( $old_args['show_count'] ) {
			$args['link'] = str_replace( '%title', '%title (%count)', $args['link'] );
		}

		return get_webcomic_collections_list( $args );
	}

	/* ===== Shortcode Methods ================================================ */

	/**
	 * Deprecated shortcode method.
	 *
	 * @param array  $atts Optional attributes.
	 * @param string $content Unused shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @deprecated
	 * @suppress PhanDeprecatedFunction - Required for compatibility.
	 */
	protected static function webcomic_collection_title_shortcode( $atts, string $content, string $name ) : string {
		$args = shortcode_atts(
			[
				'prefix'     => '',
				'collection' => '',
			], $atts, $name
		);

		return static::webcomic_collection_title( $args['prefix'], $args['collection'] );
	}

	/**
	 * Deprecated shortcode method.
	 *
	 * @param array  $atts Optional attributes.
	 * @param string $content Unused shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_collection_link_shortcode( $atts, string $content, string $name ) : string {
		$args = shortcode_atts(
			[
				'format'     => '%link',
				'link'       => '%title',
				'collection' => '',
			], $atts, $name
		);

		return static::webcomic_collection_link_( $args['format'], $args['link'], $args['collection'] );
	}

	/**
	 * Deprecated shortcode method.
	 *
	 * @param array  $atts Optional attributes.
	 * @param string $content Unused shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @deprecated
	 */
	protected static function webcomic_link_shortcode( $atts, string $content, string $name ) : string {
		$args = shortcode_atts(
			[
				'format'         => '%link',
				'link'           => [
					'archive'  => '%title',
					'first'    => '&laquo;',
					'previous' => '&lsaquo;',
					'next'     => '&rsaquo;',
					'last'     => '&raquo;',
					'random'   => '&infin;',
				],
				'in_same_term'   => false,
				'excluded_terms' => '',
				'taxonomy'       => 'storyline',
				'collection'     => '',
				'the_post'       => false,
				'cache'          => true,
			], $atts, $name
		);

		$args['relation'] = substr( $name, 0, strpos( $name, '_' ) );

		if ( is_array( $args['link'] ) ) {
			$args['link'] = $args['link'][ $args['relation'] ];
		}

		if ( false === $args['cache'] ) {
			$args['relation'] .= '-nocache';
		}

		return static::relative_webcomic_link_( $args['format'], $args['link'], $args['relation'], $args['in_same_term'], $args['excluded_terms'], $args['taxonomy'], $args['collection'] );
	}

	/**
	 * Deprecated shortcode method.
	 *
	 * @param array  $atts Optional attributes.
	 * @param string $content Unused shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @deprecated
	 * @suppress PhanDeprecatedFunction - Required for compatibility.
	 */
	protected static function webcomic_term_description_shortcode( $atts, string $content, string $name ) : string {
		$args = shortcode_atts(
			[
				'term'       => null,
				'collection' => get_webcomic_collection(),
			], $atts
		);

		$taxonomy = '';

		preg_match( '/storyline|character/', $name, $match );

		if ( $match ) {
			$taxonomy = "{$args['collection']}_{$match[0]}";
		}

		return static::webcomic_term_description( $args['term'], $taxonomy );
	}

	/**
	 * Deprecated shortcode method.
	 *
	 * @param array  $atts Optional attributes.
	 * @param string $content Unused shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @deprecated
	 * @suppress PhanDeprecatedFunction - Required for compatibility.
	 */
	protected static function webcomic_term_title_shortcode( $atts, string $content, string $name ) : string {
		$args = shortcode_atts(
			[
				'prefix'     => '',
				'term'       => null,
				'collection' => get_webcomic_collection(),
			], $atts
		);

		$taxonomy = '';

		preg_match( '/storyline|character/', $name, $match );

		if ( $match ) {
			$taxonomy = "{$args['collection']}_{$match[0]}";
		}

		if ( $content ) {
			$args['prefix'] = do_shortcode( $content );
		}

		return $args['prefix'] . static::webcomic_term_description( $args['term'], $taxonomy );
	}

	/**
	 * Deprecated shortcode method.
	 *
	 * @param array  $atts Optional attributes.
	 * @param string $content Unused shortcode content.
	 * @param string $name Shortcode name.
	 * @return string
	 * @deprecated
	 * @suppress PhanDeprecatedFunction - Required for compatibility.
	 */
	protected static function webcomic_term_link_shortcode( $atts, string $content, string $name ) : string {
		$args = shortcode_atts(
			[
				'format'     => '%link',
				'link'       => '%title',
				'target'     => 'archive',
				'args'       => [],
				'collection' => get_webcomic_collection(),
				'cache'      => true,
			], $atts
		);

		if ( is_string( $args['args'] ) ) {
			parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
		}

		$args['relation'] = substr( $name, 0, strpos( $name, '_' ) );

		if ( false === $args['cache'] ) {
			$args['relation'] .= '-nocache';
		}

		$taxonomy = '';

		preg_match( '/storyline|character/', $name, $match );

		if ( $match ) {
			$taxonomy = "{$args['collection']}_{$match[0]}";
		}

		return static::relative_webcomic_term_link( $args['format'], $args['link'], $args['target'], $args['relation'], $taxonomy, $args['args'] );
	}
}
