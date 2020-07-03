$(function () {
    
    // Get client / local timezone using ECMAScript Internationalization API
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    /**
     * Click event action for adding more Item input fields
     * New input fields will not appear if last one not filled up
     */
    $('.add-item').click(function () {
        let itemInput = $("input.item:first-child").clone();
        let lastInputItem = $('#itemInputs').find("input.item:last-child");
        if (lastInputItem.val() === '') {
            lastInputItem.focus();
        } else {
            itemInput.val('');
            $('#itemInputs').append(itemInput);
        }
    });

    /**
     * Keypress event action over input fields
     * Based on different control classes and input types
     * Input value is validated with proper regular expression
     */
    $(document).on('keypress', 'input', function (event) {
        let regex = '';
        if ($(this).hasClass('text-only')) {
            // Allow only letters
            regex = '^[a-zA-Z]$';
        } else if ($(this).hasClass('text-s-num-only')) {
            // Allow only letters, spaces and numbers
            regex = '^[0-9a-zA-Z ]$';
        } else if ($(this).hasClass('text-s-only')) {
            // Allow only letters and spaces
            regex = '^[a-zA-Z ]$';
        } else if ($(this).attr('type') === 'number') {
            // Allow only numbers
            regex = '^[0-9]$';
        }

        if ($(this).attr('maxlength')) {
            // Check max character length restriction if defined in attribute
            let maxStringLength = parseInt($(this).attr('maxlength'));
            if (maxStringLength > 0 && $(this).val().length >= maxStringLength) {
                return false;
            }
        }

        return allowInput(regex, event);
    });

    /**
     * Auto append BD mobile country code if input field is empty and focused 
     */
    $('#inputPhone').on('focus', function () {
        if ($(this).val() === '') {
            $(this).val('880');
        }
    });

    /**
     * Validate proper mobile phone number BD with regular expression
     */
    $('#inputPhone').on('blur', function () {
        var phone_rex = /(^(880)[0-9]{9,10})\b/;
        if (!phone_rex.test($(this).val())) {
            alertMessage('Not a valid mobile number', 'danger');
            $(this).addClass('is-invalid');
            return false;
        } else {
            $(this).removeClass('is-invalid');
            $('#alertMessage').html('');
        }
    });

    /**
     * Check and validate textarea input not exceed more than 30 words as input
     */
    $('textarea').keyup(function () {
        var str = $(this).val();
        // Check if value has more than 20 words
        if (str.split(' ').length > 30) {
            // Create string with first 20 words
            var strAllowed = str.split(' ').splice(0, 30).join(' ');
            // Overwrite the content with the first 30 words
            $(this).val(strAllowed);
        }
    });

    /**
     * Click event action for form submit button
     * If any required input field not filled up properly error class will be added 
     */
    $('button[type=submit]').on('click', function () {
        let formInputs = $(this).closest('form').find('input[required=true]');
        formInputs.each(function () {
            if ($.trim($(this).val()) === '') {
                $(this).addClass('is-invalid');
            }
        });
    });

    /**
     * Input key up event action
     * If any required input field with error class is filled up, error class will be removed
     */
    $(document).on('keyup', 'input.is-invalid', function () {
        $(this).removeClass('is-invalid');
    });

    /**
     * Form submit action event
     * Send form data with Fetch api and show appropriate message based on returned response
     */
    $('#submitForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        formData.append('timezone', timezone);

        fetch($(this).attr('action'),
                {
                    method: $(this).attr('method'),
                    body: formData
                })
                .then(response => response.json())
                .then(data => (data.success ? alertMessage(data.message, 'success') : alertMessage(data.message, 'danger')))
                .catch(error => alertMessage(error.message, 'danger'))
                .finally(function () {
                    $('#submitForm')[0].reset();
                    $(this).removeClass('is-invalid');
                    let itemInput = $("input.item:first-child").clone();
                    $("input.item").remove();
                    $('#itemInputs').append(itemInput);
                });

        return false;
    });

    /**
     * Click action event on descending / ascending control class
     */
    $('.table .desc, .table .asc').on('click', function () {
        let orderBy = $(this).data('order');
        let direction = 'asc';
        if ($(this).hasClass('asc')) {
            direction = 'desc';
        }
        window.location.href = baseUrl + 'report?order_by=' + orderBy + '&direction=' + direction;
    });
});

/**
 * Show alert message by appending html code to proper element ID
 * 
 * @param {string} msg Alert message content
 * @param {string} type Alert class type
 * @returns {void}
 */
function alertMessage(msg = '', type = 'success') {
    let mHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">'
            + '<strong>' + (type == 'success' ? 'Success' : 'Error') + '!</strong> ' + msg
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            + '</div>';

    $('body').find('#alertMessage').html(mHtml);
}

/**
 * Validate regex on input event
 * 
 * @param {string} rx Regular expression syntax 
 * @param {type} event Element binding event object
 * @returns {Boolean}
 */
function allowInput(rx, event) {
    var regex = new RegExp(rx);
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    } else {
        return true;
    }

}
