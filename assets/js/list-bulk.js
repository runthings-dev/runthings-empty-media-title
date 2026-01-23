/**
 * List view bulk action confirmation.
 *
 * Adds a confirmation dialog before executing the Clear titles bulk action
 * in the Media Library list view.
 */
(function ($) {
    'use strict';

    $(function () {
        $('#doaction, #doaction2').on('click', function (event) {
            var $button = $(this);
            var selectName = $button.attr('id') === 'doaction' ? 'action' : 'action2';
            var $select = $('select[name="' + selectName + '"]');
            var action = $select.val();

            if (action === runthingsEmtList.action) {
                if (!confirm(runthingsEmtList.confirmText)) {
                    event.preventDefault();
                }
            }
        });
    });
})(jQuery);

