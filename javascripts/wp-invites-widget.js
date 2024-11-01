var wp_invites_widget = {
	init: function () {
		jQuery ('form[data_wp_invites_widget]').each (wp_invites_widget.hook);
	},

	hook: function () {
		var form = jQuery (this);
		var desc = form.parent ().children ('p[data_wp_invites_widget_desc]');

		var msg = form.parent ().children ('p[data_wp_invites_widget_msg]');
		if (msg.length == 0) {
			desc.after ('<p data_wp_invites_widget_msg="true"></p>');
		}

		var options = {
			target: form.parent ().children ('p[data_wp_invites_widget_msg]'),
			clearForm: true,
			beforeSubmit: wp_invites_widget.load,
			success: wp_invites_widget.success
		};

		form.ajaxForm (options);
	},

	load: function (data, form) {
		form.parent ().children ('p[data_wp_invites_widget_msg]').stop ();

		var email = form.find ('input[type=text]');
		var match = email.val ().match (/([a-z0-9._-]+@[a-z0-9._-]+.[a-z]+)/gi);
		var error = false;

		if (match != null) {
			if (match.length == 0) {
				error = true
			}
		} else {
			error = true;
		}

		if (error) {
			email [0].wp_invites_widget_border = email.css ('border');
			email.css ('border', '1px solid #f00');
			return false;
		} else if (email [0].wp_invites_widget_border != null) {
			email.css ('border', email [0].wp_invites_widget_border);
		}

		var submit = form.find ('input[type=submit]');
		var loader = form.find ('img');
		submit.attr ('disabled', 'true');
		if (loader.length == 0) {
			submit.after ('<img src="' + form.attr ('data_wp_invites_widget_pluginurl') + '/wp-invites-widget/images/ubuntu.loader.gif" data_wp_invites_widget_loader="true" />');
		} else {
			loader.show ();
		}
	},

	success: function (response, status, form) {
		form.find ('img').hide ();
		form.find ('input[type=submit]').removeAttr ('disabled');

		var message = form.parent ().children ('p[data_wp_invites_widget_msg]');

		if (response == 'FALSE') {
			var email = form.find ('input[type=text]');
			email [0].wp_invites_widget_border = email.css ('border');
			email.css ('border', '1px solid #f00');
			message.hide ();
		} else {
			message.show ();
			message.animate ({opacity: 1}, 5000, null, function () {
				jQuery (this).hide ('slow');
			});
		}
	}
}

jQuery (document).ready (wp_invites_widget.init);