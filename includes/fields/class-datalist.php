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

if( ! class_exists( __NAMESPACE__ . '\Datalist' ) ) {

    class Datalist extends Includes\Field {
        public function __construct( $networkField = false, $options = array() ) {
            parent::__construct( $networkField, $options );

            $this->template = '
                <div class="alchemy__field alchemy__clearfix field field--datalist jsAlchemyDatalistBlock" id="field--{{ID}}" data-alchemy=\'{"id":"{{ID}}","type":"datalist"}\'>
                    <div class="field__side">
                        <label class="field__label" for="{{ID}}">{{TITLE}}</label>
                        {{DESCRIPTION}}
                    </div>
                    <div class="field__content"{{PADDED}}>
                        <select style="width: 100%;" class="jsAlchemyDatalistSelect"{{MULTIPLE}} {{ATTRIBUTES}}>{{OPTIONS}}</select>
                        {{CLEAR}}
                    </div>
                </div>
            ';
        }

        public function normalize_field_keys( $field ) {
            $field = parent::normalize_field_keys( $field );

            $field['multiple'] = isset( $field['multiple'] ) ? $field['multiple'] : false;
            $field['clear'] = $field['multiple'] ? '' : '<button type="button" class="button button-secondary jsAlchemyDatalistClear"><span class="dashicons dashicons-trash"></span></button>';
            $field['multiple'] = $this->is_multiple( $field['multiple'] );
            $field['options'] = $this->get_options_html( $field );
            $field['padded'] = '' !== $field['multiple'] ? '' : 'style="padding-right: 50px;"';
            $field[ 'attributes' ] = $this->concat_attributes( array(
                'id' => $field[ 'id' ],
                'name' => isset( $field['name'] ) ? $field['name'] : $field['id'],
            ) );

            return $field;
        }

        public function get_options_html( $field ) {
            $optionsHTML = '<option value="default"></option>';

            if( is_array( $field['value'] ) ) {
                foreach ( $field['options'] as $id => $option ) {
                    $field['options'][$id]['alchemy_is_selected'] = in_array( $option['value'], $field['value'] );
                }
            }

            $optionsHTML .= join('', array_map( function( $option ){
                $isSelected = isset( $option['alchemy_is_selected'] ) ? $option['alchemy_is_selected'] : false;

                return sprintf(
                    '<option value="%1$s"%3$s>%2$s</option>',
                    esc_attr( $option['value'] ),
                    $option['name'],
                    $this->is_selected( $isSelected )
                );
            }, $field['options'] ));

            return $optionsHTML;
        }

        private function is_selected( $value ) {
            return $value ? ' selected="selected"' : '';
        }

        public function is_multiple( $value ) {
            return $value ? ' multiple="multiple"' : '';
        }
    }
}