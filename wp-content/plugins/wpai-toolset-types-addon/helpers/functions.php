<?php

if (!function_exists('pmti_get_join_attr')):

    /**
     * @param bool $attributes
     * @return string
     */
    function pmti_get_join_attr($attributes = false ) {
        // validate
        if ( empty($attributes) ) {
            return '';
        }
        // vars
        $e = [];
        // loop through and render
        foreach ( $attributes as $k => $v ) {
            $e[] = $k . '="' . esc_attr( $v ) . '"';
        }
        // echo
        return implode(' ', $e);
    }

endif;

if (!function_exists('pmti_join_attr')):

    /**
     * @param bool $attributes
     */
    function pmti_join_attr($attributes = false ){
        echo pmti_get_join_attr( $attributes );
    }

endif;