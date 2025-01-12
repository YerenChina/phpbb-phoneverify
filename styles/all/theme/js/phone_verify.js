;(function($, window, document) {
    'use strict';

    $(function() {
        var countdown = 0;
        var timer = null;

        function updateSendButton() {
            var $btn = $('#send_code');
            if (countdown > 0) {
                $btn.prop('disabled', true).val(phoneVerifyLang.SEND_CODE + ' (' + countdown + ')');
                countdown--;
                timer = setTimeout(updateSendButton, 1000);
            } else {
                $btn.prop('disabled', false).val(phoneVerifyLang.SEND_CODE);
            }
        }

        $('#send_code').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var phone_number = $('#phone_number').val();

            if (!phone_number.match(/^1[3-9]\d{9}$/)) {
                alert(phoneVerifyLang.PHONE_NUMBER_INVALID);
                return;
            }

            $btn.prop('disabled', true);

            $.ajax({
                url: $btn.data('url'),
                type: 'POST',
                data: {
                    phone_number: phone_number,
                    form_token: $('input[name="form_token"]').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert(response.error);
                        $btn.prop('disabled', false);
                    } else {
                        countdown = 60;
                        updateSendButton();
                        alert(phoneVerifyLang.VERIFY_CODE_SENT);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert(phoneVerifyLang.SMS_SEND_FAILED);
                    $btn.prop('disabled', false);
                }
            });
        });
    });
})(jQuery, window, document);