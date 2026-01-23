(function ($) {
    'use strict';

    if (typeof wp === 'undefined' || !wp.media || !wp.media.view) {
        return;
    }

    /**
     * ClearTitleButton - Extends wp.media.view.Button
     * Similar pattern to DeleteSelectedButton in media-grid.js
     */
    var ClearTitleButton = wp.media.view.Button.extend({
        initialize: function () {
            wp.media.view.Button.prototype.initialize.apply(this, arguments);
            this.controller.on('selection:toggle', this.toggleDisabled, this);
            this.controller.on('select:activate', this.selectActivate, this);
            this.controller.on('select:deactivate', this.selectDeactivate, this);
        },

        toggleDisabled: function () {
            this.model.set('disabled', !this.controller.state().get('selection').length);
        },

        selectActivate: function () {
            this.toggleDisabled();
            this.$el.removeClass('hidden');
        },

        selectDeactivate: function () {
            this.toggleDisabled();
            this.$el.addClass('hidden');
        },

        render: function () {
            wp.media.view.Button.prototype.render.apply(this, arguments);
            if (this.controller.isModeActive('select')) {
                this.$el.addClass('clear-title-button');
            } else {
                this.$el.addClass('clear-title-button hidden');
            }
            this.toggleDisabled();
            return this;
        }
    });

    // Store original createToolbar
    var originalCreateToolbar = wp.media.view.AttachmentsBrowser.prototype.createToolbar;

    // Extend createToolbar to add our button
    wp.media.view.AttachmentsBrowser.prototype.createToolbar = function () {
        // Call original
        originalCreateToolbar.apply(this, arguments);

        // Only add in grid mode (media library grid view)
        if (!this.controller.isModeActive('grid')) {
            return;
        }

        var controller = this.controller;

        this.toolbar.set('clearTitleButton', new ClearTitleButton({
            text: runthingsEmtBulk.buttonText,
            style: 'primary',
            disabled: true,
            controller: controller,
            priority: -75, // priority -75 places it after delete (-80) but before bulk select toggle (-70)
            click: function () {
                var selection = controller.state().get('selection');
                var ids = selection.pluck('id');

                if (!ids.length) {
                    return;
                }

                if (!confirm(runthingsEmtBulk.confirmText)) {
                    return;
                }

                var button = this;
                button.model.set('disabled', true);

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'runthings_emt_bulk_clear',
                        nonce: runthingsEmtBulk.nonce,
                        ids: ids
                    },
                    success: function (response) {
                        if (response.success) {
                            var count = ids.length;
                            var successText = count === 1
                                ? runthingsEmtBulk.successTextSingular
                                : runthingsEmtBulk.successTextPlural;
                            // Update models in the selection to reflect cleared titles
                            selection.each(function (model) {
                                model.set('title', '');
                            });
                            alert(successText.replace('%d', count));
                            // Clear selection and exit bulk select mode
                            selection.reset();
                            controller.deactivateMode('select').activateMode('edit');
                        } else {
                            alert(response.data.message || runthingsEmtBulk.errorText);
                            button.model.set('disabled', false);
                        }
                    },
                    error: function () {
                        alert(runthingsEmtBulk.errorText);
                        button.model.set('disabled', false);
                    }
                });
            }
        }).render());
    };

})(jQuery);

