<?php
/**
 * Company Class
 *
 * This is a starting point for handling company data.
 * Feel free to modify, extend, or completely replace this approach.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class WCD_Company {

    /**
     * Company data structure
     *
     * @var array
     */
    private $data = array(
        'name'           => '',
        'rating'         => 0,
        'benefits'       => array(),
        'cons'           => array(),
        'has_free_trial' => false,
        'summary'        => '',
    );

    /**
     * Constructor
     *
     * @param array $data Optional. Company data to initialize with.
     */
    public function __construct( $data = array() ) {
        if ( ! empty( $data ) ) {
            $this->set_data( $data );
        }
    }

    /**
     * Set company data
     *
     * @param array $data Company data.
     */
    public function set_data( $data ) {
        foreach ( $data as $key => $value ) {
            if ( array_key_exists( $key, $this->data ) ) {
                $this->data[ $key ] = $value;
            }
        }
    }

    /**
     * Get company data
     *
     * @return array
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * Get a specific property
     *
     * @param string $key Property key.
     * @return mixed
     */
    public function get( $key ) {
        return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
    }

    /**
     * Set a specific property
     *
     * @param string $key Property key.
     * @param mixed $value Property value.
     */
    public function set( $key, $value ) {
        if ( array_key_exists( $key, $this->data ) ) {
            $this->data[ $key ] = $value;
        }
    }

    // TODO: Add methods for:
    // - Saving to WordPress (post, custom table, or option)
    // - Loading from WordPress
    // - Validation
    // - Any other business logic you need
}
