<?php

/**
 * @package Alchemy_Options\Includes\Fields
 *
 */

namespace Alchemy_Options\Includes\Fields;

use Alchemy_Options\Includes;

//no direct access allowed
if( ! defined( 'ALCHEMY_OPTIONS_VERSION' ) ) {
    exit;
}

if( ! class_exists( __NAMESPACE__ . '\Upload' ) ) {

    class Upload extends Includes\Field {
        public function __construct( $networkField = false, $options = array() ) {
            parent::__construct( $networkField, $options );

            $this->template = '
                <div class="alchemy__field alchemy__clearfix field field--{{TYPE}} jsAlchemyUploader" id="field--{{ID}}" data-alchemy=\'{"id":"{{ID}}","type":"{{TYPE}}"}\'>
                    <div class="field__side">
                        <label class="field__label" for="{{ID}}">{{TITLE}}</label>
                        {{DESCRIPTION}}
                    </div>
                    <div class="field__content">
                        <input {{ATTRIBUTES}} />
                        <button type="button" class="button button-primary jsAlchemyUploadTrigger" data-strings=\'{"title":"{{ADD-BUTTON-TITLE}}","text":"{{ADD-BUTTON-TEXT}}"}\'><span class="dashicons dashicons-admin-media"></span></button>
                        <button type="button" class="button button-secondary jsAlchemyUploadRemove"><span class="dashicons dashicons-trash"></span></button>
                        <div class="field__results jsAlchemyUploaderResults">{{IMAGE}}</div>
                    </div>
                </div>
            ';
        }

        public function render_saved_upload( $value ) {
            $savedUpload = '';

            if( $value ) {
                $imageData = wp_get_attachment_image_src( $value, 'thumbnail' );

                if( $imageData ) {
                    $savedUpload .= sprintf( '<img src="%s" alt="" />', $imageData[0] );
                }
            }

            return $savedUpload;
        }

        public function normalize_field_keys( $field ) {
            $field = parent::normalize_field_keys( $field );
            $passedAttrs = isset( $field[ 'attributes' ] ) ? $field[ 'attributes' ] : array();
            $mergedAttrs = array_merge( array(
                'type' => 'hidden',
                'id' => $field[ 'id' ],
                'name' => isset( $field['name'] ) ? $field['name'] : $field['id'],
                'value' => $field[ 'value' ],
                'class' => 'jsAlchemyUploaderInput'
            ), $passedAttrs );

            $field[ 'attributes' ] = $this->concat_attributes( $mergedAttrs );

            //check for value
            $field[ 'image' ] = $this->render_saved_upload( $field[ 'value' ] );

            $field[ 'add-button-title' ] = isset( $field[ 'button-title' ] ) ? $field[ 'button-title' ] : __( 'Select or Upload Media', 'alchemy-options' );
            $field[ 'add-button-text' ] = isset( $field[ 'button-text' ] ) ? $field[ 'button-text' ] : __( 'Use this media', 'alchemy-options' );

            return $field;
        }
    }
}