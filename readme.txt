=== WP-Invites widget ===
Author: nettle
Tags: captcha,registration,user,admin,access,authenification,register,widget
Requires at least: 2.6
Tested up to: 2.9.1
Stable tag: 1.0
A widget for the WP-invites plugin

== Description ==

####Description
This is a widget written for the [WP-Invites](http://wordpress.org/extend/plugins/wp-invites/) plugin by Jehy. It simply adds a widget for your registered and logged in users to invite friends by their's e-mail-address. This widget requires an installation of [WP-Invites](http://wordpress.org/extend/plugins/wp-invites/).

The widget is very simple. The only thing the users will have to do is to write one or more email addresses in the a textfield. The plugin will do the rest. This is how it is done:

1. The user adds one or more email addresses in the textfield.
2. The form is sent through an ajax request to the server.
3. The plugin loads the WP-Invites plugin and makes one invitation code.
4. The invitation code, the inviter’s username and email address are added into the invitation email.
5. A message is sent back to the widget informing the user that the email has successfully been sent.
6. The invited gets an email with the invitation code and an URL to the signup page (or the register page in BuddyPress), and will now be able to create an account.

You can configure the widget with your own title, description, invitation email and a message after the invitation process is done. You’ll also be able to have several different copys of this widget with their own configs.

###Installation
This widget requires an installation of [WP-Invites](http://wordpress.org/extend/plugins/wp-invites/). Please take a look at the instructions on the [installation page ](http://wordpress.org/extend/plugins/wp-invites-widget/installation/).

== Installation ==
1. [Download](http://wordpress.org/extend/plugins/wp-invites/) the WP-invites plugin;
2. Upload the complete folders of `wp-invites` and `wp-invites-widget` to the `/wp-content/plugins/` directory;  
3. Activate the plugins through the 'Plugins' menu in WordPress;  
4. Add the widget to you sidebar through 'Appearance > Wigdets' menu.