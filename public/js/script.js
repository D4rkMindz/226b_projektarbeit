/**
 * Base url
 *
 @returns {|jQuery}
 */
function baseurl() {
	return $('head base').attr('href');
}

/**
 * Send GET AJAX request.
 *
 * @param url {string} - url
 * @param data
 * @return Promise
 */
function sendGetAjax(url, data) {
	return new Promise((resolve,
						reject = function (xhr) {
							hideLoader();
							notify({
								type: 'error',
								msg: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error. Please try it again later'
							});
						}) => {
		var token = window.localStorage.getItem('token');

		$.ajax({
			method: 'GET',
			contentType: 'application/json',
			cache: false,
			processData: true,
			headers: {"Authorization": token},
			url: url
		}).done(function (response) {
			resolve(response);
		}).fail((xhr) => {
			ajaxFail(xhr, resolve, reject, 'GET', url, {});
		});
	});
}

/**
 * Send POST AJAX request.
 *
 * @param url
 * @param data
 * @returns {Promise}
 */
function sendPostAjax(url, data) {
	return new Promise((resolve, reject = function (xhr) {
		alert(xhr.responseText);
	}) => {
		let token = window.localStorage.getItem('token');
		$.ajax({
			method: 'POST',
			contentType: 'application/json',
			processData: true,
			dataType: 'json',
			headers: {"Authorization": token},
			url: url,
			data: data
		}).done(function (response) {
			resolve(response);
		}).fail(function (xhr) {
			reject(xhr);
		});
	});
}

function setErrors(xhr) {
	$.each('text-danger').removeClass('text-danger');
	$.each('p.help-block').addClass('hidden');
	$.each(xhr.responseJSON.errors, function (i, el) {
		var field = $('[data-id='+el.field+'-error]');
		var parent = field.parent('.form-group');
		parent.addClass('text-danger');
		field.html(el.message);
	});
}

/**
 * Show loading animation.
 */
function showLoader() {
	$("#loader").show();
}

/**
 * Hide loading animation.
 */
function hideLoader() {
	$("#loader").hide();
}