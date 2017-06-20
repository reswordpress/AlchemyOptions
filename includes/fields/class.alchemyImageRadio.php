<?php
//no direct access allowed
if( ! defined( 'ALCHEMY_OPTIONS_VERSION' ) ) {
    exit;
}

if( ! class_exists( 'Alchemy_Image_Radio_Field' ) ) {

    class Alchemy_Image_Radio_Field extends Alchemy_Field {
        public function __construct( $networkField = false ) {
            parent::__construct( $networkField );

            $this->template = '
                <div class="alchemy__field field field--image-radio jsAlchemyImageRadios" id="field--{{NAME}}" data-alchemy=\'{"id":"{{NAME}}","type":"image-radio"}\'>
                    <fieldset>
                        <legend class="field__label">{{TITLE}}</legend>
                        {{CHOICES}}
                    </fieldset>
                    {{DESCRIPTION}}
                </div>
            ';
        }

        public function alch_get_image_radio_choices( $id, $choices ) {
            $choicesHTML = "";

            if( is_array( $choices ) && count( $choices ) > 0 ) {
                foreach ( $choices as $choice ) {
                    $fieldID = esc_attr( $id . '_' . $this->make_label( $choice[ 'value' ] ) );

                    $choicesHTML .= sprintf (
                        '<input%2$s data-value=\'%3$s\' %4$s %5$s/><label%1$s class="field__image-label %7$s %8$s jsAlchemyImageRadioLabel">%6$s</label>',
                        $this->concat_attributes( array( 'for' => $fieldID ) ),
                        $this->concat_attributes( array( 'id' => $fieldID, 'name' => $id, 'type' => 'radio' ) ),
                        esc_attr( $choice[ 'value' ] ),
                        $this->is_disabled( $choice[ 'disabled' ] ),
                        $this->is_checked( $choice[ 'checked' ] ),
                        sprintf( '<img src="%1$s" />', $choice[ 'image' ] ),
                        $this->is_label_disabled( $choice[ 'disabled' ] ),
                        $this->is_label_active( $choice[ 'checked' ] )
                    );
                }
            }

            return $choicesHTML;
        }

        public function normalize_field_keys( $field ) {
            $field = parent::normalize_field_keys( $field );

            $field[ 'name' ] = $field[ 'id' ];

            foreach( $field[ 'choices' ] as $i => $choice ) {
                $field[ 'choices' ][ $i ][ 'disabled' ] = isset( $choice[ 'disabled' ] ) ? $choice[ 'disabled' ] : false;
                $field[ 'choices' ][ $i ][ 'checked' ] = isset( $choice[ 'checked' ] ) ? $choice[ 'checked' ] : false;
            }

            if( isset( $field[ 'value' ] ) && is_array( $field[ 'value' ] ) ) {
                foreach ( $field[ 'choices' ] as $i => $choice ) {
                    $field[ 'choices' ][ $i ][ 'checked' ] = in_array( $choice[ 'value' ], $field[ 'value' ] );
                }
            }

            $field[ 'choices' ] = $this->alch_get_image_radio_choices( $field[ 'id' ], $field[ 'choices' ] );

            return $field;
        }

        public function is_label_active( $value ) {
            return $value ? 'field__image-label--active' : '';
        }

        public function is_label_disabled( $value ) {
            return $value ? 'field__image-label--disabled' : '';
        }

        public function is_checked( $value ) {
            return checked( $value, true, false );
        }
    }
}