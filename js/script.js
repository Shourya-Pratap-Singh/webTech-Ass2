/**
 * Apex Institute of Technology - Registration Portal Frontend Logic
 * Implemented with JavaScript & jQuery (DOM manipulation, real-time validation, AJAX)
 */

$(document).ready(function () {
    const $form = $('#registrationForm');
    const $submitBtn = $('#submitBtn');
    const $btnText = $submitBtn.find('.btn-text');
    const $btnLoader = $submitBtn.find('.btn-loader');
    const $resetBtn = $('#resetBtn');
    const $alertBox = $('#alertBox');
    const $registrationSection = $('#registrationSection');
    const $successSection = $('#successSection');
    const $receiptContainer = $('#receiptContainer');

    // --------------------------------------------------------------------------
    // 1. Real-time Character Counter for Address
    // --------------------------------------------------------------------------
    $('#address').on('input', function () {
        const len = $(this).val().length;
        $('#addressCount').text(len);
        if (len > 250) {
            $('#addressCount').css('color', '#ef4444');
        } else {
            $('#addressCount').css('color', '#64748b');
        }
    });

    // Restrict Phone and PIN code inputs to numbers only
    $('#phone, #pincode').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // --------------------------------------------------------------------------
    // 2. Validation Helper Functions
    // --------------------------------------------------------------------------
    function markError($field, $errorSpan, message) {
        $field.addClass('is-invalid').removeClass('is-valid');
        $errorSpan.text(message).show();
    }

    function markSuccess($field, $errorSpan) {
        $field.removeClass('is-invalid').addClass('is-valid');
        $errorSpan.text('').hide();
    }

    function validateField(fieldName) {
        let isValid = true;

        switch (fieldName) {
            case 'fullName': {
                const val = $('#fullName').val().trim();
                const $span = $('#fullNameError');
                if (val.length < 3) {
                    markError($('#fullName'), $span, 'Full name must be at least 3 characters.');
                    isValid = false;
                } else if (!/^[a-zA-Z\s.]+$/.test(val)) {
                    markError($('#fullName'), $span, 'Name must contain only alphabets and spaces.');
                    isValid = false;
                } else {
                    markSuccess($('#fullName'), $span);
                }
                break;
            }
            case 'dob': {
                const val = $('#dob').val();
                const $span = $('#dobError');
                if (!val) {
                    markError($('#dob'), $span, 'Date of birth is required.');
                    isValid = false;
                } else {
                    const birthYear = new Date(val).getFullYear();
                    const currentYear = new Date().getFullYear();
                    const age = currentYear - birthYear;
                    if (age < 15 || age > 60 || isNaN(age)) {
                        markError($('#dob'), $span, 'Candidate age must be between 15 and 60 years.');
                        isValid = false;
                    } else {
                        markSuccess($('#dob'), $span);
                    }
                }
                break;
            }
            case 'gender': {
                const checked = $('input[name="gender"]:checked').val();
                const $span = $('#genderError');
                if (!checked) {
                    $span.text('Please select a gender.').show();
                    isValid = false;
                } else {
                    $span.text('').hide();
                }
                break;
            }
            case 'course': {
                const val = $('#course').val();
                const $span = $('#courseError');
                if (!val) {
                    markError($('#course'), $span, 'Please select an academic program.');
                    isValid = false;
                } else {
                    markSuccess($('#course'), $span);
                }
                break;
            }
            case 'qualification': {
                const val = $('#qualification').val();
                const $span = $('#qualificationError');
                if (!val) {
                    markError($('#qualification'), $span, 'Please select your highest qualification.');
                    isValid = false;
                } else {
                    markSuccess($('#qualification'), $span);
                }
                break;
            }
            case 'percentage': {
                const val = parseFloat($('#percentage').val());
                const $span = $('#percentageError');
                if (isNaN(val) || val < 35 || val > 100) {
                    markError($('#percentage'), $span, 'Score must be between 35% and 100%.');
                    isValid = false;
                } else {
                    markSuccess($('#percentage'), $span);
                }
                break;
            }
            case 'passingYear': {
                const val = $('#passingYear').val();
                const $span = $('#passingYearError');
                if (!val) {
                    markError($('#passingYear'), $span, 'Please select passing year.');
                    isValid = false;
                } else {
                    markSuccess($('#passingYear'), $span);
                }
                break;
            }
            case 'email': {
                const val = $('#email').val().trim();
                const $span = $('#emailError');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!val || !emailRegex.test(val)) {
                    markError($('#email'), $span, 'Please enter a valid email address.');
                    isValid = false;
                } else {
                    markSuccess($('#email'), $span);
                }
                break;
            }
            case 'phone': {
                const val = $('#phone').val().trim();
                const $span = $('#phoneError');
                if (!/^[0-9]{10}$/.test(val)) {
                    markError($('#phone'), $span, 'Please enter a valid 10-digit mobile number.');
                    isValid = false;
                } else {
                    markSuccess($('#phone'), $span);
                }
                break;
            }
            case 'address': {
                const val = $('#address').val().trim();
                const $span = $('#addressError');
                if (val.length < 10) {
                    markError($('#address'), $span, 'Address must be at least 10 characters.');
                    isValid = false;
                } else {
                    markSuccess($('#address'), $span);
                }
                break;
            }
            case 'city': {
                const val = $('#city').val().trim();
                const $span = $('#cityError');
                if (val.length < 2) {
                    markError($('#city'), $span, 'City is required.');
                    isValid = false;
                } else {
                    markSuccess($('#city'), $span);
                }
                break;
            }
            case 'state': {
                const val = $('#state').val().trim();
                const $span = $('#stateError');
                if (val.length < 2) {
                    markError($('#state'), $span, 'State is required.');
                    isValid = false;
                } else {
                    markSuccess($('#state'), $span);
                }
                break;
            }
            case 'pincode': {
                const val = $('#pincode').val().trim();
                const $span = $('#pincodeError');
                if (!/^[0-9]{6}$/.test(val)) {
                    markError($('#pincode'), $span, 'PIN Code must be a 6-digit number.');
                    isValid = false;
                } else {
                    markSuccess($('#pincode'), $span);
                }
                break;
            }
            case 'declaration': {
                const checked = $('#declaration').is(':checked');
                const $span = $('#declarationError');
                if (!checked) {
                    $span.text('You must agree to the declaration before submitting.').show();
                    isValid = false;
                } else {
                    $span.text('').hide();
                }
                break;
            }
        }

        updateProgressBar();
        return isValid;
    }

    // Attach blur and change listeners for real-time validation
    $('#fullName, #dob, #course, #qualification, #percentage, #passingYear, #email, #phone, #address, #city, #state, #pincode').on('blur change input', function () {
        validateField($(this).attr('name'));
    });

    $('input[name="gender"]').on('change', function () {
        validateField('gender');
    });

    $('#declaration').on('change', function () {
        validateField('declaration');
    });

    // --------------------------------------------------------------------------
    // 3. Progress Step Tracker
    // --------------------------------------------------------------------------
    function updateProgressBar() {
        const step1 = $('#fullName').val().trim() && $('#dob').val() && $('input[name="gender"]:checked').length;
        const step2 = $('#course').val() && $('#qualification').val() && $('#percentage').val() && $('#passingYear').val();
        const step3 = $('#email').val().trim() && $('#phone').val().trim() && $('#address').val().trim() && $('#city').val() && $('#state').val() && $('#pincode').val();
        const step4 = $('#declaration').is(':checked');

        $('.progress-step[data-step="1"]').toggleClass('active', Boolean(step1));
        $('.progress-step[data-step="2"]').toggleClass('active', Boolean(step2));
        $('.progress-step[data-step="3"]').toggleClass('active', Boolean(step3));
        $('.progress-step[data-step="4"]').toggleClass('active', Boolean(step4));
    }

    // --------------------------------------------------------------------------
    // 4. Form Reset Button
    // --------------------------------------------------------------------------
    $resetBtn.on('click', function () {
        if (confirm('Are you sure you want to clear all entered information?')) {
            $form[0].reset();
            $('input, select, textarea').removeClass('is-valid is-invalid');
            $('.error-msg').text('').hide();
            $('#addressCount').text('0');
            $('.progress-step').removeClass('active');
            $('.progress-step[data-step="1"]').addClass('active');
            $alertBox.hide();
        }
    });

    // --------------------------------------------------------------------------
    // 5. AJAX Form Submission via jQuery
    // --------------------------------------------------------------------------
    $form.on('submit', function (e) {
        e.preventDefault();

        // Validate all fields
        const fields = [
            'fullName', 'dob', 'gender', 'course', 'qualification',
            'percentage', 'passingYear', 'email', 'phone',
            'address', 'city', 'state', 'pincode', 'declaration'
        ];

        let allValid = true;
        let $firstErrorField = null;

        fields.forEach(function (field) {
            const valid = validateField(field);
            if (!valid) {
                allValid = false;
                if (!$firstErrorField) {
                    $firstErrorField = $('[name="' + field + '"]').first();
                }
            }
        });

        if (!allValid) {
            showAlert('Please complete all required fields correctly before submitting.', 'danger');
            if ($firstErrorField && $firstErrorField.length) {
                $('html, body').animate({
                    scrollTop: $firstErrorField.offset().top - 120
                }, 400);
                $firstErrorField.focus();
            }
            return;
        }

        // Hide alert and start loading animation on button
        $alertBox.hide();
        $submitBtn.prop('disabled', true);
        $btnText.hide();
        $btnLoader.show();

        // Prepare serialized form data
        const formData = $form.serialize();

        // Send AJAX request to PHP backend
        $.ajax({
            url: 'process.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                if (response.status === 'success') {
                    // Transition: Hide form and display formatted receipt
                    $registrationSection.fadeOut(300, function () {
                        $receiptContainer.html(response.html);
                        $successSection.fadeIn(400);

                        // Smooth scroll to top of confirmation receipt
                        $('html, body').animate({ scrollTop: 0 }, 300);

                        // Attach event handlers for receipt actions
                        attachReceiptHandlers();
                    });
                } else {
                    showAlert(response.message || 'An error occurred during submission.', 'danger');
                    resetSubmitButton();
                }
            },
            error: function (xhr) {
                resetSubmitButton();
                let errorMsg = 'Failed to submit application. Please check your network or server.';

                if (xhr.status === 422 && xhr.responseJSON) {
                    errorMsg = xhr.responseJSON.message || 'Validation errors found.';
                    if (xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (field, msg) {
                            const $f = $('[name="' + field + '"]');
                            const $s = $('#' + field + 'Error');
                            markError($f, $s, msg);
                        });
                    }
                }

                showAlert(errorMsg, 'danger');
                $('html, body').animate({
                    scrollTop: $alertBox.offset().top - 100
                }, 300);
            }
        });
    });

    // Helper: Reset submit button loading state
    function resetSubmitButton() {
        $submitBtn.prop('disabled', false);
        $btnLoader.hide();
        $btnText.show();
    }

    // Helper: Show alert banner
    function showAlert(message, type) {
        $alertBox
            .removeClass('alert-danger alert-success')
            .addClass('alert-' + type)
            .find('.alert-text')
            .text(message);
        $alertBox.find('.alert-icon')
            .attr('class', type === 'danger' ? 'alert-icon fa-solid fa-triangle-exclamation' : 'alert-icon fa-solid fa-circle-check');
        $alertBox.slideDown(250);
    }

    // --------------------------------------------------------------------------
    // 6. Receipt Action Handlers (Print & Register Another)
    // --------------------------------------------------------------------------
    function attachReceiptHandlers() {
        // Print Button
        $('#printReceiptBtn').off('click').on('click', function () {
            window.print();
        });

        // Register Another Applicant Button
        $('#newRegBtn').off('click').on('click', function () {
            $form[0].reset();
            $('input, select, textarea').removeClass('is-valid is-invalid');
            $('.error-msg').text('').hide();
            $('#addressCount').text('0');
            $('.progress-step').removeClass('active');
            $('.progress-step[data-step="1"]').addClass('active');
            resetSubmitButton();

            $successSection.fadeOut(300, function () {
                $registrationSection.fadeIn(300);
                $('html, body').animate({ scrollTop: 0 }, 300);
            });
        });
    }
});
