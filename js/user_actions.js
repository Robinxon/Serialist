$("#send-settings").click(function(e){
	var valid = this.form.checkValidity();
	if (valid) {
		event.preventDefault();
		
		var form = $('#user-settings')[0];
		var data = new FormData(form);
		data.append('ajax', true);
		
		$.ajax({
			method: 'post',
			url: '../user/change_settings.php',
			processData: false,
			contentType: false,
			data: data,
			success: function(data) {
				console.log(data);
				window.location.replace("../settings/");
			},
			error: function(e) {
				alert(e.responseText);
			}
		});
	}
});

function click_delete(id_user, id_media, hash) {
	event.preventDefault();
	$.ajax({
		method: 'get',
		url: '../user/list_delete.php',
		data:
		{
			'ajax': true,
			'id_user': id_user,
			'id_media': id_media,
			'hash': hash
		},
		success: function(data) {
			if(data != '')
				alert(data);
			location.reload();
		}
	});
}

function click_status(id_user, id_media, hash, type) {
	event.preventDefault();
	$.ajax({
		method: 'get',
		url: '../user/list_status.php',
		data:
		{
			'ajax': true,
			'id_user': id_user,
			'id_media': id_media,
			'hash': hash,
			'status': type
		},
		success: function(data) {
			if(data != '')
				alert(data);
			location.reload();
		}
	});
}

function click_rate(id_user, id_media, hash, rating) {
	event.preventDefault();
	$.ajax({
		method: 'get',
		url: '../user/list_rate.php',
		data:
		{
			'ajax': true,
			'id_user': id_user,
			'id_media': id_media,
			'hash': hash,
			'rating': rating
		},
		success: function(data) {
			if(data != '')
				alert(data);
			location.reload();
		}
	});
}

function click_favourite(id_user, id_media, hash, favourite) {
	event.preventDefault();
	$.ajax({
		method: 'get',
		url: '../user/list_favourite.php',
		data:
		{
			'ajax': true,
			'id_user': id_user,
			'id_media': id_media,
			'hash': hash,
			'favourite': favourite
		},
		success: function(data) {
			if(data != '')
				alert(data);
			location.reload();
		}
	});
}

function click_progress(id_user, id_media, hash, progress) {
	event.preventDefault();
	$.ajax({
		method: 'get',
		url: '../user/list_progress.php',
		data:
		{
			'ajax': true,
			'id_user': id_user,
			'id_media': id_media,
			'hash': hash,
			'progress': progress
		},
		success: function(data) {
			if(data != '')
				alert(data);
			location.reload();
		}
	});
}

function click_progress_plus(id_user, id_media, hash) {
	event.preventDefault();
	$.ajax({
		method: 'get',
		url: '../user/list_progress_plus.php',
		data:
		{
			'ajax': true,
			'id_user': id_user,
			'id_media': id_media,
			'hash': hash
		},
		success: function(data) {
			if(data != '')
				alert(data);
			location.reload();
		}
	});
}

$("#media-send").click(function(e){
	var valid = this.form.checkValidity();
	
	if (valid) {
		event.preventDefault();
		
		var form = $('#media-edit-form')[0];
		var data = new FormData(form);
		data.append('ajax', true);
		
		$.ajax({
			method: 'post',
			url: '../media_edit/media_upload.php',
			processData: false,
			contentType: false,
			data: data,
			success: function(data) {
				console.log(data);
				window.location.replace("../browse/");
			},
			error: function(e) {
				alert(e.responseText);
			}
		});
	}
});

$("#staff-send").click(function(e){
	var valid = this.form.checkValidity();
	if (valid) {
		event.preventDefault();
		
		var form = $('#staff-edit-form')[0];
		var data = new FormData(form);
		data.append('ajax', true);
		
		$.ajax({
			method: 'post',
			url: '../staff_edit/staff_upload.php',
			processData: false,
			contentType: false,
			data: data,
			success: function(data) {
				console.log(data);
				window.location.replace("../browse/");
			},
			error: function(e) {
				alert(e.responseText);
			}
		});
	}
});

$("#submission-media-accept").click(function(e){
	event.preventDefault();
	var form = $('#submission-media-form')[0];
	var data = new FormData(form);
	data.append('accepted', '1');
	data.append('ajax', true);
	
	$.ajax({
		url: '../submission_media/submission_media_upload.php',
		method: 'post',
		processData: false,
		contentType: false,
		data: data,
		success: function(data) {
			console.log(data);
			window.location.replace("../submissions/");
		},
		error: function(e) {
			alert(e.responseText);
		}
	});
});

$("#submission-media-decline").click(function(e){
	event.preventDefault();
	var form = $('#submission-media-form')[0];
	var data = new FormData(form);
	data.append('accepted', '0');
	data.append('ajax', true);
	
	$.ajax({
		url: '../submission_media/submission_media_upload.php',
		method: 'post',
		processData: false,
		contentType: false,
		data: data,
		success: function(data) {
			console.log(data);
			window.location.replace("../submissions/");
		},
		error: function(e) {
			alert(e.responseText);
		}
	});
});

$("#submission-staff-accept").click(function(e){
	event.preventDefault();
	var form = $('#submission-staff-form')[0];
	var data = new FormData(form);
	data.append('accepted', '1');
	data.append('ajax', true);
	
	$.ajax({
		url: '../submission_staff/submission_staff_upload.php',
		method: 'post',
		processData: false,
		contentType: false,
		data: data,
		success: function(data) {
			console.log(data);
			window.location.replace("../submissions/");
		},
		error: function(e) {
			alert(e.responseText);
		}
	});
});

$("#submission-staff-decline").click(function(e){
	event.preventDefault();
	var form = $('#submission-staff-form')[0];
	var data = new FormData(form);
	data.append('accepted', '0');
	data.append('ajax', true);
	
	$.ajax({
		url: '../submission_staff/submission_staff_upload.php',
		method: 'post',
		processData: false,
		contentType: false,
		data: data,
		success: function(data) {
			console.log(data);
			window.location.replace("../submissions/");
		},
		error: function(e) {
			alert(e.responseText);
		}
	});
});

function click_delete_media(id_user, id_media, hash) {
	event.preventDefault();
	$.ajax({
		method: 'get',
		url: '../user/media_delete.php',
		data:
		{
			'ajax': true,
			'id_user': id_user,
			'id_media': id_media,
			'hash': hash
		},
		success: function(data) {
			if(data != '')
				alert(data);
			window.location.replace("../browse/");
		}
	});
}

function click_delete_staff(id_user, id_staff, hash) {
	event.preventDefault();
	$.ajax({
		method: 'get',
		url: '../user/staff_delete.php',
		data:
		{
			'ajax': true,
			'id_user': id_user,
			'id_staff': id_staff,
			'hash': hash
		},
		success: function(data) {
			if(data != '')
				alert(data);
			window.location.replace("../browse/");
		}
	});
}