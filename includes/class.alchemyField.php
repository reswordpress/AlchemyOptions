<?php
//no direct access allowed
if( ! defined( 'ALCHEMY_OPTIONS_VERSION' ) ) {
    exit;
}

if( ! class_exists( 'Alchemy_Field' ) ) {

    class Alchemy_Field implements iAlchemy_Field {
        protected $template = '';

        public function __construct() {
            $this->template = '';
        }

        public function normalize_field_keys( $field ) {
            $field[ 'description' ] = isset( $field[ 'desc' ] ) ? $field[ 'desc' ] : '';
            unset( $field[ 'desc' ] );

            $savedData = get_option( $field[ 'id' ] );
            $field[ 'value' ] = $savedData[ 'value' ];

            return $field;
        }

        public function get_html( $data ) {
            $fieldHTML = $this->template;

            foreach ( $data as $key => $val ) {
                if( 'string' === gettype( $val ) ) {
                    $fieldHTML = str_replace( "{{" . strtoupper( $key ) . "}}", $val, $fieldHTML );
                }
            }

            return $fieldHTML;
        }

        public function is_disabled( $value ) {
            return disabled( $value, true, false );
        }

        public function make_label( $text ) {
            return strtolower( str_replace( " ", "_", trim( $text ) ) );
        }

        public function concat_attributes( $attrs ) {
            $attrString = "";

            if( is_array( $attrs ) && count( $attrs ) > 0 ) {
                foreach ( $attrs as $attrName => $attrValue ) {
                    $attrString .= sprintf( ' %1$s="%2$s"', $attrName, esc_attr( $attrValue ) );
                }
            }

            return $attrString;
        }

        public function array_has_string_keys( $array ) {
            return count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
        }
    }
}