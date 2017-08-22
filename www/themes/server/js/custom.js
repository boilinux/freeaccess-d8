jQuery(function($) {
	$(document).ready(function() {
		if ($('.path-frontpage').length) {
			var created_field = $('input#edit-created[name="created"]');
			var created_field2 = $('input#edit-created-1[name="created_1"]');

			created_field.datepicker({
				format: 'mm/dd/yyyy',
        autoclose: true,
			});

			created_field2.datepicker({
				format: 'mm/dd/yyyy',
        autoclose: true,
			});

			created_field.keydown(function(e) {
			   e.preventDefault();
			   return false;
			});

			created_field2.keydown(function(e) {
			   e.preventDefault();
			   return false;
			});
		}

		$(document).ajaxComplete(function() {
			var count_down = $('span.message');
			// var body = $('body');

			if ($('span.error_message').length) {
				toastr['error']($('span.error_message').text(), "Top-up");

        
			}
		  else if (count_down.length) {
		  	toastr['success'](count_down.text(), "Top-up");

		  	var count = 20;
		  	// count_down.text(count);

		  	// add overlay
		  	$('body').append('<div class="popup_fixed_bg"></div>');

		  	var timer = setInterval(function() {

		  		if (count >= 0) {
				  	toastr['info']("", count + " seconds.");
		  		}

		  		count -= 1;

		  		if (count == 0) {
		  			location.reload();
		  		}
		  	},1000);
		  }
		});
	});
});