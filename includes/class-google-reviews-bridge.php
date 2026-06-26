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
        // Validate required params.
        if ( empty( $place_id ) || empty( $api_key ) ) {
            return [];
        }

        $limit      = max( 1, min( 5, (int) $limit ) );
        $min_rating = max( 1, min( 5, (int) $min_rating ) );

        // Build a transient key unique to this combination.
        $cache_key = 'ds_gr_' . md5( $place_id . '_' . $limit . '_' . $min_rating );
        $cached    = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

        // Fetch from Google Places API.
        $raw = self::fetch_from_api( $place_id, $api_key );

        if ( empty( $raw ) ) {
            return [];
        }

        // Filter by minimum rating.
        $filtered = array_filter( $raw, static function ( $review ) use ( $min_rating ) {
            return isset( $review['rating'] ) && (int) $review['rating'] >= $min_rating;
        } );

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

        // Cache the result.
        set_transient( $cache_key, $normalised, self::CACHE_TTL );

        return $normalised;
    }

    /**
     * Make the HTTP request to Google Places API and return raw review objects.
     *
     * @param string $place_id Google Place ID.
     * @param string $api_key  Google Places API key.
     *
     * @return array Raw review objects from the API, or [] on failure.
     */
    private static function fetch_from_api( $place_id, $api_key ) {
        $url = add_query_arg(
            [
                'place_id' => rawurlencode( $place_id ),
                'fields'   => 'reviews',
                'language' => get_locale(),
                'key'      => $api_key,
            ],
            self::API_ENDPOINT
        );

        $response = wp_remote_get(
            $url,
            [
                'timeout'   => 10,
                'sslverify' => true,
                'headers'   => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        if ( is_wp_error( $response ) ) {
            // Log in debug mode so the developer can see what went wrong.
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions
                error_log( '[DailySlider Google Reviews] API request failed: ' . $response->get_error_message() );
            }
            return [];
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $body['result']['reviews'] ) || ! is_array( $body['result']['reviews'] ) ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG && isset( $body['status'] ) && 'OK' !== $body['status'] ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions
                error_log( '[DailySlider Google Reviews] API status: ' . esc_html( $body['status'] ) );
            }
            return [];
        }

        return $body['result']['reviews'];
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
        $limits   = range( 1, 5 );
        $ratings  = range( 1, 5 );

        foreach ( $limits as $limit ) {
            foreach ( $ratings as $min_rating ) {
                $cache_key = 'ds_gr_' . md5( $place_id . '_' . $limit . '_' . $min_rating );
                delete_transient( $cache_key );
            }
        }
    }
}
