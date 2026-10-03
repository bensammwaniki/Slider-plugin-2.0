<?php
/**
 * Google Reviews Bridge
 *
 * Fetches Google Places reviews via the Places API (Place Details endpoint),
 * normalises them into the exact array shape consumed by ReviewCarousel_Widget,
 * and caches results in WP transients for 6 hours.
 *
 * @package DailySlider
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DailySlider_Google_Reviews_Bridge {

    /**
     * Google Places API endpoint.
     */
    const API_ENDPOINT = 'https://maps.googleapis.com/maps/api/place/details/json';

    /**
     * Transient TTL: 6 hours.
     */
    const CACHE_TTL = 6 * HOUR_IN_SECONDS;

    /**
     * Long-term Backup Transient TTL: 30 days.
     * Used as a resilient fallback if the live API request fails or is unavailable.
     */
    const BACKUP_CACHE_TTL = 30 * DAY_IN_SECONDS;

    /**
     * Diagnostic properties for detailed error reporting.
     */
    public static $last_error  = '';
    public static $last_status = '';
    public static $raw_count   = 0;

    /**
     * Fetch, filter, and return normalised Google reviews.
     *
     * Returns an array of items matching the ReviewCarousel repeater shape:
     *   [
     *     'name'          => string,   // reviewer's display name
     *     'dagination'    => string,   // relative time (e.g. "2 weeks ago")
     *     'text'          => string,   // review body
     *     'rating'        => [ 'size' => int ],  // 1–5
     *     'avatar_image'  => [ 'id' => '', 'url' => string ],
     *   ]
     *
     * Returns an empty array on any error or when no reviews match the filter.
     *
     * @param string $place_id   Google Place ID.
     * @param string $api_key    Google Places API key.
     * @param int    $limit      Maximum number of reviews to return (1–5).
     * @param int    $min_rating Minimum star rating to include (1–5).
     *
     * @return array
     */
    public static function get_reviews( $place_id, $api_key, $limit = 5, $min_rating = 4 ) {
        self::$last_error  = '';
        self::$last_status = '';
        self::$raw_count   = 0;

        // Validate required params.
        if ( empty( $place_id ) || empty( $api_key ) ) {
            self::$last_error = 'API Key or Place ID is missing.';
            return [];
        }

        $resolved = self::resolve_identifier( $place_id );
        if ( ! $resolved || empty( $resolved['value'] ) ) {
            self::$last_error = 'Could not resolve Place ID or CID from the input string.';
            return [];
        }

        $limit      = max( 1, min( 5, (int) $limit ) );
        $min_rating = max( 1, min( 5, (int) $min_rating ) );

        // Build unique cache keys for primary (6h) and backup (30d) transients.
        $hash       = md5( $resolved['value'] . '_' . $limit . '_' . $min_rating );
        $cache_key  = 'ds_gr_' . $hash;
        $backup_key = 'ds_gr_bk_' . $hash;

        // In Elementor edit mode, bypass primary cache to ensure live preview updates when editing settings.
        $is_edit_mode = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor && \Elementor\Plugin::$instance->editor->is_edit_mode();
        if ( ! $is_edit_mode ) {
            $cached = get_transient( $cache_key );
            if ( false !== $cached && is_array( $cached ) && ! empty( $cached ) ) {
                return $cached;
            }
        }

        // Fetch live from Google Places API.
        $raw = self::fetch_from_api( $resolved, $api_key );

        if ( empty( $raw ) ) {
            // Live fetch failed or returned empty. Check if we have a last-known-good backup cache!
            $backup = get_transient( $backup_key );
            if ( false !== $backup && is_array( $backup ) && ! empty( $backup ) ) {
                $orig_error = self::$last_error;
                self::$last_error = ( $orig_error ? $orig_error . ' ' : '' ) . '[Resilience Fallback] Serving last known cached reviews.';
                return $backup;
            }
            return [];
        }

        // Filter by minimum rating.
        $filtered = array_filter( $raw, static function ( $review ) use ( $min_rating ) {
            return isset( $review['rating'] ) && (int) $review['rating'] >= $min_rating;
        } );

        if ( empty( $filtered ) ) {
            // No reviews passed the min_rating filter. Check backup cache before failing!
            $backup = get_transient( $backup_key );
            if ( false !== $backup && is_array( $backup ) && ! empty( $backup ) ) {
                self::$last_error = 'Google returned ' . count( $raw ) . ' review(s), but 0 matched your Minimum Star Rating (' . $min_rating . '★). Serving last known cached reviews.';
                return $backup;
            }
            self::$last_error = 'Google returned ' . count( $raw ) . ' review(s), but 0 reviews matched your Minimum Star Rating (' . $min_rating . '★). Try setting Minimum Star Rating to 1★.';
            return [];
        }

        // Sort: highest rating first, then most recent.
        usort( $filtered, static function ( $a, $b ) {
            if ( $a['rating'] !== $b['rating'] ) {
                return $b['rating'] <=> $a['rating'];
            }
            return ( $b['time'] ?? 0 ) <=> ( $a['time'] ?? 0 );
        } );

        // Trim to requested limit.
        $filtered = array_slice( array_values( $filtered ), 0, $limit );

        // Normalise to ReviewCarousel_Widget repeater shape.
        $normalised = array_map( [ __CLASS__, 'normalise_review' ], $filtered );

        // Cache the result in both Primary (6h) and Long-term Backup (30d) transients!
        if ( ! empty( $normalised ) ) {
            set_transient( $cache_key, $normalised, self::CACHE_TTL );
            set_transient( $backup_key, $normalised, self::BACKUP_CACHE_TTL );
        }

        return $normalised;
    }

    /**
     * Make the HTTP request to Google Places API and return raw review objects.
     *
     * @param array  $resolved Normalized identifier array.
     * @param string $api_key  Google Places API key.
     *
     * @return array Raw review objects from the API, or [] on failure.
     */
    private static function fetch_from_api( $resolved, $api_key ) {
        $lang = substr( get_locale(), 0, 2 );
        if ( empty( $lang ) ) {
            $lang = 'en';
        }

        $query_params = [
            'fields'   => 'name,rating,reviews,user_ratings_total',
            'language' => $lang,
            'key'      => $api_key,
        ];

        if ( isset( $resolved['type'] ) && 'cid' === $resolved['type'] ) {
            $query_params['cid'] = $resolved['value'];
        } else {
            $query_params['place_id'] = $resolved['value'];
        }

        $url = add_query_arg( $query_params, self::API_ENDPOINT );

        $response = wp_remote_get(
            $url,
            [
                'timeout'   => 12,
                'sslverify' => true,
                'headers'   => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        if ( is_wp_error( $response ) ) {
            self::$last_status = 'HTTP_ERROR';
            self::$last_error  = 'Network/HTTP Error: ' . $response->get_error_message();
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions
                error_log( '[DailySlider Google Reviews] API request failed: ' . $response->get_error_message() );
            }
            return [];
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        $status = $body['status'] ?? 'UNKNOWN';
        $error_msg = $body['error_message'] ?? '';
        self::$last_status = $status;

        if ( 'OK' !== $status ) {
            switch ( $status ) {
                case 'REQUEST_DENIED':
                    self::$last_error = 'Google Places API Error (REQUEST_DENIED): ' . ( $error_msg ? $error_msg : 'The provided API key is invalid or Places API is not enabled in Google Cloud Console.' );
                    break;
                case 'INVALID_REQUEST':
                    self::$last_error = 'Google Places API Error (INVALID_REQUEST): ' . ( $error_msg ? $error_msg : 'Missing or invalid Place ID or CID parameter.' );
                    break;
                case 'NOT_FOUND':
                    self::$last_error = 'Google Places API Error (NOT_FOUND): ' . ( $error_msg ? $error_msg : 'The specified Place ID or CID was not found by Google.' );
                    break;
                case 'OVER_QUERY_LIMIT':
                    self::$last_error = 'Google Places API Error (OVER_QUERY_LIMIT): Quota exceeded or billing not enabled on your Google Cloud account.';
                    break;
                case 'ZERO_RESULTS':
                    self::$last_error = 'Google Places API returned ZERO_RESULTS for this Place ID or CID.';
                    break;
                default:
                    self::$last_error = 'Google Places API Error (' . esc_html( $status ) . '): ' . esc_html( $error_msg );
                    break;
            }

            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions
                error_log( '[DailySlider Google Reviews] API error status: ' . $status . ' - ' . $error_msg );
            }
            return [];
        }

        $reviews = $body['result']['reviews'] ?? [];
        if ( empty( $reviews ) || ! is_array( $reviews ) ) {
            self::$raw_count  = 0;
            self::$last_error = 'Google Places API returned 0 reviews for this place. Note: Google Place Details API provides up to 5 of the most helpful reviews.';
            return [];
        }

        self::$raw_count = count( $reviews );
        return $reviews;
    }

    /**
     * Normalise a single raw Google review object into the ReviewCarousel repeater shape.
     *
     * @param array $review Raw review from the Places API.
     *
     * @return array Normalised review item.
     */
    private static function normalise_review( $review ) {
        return [
            // Reviewer's display name.
            'name'         => sanitize_text_field( $review['author_name'] ?? '' ),

            // Relative time description (e.g. "3 weeks ago") used as the "dagination" subtitle.
            'dagination'   => sanitize_text_field( $review['relative_time_description'] ?? '' ),

            // Review body text.
            'text'         => sanitize_textarea_field( $review['text'] ?? '' ),

            // Rating in the same nested shape the repeater slider control uses.
            'rating'       => [
                'size' => (int) ( $review['rating'] ?? 5 ),
            ],

            // Avatar: no WP attachment ID (external URL), fallback to placeholder.
            'avatar_image' => [
                'id'  => '',
                'url' => esc_url_raw( $review['profile_photo_url'] ?? '' ),
            ],
        ];
    }

    /**
     * Bust all cached reviews for a given Place ID.
     * Useful if reviews need to be refreshed before the 6-hour TTL expires.
     * Iterates over all limit/min_rating combinations we might have cached.
     *
     * @param string $place_id Google Place ID.
     *
     * @return void
     */
    public static function clear_cache( $place_id ) {
        $resolved = self::resolve_identifier( $place_id );
        if ( ! $resolved ) {
            return;
        }

        $limits   = range( 1, 5 );
        $ratings  = range( 1, 5 );

        foreach ( $limits as $limit ) {
            foreach ( $ratings as $min_rating ) {
                $hash = md5( $resolved['value'] . '_' . $limit . '_' . $min_rating );
                delete_transient( 'ds_gr_' . $hash );
                delete_transient( 'ds_gr_bk_' . $hash );
            }
        }
    }

    /**
     * Follow redirects for short URLs to get the final long Google Maps URL.
     *
     * @param string $url Short URL.
     *
     * @return string Long URL or original URL if not a redirect.
     */
    public static function follow_redirects( $url ) {
        if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
            return $url;
        }

        // Only resolve maps.app.goo.gl or goo.gl/maps short URLs.
        if ( stripos( $url, 'maps.app.goo.gl' ) === false && stripos( $url, 'goo.gl/maps' ) === false ) {
            return $url;
        }

        $response = wp_safe_remote_head(
            $url,
            [
                'timeout'     => 5,
                'redirection' => 5,
            ]
        );

        if ( is_wp_error( $response ) ) {
            return $url;
        }

        // Check if there was a redirect location.
        $location = wp_remote_retrieve_header( $response, 'location' );
        if ( ! empty( $location ) ) {
            return $location;
        }

        // Sometimes the redirect is stored in the response object.
        if ( isset( $response['http_response'] ) ) {
            $http_response = $response['http_response'];
            if ( method_exists( $http_response, 'get_response_object' ) ) {
                $response_obj = $http_response->get_response_object();
                if ( isset( $response_obj->url ) ) {
                    return $response_obj->url;
                }
            }
        }

        return $url;
    }

    /**
     * Add two decimal strings of arbitrary length.
     *
     * @param string $num1 First number string.
     * @param string $num2 Second number string.
     *
     * @return string Result string.
     */
    private static function add_strings( $num1, $num2 ) {
        $res   = '';
        $carry = 0;
        $i     = strlen( $num1 ) - 1;
        $j     = strlen( $num2 ) - 1;
        while ( $i >= 0 || $j >= 0 || $carry > 0 ) {
            $sum = $carry;
            if ( $i >= 0 ) {
                $sum += (int) $num1[ $i-- ];
            }
            if ( $j >= 0 ) {
                $sum += (int) $num2[ $j-- ];
            }
            $carry = (int) ( $sum / 10 );
            $res   = ( $sum % 10 ) . $res;
        }
        return $res;
    }

    /**
     * Multiply a decimal string by a small integer.
     *
     * @param string $num        Number string.
     * @param int    $multiplier Multiplier.
     *
     * @return string Result string.
     */
    private static function multiply_string_by_int( $num, $multiplier ) {
        $res   = '';
        $carry = 0;
        $i     = strlen( $num ) - 1;
        while ( $i >= 0 || $carry > 0 ) {
            $prod = $carry;
            if ( $i >= 0 ) {
                $prod += ( (int) $num[ $i-- ] ) * $multiplier;
            }
            $carry = (int) ( $prod / 10 );
            $res   = ( $prod % 10 ) . $res;
        }
        return $res;
    }

    /**
     * Convert hexadecimal to decimal with arbitrary precision.
     *
     * @param string $hex Hexadecimal string.
     *
     * @return string Decimal string.
     */
    public static function hex_to_dec( $hex ) {
        $hex = ltrim( strtolower( trim( $hex ) ), '0x' );
        if ( empty( $hex ) ) {
            return '0';
        }

        if ( function_exists( 'gmp_init' ) ) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions
            return gmp_strval( gmp_init( $hex, 16 ), 10 );
        }

        if ( function_exists( 'bcadd' ) ) {
            $dec = '0';
            $len = strlen( $hex );
            for ( $i = 0; $i < $len; $i++ ) {
                $dec = bcmul( $dec, '16' );
                $dec = bcadd( $dec, (string) hexdec( $hex[ $i ] ) );
            }
            return $dec;
        }

        // Pure PHP fallback.
        $dec = '0';
        $len = strlen( $hex );
        for ( $i = 0; $i < $len; $i++ ) {
            $dec = self::multiply_string_by_int( $dec, 16 );
            $dec = self::add_strings( $dec, (string) hexdec( $hex[ $i ] ) );
        }
        return $dec;
    }

    /**
     * Resolve the input string (which can be a Place ID, CID, hex CID, or Google Maps URL)
     * into a normalized array: [ 'type' => 'place_id'|'cid', 'value' => string ]
     *
     * @param string $input User input from settings.
     *
     * @return array|false Normalized array, or false on empty input.
     */
    public static function resolve_identifier( $input ) {
        $input = trim( $input );
        if ( empty( $input ) ) {
            return false;
        }

        // 1. If it's a URL, follow redirects if it's a short URL, then parse query parameters.
        if ( stripos( $input, 'http' ) === 0 ) {
            $input = self::follow_redirects( $input );

            $query = parse_url( $input, PHP_URL_QUERY );
            if ( $query ) {
                parse_str( $query, $params );
                if ( isset( $params['cid'] ) ) {
                    $input = trim( $params['cid'] );
                } elseif ( isset( $params['ftid'] ) ) {
                    $input = trim( $params['ftid'] );
                } elseif ( isset( $params['query_place_id'] ) ) {
                    $input = trim( $params['query_place_id'] );
                } elseif ( isset( $params['place_id'] ) ) {
                    $input = trim( $params['place_id'] );
                }
            }

            // 2. If it is still a URL, extract hex pair pattern from URL path (e.g. 0x89c259a9b3117469:0xd134e199a405a163).
            if ( stripos( $input, 'http' ) === 0 ) {
                if ( preg_match( '/0x[a-fA-F0-9]+:0x([a-fA-F0-9]+)/', $input, $matches ) ) {
                    $input = $matches[1];
                }
            }
        }

        // 3. Handle colon-separated hex format (e.g. 0x89c2598d6499313b:0xa13d4bdf168fefc).
        if ( strpos( $input, ':' ) !== false ) {
            $parts = explode( ':', $input );
            $input = trim( end( $parts ) );
        }

        // 4. Handle hex representation.
        $is_hex = false;
        if ( stripos( $input, '0x' ) === 0 ) {
            $input  = substr( $input, 2 );
            $is_hex = true;
        }

        if ( $is_hex || ( 16 === strlen( $input ) && ctype_xdigit( $input ) ) ) {
            if ( ctype_xdigit( $input ) ) {
                return [
                    'type'  => 'cid',
                    'value' => self::hex_to_dec( $input ),
                ];
            }
        }

        // 5. Handle pure decimal CID.
        if ( ctype_digit( $input ) ) {
            return [
                'type'  => 'cid',
                'value' => $input,
            ];
        }

        // 6. Handle standard Place ID.
        if ( preg_match( '/^[A-Za-z0-9_-]{27,}$/', $input ) ) {
            return [
                'type'  => 'place_id',
                'value' => $input,
            ];
        }

        return [
            'type'  => 'place_id',
            'value' => $input,
        ];
    }
}
