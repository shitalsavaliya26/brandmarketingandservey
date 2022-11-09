$(document).ready(function () {
	$.each($.validator.methods, function (key, value) {
		$.validator.methods[key] = function () {
			if (arguments.length > 0) {
				arguments[0] = $.trim(arguments[0]);
			}
			return value.apply(this, arguments);
		};
	});

	setTimeout(function () {
		$('.alert').hide('100');
	}, 4000);

	$('[data-toggle="tooltip"]').tooltip();
});
var confirmDelete = function (element, text) {
	if (confirm(text)) {
		/*alert("True");
		console.log("Confirm Yes ::",text);*/
		return true;
	} else {
		/*alert("false");
		console.log("Confirm No ::",text);
		element.preventDefault()*/;
		return false;
	}

}
var alerthtml = function (alertType = 'error', message) {
	var className = '';
	var type = '';
	if (alertType == 'error') {
		className = 'danger';
		type = 'Error';
	}
	if (alertType == 'success') {
		className = 'success';
		type = 'Success';
	}
	if (alertType == 'warning') {
		className = 'warning';
		type = 'Warning';
	}
	var html = '<div class="alert alert-' + className + '" role="alert"><strong>' + type + '!</strong> ' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

	return html;
}

$(document).ready(function () {
	// alert($(window).innerWidth() );
	if ($(document).width() <= 980) {
		// $('.mini-navbar1').addClass('mini-navbar');
	}
})

$(document).ready(function() {
	$("#currencyform").validate({
		rules: {
			amount: {
				required: true,
			},
		},
		messages: {
			amount: {
				required: "Please enter currency rate",
			},
		},
	});
});


/* currency rate form */
$(document).ready(function() {
	$("#currencyrateform").validate({
		rules: {
			amount: {
				required: true,
			},
		},
		messages: {
			amount: {
				required: "Please enter currency rate",
			},
		},
	});
});


/*******************************slick slider****************************************/

$(document).ready(function () {
	$('.news-slider').slick({
		infinite: true,
		autoplay: true,
		dots: true,
		arrows: false,
	});
});


/* currencyapicall */
$(document).on("click","#currencyapicall",function() {
    var spinner = $('#loader');
    spinner.show();
	$.ajax({
        url: currencyRateApi,
        type: 'GET',
        success: function (response) {
			$(window).scrollTop(0);
			location.href = currencyindex;
        },
    });
});

// document.getElementById("element").className = "capital-input";

$('.modal').on('shown.bs.modal', function () {
	$('.news-slider').slick('setPosition');
})

/** Reports: Agent Expand/Collapse */
$(".panel-title > a").click(function () {
    $(this)
    .find("i")
    .toggleClass("mdi-plus-circle mdi-minus")
    .closest("panel")
    .siblings("panel")
    .find("i")
    .removeClass("mdi-minus")
    .addClass("mdi-plus-circle");
});