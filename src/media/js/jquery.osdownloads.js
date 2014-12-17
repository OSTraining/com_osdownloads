(function osdownloadsClosure($) {

    $(function osdownloadsDomReady() {
        // Move the popup containers to the body
        $('.reveal-modal').appendTo($('body'));
    });

    $.fn.osdownloads = function osdownloads(options) {
        var defaults = {
            animation: 'fade',
            elementsPrefix: 'osdownloads',
            popupElementId: 'osdownloadsRequirementsPopup'
        };

        var options = $.extend({}, defaults, options);

        if (this.length) {
            return this.each(function osdownloadsEachElement() {
                var $this = $(this),
                    $btnContinue = $('#' + options.elementsPrefix + 'DownloadContinue'),
                    $popup = $('#' + options.popupElementId),
                    $errorAgreeTerms = $('#' + options.elementsPrefix + 'ErrorAgreeTerms'),
                    $errorInvalidEmail = $('#' + options.elementsPrefix + 'ErrorInvalidEmail'),
                    $fieldAgree = $('#' + options.elementsPrefix + 'RequireAgree'),
                    $fieldEmail = $('#' + options.elementsPrefix + 'RequireEmail'),
                    $groupEmail = $('#' + options.elementsPrefix + 'EmailGroup'),
                    $groupAgree = $('#' + options.elementsPrefix + 'AgreeGroup'),
                    $requiredEmailMessage = $('#' + options.elementsPrefix + 'RequiredEmailMessage'),
                    directPage = $this.data('direct-page'),
                    showEmail = $this.data('show-email') == 1,
                    requireEmail = $this.data('require-email') == 1,
                    requireAgree = $this.data('require-agree') == 1;

                var isValidForm = function () {
                    var emailRegex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/,
                        errorElement = null,
                        hasError = false;

                    if (requireAgree) {
                        if (! $fieldAgree.is(':checked')) {
                            hasError = true;
                            $errorAgreeTerms.show();
                        } else {
                            $errorAgreeTerms.hide();
                        }
                    }

                    if (requireEmail) {
                        var email = $fieldEmail.val().trim();

                        if (email === '' || ! emailRegex.test(email)) {
                            hasError = true;
                            $errorInvalidEmail.show();
                        } else {
                            $errorInvalidEmail.hide();
                        }
                    } else {
                        if (showEmail) {
                            var email = $fieldEmail.val().trim();

                            if (email != '' && ! emailRegex.test(email)) {
                                hasError = true;
                                $errorInvalidEmail.show();
                            } else {
                                $errorInvalidEmail.hide();
                            }
                        }
                    }

                    if (hasError) {
                        return false;
                    }

                    return true;
                };

                var showPopup = function (selector) {
                    $(selector).reveal({
                         animation: options.animation,
                         animationspeed: 200,
                         closeonbackgroundclick: true,
                         dismissmodalclass: 'close-reveal-modal',
                    });
                };

                var goToDirectPage = function () {
                    if (directPage) {
                        window.location = directPage;
                    }
                };

                var download = function () {
                    var url = $this.attr('href');

                    if ($fieldEmail.length > 0) {
                        url += '&email=' + $fieldEmail.val().trim();
                    }

                    // Create the popup element
                    $container = $('<div>')
                        .attr('id', options.elementsPrefix + 'PopupIframe')
                        .addClass('reveal-modal');

                    $iframe = $('<iframe>').attr('src', url);
                    $iframe.iframeAutoHeight({
                        heightOffset: 10
                    });
                    $close = $('<a class="close-reveal-modal">&#215;</a>');

                    $iframe.appendTo($container);
                    $close.appendTo($container);
                    $container.appendTo($('body'));

                    $container.on('reveal:close', function() {
                        setTimeout(function timeoutRemoveIframePopup() {
                            $container.remove();
                        }, 500);
                    });

                    // Close the requirements popup
                    $popup.trigger('reveal:close');

                    setTimeout(function timeoutShowPopup() {
                        showPopup('#' + options.elementsPrefix + 'PopupIframe');
                    }, 500);
                };

                $this.on('click', function downloadBtnOnClick(event) {
                    event.preventDefault();

                    if (showEmail || requireEmail || requireAgree) {
                        if (showEmail || requireEmail) {
                            if (requireEmail) {
                                $requiredEmailMessage.show();
                            } else {
                                $requiredEmailMessage.hide();
                            }

                            $groupEmail.show();
                        } else {
                            $groupEmail.hide();
                        }

                        if (requireAgree) {
                            $groupAgree.show();
                        } else {
                            $groupAgree.hide();
                        }

                        $btnContinue.attr('href', $this.attr('href'));

                        showPopup('#' + options.popupElementId);

                        $popup.on(
                            'reveal:close',
                            function requirementsRevealOnClose() {
                                // Clean fields
                                $fieldEmail.val('');
                                $fieldAgree.attr('checked', false);
                                $('.reveal-modal .error').hide();
                            }
                        );

                        $btnContinue.off();
                        $btnContinue.on('click', function continueBtnOnClick(event) {
                            event.preventDefault();

                            if (isValidForm()) {
                                download();
                            }
                        });
                    } else {
                        download();
                    }
                });
            });
        }
    };
})(jQuery);
