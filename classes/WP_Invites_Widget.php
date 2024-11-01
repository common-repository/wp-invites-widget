<?php

class WP_Invites_Widget extends WP_Widget {

	const CLASSNAME = 'wp-invites-widget';
	const DEFAULT_DESC = 'Write the e-mail address to the person you want to invite and click send.';
	const DEFAULT_INVITATION_EMAIL_MESSAGE = "Hi,\n\nyour friend USER_NAME, wants you to join us at SITE_NAME.\n\nGo to SIGNUP_PAGE and create yourself an account.\n\nYou'll need an invitation code: INVITATION_CODE";
	const DEFAULT_INVITATION_EMAIL_SUBJECT = 'USER_NAME has invited you to join SITE_NAME';
	const DEFAULT_SEND_SUCCESS = 'Your invitation has successfully been sent.';
	const DEFAULT_TITLE = 'Invite someone';
	const DESCRIPTION = 'A widget for the WP-invites plugin.';
	const OPTION_NAME = 'widget_wp-invites-widget';
	const WIDGETNAME = 'WP-invites widget';

	function WP_Invites_Widget () {
		$widget_ops = array (
			'classname' => self::CLASSNAME,
			'description' => self::DESCRIPTION
		);

		$control_ops = array ('id_base' => 'wp-invites-widget');

		$this->WP_Widget ('wp-invites-widget', self::WIDGETNAME, $widget_ops, $control_ops);
	}

	private $instance;
	private $lastMailMessage;

	public function widget ($args, $instance) {
		if (!(is_user_logged_in ())) {
			return;
		}

		extract ($args);

		$title = apply_filters ('widget_title', $instance ['title']);
		$desc = $instance ['desc'];

		echo $before_widget;

		if ($title) {
			echo $before_title . $title . $after_title;
		}

		if ($desc) {
			echo '<p data_wp_invites_widget_desc="true">' . $desc . '</p>';
		}

		echo '
			<form action="' . get_option ('siteurl') . '/wp-admin/admin-ajax.php" method="post" data_wp_invites_widget="true" data_wp_invites_widget_pluginurl="' . WP_PLUGIN_URL . '">
				<input type="hidden" name="action" value="wp_invites_widget" />
				<input type="hidden" name="id" value="' . $this->extractId ($widget_id) . '" />
				<p>
					<label for="' . $this->get_field_id ('email') . '">' . __ ('Email') . ':</label>
					<input id="' . $this->get_field_id ('email') . '" type="text" name="email" style="width:100%;" />
					<input  type="submit" value="' . __ ('Send') . '" />
				</p>
			</form>';

		echo $after_widget;
	}

	public function update ($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance ['title'] = strip_tags ($new_instance ['title']);
		$instance ['desc'] = strip_tags ($new_instance ['desc']);
		$instance ['send_success'] = strip_tags ($new_instance ['send_success']);
		$instance ['invitation_email_message'] = strip_tags ($new_instance ['invitation_email_message']);
		$instance ['invitation_email_subject'] = strip_tags ($new_instance ['invitation_email_subject']);

		return $instance;
	}

	public function form ($instance) {
		$defaults = array (
			'title' => self::DEFAULT_TITLE,
			'desc' => self::DEFAULT_DESC,
			'send_success' => self::DEFAULT_SEND_SUCCESS,
			'invitation_email_message' => self::DEFAULT_INVITATION_EMAIL_MESSAGE,
			'invitation_email_subject' => self::DEFAULT_INVITATION_EMAIL_SUBJECT
		);

		$instance = wp_parse_args ((array) $instance, $defaults);

		echo '
			<p>
				<label for="' . $this->get_field_id ('title') . '">' . __ ('Title') . ':</label>
				<input id="' . $this->get_field_id ('title') . '" type="text" name="' . $this->get_field_name ('title') . '" value="' . $instance ['title'] . '" style="width:100%;" />
			</p>
			<p>
				<label for="' . $this->get_field_id ('desc') . '">' . __ ('Description') . ':</label>
				<textarea id="' . $this->get_field_id ('desc') . '" name="' . $this->get_field_name ('desc') . '" style="width:100%;">' . $instance ['desc'] . '</textarea>
			</p>
			<p>
				<label for="' . $this->get_field_id ('send_success') . '">' . __ ('Message to user') . ':</label>
				<textarea id="' . $this->get_field_id ('send_success') . '" name="' . $this->get_field_name ('send_success') . '" rows="2" style="width:100%;">' . $instance ['send_success'] . '</textarea>
			</p>
			<p>
				Use these labels in you text:
			</p>
			<ul>
				<li><strong>USER_NAME</strong> – the user\'s username</li>
				<li><strong>SITE_NAME</strong> – this site\'s name</li>
				<li><strong>SIGNUP_PAGE</strong> – a link to the signup-form</li>
				<li><strong>INVITATION_CODE</strong> – the invitation code.</li>
			</ul>
			<p>
				<label for="' . $this->get_field_id ('invitation_email_subject') . '">' . __ ('Invitation e-mail subject') . ':</label>
				<input id="' . $this->get_field_id ('invitation_email_subject') . '" type="text" name="' . $this->get_field_name ('invitation_email_subject') . '" value="' . $instance ['invitation_email_subject'] . '" style="width:100%;" />
			</p>
			<p>
				<label for="' . $this->get_field_id ('invitation_email_message') . '">' . __ ('Invitation e-mail message') . ':</label>
				<textarea id="' . $this->get_field_id ('invitation_email_message') . '" name="' . $this->get_field_name ('invitation_email_message') . '" rows="10" style="width:100%;">' . $instance ['invitation_email_message'] . '</textarea>
			</p>';
	}

	public function setInstance ($id) {
		$option = (array) get_option (self::OPTION_NAME);
		if (array_key_exists ($id, $option)) {
			$this->instance = $option [$id];
		}
	}

	public function sendInvitationEmail ($from_user, $email, $code) {
		$subject = self::DEFAULT_INVITATION_EMAIL_SUBJECT;
		$message = self::DEFAULT_INVITATION_EMAIL_MESSAGE;

		if (is_array ($this->instance)) {
			if (array_key_exists ('invitation_email_subject', $this->instance)) {
				$subject = $this->instance ['invitation_email_subject'];
			}
		}

		if (is_array ($this->instance)) {
			if (array_key_exists ('invitation_email_message', $this->instance)) {
				$message = $this->instance ['invitation_email_message'];
			}
		}

		$subject = str_replace ('USER_NAME', $from_user->display_name, $subject);
		$subject = str_replace ('SITE_NAME', get_bloginfo ('name'), $subject);
		$subject = str_replace ('INVITATION_CODE', $code, $subject);

		if (defined ('BP_PLUGIN_DIR')) {
			$subject = str_replace ('SIGNUP_PAGE', get_bloginfo ('url') . '/register', $subject);
		} else {
			$subject = str_replace ('SIGNUP_PAGE', get_bloginfo ('url') . '/wp-signup.php', $subject);
		}

		$message = str_replace ('USER_NAME', $from_user->display_name, $message);
		$message = str_replace ('SITE_NAME', get_bloginfo ('name'), $message);
		$message = str_replace ('INVITATION_CODE', $code, $message);

		if (defined ('BP_PLUGIN_DIR')) {
			$message = str_replace ('SIGNUP_PAGE', get_bloginfo ('url') . '/register', $message);
		} else {
			$message = str_replace ('SIGNUP_PAGE', get_bloginfo ('url') . '/wp-signup.php', $message);
		}

		$headers = 'From: ' . $from_user->display_name . ' <' . $from_user->user_email . '>' . "\r\n";

		$this->lastMailMessage = wp_mail ($email, $subject, $message, $headers);
	}

	public function getMessage () {
		if (is_array ($this->instance)) {
			if (array_key_exists ('send_success', $this->instance)) {
				return $this->instance ['send_success'];
			}
		}

		return self::DEFAULT_SEND_SUCCESS;
	}

	private function extractId ($id) {
		return substr ($id, strrpos ($id, '-') + 1);
	}

}

?>