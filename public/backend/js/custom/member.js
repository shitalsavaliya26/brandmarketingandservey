$.validator.addMethod(
    "alphanumeric",
    function (value, element) {
        return this.optional(element) || /^[\w.]+$/i.test(value);
    },
    'This field consists of Letters, numbers, and underscores only.'
    );
$.validator.addMethod(
    "positiveNumber",
    function (value) {
        return Number(value) > 0;
    },
    'Value must be greater than 0'
    );
$.validator.addMethod("regex", function (value, element, regexpr) {
    return regexpr.test(value);
}, "Please enter a valid value.");
/* fund wallet approve disapprove code*/


$.validator.addMethod('ge', function (value, element, param) {
    return this.optional(element) || value >= $(param).val();
}, 'Invalid value.');


$.validator.addMethod('noSpace', function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, 'No space please.');

$("#customer_register").on('submit', function (e) {
    if (!$(this).validate()) {
        e.preventDefault();
    }
});


// $.validator.addMethod('dimention', function(value, element, param) {
//     if(element.files.length == 0){
//         return true;
//     }
//     var width = $(element).data('imageWidth');
//     var height = $(element).data('imageHeight');

//     var val = width/height;
//     if(val == param){
//         return true;
//     }
//     else{
//         return false;
//     }
// },'Please upload an image with the mentioned aspect ratio');

/*Create brand*/
$("#brand-form").validate({
    rules: {
        name: {
            required: true,
            maxlength: 255,
            remote: {
                url: brandExits,
                type: "post",
                data: {
                    _token: $("input[name=_token]").val()
                },
                dataFilter: function(data) {
                    var data = JSON.parse(data);
                    if (data.valid != true) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        },
        logo: {
            required: true,
            extension: 'png|jpeg|jpg'
        },
        website_url: {
            required: true,
            regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
        },
        "country_id[]":{
            required: true,
        },
    },
    messages: {
        name: {
            required: 'Please enter name',
            maxlength: "Maximum limit of name is 255 character",
            remote:"Brand already exits!"
        },
        logo: {
            required: 'Please select logo',
        },        
        website_url: {
            required: 'Please enter website url',
            regex: 'Please enter valid website url',
        },
        "country_id[]":{
            required: "Please select country",
        },
    },

});
/*Create Brand*/

/*Edit Brand*/
$("#brand-form-edit").validate({

    rules: {
        name: {
            required: true,
            maxlength: 255,
            remote: {
                url: brandExits,
                type: "post",
                data: {
                    _token: $("input[name=_token]").val(),
                    group_id:group_id
                },
                dataFilter: function(data) {
                    var data = JSON.parse(data);
                    if (data.valid != true) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        },
        logo: {
            extension: 'png|jpeg|jpg'
        },
        website_url: {
            required: true,
            regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
        }
    },
    messages: {
        name: {
            required: 'Please enter name',
            maxlength: "Maximum limit of name is 255 character",
            remote:"Brand already exits!"
        },
        logo: {
            extension: 'Only png,jpeg,jpg allowed.',
        },        
        website_url: {
            required: 'Please enter website url',
            regex: 'Please enter valid website url',
        }
    },
});
/*Edit Brand*/


/*Create subscriber*/
$("#subscriber-form").validate({
    rules: {
        firstname: {
            required: true,
            maxlength: 255
        },
        lastname: {
            required: true,
            maxlength: 255
        },
        organization_name: {
            required: true,
            maxlength: 255
        },
        logo: {
            required: true,
            extension: 'png|jpeg|jpg'
        },
        website_url: {
            required: true,
            regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
        },
        contact_number: {
            required: true,
            digits: true,
            minlength: 9,
            maxlength: 15,
        },
        email: {
            required: true,
            email: true,
            maxlength: 50,
        },
        country_id:{
            required: true,
        }
    },
    messages: {
        firstname: {
            required: 'Please enter firstname',
            maxlength: "Maximum limit of firstname is 255 character",
        },
        lastname: {
            required: 'Please enter lastname',
            maxlength: "Maximum limit of lastname is 255 character",
        },
        organization_name: {
            required: 'Please enter organization name',
            maxlength: "Maximum limit of organization name is 255 character",
        },
        logo: {
            required: 'Please select logo',
        },        
        website_url: {
            required: 'Please enter website url',
            regex: 'Please enter valid website url',
        },
        contact_number: {
            required: 'Please enter contact number',
            digits: 'Please enter digits only',
            minlength: 'Please enter at least 9 digits.',
            maxlength: "Maximum limit of contact number is 15 digits",
        },
        email: {
            required: "Please enter email address",
            email: "Please enter valid email address",
            maxlength: "Maximum limit of username is 50 character",
        },
        country_id: {
            required: "Please select country",
        },
    },

});
/*Create subscriber*/

/*Edit subscriber*/
$("#subscriber-form-edit").validate({
    rules: {
        firstname: {
            required: true,
            maxlength: 255
        },
        lastname: {
            required: true,
            maxlength: 255
        },
        organization_name: {
            required: true,
            maxlength: 255
        },
        logo: {
            extension: 'png|jpeg|jpg'
        },
        website_url: {
            required: true,
            regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
        },
        contact_number: {
            required: true,
            digits: true,
            minlength: 9,
            maxlength: 15,
        },
        email: {
            required: true,
            email: true,
            maxlength: 50,
        },
        country_id:{
            required: true,
        }
    },
    messages: {
        firstname: {
            required: 'Please enter firstname',
            maxlength: "Maximum limit of firstname is 255 character",
        },
        lastname: {
            required: 'Please enter lastname',
            maxlength: "Maximum limit of lastname is 255 character",
        },
        organization_name: {
            required: 'Please enter organization name',
            maxlength: "Maximum limit of organization name is 255 character",
        },
        logo: {
            extension: 'Please choose image files only',
        },        
        website_url: {
            required: 'Please enter website url',
            regex: 'Please enter valid website url',
        },
        contact_number: {
            required: 'Please enter contact number',
            digits: 'Please enter digits only',
            minlength: 'Please enter at least 9 digits.',
            maxlength: "Maximum limit of contact number is 15 digits",
        },
        email: {
            required: "Please enter email address",
            email: "Please enter valid email address",
            maxlength: "Maximum limit of username is 50 character",
        },
        country_id: {
            required: "Please select country",
        },
    },

});
/*Edit subscriber*/


/*Create topic*/
$("#topic-form").validate({
    rules: {
        name: {
            required: true,
        },
        email_list_to_email_list_to: {
            checkTags: true,
        },
        // email_list_cc_email_list_cc: {
        //     checkTags: true,
        // }
    },
    messages: {
        name: {
            required: 'Please enter name',
        },
        email_list_to_email_list_to:{
            checkTags: 'Please enter at least one mail',
        },
        email_list_cc_email_list_cc:{
            checkTags: 'Please enter at least one mail',
        }
    },
});
/*Create topic end*/

/*Edit topic*/
$("#topic-form-edit").validate({
    rules: {
        name: {
            required: true,
        },
        email_list_to_email_list_to: {
            checkTags: true,
        },
        // email_list_cc_email_list_cc: {
        //     checkTags: true,
        // }
    },
    messages: {
        name: {
            required: 'Please enter name',
        },
        email_list_to_email_list_to:{
            checkTags: 'Please enter at least one mail',
        },
        email_list_cc_email_list_cc:{
            checkTags: 'Please enter at least one mail',
        }
    },
});
/*Edit topic end*/

/**
     * Image size must be less than 10KB
     */
 $.validator.addMethod(
    "less_than_2000kb",
    function (value, elem, param) {
        if (value != "") {
            var size = parseFloat(elem.files[0].size / 1024).toFixed(2);
            return size < 2000;
        } else {
            return true;
        }
    },
    "PDF size must be less than 2MB"
);


/*Create notification topic*/
$("#notification-topic-form").validate({
     ignore: [],
    rules: {
        name: {
            required: true,
            maxlength: 255
        },
        attachment_type: {
            required: true,
        },
        file: {
            // required: true,
            extension: 'png|jpeg|jpg'
        },
        pdf: {
            required: function(element){
                return ($('input[name="attachment_type"]:checked').val() == 'pdf');
            },
            extension: 'pdf',
            less_than_2000kb: true,
        },
        link: {
            required: function(element){
                return ($('input[name="attachment_type"]:checked').val() == 'link');
            },
            url: true
        },
    },
    messages: {
        name: {
            required: 'Please enter name',
            maxlength: "Maximum limit of name is 255 character",
        },
        attachment_type: {
            required: 'Please select attachment type',
        },
        file:{
            required: 'Please select image',
            extension: 'Please choose image files only',
        },
        pdf:{
            required: 'Please select pdf',
            extension: 'Please choose pdf files only',
        },
        link:{
            required: 'Please enter link',
            url: 'Please enter valid url',
        }
    },
});
/*Create notification topic end*/


/*Edit notification topic*/
$("#notification-topic-form-edit").validate({
    rules: {
        name: {
            required: true,
            maxlength: 255
        },
        pdf: {
            // required: function(element){
            //     return ($('input[name="attachment_type"]:checked').val() == 'pdf');
            // },
            extension: 'pdf',
            less_than_2000kb: true,
        },
    },
    messages: {
        name: {
            required: 'Please enter name',
        },
        pdf:{
            // required: 'Please select pdf',
            extension: 'Please choose pdf files only',
        },
    },
});
/*Edit notification topic end*/

/*Create buy*/
$("#buy-form").validate({
    rules: {
        link: {
            required: true,
            regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
        }
    },
    messages: {
        link: {
            required: 'Please enter link',
            regex: 'Please enter valid link',
        },
    },
});
/*Create buy end*/

/*Edit buy*/
$("#buy-form-edit").validate({
    rules: {
        link: {
            required: true,
            regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
        }
    },
    messages: {
        link: {
            required: 'Please enter link',
            regex: 'Please enter valid link',
        },
    },
});
/*Edit buy end*/

/*Create win*/
$("#win-form").validate({
    rules: {
        attachment_type: {
            required: true,
        },
        attachment: {
            required: {
                depends: function() {
                    return $('input[name=attachment_type]:checked').val() == 'link';
                }
            },
            regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
        },
        document: {
            required: function() {
                return ($('input[name=attachment_type]:checked').val() == 'pdf' || $('input[name=attachment_type]:checked').val() == 'image');
            },
            extension: function(element){
                if($('input[name=attachment_type]:checked').val() == 'image'){
                    return 'png|jpeg|jpg';
                }
                else if($('input[name=attachment_type]:checked').val() == 'pdf'){
                    return 'pdf';
                }
            }
        }

    },
    messages: {
        attachment_type: {
            required: 'Please select any one attachment type',
        },
        attachment: {
            required: 'Please enter link',
            regex: 'Please enter valid link',
        },
        document: {
            required: 'Please enter document',
            extension: 'Please enter valid format of document.',
        }

    },
});
/*Create win end*/

/*Edit win*/
$("#win-form-edit").validate({
    rules: {
        attachment_type: {
            required: true,
        },
        attachment: {
            required: {
                depends: function() {
                    return $('input[name=attachment_type]:checked').val() == 'link';
                }
            },
            regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
        },
        document: {
            required: function() {
                return ($('input[name=attachment_type]:checked').val() == 'pdf' || $('input[name=attachment_type]:checked').val() == 'image');
            },
            extension: function(element){
                if($('input[name=attachment_type]:checked').val() == 'image'){
                    return 'png|jpeg|jpg';
                }
                else if($('input[name=attachment_type]:checked').val() == 'pdf'){
                    return 'pdf';
                }
            }
        }

    },
    messages: {
        attachment_type: {
            required: 'Please select any one attachment type',
        },
        attachment: {
            required: 'Please enter link',
            regex: 'Please enter valid link',
        },
        document: {
            required: 'Please enter document',
            extension: 'Please enter valid format of document.',
        }

    },
});
/*Edit win end*/


$("#poll_form").validate({
    groups: {
        question: "question[1], question[2]"
    },
    ignore: "input[type='text']:hidden",
    rules: {
        question: {
            required: true,
            minlength: 2,
        }
    },


    messages: {
        question: {
            required: "Please enter question",
            minlength: "Please enter minimum 2 character",
        }
    },
});



$("#poll_form_create").validate({
    groups: {
        question: "question[1], question[2]"
    },
    ignore: "input[type='text']:hidden",
    rules: {
        question: {
            required: true,
            minlength: 2,
        }
    },
    messages: {
        question: {
            required: "Please enter question",
            minlength: "Please enter minimum 2 character",
        },
    },
});

$("#topup-form").validate({
    rules: {
        nameoncard: {
            required: true,
            regex: /^[a-zA-Z\s]+$/,
            maxlength: 100
        },
        cardnumber: {
            required: true,
        },
        cvv: {
            required: true,
            digits:true,
        },
        expirationdate: {
            required: true,
        },
        amount: {
            required: true,
            regex: /^[1-9]\d*(\.\d+)?$/,
        },
    },


    messages: {
        nameoncard: {
            required: "Please enter name",
            regex: "Please enter characters only",
            maxlength: "Maximum limit of name is 100 character"

        },
        cardnumber: {
            required: "Please enter your card number",
        },
        cvv: {
            required: "Please enter cvv",
            digits: "Only accept digit",
        },
        expirationdate: {
            required: "Please enter expiration date of card",
        },
        amount: {
            required: "Please enter amount",
            regex: "Only accept 2 digit after decimal and value greater than or equal to 1",
        }
    },
});

$("#saved-topup-form").validate({
    rules: {
        cvv: {
            required: true,
            digits:true,
        },
        amount: {
            required: true,
            regex: /^[1-9]\d*(\.\d+)?$/,
        },
    },

    messages: {
        cvv: {
            required: "Please enter cvv",
            digits: "Only accept digit",
        },
        amount: {
            required: "Please enter amount",
            regex: "Only accept 2 digit after decimal and value greater than or equal to 1",
        }
    },
});


/*Profile Edit*/
$("#profile-form-edit").validate({
    rules: {
        organization_name: {
            required: true,
            maxlength: 255
        },
        contact_number: {
            required: true,
            digits: true,
            minlength: 9,
            maxlength: 15,
        },
        firstname: {
            required: true,
            maxlength: 255
        },
        lastname: {
            required: true,
            maxlength: 255
        },
        email: {
            required: true,
            email: true,
            maxlength: 50,
        },
        country_id:{
            required: true,
        },
        logo: {
            // required: true,
            // extension: 'png|jpeg|jpg',
            // dimention:1
        },
        desktop_logo:{
            // required: true,
            extension: 'png|jpeg|jpg',
            // dimention:3
        },
        currency_id:{
            required: true,
            // dimention:3
        }
    },
    messages: {
        firstname: {
            required: 'Please enter name',
            maxlength: "Maximum limit of name is 255 character",
        },
        lastname: {
            required: 'Please enter surname',
            maxlength: "Maximum limit of surname is 255 character",
        },
        organization_name: {
            required: 'Please enter organization name',
            maxlength: "Maximum limit of organization name is 255 character",
        },
        logo: {
            required: 'Please select logo',
        },
        contact_number: {
            required: 'Please enter contact number',
            digits: 'Please enter digits only',
            minlength: 'Please enter at least 9 digits.',
            maxlength: "Maximum limit of contact number is 15 digits",
        },
        email: {
            required: "Please enter email address",
            email: "Please enter valid email address",
            maxlength: "Maximum limit of username is 50 character",
        },
        country_id: {
            required: "Please select country",
        },
        password:{
            required:"Please enter password",
            minlength: "Please enter at least 6 characters",
            maxlength: "Maximum limit of passwoed is 30 characters",
        },
        desktop_logo:{
            required: 'Please select logo',
        },
        currency_id:{
            required: "Please select currency",
        }
    },

});
/*Profile Edit end*/

/*Subscriber Register*/
$("#register-form").validate({
    rules: {
        // firstname: {
        //     required: true,
        //     maxlength: 255
        // },
        // lastname: {
        //     required: true,
        //     maxlength: 255
        // },
        organization_name: {
            required: true,
            maxlength: 255
        },
        logo: {
            required: true,
            extension: 'png|jpeg|jpg',
            // dimention:1
        },
        contact_number: {
            required: true,
            digits: true,
            minlength: 9,
            maxlength: 15,
        },
        email: {
            required: true,
            email: true,
            maxlength: 50,
        },
        country_id:{
            required: true,
        },
        currency_id:{
            required: true,
        },
        password:{
            required:true,
            minlength: 6,
            maxlength: 30,
        },
        desktop_logo:{
            required:true,
            extension: 'png|jpeg|jpg',
            // dimention:3
        }
    },
    messages: {
        firstname: {
            required: 'Please enter firstname',
            maxlength: "Maximum limit of firstname is 255 character",
        },
        lastname: {
            required: 'Please enter lastname',
            maxlength: "Maximum limit of lastname is 255 character",
        },
        organization_name: {
            required: 'Please enter organization name',
            maxlength: "Maximum limit of organization name is 255 character",
        },
        logo: {
            required: 'Please select logo',
        },
        contact_number: {
            required: 'Please enter contact number',
            digits: 'Please enter digits only',
            minlength: 'Please enter at least 9 digits.',
            maxlength: "Maximum limit of contact number is 15 digits",
        },
        email: {
            required: "Please enter email address",
            email: "Please enter valid email address",
            maxlength: "Maximum limit of username is 50 character",
        },
        country_id: {
            required: "Please select country",
        },
        currency_id: {
            required: "Please select currency",
        },
        password:{
            required:"Please enter password",
            minlength: "Please enter at least 6 characters",
            maxlength: "Maximum limit of passwoed is 30 characters",
        },
        desktop_logo:{
            required: 'Please select logo',
        }
    },

});
/*Subscriber register end*/





/* select currency-form start*  */
$("#currency-form").validate({
    rules: {
        currency_id:{
            required: true,
        },
    },
    messages: {
        currency_id: {
            required: "Please select currency",
        },
    },
});
/* select currency-form end*  */


/* poll title create */


$("#poll_title_form").validate({
    rules: {
        title:{
            required: true,
        },
    },
    messages: {
        title: {
            required: "Please enter poll title",
        },
    },
});



/*Withdraw form*/
$("#withdraw-form").validate({
    rules: {
        bank_name: {
            required: true,
            regex: /^[a-zA-Z\s]+$/,
            minlength: 5,
            maxlength: 100,
        },
        branch_name: {
            required: true,
            regex: /^[a-zA-Z\s]+$/,
            minlength: 5,
            maxlength: 100,
        },
        acc_holder_name: {
            required: true,
            regex: /^[a-zA-Z\s]+$/,
            minlength: 5,
            maxlength: 100,
        },
        acc_number: {
            required: true,
            digits: true,
            minlength: 5,
            maxlength: 20,
        },
        amount: {
            required: true,
            regex: /^[1-9]\d*(\.\d+)?$/,
        },
        ifsc:{
            required: true,
            minlength: 5,
            maxlength: 15,
        }
    },


    messages: {
        bank_name: {
            required: "Please enter bank name",
            regex: "Please enter characters only",
            minlength: 'Please enter at least 5 character.',
            maxlength: "Maximum limit of bank name is 100 character"
        },
        branch_name: {
            required: "Please enter branch name",
            regex: "Please enter characters only",
            minlength: 'Please enter at least 5 character.',
            maxlength: "Maximum limit of branch name is 100 character"
        },
        acc_holder_name: {
            required: "Please enter account holder name",
            regex: "Please enter characters only",
            minlength: 'Please enter at least 5 character.',
            maxlength: "Maximum limit of account holder name is 100 character",
        },
        acc_number: {
            required: "Please enter account number",
            digits: 'Please enter digits only',
            minlength: 'Please enter at least 5 digits.',
            maxlength: "Maximum limit of account number is 20 digits",
        },
        amount: {
            required: "Please enter amount",
            regex: "Only accept 2 digit after decimal and value greater than or equal to 1",
        },
        ifsc: {
            required: "Please enter swift code",
            minlength: 'Please enter at least 5 character.',
            maxlength: "Maximum limit of swift code is 15 character"
        },
    },
});
/*Withdraw form end*/

/*Change password form*/
$("#change-password-form").validate({
    rules : {
        password : {
            required: true,
            minlength : 6
        },
        password_confirmation : {
            required: true,
            minlength : 6,
            equalTo : "#password"
        }
    },

    messages: {
        password: {
            required: "Please enter password",
            minlength: 'Please enter at least 6 character.',
        },
        password_confirmation: {
            required: "Please enter confirm password",
            minlength: 'Please enter at least 6 character.',
            equalTo: 'Enter Confirm Password Same as Password'
        },
    },
});
/*Change password form end*/

$("[name^=option]").each(function () {
    $(this).rules('add', {
        require_from_group: [2, $("[name^=option]")],
        minlength: 2,
        messages: {
            require_from_group: "Minimum 2 options are required"
        }
    });
});

/*Announcement*/
jQuery.validator.addMethod("imageextension", function (value, element) {
    if (value) {

        var ext = $(element).val().split('.').pop().toLowerCase();
        if (value && $.inArray(ext, ['jpg', 'jpeg', 'png', 'JPG', 'JPEG']) == -1) {
            return false;
        }
    }
    return true;
}, "Please choose image files only.");

$.validator.addMethod("checkTags", function(value, element) { 
    if($("."+element.id).find(".tag").length <= 0){
        return false;
    }
    return true;
}, 'Please enter at least one mail');



$("#new-topup-form").validate({
    rules: {
        amount: {
            required: true,
            regex: /^[1-9]\d*(\.\d+)?$/,
        },
    },

    messages: {
        amount: {
            required: "Please enter amount",
            regex: "Only accept 2 digit after decimal and value greater than or equal to 1",
        }
    },
});