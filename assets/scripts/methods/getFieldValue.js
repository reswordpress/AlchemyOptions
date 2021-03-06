function getFieldValue( alchemyField, escapeMeta = false ) {
    const data = alchemyField.data('alchemy');

    if( ! data ) {
        return;
    }

    let value;

    switch (data.type) {
        case 'text' :
        case 'url' :
        case 'password' :
        case 'email' :
        case 'tel' :
        case 'select' :
        case 'textarea' :
        case 'colorpicker' :
        case 'datepicker' :
        case 'button-group' :
        case 'upload' :
        case 'slider' :
            value = alchemyField.find('input,select,textarea').val();
            break;
        case 'checkbox':
        case 'radio':
        case 'image-radio':
            value = [];

            alchemyField.find(':checked').each((i, el) => {
                value.push($(el).data('value'));
            });
            break;
        case 'editor' :
            const $area = $('.jsAlchemyEditorTextarea ', alchemyField);

            if( $area.hasClass('tinymce--init') && typeof( tinymce ) !== 'undefined' ) {
                $area.html(tinymce.get($area.attr('id')).getContent());
            }

            value = $area.html();
            break;
        case 'sections' :
            value = [];

            const $childFields = alchemyField.children('.jsAlchemySectionsTabs').children('.jsAlchemySectionsTab').children('.alchemy__field');

            if( $childFields[0] ) {
                $childFields.each((i, el) => {
                    const $el = $(el);
                    const data = $el.data('alchemy');

                    if( data ) {
                        value.push({
                            'type': data.type,
                            'value': getFieldValue($el)
                        })
                    }
                });
            }
            break;
        case 'repeater' :
            value = [];

            const fields = alchemyField.children('fieldset').children('.field__content').children('.jsAlchemyRepeaterSortable').children('.repeatee');

            if( fields[0] ) {
                fields.each((i, el) => {
                    const $repeatee = $(el);
                    const repeateeData = $repeatee.data('alchemy');
                    const $childFields = $repeatee.children('.repeatee__content').children('.alchemy__field');
                    const valueToStore = {
                        isVisible: $repeatee.children('.jsAlchemyRepeateeVisible').val(),
                        fields: {}
                    };

                    if(data.typed) {
                        valueToStore.typeID = repeateeData.repeateeTypeID
                    }

                    if( repeateeData.fieldIDs ) {
                        $.each(repeateeData.fieldIDs, (ind, field) => {
                            valueToStore.fields[field.id] = {
                                'type': field.type,
                                'value': getFieldValue( $childFields.eq(ind) )
                            };
                        });
                    }

                    value.push(valueToStore);
                });
            }
            break;
        case 'post-type-select' :
            const selectVal = alchemyField.find('select').val();

            value = {
                'type': data['post-type'],
                'ids': typeof selectVal === 'string' ? [selectVal]: selectVal
            };
            break;
        case 'taxonomy-select' :
            const taxSelectVal = alchemyField.find('select').val();

            value = {
                'taxonomy': data.taxonomy,
                'ids': typeof taxSelectVal === 'string' ? [taxSelectVal]: taxSelectVal
            };
            break;
        case 'datalist' :
            const datalistSelectVal = alchemyField.find('select').val();

            value = typeof datalistSelectVal === 'string' ? [datalistSelectVal]: datalistSelectVal;
            break;
        case 'field-group' :
            value = {};

            const $groupFields = alchemyField.children('fieldset').children('.jsAlchemyFiledGroupWrapper');

            if( $groupFields[0] ) {
                $groupFields.each((i, el) => {
                    const $group = $(el);
                    const groupData = $group.data('fields');
                    const $childFields = $group.children('.alchemy__field');

                    if( groupData ) {
                        $.each(groupData, (ind, field) => {
                            value[field.id] = {
                                'type': field.type,
                                'value': getFieldValue( $childFields.eq(ind) )
                            };
                        });

                    }
                });
            }
            break;
        default : break;
    }

    // need to escape backslashes before sending
    return typeof value === 'string' ? value.replace(/\\/g, '&#92;') : value;
}

export default getFieldValue;