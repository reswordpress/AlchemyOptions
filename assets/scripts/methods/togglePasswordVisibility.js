export default function(scope = document) {
    const $passwordToggles = $('.jsAlchemyTogglePassword', scope);

    if( $passwordToggles[0] ) {
        $passwordToggles.each((i, toggle) => {
            const $toggle = $(toggle);
            const $target = $toggle.prev('input');
            const $icon = $toggle.find('span');

            $toggle.on('click', function(){
                $icon.toggleClass('dashicons-lock').toggleClass('dashicons-unlock');
                $target.attr('type', $target.attr('type') === 'text' ? 'password' : 'text');
            });
        });
    }
}