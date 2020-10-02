'use strict';

var form, redirectUrl;

jQuery('.flw-simple-pay-now-form').on('submit', function (evt) {
    evt.preventDefault();
    form = $("#" + this.id);

    var config = buildConfigObj(this);
    FlutterwaveCheckout(config);
});

/**
 * Builds config object to be sent to GetPaid
 *
 * @return object - The config object
 */
var buildConfigObj = function (form) {
    var formData = jQuery(form).data();
    var amount = formData.amount || jQuery(form).find('#flw-amount').val();
    var email = formData.email || jQuery(form).find('#flw-customer-email').val();
    var txref = 'JRF_' + form.id.toUpperCase() + '_' + new Date().valueOf();
    var paymentplan = jQuery(form).find(' #flw-paymentplan ').val();
    var currency = formData.currency || jQuery(form).find(' #flw-currency ').val();
    ;
    var country = '';

    switch (currency) {
        case 'KES':
            country = 'KE';
            break;
        case 'GHS':
            country = 'GH';
            break;
        case 'ZAR':
            country = 'ZA';
            break;
        case 'TZS':
            country = 'TZ';
            break;
        case 'USD':
            country = 'US';
            break;

        default:
            country = 'NG';
            break;
    }

    return {
        amount: amount,
        country: country,
        currency: currency,
        customizations: {
            title: formData.title,
            // description: formData.desc,
            logo: formData.logo,
        },
        customer: {
            email: email
        },
        public_key: formData.pbkey,
        tx_ref: txref,
        payment_plan: paymentplan,
        onclose: function () {
            redirectTo(redirectUrl);
        },
        callback: function (res) {
            sendPaymentRequestResponse(res, formData.module);
        }
    };
};

/**
 * Calls saveResponse function and processes the callback  5531 8866 5214 2950
 *
 * @param object Response object from GetPaid
 * @param string Name of the module called
 *
 * @return void
 */
var sendPaymentRequestResponse = function (res, module) {
    saveResponse(res, module, function (response, error) {
        if (error) return console.log('error: ', error);
        redirectUrl = response.redirect_url;

        if (!redirectUrl) {
            var responseMsg = res.status
            jQuery(form)
                .find('#notice')
                .text('Transaction ' + responseMsg)
                .removeClass(function () {
                    return jQuery(form).find('#notice').attr('class');
                })
                .addClass(response.status);
        } else {
            setTimeout(redirectTo, 5000, redirectUrl);
        }
    });
};

/**
 * Sends payment response from GetPaid to the endpoint that saves the record
 *
 * @param object Response object from GetPaid passed through sendPaymentRequestResponse
 * @param string Name of the module called passed through sendPaymentRequestResponse
 * @param function Callback to be called when request returns
 *
 * @return void
 */
var saveResponse = function (data, module, cb) {
    var request = {
        'data': JSON.stringify(data),
        'format': 'json',
        'module': 'rave_payment_forms',
        'option': 'com_ajax',
        'title': module,
    };

    jQuery.ajax({
        data: request,
        type: 'POST',
        success: function (response) {
            cb(response.data, null);
        },
        error: function (err) {
            cb(null, err);
        }
    });
};

/**
 * Redirect to set url
 *
 * @param string url - The link to redirect to
 *
 * @return void
 */
var redirectTo = function (url) {
    if (url) {
        location.href = url;
    }
    redirectUrl = null;
};
