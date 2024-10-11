<?php
/*
* Plugin Name: Easy Testimonial Slider
* Plugin URI:https://www.i13websolution.com/product/wordpress-easy-testimonial-slider-plugin/
* Author URI:https://www.i13websolution.com/
* Description:This is beautiful responsive testimonial slider plugin for WordPress.Add any number of testimonial from admin panel.
* Author:I Thirteen Web Solution
* Version:1.0.19
* Text Domain:easy-testimonial-rotator
* Domain Path:/languages
*/
if ( ! class_exists( 'Etr_I13_Captcha_Img' ) ) {
	 $classPath = dirname( __FILE__ ) . '/class/etr_i13_captcha_img.php';
	 $classPath = str_replace( '\\', '/', $classPath );
	 require_once "$classPath";
}

	add_action( 'admin_menu', 'etr_add_best_testimonial_slider_admin_menu' );
	register_activation_hook( __FILE__, 'etr_install_best_testimonial_slider' );
	register_deactivation_hook( __FILE__, 'etr_easy_testimonials_remove_access_capabilities' );
	add_action( 'wp_enqueue_scripts', 'etr_best_testimonial_slider_load_styles_and_js' );
	add_shortcode( 'print_best_testimonial_slider', 'etr_print_best_testimonial_slider_func' );
	add_shortcode( 'print_best_testimonial_form', 'etr_print_best_testimonial_form_func' );
	add_filter( 'widget_text', 'do_shortcode' );
	add_action( 'admin_notices', 'etr_best_testimonial_slider_admin_notices' );
	add_action( 'wp_ajax_etr_get_grav_avtar', 'etr_get_grav_avtar_callback' );
	add_action( 'wp_ajax_nopriv_etr_get_grav_avtar', 'etr_get_grav_avtar_callback' );
	add_action( 'wp_ajax_etr_get_new_captcha', 'etr_get_new_captcha_callback' );
	add_action( 'wp_ajax_nopriv_etr_get_new_captcha', 'etr_get_new_captcha_callback' );
	add_action( 'wp_ajax_etr_save_testimonial', 'etr_save_testimonial_callback' );
	add_action( 'wp_ajax_nopriv_etr_save_testimonial', 'etr_save_testimonial_callback' );
	add_filter( 'user_has_cap', 'etr_easy_testimonial_admin_cap_list', 10, 4 );

	add_action( 'plugins_loaded', 'etr_load_lang_for_best_testimonial' );
function etr_load_lang_for_best_testimonial() {

		load_plugin_textdomain( 'easy-testimonial-rotator', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		add_filter( 'map_meta_cap', 'map_etr_easy_testimonial_meta_caps', 10, 4 );
}

function etr_easy_testimonial_admin_cap_list( $allcaps, $caps, $args, $user ) {

	if ( ! in_array( 'administrator', $user->roles ) ) {

		return $allcaps;
	} else {

		if ( ! isset( $allcaps['etr_testimonial_slider_settings'] ) ) {

			$allcaps['etr_testimonial_slider_settings'] = true;
		}

		if ( ! isset( $allcaps['etr_testimonial_slider_form_settings'] ) ) {

			$allcaps['etr_testimonial_slider_form_settings'] = true;
		}
		if ( ! isset( $allcaps['etr_testimonial_slider_view_testimonials'] ) ) {

			$allcaps['etr_testimonial_slider_view_testimonials'] = true;
		}
		if ( ! isset( $allcaps['etr_testimonial_slider_add_testimonial'] ) ) {

			$allcaps['etr_testimonial_slider_add_testimonial'] = true;
		}
		if ( ! isset( $allcaps['etr_testimonial_slider_edit_testimonial'] ) ) {

			$allcaps['etr_testimonial_slider_edit_testimonial'] = true;
		}
		if ( ! isset( $allcaps['etr_testimonial_slider_delete_testimonial'] ) ) {

			$allcaps['etr_testimonial_slider_delete_testimonial'] = true;
		}
		if ( ! isset( $allcaps['etr_testimonial_slider_preview'] ) ) {

			$allcaps['etr_testimonial_slider_preview'] = true;
		}
	}

	return $allcaps;

}


function map_etr_easy_testimonial_meta_caps( array $caps, $cap, $user_id, array $args ) {

	if ( ! in_array(
		$cap,
		array(
			'etr_testimonial_slider_settings',
			'etr_testimonial_slider_form_settings',
			'etr_testimonial_slider_view_testimonials',
			'etr_testimonial_slider_add_testimonial',
			'etr_testimonial_slider_edit_testimonial',
			'etr_testimonial_slider_delete_testimonial',
			'etr_testimonial_slider_preview',

		),
		true
	) ) {

		return $caps;
	}

	$caps = array();

	switch ( $cap ) {

		case 'etr_testimonial_slider_settings':
			  $caps[] = 'etr_testimonial_slider_settings';
			break;

		case 'etr_testimonial_slider_form_settings':
				$caps[] = 'etr_testimonial_slider_form_settings';
			break;

		case 'etr_testimonial_slider_view_testimonials':
				$caps[] = 'etr_testimonial_slider_view_testimonials';
			break;

		case 'etr_testimonial_slider_add_testimonial':
				$caps[] = 'etr_testimonial_slider_add_testimonial';
			break;

		case 'etr_testimonial_slider_edit_testimonial':
				$caps[] = 'etr_testimonial_slider_edit_testimonial';
			break;

		case 'etr_testimonial_slider_delete_testimonial':
				$caps[] = 'etr_testimonial_slider_delete_testimonial';
			break;

		case 'etr_testimonial_slider_preview':
				$caps[] = 'etr_testimonial_slider_preview';
			break;

		default:
				$caps[] = 'do_not_allow';
			break;
	}

	return apply_filters( 'etr_easy_testimonial_meta_caps', $caps, $cap, $user_id, $args );
}


function etr_easy_testimonial_add_access_capabilities() {

	// Capabilities for all roles.
	$roles = array( 'administrator' );
	foreach ( $roles as $role ) {

			$role = get_role( $role );
		if ( empty( $role ) ) {
				continue;
		}

		if ( ! $role->has_cap( 'etr_testimonial_slider_settings' ) ) {

				$role->add_cap( 'etr_testimonial_slider_settings' );
		}

		if ( ! $role->has_cap( 'etr_testimonial_slider_form_settings' ) ) {

				$role->add_cap( 'etr_testimonial_slider_form_settings' );
		}

		if ( ! $role->has_cap( 'etr_testimonial_slider_view_testimonials' ) ) {

				$role->add_cap( 'etr_testimonial_slider_view_testimonials' );
		}

		if ( ! $role->has_cap( 'etr_testimonial_slider_add_testimonial' ) ) {

				$role->add_cap( 'etr_testimonial_slider_add_testimonial' );
		}

		if ( ! $role->has_cap( 'etr_testimonial_slider_edit_testimonial' ) ) {

				$role->add_cap( 'etr_testimonial_slider_edit_testimonial' );
		}

		if ( ! $role->has_cap( 'etr_testimonial_slider_delete_testimonial' ) ) {

				$role->add_cap( 'etr_testimonial_slider_delete_testimonial' );
		}

		if ( ! $role->has_cap( 'etr_testimonial_slider_preview' ) ) {

				$role->add_cap( 'etr_testimonial_slider_preview' );
		}
	}

	$user = wp_get_current_user();
	$user->get_role_caps();

}

function etr_easy_testimonials_remove_access_capabilities() {

			$wp_roles = new WP_Roles();

	foreach ( $wp_roles->roles as $role => $details ) {
			$role = $wp_roles->get_role( $role );
		if ( empty( $role ) ) {
				continue;
		}

			$role->remove_cap( 'etr_testimonial_slider_settings' );
			$role->remove_cap( 'etr_testimonial_slider_form_settings' );
			$role->remove_cap( 'etr_testimonial_slider_view_testimonials' );
			$role->remove_cap( 'etr_testimonial_slider_add_testimonial' );
			$role->remove_cap( 'etr_testimonial_slider_edit_testimonial' );
			$role->remove_cap( 'etr_testimonial_slider_delete_testimonial' );
			$role->remove_cap( 'etr_testimonial_slider_preview' );

	}

	// Refresh current set of capabilities of the user, to be able to directly use the new caps.
	$user = wp_get_current_user();
	$user->get_role_caps();

}
function etr_save_testimonial_callback() {

	global $wpdb;
	$etr_i13_captcha_img = new Etr_I13_Captcha_Img();

	if ( isset( $_POST ) && is_array( $_POST ) && isset( $_POST['tnonce'] ) ) {

		$retrieved_nonce = '';

		if ( isset( $_POST['tnonce'] ) && '' != $_POST['tnonce'] ) {

			  $retrieved_nonce = sanitize_text_field( $_POST['tnonce'] );
		}
		if ( ! wp_verify_nonce( $retrieved_nonce, 'SubmitNonce' ) ) {

			wp_die( 'Security check fail' );
		}

		if ( isset( $_POST['form_id'] ) && (int) $_POST['form_id'] > 0 ) {

			$settings_main = get_option( 'best_testimonial_options' );
			$settings_main['id'] = 1;
			$settings = get_option( 'i13_default_form_options' );
			$formId = 1;

			if ( is_array( $settings_main ) ) {

				   $errors = array();

					$auth_name = '';
				if ( isset( $_POST['auth_name'] ) ) {
					$auth_name = trim( sanitize_text_field( $_POST['auth_name'] ) );
				}
				if ( $settings['show_author_name'] && $settings['is_author_name_field_required'] ) {

					if ( '' == trim( $auth_name ) || null == $auth_name ) {

						$errors[ 'auth_name_' . $formId ] = $settings['required_field_error_msg'];

					}
				}

					$auth_desn = '';
				if ( isset( $_POST['auth_desn'] ) ) {
					$auth_desn = trim( sanitize_text_field( $_POST['auth_desn'] ) );
				}
				if ( $settings['show_author_des'] && $settings['is_author_designation_field_required'] ) {

					if ( '' == trim( $auth_desn ) || null == $auth_desn ) {

						$errors[ 'auth_desn_' . $formId ] = $settings['required_field_error_msg'];

					}
				}

					$auth_email = '';
				if ( isset( $_POST['auth_email'] ) ) {
					$auth_email = trim( sanitize_email( $_POST['auth_email'] ) );
				}
				if ( $settings['show_author_email'] && $settings['is_author_email_field_required'] ) {

					if ( '' == trim( $auth_email ) || null == $auth_email ) {

						$errors[ 'auth_email_' . $formId ] = $settings['required_field_error_msg'];

					} else {
						if ( false === filter_var( $auth_email, FILTER_VALIDATE_EMAIL ) ) {

							$errors[ 'auth_email_' . $formId ] = $settings['invalid_email_field_error_msg'];
						}
					}
				} else if ( $settings['show_author_email'] && ! $settings['is_author_email_field_required'] ) {

					if ( '' != trim( $auth_email ) || null == $auth_email ) {

						if ( false === filter_var( $auth_email, FILTER_VALIDATE_EMAIL ) ) {

							  $errors[ 'auth_email_' . $formId ] = $settings['invalid_email_field_error_msg'];
						}
					}
				}

					$HdnMediaGrevEmail = '';
				if ( isset( $_POST['HdnMediaGrevEmail'] ) ) {
					$HdnMediaGrevEmail = trim( sanitize_text_field( $_POST['HdnMediaGrevEmail'] ) );
				}
				if ( $settings['show_photo_upload'] && $settings['photo_upload_field_required'] ) {

					if ( '' == trim( $HdnMediaGrevEmail ) || null == $HdnMediaGrevEmail ) {

						$errors[ 'HdnMediaGrevEmail_' . $formId ] = $settings['required_field_error_msg'];

					} else {
						if ( false === filter_var( $HdnMediaGrevEmail, FILTER_VALIDATE_EMAIL ) ) {

							$errors[ 'HdnMediaGrevEmail_' . $formId ] = $settings['invalid_email_field_error_msg'];
						}
					}
				} else if ( $settings['show_photo_upload'] && ! $settings['photo_upload_field_required'] ) {

					if ( '' != trim( $HdnMediaGrevEmail ) || null != $HdnMediaGrevEmail ) {

						if ( false === filter_var( $HdnMediaGrevEmail, FILTER_VALIDATE_EMAIL ) ) {

							 $errors[ 'HdnMediaGrevEmail_' . $formId ] = $settings['invalid_email_field_error_msg'];
						}
					}
				}

					$testimonial = '';
				if ( isset( $_POST['testimonial'] ) ) {
					$testimonial = trim( sanitize_text_field( $_POST['testimonial'] ) );
				}
				if ( '' == trim( $testimonial ) || null == $testimonial ) {

					  $errors[ 'testimonial_' . $formId ] = $settings['required_field_error_msg'];

				}

					$captcha = '';
					$cpatcha_name = '';
				if ( isset( $_POST['captcha'] ) ) {
					$captcha = trim( sanitize_text_field( $_POST['captcha'] ) );
				}
				if ( isset( $_POST['cpatcha_name'] ) ) {
					$cpatcha_name = trim( sanitize_text_field( $_POST['cpatcha_name'] ) );
				}
				if ( $settings['show_captcha'] ) {

					if ( '' == trim( $captcha ) || null == $captcha || '' == trim( $cpatcha_name ) || null == $cpatcha_name ) {

						$errors[ 'cpatcha_' . $formId ] = $settings['required_field_error_msg'];

					} else {

						if ( ! $etr_i13_captcha_img->etr_verifyCaptcha( $cpatcha_name, $captcha ) ) {

							$errors[ 'cpatcha_' . $formId ] = $settings['invalid_captcha'];
						}
					}
				}

				if ( count( $errors ) > 0 ) {

					 $result = array( 'result' => array( 'fields_error' => $errors ) );
					 echo json_encode( $result );
					 die;

				} else {

					$auth_name = trim( sanitize_text_field( $auth_name ) );
					$auth_desn = sanitize_text_field( $auth_desn );
					$auth_email = sanitize_email( $auth_email );
					$testimonial = sanitize_textarea_field( $testimonial );
					$gravatar_email = sanitize_email( $HdnMediaGrevEmail );
					$slider_id = intval( $formId );

					if ( $settings['auto_approve_testimonial'] ) {
						  $status = 1;
					} else {
						$status = 0;
					}

							$createdOn = wp_date( 'Y-m-d h:i:s' );
					if ( function_exists( 'date_i18n' ) ) {

							$createdon = date_i18n( 'Y-m-d ' . get_option( 'time_format' ), false, false );
						if ( get_option( 'time_format' ) == 'H:i' ) {
							$createdon = wp_date( 'Y-m-d H:i:s', strtotime( $createdOn ) );
						} else {
							$createdon = wp_date( 'Y-m-d h:i:s', strtotime( $createdOn ) );
						}
					}

							$table_name = $wpdb->prefix . 'b_testimo_slide';

														$wpdb->insert(
															$table_name,
															array(
																'testimonial' => stripslashes_deep( $testimonial ),
																'auth_name' => stripslashes_deep( $auth_name ),
																'auth_desn' => stripslashes_deep( $auth_desn ),
																'auth_email' => stripslashes_deep( $auth_email ),
																'createdon' => $createdOn,
																'gravatar_email' => stripslashes_deep( $gravatar_email ),
																'status' => $status,

															),
															array(
																'%s',
																'%s',
																'%s',
																'%s',
																'%s',
																'%s',
																'%d',

															)
														);

					$newTestimonial_id = 0;
					if ( isset( $wpdb->insert_id ) ) {
						$newTestimonial_id = $wpdb->insert_id;
					}

					if ( $newTestimonial_id > 0 ) {

						if ( $settings['notify_admin_new_testimonial'] ) {

							 $email_subject = stripslashes_deep( $settings['email_subject'] );
							 $email_From_name = stripslashes_deep( $settings['email_From_name'] );
							 $email_From = stripslashes_deep( $settings['email_From'] );
							 $email_to = stripslashes_deep( $settings['email_to'] );
							 $email_body = stripslashes_deep( $settings['email_body'] );

							 $adminData = get_user_by( 'email', get_option( 'admin_email' ) );
							 $admin_name = '';
							if ( is_object( $adminData ) ) {
								$admin_name = $adminData->user_nicename;

							}
							 $testimonial_moderate_edit_link_plain = admin_url() . "admin.php?page=best_testimonial_slider_testimonial_management&action=addedit&id=$newTestimonial_id";
							 $testimonial_moderate_edit_link_html = '<a href="' . $testimonial_moderate_edit_link_plain . '">Edit Testimonial</a>';

							 $email_body = str_replace( '[admin_name]', $admin_name, $email_body );
							 $email_body = str_replace( '[admin_email]', get_option( 'admin_email' ), $email_body );
							 $email_body = str_replace( '[testimonial_moderate_edit_link_plain]', $testimonial_moderate_edit_link_plain, $email_body );
							 $email_body = str_replace( '[testimonial_moderate_edit_link_html]', $testimonial_moderate_edit_link_html, $email_body );
							 $email_body = str_replace( '[sitename]', get_bloginfo( 'name' ), $email_body );

							 $charSet = get_bloginfo( 'charset' );

							 $mailheaders = '';
							 // $mailheaders .= "X-Priority: 1\n";
							 $mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
							 $mailheaders .= "From: $email_From_name <$email_From>\r\n";
							 // $mailheaders .= "Bcc: $emailTo" . "\r\n";
							 $message = '<html><head></head><body>' . $email_body . '</body></html>';
							 $Rreturns = wp_mail( $email_to, $email_subject, $message, $mailheaders );

						}

						$resetFormsFields = array();

						$uploads = wp_upload_dir();
						$baseurl = $uploads['baseurl'];
						$baseurl .= '/easy-testimonial-rotator/';
						$captchaImgName = $etr_i13_captcha_img->etr_generateI13Captcha();
						$captchaRefreshed = array(
							'cpatcha_name' => $captchaImgName,
							'captcha_url' => $baseurl . $captchaImgName . '.jpeg',
						);
						foreach ( $_POST as $k => $v ) {

							if ( 'tnonce' != $k && 'action' != $k && 'form_id' != $k ) {
								$resetFormsFields[ $k . '_' . $slider_id ] = '';
							}
						}

						$result = array(
							'result' => array(
								'success' => $settings['success_msg'],
								'resetFormsFields' => $resetFormsFields,
								'captchaRefreshed' => $captchaRefreshed,
							),
						);
						echo json_encode( $result );
						die;
					} else {

						$uploads = wp_upload_dir();
						$baseurl = $uploads['baseurl'];
						$baseurl .= '/easy-testimonial-rotator/';
						$captchaImgName = $etr_i13_captcha_img->etr_generateI13Captcha();
						$captchaRefreshed = array(
							'cpatcha_name' => $captchaImgName,
							'captcha_url' => $baseurl . $captchaImgName . '.jpeg',
						);

						$result = array(
							'result' => array(
								'error' => $settings['error_msg'],
								'captchaRefreshed' => $captchaRefreshed,
							),
						);
						echo json_encode( $result );
						die;

					}
				}
			} else {
				   $result = array( 'result' => array( 'error' => __( 'Does not found such form', 'easy-testimonial-rotator' ) ) );
				   echo json_encode( $result );
				   die;
			}
		}

			die;
	}
	die;

}

function etr_get_new_captcha_callback() {

	if ( isset( $_POST ) && is_array( $_POST ) && isset( $_POST['vNonce'] ) ) {

		$retrieved_nonce = '';
		$etr_i13_captcha_img = new Etr_I13_Captcha_Img();

		if ( isset( $_POST['vNonce'] ) && '' != $_POST['vNonce'] ) {

			  $retrieved_nonce = sanitize_text_field( $_POST['vNonce'] );
		}
		if ( ! wp_verify_nonce( $retrieved_nonce, 'vNonce' ) ) {

			wp_die( 'Security check fail' );
		}

		if ( isset( $_POST['oldcaptcha'] ) && '' != $_POST['oldcaptcha'] ) {
			$oldCaptcha = trim( sanitize_text_field( $_POST['oldcaptcha'] ) );
			$etr_i13_captcha_img->etr_removeByName( $oldCaptcha );
		}

			$uploads = wp_upload_dir();
			$baseurl = $uploads['baseurl'];
			$baseurl .= '/easy-testimonial-rotator/';
			$captchaImgName = $etr_i13_captcha_img->etr_generateI13Captcha();
			$return = array(
				'cpatcha_name' => $captchaImgName,
				'captcha_url' => $baseurl . $captchaImgName . '.jpeg',
			);
			echo json_encode( $return );
			die;
	}
	die;

}

function etr_get_grav_avtar_callback() {

	if ( isset( $_POST ) && is_array( $_POST ) && isset( $_POST['email'] ) ) {

		$retrieved_nonce = '';

		if ( isset( $_POST['vNonce'] ) && '' != $_POST['vNonce'] ) {

				$retrieved_nonce = sanitize_text_field( $_POST['vNonce'] );
		}
		if ( ! wp_verify_nonce( $retrieved_nonce, 'vNonce' ) ) {

			  wp_die( 'Security check fail' );
		}

			$email = sanitize_text_field( $_POST['email'] );
			$email = md5( $email );
			$url = "https://www.gravatar.com/avatar/$email?s=200";

			echo esc_html( $url );
			die;
	}
	  die;

}



function etr_best_testimonial_slider_load_styles_and_js() {
	if ( ! is_admin() ) {

		  wp_register_style( 'best-testimonial-bx', plugins_url( '/css/best-testimonial-bx.css', __FILE__ ), array(), '1.0.12' );
		  wp_register_style( 'best-testimonial-bx-cols-css', plugins_url( '/css/best-testimonial-bx-cols-css.css', __FILE__ ), array(), '1.0.12' );
		  wp_register_script( 'best-testimonial-slider', plugins_url( '/js/best-testimonial-slider.js', __FILE__ ), array( 'jquery' ), '1.0.12' );

	}
}

function etr_best_testimonial_slider_admin_notices() {

	if ( is_plugin_active( 'easy-testimonial-rotator/easy-testimonial-rotator.php' ) ) {

		$uploads = wp_upload_dir();
		$baseDir = $uploads ['basedir'];
		$baseDir = str_replace( '\\', '/', $baseDir );
		$pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';

		if ( file_exists( $pathToImagesFolder ) && is_dir( $pathToImagesFolder ) ) {

			if ( ! is_writable( $pathToImagesFolder ) ) {
				echo "<div class='updated'><p>" . esc_html( __( 'Easy Testimonial Rotator is active but does not have write permission on', 'easy-testimonial-rotator' ) ) . '</p><p><b>' . esc_html( $pathToImagesFolder ) . '</b> ' . esc_html( __( ' directory.Please allow write permission', 'easy-testimonial-rotator' ) ) . '.</p></div> ';
			}
		} else {

			wp_mkdir_p( $pathToImagesFolder );
			@file_put_contents( $pathToImagesFolder . '/index.php', '' );
			if ( ! file_exists( $pathToImagesFolder ) && ! is_dir( $pathToImagesFolder ) ) {
				echo "<div class='updated'><p>" . esc_html( __( 'Easy Testimonial Rotator is active but plugin does not have permission to create directory', 'easy-testimonial-rotator' ) ) . '</p><p><b>' . esc_html( $pathToImagesFolder ) . '</b>' . esc_html( __( ' .Please create easy-testimonial-rotator directory inside upload directory and allow write permission', 'easy-testimonial-rotator' ) ) . '.</p></div> ';
			}
		}
	}
}

function etr_install_best_testimonial_slider() {

	   global $wpdb;
	   $table_name = $wpdb->prefix . 'b_testimo_slide';

			  $sql = 'CREATE TABLE ' . $table_name . " (
                          `id` int(10)  NOT NULL AUTO_INCREMENT,
                          `testimonial` text  NOT NULL,
                          `image_name` varchar(500)  NOT NULL,
                          `auth_name` varchar(500)  DEFAULT NULL,
                          `auth_desn` varchar(500)  DEFAULT NULL,
                          `auth_email` varchar(500)  DEFAULT NULL,
                          `createdon` datetime NOT NULL,
                          `gravatar_email` varchar(200)  DEFAULT NULL,
                          `status` tinyint(1) NOT NULL DEFAULT '0',
                           PRIMARY KEY (`id`)
                );
                
                                 
                ";
		   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		   dbDelta( $sql );

		   $uploads = wp_upload_dir();
		   $baseDir = $uploads ['basedir'];
		   $baseDir = str_replace( '\\', '/', $baseDir );
		   $pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';
		   @file_put_contents( $pathToImagesFolder . '/index.php', '' );
			wp_mkdir_p( $pathToImagesFolder );

			$settings_slider = array();
			$settings_slider['auto'] = null;
			$settings_slider['is_circular_slider'] = 1;
			$settings_slider['speed'] = 1000;
			$settings_slider['pause'] = 10000;
			$settings_slider['box_border_color'] = '#FFFFFF';
			$settings_slider['box_border_size'] = 5;
			$settings_slider['box_shadow_color'] = '#FFFFFF';
			$settings_slider['slider_back_color'] = '#FFFFFF';
			$settings_slider['is_adaptive_height'] = 1;
			$settings_slider['show_arrows'] = 1;
			$settings_slider['show_author_name'] = 1;
			$settings_slider['show_author_des'] = 1;
			$settings_slider['show_pagination'] = 1;
			$settings_slider['touch_enabled'] = 1;
			$settings_slider['resize_images'] = 1;

	if ( ! get_option( 'best_testimonial_options' ) ) {

		update_option( 'best_testimonial_options', $settings_slider );
	}

			$settings = array();
			$settings['show_captcha'] = 1;
			$settings['show_author_name'] = 1;
			$settings['show_author_des'] = 1;
			$settings['show_author_email'] = 1;
			$settings['show_photo_upload'] = 1;
			$settings['is_author_name_field_required'] = 1;
			$settings['is_author_designation_field_required'] = 1;
			$settings['is_author_email_field_required'] = 1;
			$settings['photo_upload_field_required'] = 1;

			$settings['testimonial_label'] = 'Testimonial';
			$settings['author_name_label'] = 'Author Name';
			$settings['author_designation_lable'] = 'Author Designation';
			$settings['author_photo_label'] = 'Upload Author Photo';
			$settings['author_photo_link_label'] = 'Click here to use gravatar.com photo avatar';
			$settings['author_email_label'] = 'Author Email';
			$settings['testimonial_order_label'] = 'Testimonial Order';
			$settings['captcha_label'] = 'Enter Captcha';
			$settings['new_captcha_label'] = 'Get New Captcha';
			$settings['status_label'] = 'Status';
			$settings['submit_label'] = 'Submit';
			$settings['required_field_error_msg'] = 'This field is required.';
			$settings['invalid_email_field_error_msg'] = 'Please enter a valid email.';
			$settings['invalid_photo_field_error_msg'] = 'Please upload valid file. Only .jpg,.jpeg,.png extensions are allowed.';
			$settings['invalid_captcha'] = 'Invalid captcha code.';
			$settings['success_msg'] = 'New testimonial submitted successfully.Admin will mordred soon.';
			$settings['error_msg'] = 'An error occurred while submitting testimonial.';
			$settings['auto_approve_testimonial'] = 0;
			$settings['notify_admin_new_testimonial'] = 1;
			$settings['email_subject'] = 'New testimonial received';
			$settings['email_From_name'] = get_bloginfo( 'name' );
			$settings['email_From'] = get_option( 'admin_email' );
			$settings['email_to'] = get_option( 'admin_email' );
			$settings['email_body'] = "Hello [admin_name],
                                        <br class='blank' />
                                        You have just received new testimonial, You can check it by visiting here [testimonial_moderate_edit_link_html]
                                        <br class='blank' />
                                        Thanks &amp; Regards
                                        <br class='blank' />
                                        [sitename]";

	if ( ! get_option( 'i13_default_form_options' ) ) {

		update_option( 'i13_default_form_options', $settings );
	}

			etr_easy_testimonial_add_access_capabilities();

}




function etr_add_best_testimonial_slider_admin_menu() {

	$hook_suffix_r_t_s = add_menu_page( __( 'Easy Testimonial Rotator', 'easy-testimonial-rotator' ), __( 'Easy Testimonial Rotator', 'easy-testimonial-rotator' ), 'etr_testimonial_slider_settings', 'best_testimonial_slider', 'etr_best_testimonial_slider_admin_options' );
	$hook_suffix_r_t_s = add_submenu_page( 'best_testimonial_slider', __( 'Slider Settings', 'easy-testimonial-rotator' ), __( 'Slider Settings', 'easy-testimonial-rotator' ), 'etr_testimonial_slider_settings', 'best_testimonial_slider', 'etr_best_testimonial_slider_admin_options' );
	$hook_suffix_r_t_s_1 = add_submenu_page( 'best_testimonial_slider', __( 'Form Settings', 'easy-testimonial-rotator' ), __( 'Form Settings', 'easy-testimonial-rotator' ), 'etr_testimonial_slider_form_settings', 'best_testimonial_slider_form_settings', 'etr_best_testimonial_slider_forms' );
	$hook_suffix_r_t_s_2 = add_submenu_page( 'best_testimonial_slider', __( 'Manage Testimonials', 'easy-testimonial-rotator' ), __( 'Manage Testimonials', 'easy-testimonial-rotator' ), 'etr_testimonial_slider_view_testimonials', 'best_testimonial_slider_testimonial_management', 'etr_best_testimonial_management' );
	$hook_suffix_r_t_s_3 = add_submenu_page( 'best_testimonial_slider', __( 'Preview Slider', 'easy-testimonial-rotator' ), __( 'Preview Slider', 'easy-testimonial-rotator' ), 'etr_testimonial_slider_preview', 'best_testimonial_slider_preview', 'etr_best_testimonial_preview_admin' );

	add_action( 'load-' . $hook_suffix_r_t_s, 'etr_best_testimonial_slider_admin_init' );
	add_action( 'load-' . $hook_suffix_r_t_s_1, 'etr_best_testimonial_slider_admin_init' );
	add_action( 'load-' . $hook_suffix_r_t_s_2, 'etr_best_testimonial_slider_admin_init' );
	add_action( 'load-' . $hook_suffix_r_t_s_3, 'etr_best_testimonial_slider_admin_init' );

}

function etr_best_testimonial_slider_admin_init() {

	$url = plugin_dir_url( __FILE__ );
	wp_enqueue_script( 'jquery.validate', $url . 'js/jquery.validate.js', array(), '1.0.12' );
	wp_enqueue_script( 'best-testimonial-slider', $url . 'js/best-testimonial-slider.js', array(), '1.0.12' );
	wp_enqueue_style( 'admin-css-responsive', plugins_url( '/css/admin-css-responsive.css', __FILE__ ), array(), '1.0.12' );
	wp_enqueue_style( 'best-testimonial-bx-cols-css', $url . 'css/best-testimonial-bx-cols-css.css', array(), '1.0.12' );
	wp_enqueue_style( 'best-testimonial-bx', $url . 'css/best-testimonial-bx.css', array(), '1.0.12' );
	etr_best_testimonial_slider_admin_scripts_init();

}


function etr_best_testimonial_slider_forms() {

	if ( ! current_user_can( 'etr_testimonial_slider_form_settings' ) ) {

		   wp_die( esc_html( __( 'Access Denied', 'easy-testimonial-rotator' ) ) );

	}

	   $url = plugin_dir_url( __FILE__ );

	if ( isset( $_POST['btnsave'] ) ) {

		if ( ! check_admin_referer( 'action_image_add_edit', 'add_edit_image_nonce' ) ) {

			wp_die( 'Security check fail' );
		}

		 global $wpdb;

		if ( isset( $_POST['btnsave'] ) ) {

			$_POST = stripslashes_deep( $_POST );

			$settings = array(

				'show_captcha' => isset( $_POST['show_captcha'] ) ? intval( $_POST['show_captcha'] ) : 0,
				'show_author_name' => isset( $_POST['show_author_name'] ) ? intval( $_POST['show_author_name'] ) : 1,
				'show_author_des' => isset( $_POST['show_author_des'] ) ? intval( $_POST['show_author_des'] ) : 0,
				'show_author_email' => isset( $_POST['show_author_email'] ) ? intval( $_POST['show_author_email'] ) : 0,
				'show_photo_upload' => isset( $_POST['show_photo_upload'] ) ? intval( $_POST['show_photo_upload'] ) : 0,
				'is_author_name_field_required' => isset( $_POST['is_author_name_field_required'] ) ? intval( $_POST['is_author_name_field_required'] ) : 0,
				'is_author_designation_field_required' => isset( $_POST['is_author_designation_field_required'] ) ? intval( $_POST['is_author_designation_field_required'] ) : 0,
				'is_author_email_field_required' => isset( $_POST['is_author_email_field_required'] ) ? intval( $_POST['is_author_email_field_required'] ) : 0,
				'photo_upload_field_required' => isset( $_POST['photo_upload_field_required'] ) ? intval( $_POST['photo_upload_field_required'] ) : 0,
				'testimonial_label' => isset( $_POST['testimonial_label'] ) ? sanitize_text_field( $_POST['testimonial_label'] ) : '',
				'author_name_label' => isset( $_POST['author_name_label'] ) ? sanitize_text_field( $_POST['author_name_label'] ) : '',
				'author_designation_lable' => isset( $_POST['author_designation_lable'] ) ? sanitize_text_field( $_POST['author_designation_lable'] ) : '',
				'author_photo_label' => isset( $_POST['author_photo_label'] ) ? sanitize_text_field( $_POST['author_photo_label'] ) : '',
				'author_photo_link_label' => isset( $_POST['author_photo_link_label'] ) ? sanitize_text_field( $_POST['author_photo_link_label'] ) : '',
				'author_email_label' => isset( $_POST['author_email_label'] ) ? sanitize_text_field( $_POST['author_email_label'] ) : '',
				'captcha_label' => isset( $_POST['captcha_label'] ) ? sanitize_text_field( $_POST['captcha_label'] ) : '',
				'new_captcha_label' => isset( $_POST['new_captcha_label'] ) ? sanitize_text_field( $_POST['new_captcha_label'] ) : '',
				'status_label' => isset( $_POST['status_label'] ) ? sanitize_text_field( $_POST['status_label'] ) : '',
				'submit_label' => isset( $_POST['submit_label'] ) ? sanitize_text_field( $_POST['submit_label'] ) : '',
				'required_field_error_msg' => isset( $_POST['required_field_error_msg'] ) ? sanitize_textarea_field( $_POST['required_field_error_msg'] ) : '',
				'invalid_email_field_error_msg' => isset( $_POST['invalid_email_field_error_msg'] ) ? sanitize_textarea_field( $_POST['invalid_email_field_error_msg'] ) : '',
				'invalid_photo_field_error_msg' => isset( $_POST['invalid_photo_field_error_msg'] ) ? sanitize_textarea_field( $_POST['invalid_photo_field_error_msg'] ) : '',
				'invalid_captcha' => isset( $_POST['invalid_captcha'] ) ? sanitize_textarea_field( $_POST['invalid_captcha'] ) : '',
				'success_msg' => isset( $_POST['success_msg'] ) ? sanitize_textarea_field( $_POST['success_msg'] ) : '',
				'error_msg' => isset( $_POST['error_msg'] ) ? sanitize_textarea_field( $_POST['error_msg'] ) : '',
				'auto_approve_testimonial' => isset( $_POST['auto_approve_testimonial'] ) ? intval( $_POST['auto_approve_testimonial'] ) : 0,
				'notify_admin_new_testimonial' => isset( $_POST['notify_admin_new_testimonial'] ) ? intval( $_POST['notify_admin_new_testimonial'] ) : 0,
				'email_subject' => isset( $_POST['email_subject'] ) ? sanitize_text_field( $_POST['email_subject'] ) : '',
				'email_From_name' => isset( $_POST['email_From_name'] ) ? sanitize_text_field( $_POST['email_From_name'] ) : '',
				'email_From' => isset( $_POST['email_From'] ) ? sanitize_email( $_POST['email_From'] ) : '',
				'email_to' => isset( $_POST['email_to'] ) ? sanitize_email( $_POST['email_to'] ) : '',
				'email_body' => isset( $_POST['email_body'] ) ? wp_kses_post( $_POST['email_body'] ) : '',

			);

			$settings = update_option( 'i13_default_form_options', $settings );

			$best_testimonial_slider_messages = array();
			$best_testimonial_slider_messages['type'] = 'succ';
			$best_testimonial_slider_messages['message'] = __( 'Form settings updated successfully.', 'easy-testimonial-rotator' );
			update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );

		}

		 $location = 'admin.php?page=best_testimonial_slider_form_settings';
				 wp_redirect( esc_url( $location ) );
		 exit;

	}

	   $settings = get_option( 'i13_default_form_options' );

	?>
<div style="width: 100%;">  
			<div style="float:left;width:100%;">
				<div class="wrap">
					<table><tr>
						<td>
							<div class="fb-like" data-href="https://www.facebook.com/i13websolution" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>
							<div id="fb-root"></div>
							  <script>(function(d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (d.getElementById(id)) return;
								js = d.createElement(s); js.id = id;
								js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2&appId=158817690866061&autoLogAppEvents=1';
								fjs.parentNode.insertBefore(js, fjs);
							  }(document, 'script', 'facebook-jssdk'));</script>
						</td> 
						<td>
							<a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
								<img id="help us for free plugin" height="30" width="90" src="<?php echo esc_url( plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ); ?>" border="0" alt="help us for free plugin" title="help us for free plugin">
							</a>
						</td>
						</tr>
					</table>
				 <span><h3 style="color: blue;"><a target="_blank" href="https://www.i13websolution.com/product/wordpress-easy-testimonial-slider-plugin/"><?php echo esc_html( __( 'UPGRADE TO PRO VERSION', 'easy-testimonial-rotator' ) ); ?></a></h3></span>
				   <div id="post-body-content" >
									
  
			   <?php

				 $messages = get_option( 'best_testimonial_slider_messages' );
				 $type = '';
				 $message = '';
				if ( isset( $messages['type'] ) && '' != $messages['type'] ) {

					 $type = $messages['type'];
					 $message = $messages['message'];

					 update_option( 'best_testimonial_slider_messages', array() );

				}

				if ( trim( $type ) == 'err' ) {
					echo "<div class='notice notice-error is-dismissible'><p>";
					echo esc_html( $message );
					echo '</p></div>';} else if ( trim( $type ) == 'succ' ) {
					echo "<div class='notice notice-success is-dismissible'><p>";
					echo esc_html( $message );
					echo '</p></div>';}

					?>
					  
				   <h2><?php echo esc_html( __( 'Form Settings', 'easy-testimonial-rotator' ) ); ?></h2>
					
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-2">
							
							<div id="post-body-content">
								<form method="post" action="" id="scrollersettiings" name="scrollersettiings" >
								 
								   <fieldset class="fieldsetAdmin">
											<legend><?php echo esc_html( __( 'Form Settings', 'easy-testimonial-rotator' ) ); ?></legend> 
									<div class="stuffbox" id="namediv" style="width:100%;">
										
										<table cellspacing="0" class="form-list" cellpadding="10">
											<tbody>
												
												<tr>
													<td class="label">
														<label for="show_author_name"><?php echo esc_html( __( 'Show Author Name Field ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="show_author_name" name="show_author_name" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_author_name'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_author_name'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												<tr id="tr_is_author_name_field_required" style="display:none">
													<td class="label">
														<label for="is_author_name_field_required"><?php echo esc_html( __( 'Author Name Field Is Required Field ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="is_author_name_field_required" name="is_author_name_field_required" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['is_author_name_field_required'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['is_author_name_field_required'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												
												<tr>
													<td class="label">
														<label for="show_author_des"><?php echo esc_html( __( 'Show Author Designation Field ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="show_author_des" name="show_author_des" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_author_des'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_author_des'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												<tr id="tr_is_author_designation_field_required" style="display:none">
													<td class="label">
														<label for="is_author_des_field_required"><?php echo esc_html( __( 'Author Designation Field Is Required Field ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="is_author_designation_field_required" name="is_author_designation_field_required" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['is_author_designation_field_required'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['is_author_designation_field_required'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												
												<tr>
													<td class="label">
														<label for="show_author_email"><?php echo esc_html( __( 'Show Author Email Field ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="show_author_email" name="show_author_email" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_author_email'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_author_email'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												<tr id="tr_is_author_email_field_required">
													<td class="label">
														<label for="is_author_email_field_required"><?php echo esc_html( __( 'Author Email Field Is Required Field ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="is_author_email_field_required" name="is_author_email_field_required" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['is_author_email_field_required'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['is_author_email_field_required'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												
												<tr>
													<td class="label">
														<label for="show_captcha"><?php echo esc_html( __( 'Protect Front-end Form with Captcha ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="show_captcha" name="show_captcha" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_captcha'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_captcha'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												 <tr>
													<td class="label">
														<label for="show_photo_upload"><?php echo esc_html( __( 'Show Photo Upload ? ', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
														<br/>
														<div style="font-size: 10px;font-weight: bold;margin-left: 10px;margin-top: 10px;">Note:- On front-end only gravatar.com Photo is allowed.</div>
													</td>
													<td class="value">
														<select id="show_photo_upload" name="show_photo_upload" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_photo_upload'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_photo_upload'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												<tr id="tr_photo_upload_field_required">
													<td class="label">
														<label for="photo_upload_field_required"><?php echo esc_html( __( 'Photo Field Is Required Field ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="photo_upload_field_required" name="photo_upload_field_required" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['photo_upload_field_required'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['photo_upload_field_required'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
										</table>
									</div>
								   </fieldset>         
								  <fieldset class="fieldsetAdmin">
											<legend><?php echo esc_html( __( 'Messages & Label Settings', 'easy-testimonial-rotator' ) ); ?></legend>  
									<div class="stuffbox" id="namediv" style="width:100%;">
										
										<table cellspacing="0" class="form-list" cellpadding="10">
											<tbody>
												<tr>
													<td class="label">
														<label for="testimonial_label"><?php echo esc_html( __( 'Testimonial Label', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														<input id="testimonial_label" value="<?php echo esc_attr( $settings['testimonial_label'] ); ?>" name="testimonial_label"  class="input-text" type="text" />           
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												 <tr>
													<td class="label">
														<label for="author_name_label"><?php echo esc_html( __( 'Author Name Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="author_name_label" value="<?php echo esc_attr( $settings['author_name_label'] ); ?>" name="author_name_label"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 <tr>
													<td class="label">
														<label for="author_designation_lable"><?php echo esc_html( __( 'Author Designation Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="author_designation_lable" value="<?php echo esc_attr( $settings['author_designation_lable'] ); ?>" name="author_designation_lable"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 <tr>
													<td class="label">
														<label for="author_photo_label"><?php echo esc_html( __( 'Author Photo Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="author_photo_label" value="<?php echo esc_attr( $settings['author_photo_label'] ); ?>" name="author_photo_label"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 <tr>
													<td class="label">
														<label for="author_photo_link_label"><?php echo esc_html( __( 'Author Photo Link Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="author_photo_link_label" value="<?php echo esc_attr( $settings['author_photo_link_label'] ); ?>" name="author_photo_link_label"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 
												 
												 <tr>
													<td class="label">
														<label for="author_email_label"><?php echo esc_html( __( 'Author Email Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="author_email_label" value="<?php echo esc_attr( $settings['author_email_label'] ); ?>" name="author_email_label"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 
												 
												 
												 <tr>
													<td class="label">
														<label for="captcha_label"><?php echo esc_html( __( 'Captcha Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="captcha_label" value="<?php echo esc_attr( $settings['captcha_label'] ); ?>" name="captcha_label"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 <tr>
													<td class="label">
														<label for="new_captcha_label"><?php echo esc_html( __( 'New Captcha Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="new_captcha_label" value="<?php echo esc_attr( $settings['new_captcha_label'] ); ?>" name="new_captcha_label"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 <tr>
													<td class="label">
														<label for="status_label"><?php echo esc_html( __( 'Status Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="status_label" value="<?php echo esc_attr( $settings['status_label'] ); ?>" name="status_label"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 <tr>
													<td class="label">
														<label for="submit_label"><?php echo esc_html( __( 'Submit Label', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <input id="submit_label" value="<?php echo esc_attr( $settings['submit_label'] ); ?>" name="submit_label"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 <tr>
													<td class="label">
														<label for="required_field_error_msg"><?php echo esc_html( __( 'Required Field Error Message', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<textarea cols="50" id="required_field_error_msg"  name="required_field_error_msg"  class="input-text" type="text" ><?php echo esc_attr( $settings['required_field_error_msg'] ); ?></textarea>            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 
												 
												 <tr>
													<td class="label">
														<label for="invalid_email_field_error_msg"><?php echo esc_html( __( 'Invalid Email Field Error Message', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														 <textarea cols="50" id="invalid_email_field_error_msg"  name="invalid_email_field_error_msg"  class="input-text" type="text" ><?php echo esc_attr( $settings['invalid_email_field_error_msg'] ); ?></textarea>            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 <tr>
													<td class="label">
														<label for="invalid_photo_field_error_msg"><?php echo esc_html( __( 'Invalid Photo Field Error Message', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<textarea cols="50" id="invalid_photo_field_error_msg"  name="invalid_photo_field_error_msg"  class="input-text" type="text" ><?php echo esc_attr( $settings['invalid_photo_field_error_msg'] ); ?></textarea> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 
												 <tr>
													<td class="label">
														<label for="invalid_captcha"><?php echo esc_html( __( 'Invalid Captcha Message', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<textarea cols="50" id="invalid_captcha"  name="invalid_captcha"  class="input-text" type="text" ><?php echo esc_attr( $settings['invalid_captcha'] ); ?></textarea> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 
												 <tr>
													<td class="label">
														<label for="success_msg"><?php echo esc_html( __( 'Success Message', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<textarea cols="50" id="success_msg"  name="success_msg"  class="input-text" type="text" ><?php echo esc_attr( $settings['success_msg'] ); ?></textarea> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 
												 <tr>
													<td class="label">
														<label for="error_msg"><?php echo esc_html( __( 'Error Message', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<textarea cols="50" id="error_msg"  name="error_msg"  class="input-text" type="text" ><?php echo esc_html( $settings['error_msg'] ); ?></textarea> 
													   <div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 
												<tr>
													<td class="label">
														
													 <?php wp_nonce_field( 'action_image_add_edit', 'add_edit_image_nonce' ); ?>
													  
													</td>
													<td class="value">
													 <?php if ( isset( $_GET['id'] ) && intval( $_GET['id'] ) > 0 ) { ?> 
															<input type="hidden" name="sliderid" id="sliderid" value="<?php echo intval( $_GET['id'] ); ?>" />
															<?php
													 }
														?>
														  
													   
													</td>
												</tr>
											</tbody>
										</table>                                    
									</div>
								  </fieldset>
									<fieldset class="fieldsetAdmin">
											<legend><?php echo esc_html( __( 'Testimonial moderation & email settings', 'easy-testimonial-rotator' ) ); ?></legend> 
									<div class="stuffbox" id="namediv" style="width:100%;">
										<style>
											#namediv input {
													width: auto;
												}
										</style>    
										<table cellspacing="0" class="form-list-email" cellpadding="10">
											<tbody>
												
												<tr>
													<td class="label">
														<label for="auto_approve_testimonial"><?php echo esc_html( __( 'Auto Approve Testimonial ?', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="auto_approve_testimonial" name="auto_approve_testimonial" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['auto_approve_testimonial'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['auto_approve_testimonial'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												<tr>
													<td class="label">
														<label for="notify_admin_new_testimonial"><?php echo esc_html( __( 'Notify Admin For New Testimonial Received', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														 <select id="notify_admin_new_testimonial" name="notify_admin_new_testimonial" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['notify_admin_new_testimonial'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['notify_admin_new_testimonial'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												<tr id="tr_email_subject">
													<td class="label">
														<label for="email_subject"><?php echo esc_html( __( 'Email Subject', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														<input style="width:450px" id="email_subject" value="<?php echo esc_attr( $settings['email_subject'] ); ?>" name="email_subject"  class="input-text" type="text" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												<tr id="tr_email_From_name">
													<td class="label">
														<label for="email_From_name"><?php echo esc_html( __( 'Email From Name', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														<input style="width:450px" id="email_From_name" value="<?php echo esc_attr( $settings['email_From_name'] ); ?>" name="email_From_name"  class="input-text" type="text" />            
														<div style="clear:both"><?php echo esc_html( __( '(ex. admin)', 'easy-testimonial-rotator' ) ); ?></div>
														<div class="error_label"></div> 
														 
													</td>
												</tr>
												<tr id="tr_email_From">
													<td class="label">
														<label for="email_From"><?php echo esc_html( __( 'Email From', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														<input style="width:450px" id="email_From" value="<?php echo esc_attr( $settings['email_From'] ); ?>" name="email_From"  class="input-text" type="text" />            
														<div style="clear:both"><?php echo esc_html( __( '(ex. admin@yoursite.com)', 'easy-testimonial-rotator' ) ); ?></div>
														<div class="error_label"></div> 
														 
													</td>
												</tr>
												<tr id="tr_email_to">
													<td class="label">
														<label for="email_to"><?php echo esc_html( __( 'Email To', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														<input style="width:450px" id="email_to" value="<?php echo esc_attr( $settings['email_to'] ); ?>" name="email_to"  class="input-text" type="text" />            
														<div style="clear:both"><?php echo esc_html( __( '(ex. admin@yoursite.com)', 'easy-testimonial-rotator' ) ); ?></div>
														<div class="error_label"></div> 
														 
													</td>
												</tr>
												<tr id="tr_email_body">
													<td class="label">
														<label for="email_body"><?php echo esc_html( __( 'Email Body', 'easy-testimonial-rotator' ) ); ?> <span class="required">*</span></label>
													</td>
													<td class="value">
														<div class="wrap">
														<?php
														// wp_editor('',"email_body", array('textarea_rows'=>12, 'editor_class'=>'ckeditor'));
														wp_editor( $settings['email_body'], 'email_body' );

														?>
														 
															
														 <input type="hidden" name="editor_val" id="editor_val" /> 
														<div style="clear:both"></div>
														<div class="error_label"></div> 
														<br/><?php echo esc_html( __( 'You can use', 'easy-testimonial-rotator' ) ); ?> [admin_name],[admin_email],[testimonial_moderate_edit_link_plain],<br/> [testimonial_moderate_edit_link_html] <br/> [sitename] <?php echo esc_html( __( 'place holder into email content', 'easy-testimonial-rotator' ) ); ?> 
														 </div>
													</td>
												</tr>
										</table>
									</div>
									</fieldset>        
									 <input type="submit"  name="btnsave" id="btnsave" value="<?php echo esc_html( __( 'Save Changes', 'easy-testimonial-rotator' ) ); ?>" class="button-primary" />&nbsp;
									 <input type="button" name="cancle" id="cancle" value="<?php echo esc_html( __( 'Cancel', 'easy-testimonial-rotator' ) ); ?>" class="button-primary" onclick="location.href='admin.php?page=best_testimonial_slider'" />
  
								</form>
								<script type="text/javascript">

									  
								   
									jQuery(document).ready(function() {
											
									   jQuery.validator.addMethod("chkCont", function(value, element) {
											
											  
											   
											  if(jQuery("#notify_admin_new_testimonial").val()=='1'){
												var editorcontent=tinyMCE.get('email_body').getContent();
												
												if (editorcontent.length){
												  return true;
												}
												else{
												   return false;
												}
										   }
										   else{
											   
											   return false;
										   }

										  },
											   "Please enter email content"
									   );
							   
										jQuery( "#show_author_name" ).change(function() {

												if(jQuery("#show_author_name").val()=="1"){

													 jQuery("#tr_is_author_name_field_required").show();

												 }
												 else{

													 jQuery("#tr_is_author_name_field_required").hide();

												 }

											  });

											   jQuery( "#show_author_des" ).change(function() {

												 if(jQuery("#show_author_des").val()=="1"){

													 jQuery("#tr_is_author_designation_field_required").show();

												 }
												 else{

													 jQuery("#tr_is_author_designation_field_required").hide();

												 }

											  });


											   jQuery( "#show_author_email" ).change(function() {

												 if(jQuery("#show_author_email").val()=="1"){

													 jQuery("#tr_is_author_email_field_required").show();

												 }
												 else{

													 jQuery("#tr_is_author_email_field_required").hide();

												 }

											  });


											   jQuery( "#show_photo_upload" ).change(function() {

												 if(jQuery("#show_photo_upload").val()=="1"){

													 jQuery("#tr_photo_upload_field_required").show();

												 }
												 else{

													 jQuery("#tr_photo_upload_field_required").hide();

												 }

											  });

											   
											jQuery( "#show_author_name" ).trigger('change');
											jQuery( "#show_author_des" ).trigger('change');
											jQuery( "#show_author_email" ).trigger('change');
											jQuery( "#show_photo_upload" ).trigger('change');
											
											
											
											jQuery.validator.setDefaults({ 
												ignore: [],
												// any other default options and/or rules
											});
											
											jQuery("#scrollersettiings").validate({
													rules: {
														show_author_name: {
															required:true
															
														}, 
														is_author_name_field_required: {
															required:true
															
														}, 
														show_author_des: {
															required:true
															
														}, 
														is_author_designation_field_required: {
															required:true
															
														}, 
														show_author_email: {
															required:true
															
														}, 
														is_author_email_field_required: {
															required:true
															
														}, 
													   
														show_captcha: {
															required:true
															
														}, 
														show_photo_upload: {
															required:true
															
														}, 
														photo_upload_field_required: {
															required:true
															
														}, 
														testimonial_label: {
															required:true,
															maxlength:200
														}, 
														author_name_label: {
															required:true,
															maxlength:200
														
														}, 
														author_designation_lable: {
															required:true,
															maxlength:200
														
														}, 
														author_photo_label: {
															required:true,
															maxlength:200
														
														}, 
														author_photo_link_label: {
															required:true,
															maxlength:200
														
														}, 
														author_email_label: {
															required:true,
															maxlength:200
														},
														captcha_label: {
															required:true,
															maxlength:200
														},
														new_captcha_label: {
															required:true,
															maxlength:200
														},
														status_label: {
															required:true,
															maxlength:200
														},
														submit_label: {
															required:true,
															maxlength:200
														},
														required_field_error_msg: {
															required:true,
															maxlength:1000
														},
														invalid_email_field_error_msg: {
															required:true,
															maxlength:1000
														},
														invalid_photo_field_error_msg: {
															required:true,
															maxlength:1000
														},
														invalid_captcha: {
															required:true,
															maxlength:1000
														},
														success_msg: {
															required:true,
															maxlength:1000
														},
														 error_msg: {
															required:true,
															maxlength:1000
														},
														 auto_approve_testimonial: {
															required:true,
															maxlength:10
														},
														 notify_admin_new_testimonial: {
															required:true,
															maxlength:10
														},
														email_subject: {
															 required: function(element) {
																return jQuery("#notify_admin_new_testimonial").val()=='1';
															  },
															 maxlength:200
														},
														 email_From_name: {
															 required: function(element) {
																return jQuery("#notify_admin_new_testimonial").val()=='1';
															  },
															 maxlength:200
														},
														 email_From: {
															 required: function(element) {
																return jQuery("#notify_admin_new_testimonial").val()=='1';
															  },
															 maxlength:200,
															 email:true
														},
														 email_to: {
															 required: function(element) {
																return jQuery("#notify_admin_new_testimonial").val()=='1';
															  },
															 maxlength:200,
															 email:true
														},
														 editor_val: {
															
															chkCont: true
														 }
														 
													 

													},
													errorClass: "image_error",
													errorPlacement: function(error, element) {
														jQuery(element).closest('td').find('.error_label').html(error);


													} 


											});
											
											jQuery( "#notify_admin_new_testimonial" ).change(function() {

												 if(jQuery("#notify_admin_new_testimonial").val()=="1"){

												  
											   

													 jQuery("#tr_email_subject").show();
													 jQuery("#tr_email_From_name").show();
													 jQuery("#tr_email_From").show();
													 jQuery("#tr_email_to").show();
													 jQuery("#tr_email_body").show();
													 
												   
														jQuery("#email_body-tmce").trigger('click');
														
														jQuery("#editor_val").rules('add', {
															chkCont: true
														});

														jQuery("#email_subject").rules('add', {
															required: function(element) {
																return jQuery("#notify_admin_new_testimonial").val()=='1';
																},
																maxlength:200
														});

														jQuery("#email_From_name").rules('add', {
															required: function(element) {
																return jQuery("#notify_admin_new_testimonial").val()=='1';
																},
																maxlength:200
														});

														jQuery("#email_From").rules('add', {
															required: function(element) {
																return jQuery("#notify_admin_new_testimonial").val()=='1';
																},
																maxlength:200,
																email:true
														});


														jQuery("#email_to").rules('add', {
															required: function(element) {
																return jQuery("#notify_admin_new_testimonial").val()=='1';
																},
																maxlength:200,
																email:true
														});
												   


												 }
												 else{

														jQuery("#tr_email_subject").hide();
														jQuery("#tr_email_From_name").hide();
														jQuery("#tr_email_From").hide();
														jQuery("#tr_email_to").hide();
														jQuery("#tr_email_body").hide();
													 
														  jQuery('#email_subject').rules('remove', 'required'); 
														  jQuery("#editor_val").rules('remove', 'chkCont');
														  jQuery("#email_subject").rules('remove', 'required');
														  jQuery("#email_From_name").rules('remove', 'required');
														  jQuery("#email_From").rules('remove', 'required');
														  jQuery("#email_From").rules('remove', 'email');
														  jQuery("#email_to").rules('remove', 'required');
														  jQuery("#email_to").rules('remove', 'email');


												 }

											  });
											  
										   jQuery( "#notify_admin_new_testimonial" ).trigger('change');   
											  
									});

								</script> 
								</div>   
							<div id="postbox-container-1" class="postbox-container"> 
									<div class="postbox"> 
										<h3 class="hndle"><span></span><?php echo esc_html( __( 'Access All Themes One price', 'easy-testimonial-rotator' ) ); ?></h3> 
										<div class="inside">
											<center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="<?php echo esc_url( plugins_url( 'images/300x250.gif', __FILE__ ) ); ?>" width="250" height="250"></a></center>

											<div style="margin:10px 5px">

											</div>
										</div></div>

									 <div class="postbox"> 
										<h3 class="hndle"><span></span><?php echo esc_html( __( 'Google For Business Coupon', 'easy-testimonial-rotator' ) ); ?></h3> 
											<div class="inside">
												<center><a href="https://goo.gl/OJBuHT" target="_blank">
														<img src="<?php echo esc_url( plugins_url( 'images/g-suite-promo-code-4.png', __FILE__ ) ); ?>" width="250" height="250" border="0">
													</a></center>
												<div style="margin:10px 5px">
												</div>
											</div>

										</div>
								</div>    
						  </div>
								  
						</div>
					
					</div>  
					
				</div>      
			</div>
	<div class="clear"></div></div>
	<?php
}
function etr_best_testimonial_slider_admin_options() {

	$action = 'addedit';
	if ( isset( $_GET['action'] ) && '' != $_GET['action'] ) {

		 $action = sanitize_text_field( $_GET['action'] );
	}
	if ( strtolower( $action ) == strtolower( 'addedit' ) ) {

		if ( ! current_user_can( 'etr_testimonial_slider_settings' ) ) {

			   wp_die( esc_html( __( 'Access Denied', 'easy-testimonial-rotator' ) ) );

		}

		   $url = plugin_dir_url( __FILE__ );

		if ( isset( $_POST['btnsave'] ) ) {

			if ( ! check_admin_referer( 'action_image_add_edit', 'add_edit_image_nonce' ) ) {

				 wp_die( 'Security check fail' );
			}

			   $auto = isset( $_POST['auto'] ) ? sanitize_text_field( $_POST['auto'] ) : '';
			   $is_circular_slider = isset( $_POST['is_circular_slider'] ) ? sanitize_text_field( $_POST['is_circular_slider'] ) : '';
			   $speed = isset( $_POST['speed'] ) ? sanitize_text_field( $_POST['speed'] ) : '';
			   $pause = isset( $_POST['pause'] ) ? sanitize_text_field( $_POST['pause'] ) : '';
			   $box_border_color = isset( $_POST['box_border_color'] ) ? sanitize_hex_color( $_POST['box_border_color'] ) : '';
			   $box_border_size = isset( $_POST['box_border_size'] ) ? intval( $_POST['box_border_size'] ) : '';
			   $box_shadow_color = isset( $_POST['box_shadow_color'] ) ? sanitize_hex_color( $_POST['box_shadow_color'] ) : '';
			   $slider_back_color = isset( $_POST['slider_back_color'] ) ? sanitize_hex_color( $_POST['slider_back_color'] ) : '';
			   $is_adaptive_height = isset( $_POST['is_adaptive_height'] ) ? sanitize_text_field( $_POST['is_adaptive_height'] ) : '';
			   $show_arrows = isset( $_POST['show_arrows'] ) ? sanitize_text_field( $_POST['show_arrows'] ) : '';
			   $show_author_name = isset( $_POST['show_author_name'] ) ? sanitize_text_field( $_POST['show_author_name'] ) : '';
			   $show_author_des = isset( $_POST['show_author_des'] ) ? sanitize_text_field( $_POST['show_author_des'] ) : '';
			   $show_pagination = isset( $_POST['show_pagination'] ) ? sanitize_text_field( $_POST['show_pagination'] ) : '';
			   $touch_enabled = isset( $_POST['touch_enabled'] ) ? sanitize_text_field( $_POST['touch_enabled'] ) : '';
			   $resize_images = isset( $_POST['resize_images'] ) ? sanitize_text_field( $_POST['resize_images'] ) : '';

			   $options = array();
			   $options['auto'] = intval( $auto );
			   $options['is_circular_slider'] = intval( $is_circular_slider );
			   $options['speed'] = intval( $speed );
			   $options['pause'] = intval( $pause );
			   $options['box_border_color'] = $box_border_color;
			   $options['box_border_size'] = $box_border_size;
			   $options['box_shadow_color'] = $box_shadow_color;
			   $options['slider_back_color'] = $slider_back_color;
			   $options['is_adaptive_height'] = intval( $is_adaptive_height );
			   $options['show_arrows'] = intval( $show_arrows );
			   $options['show_author_name'] = intval( $show_author_name );
			   $options['show_author_des'] = intval( $show_author_des );
			   $options['show_pagination'] = intval( $show_pagination );
			   $options['touch_enabled'] = intval( $touch_enabled );
			   $options['resize_images'] = intval( $resize_images );

			   $settings = update_option( 'best_testimonial_options', $options );

			   $best_testimonial_slider_messages = array();
			   $best_testimonial_slider_messages['type'] = 'succ';
			   $best_testimonial_slider_messages['message'] = esc_html( __( 'Slider settings updated successfully.', 'easy-testimonial-rotator' ) );
			   update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );

			   $location = 'admin.php?page=best_testimonial_slider';
						   wp_redirect( esc_url( $location ) );
			   exit;

		}

		   $settings = get_option( 'best_testimonial_options' );
		if ( ! is_array( $settings ) ) {

			 $settings = array();
			 $settings['auto'] = null;
			 $settings['is_circular_slider'] = 1;
			 $settings['speed'] = 1000;
			 $settings['pause'] = 10000;
			 $settings['box_border_color'] = '#FFFFFF';
			 $settings['box_border_size'] = 5;
			 $settings['box_shadow_color'] = '#FFFFFF';
			 $settings['slider_back_color'] = '#FFFFFF';
			 $settings['is_adaptive_height'] = 1;
			 $settings['show_arrows'] = 1;
			 $settings['show_author_name'] = 1;
			 $settings['show_author_des'] = 1;
			 $settings['show_pagination'] = 1;
			 $settings['touch_enabled'] = 1;
			 $settings['resize_images'] = 1;

		}

		   // var_dump((int)$settings['auto']);die;

		?>
<div style="width: 100%;">  
			<div style="float:left;width:100%;">
				<div class="wrap">
					<table><tr>
						   <td>
							<div class="fb-like" data-href="https://www.facebook.com/i13websolution" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>
							<div id="fb-root"></div>
							  <script>(function(d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (d.getElementById(id)) return;
								js = d.createElement(s); js.id = id;
								js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2&appId=158817690866061&autoLogAppEvents=1';
								fjs.parentNode.insertBefore(js, fjs);
							  }(document, 'script', 'facebook-jssdk'));</script>
							</td> 
									<td>
										<a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
											<img id="help us for free plugin" height="30" width="90" src="<?php echo esc_url( plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ); ?>" border="0" alt="help us for free plugin" title="help us for free plugin">
										</a>
									</td>
								</tr>
							</table>
							 <span><h3 style="color: blue;"><a target="_blank" href="https://www.i13websolution.com/product/wordpress-easy-testimonial-slider-plugin/"><?php echo esc_html( __( 'UPGRADE TO PRO VERSION', 'easy-testimonial-rotator' ) ); ?></a></h3></span> 
				<?php

				 $messages = get_option( 'best_testimonial_slider_messages' );
				 $type = '';
				 $message = '';
				if ( isset( $messages['type'] ) && '' != $messages['type'] ) {

					  $type = $messages['type'];
					  $message = $messages['message'];

					  update_option( 'best_testimonial_slider_messages', array() );

				}
				if ( trim( $type ) == 'err' ) {
					echo "<div class='notice notice-error is-dismissible'><p>";
					echo esc_html( $message );
					echo '</p></div>';} else if ( trim( $type ) == 'succ' ) {
					 echo "<div class='notice notice-success is-dismissible'><p>";
					 echo esc_html( $message );
					 echo '</p></div>';}

					?>
				   <h2><?php echo esc_html( __( 'Slider Settings', 'easy-testimonial-rotator' ) ); ?></h2>
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="post-body-content" >
								<form method="post" action="" id="scrollersettiings" name="scrollersettiings" >
									<div class="stuffbox" id="namediv" style="width:100%;">
										<h3><label for="link_name"><?php echo esc_html( __( 'Settings', 'easy-testimonial-rotator' ) ); ?></label></h3>
										<table cellspacing="0" class="form-list" cellpadding="10">
											<tbody>
											   
												 <tr>
													<td class="label">
														<label for="auto"><?php echo esc_html( __( 'Auto Slide ?', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="auto" name="auto" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['auto'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['auto'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												
												 <tr>
													<td class="label">
														<label for="is_circular_slider"><?php echo esc_html( __( 'Is Circular Slider ?', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="is_circular_slider" name="is_circular_slider" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['is_circular_slider'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['is_circular_slider'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr> 
												 
												 <tr>
													<td class="label">
														<label for="speed"><?php echo esc_html( __( 'Speed', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<input id="speed" value="<?php echo esc_attr( $settings['speed'] ); ?>" name="speed"  class="input-text" type="text"  style="width:80px" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												 <tr>
													<td class="label">
														<label for="pause"><?php echo esc_html( __( 'Pause', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<input id="pause" value="<?php echo esc_attr( $settings['pause'] ); ?>" name="pause"  class="input-text" type="text"  style="width:80px" />            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												<tr>
													<td class="label">
														<label for="box_border_color"><?php echo esc_html( __( 'Box Border Color', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<input id="box_border_color" value="<?php echo esc_attr( $settings['box_border_color'] ); ?>" name="box_border_color"  class="input-text" type="text"  style="width:100px" />            
														<div style="clear:both"></div>
														<div class='error_label'></div>
													</td>
												</tr>
												<tr>
													<td class="label">
														<label for="box_border_size"><?php echo esc_html( __( 'Box Border Size', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<input id="box_border_size" value="<?php echo esc_attr( $settings['box_border_size'] ); ?>" name="box_border_size"  class="input-text" type="text"  style="width:100px" />            
														<div style="clear:both"></div>
														<div class='error_label'></div>
													</td>
												</tr>
												 <tr>
													<td class="label">
														<label for="box_shadow_color"><?php echo esc_html( __( 'Box Shadow Color', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<input id="box_shadow_color" value="<?php echo esc_attr( $settings['box_shadow_color'] ); ?>" name="box_shadow_color"  class="input-text" type="text"  style="width:100px" />            
														<div style="clear:both"></div>
														<div class='error_label'></div>
													</td>
												</tr>
												 <tr>
													<td class="label">
														<label for="slider_back_color"><?php echo esc_html( __( 'Slider Backgroud Color', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<input id="slider_back_color" value="<?php echo esc_attr( $settings['slider_back_color'] ); ?>" name="slider_back_color"  class="input-text" type="text"  style="width:100px" />            
														<div style="clear:both"></div>
														<div class='error_label'></div>
													</td>
												</tr>
												<tr>
													<td class="label">
														<label for="is_adaptive_height"><?php echo esc_html( __( 'Is Adaptive Height', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="is_adaptive_height" name="is_adaptive_height" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['is_adaptive_height'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['is_adaptive_height'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
													   <div class="error_label"></div> 
													</td>
												</tr>
												<tr>
													<td class="label">
														<label for="show_author_name"><?php echo esc_html( __( 'Show Author Name ?', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="show_author_name" name="show_author_name" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_author_name'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_author_name'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
													   <div class="error_label"></div> 
													</td>
												</tr>
												 <tr id="show_author_des">
													<td class="label">
														<label for="show_author_des"><?php echo esc_html( __( 'Show Author Designation ?', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="show_author_des" name="show_author_des" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_author_des'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_author_des'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												<tr>
													<td class="label">
														<label for="show_arrows"><?php echo esc_html( __( 'Show Arrows', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="show_arrows" name="show_arrows" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_arrows'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_arrows'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												 <tr>
													<td class="label">
														<label for="show_pagination"><?php echo esc_html( __( 'Show Pagination ?', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="show_pagination" name="show_pagination" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['show_pagination'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['show_pagination'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
														<div class="error_label"></div> 
													</td>
												</tr>
												 
												<tr>
													<td class="label">
														<label for="touch_enabled"><?php echo esc_html( __( 'Touch Enabled ?', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="touch_enabled" name="touch_enabled" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['touch_enabled'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['touch_enabled'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
													   <div class="error_label"></div> 
													</td>
												</tr>
											   <tr>
													<td class="label">
														<label for="resize_images"><?php echo esc_html( __( 'Resize Images ?', 'easy-testimonial-rotator' ) ); ?><span class="required">*</span></label>
													</td>
													<td class="value">
														<select id="resize_images" name="resize_images" class="select">
															<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '1' == $settings['resize_images'] ) :
																?>
  selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Yes', 'easy-testimonial-rotator' ) ); ?></option>
															<option 
															<?php
															if ( '0' == $settings['resize_images'] ) :
																?>
  selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'No', 'easy-testimonial-rotator' ) ); ?></option>
														</select>            
														<div style="clear:both"></div>
													   <div class="error_label"></div> 
													</td>
												</tr>
												<tr>
													<td class="label">
														
													 <?php wp_nonce_field( 'action_image_add_edit', 'add_edit_image_nonce' ); ?>
														<input type="submit"  name="btnsave" id="btnsave" value="<?php echo esc_html( __( 'Save Changes', 'easy-testimonial-rotator' ) ); ?>" class="button-primary" />    

													</td>
													<td class="value">
													 <?php if ( isset( $_GET['id'] ) && intval( $_GET['id'] ) > 0 ) { ?> 
															<input type="hidden" name="sliderid" id="sliderid" value="<?php echo intval( $_GET['id'] ); ?>" />
															<?php
													 }
														?>
														  
														<input type="button" name="cancle" id="cancle" value="<?php echo esc_html( __( 'Cancel', 'easy-testimonial-rotator' ) ); ?>" class="button-primary" onclick="location.href='admin.php?page=best_testimonial_slider'" />

													</td>
												</tr>
											</tbody>
										</table>                                    
									</div>

								</form>
								<script type="text/javascript">

									
									jQuery(document).ready(function() {
											
											jQuery('#box_border_color').wpColorPicker();
											jQuery('#box_shadow_color').wpColorPicker();
											jQuery('#slider_back_color').wpColorPicker();
											
											 jQuery('#box_border_size').spinner({
												min: 0,
												max: 40,
												step: 1
											});
											
										 
											jQuery.validator.setDefaults({ 
												ignore: [],
												// any other default options and/or rules
											});
											jQuery("#scrollersettiings").validate({
													rules: {
														auto: {
															required:true,
															number:true,
															maxlength:1
														},
														is_circular_slider: {
															required:true,
															number:true,
															maxlength:1
														}
														,speed: {
															required:true,
															digits:true,
															maxlength:11
														}
														,pause: {
															required:true,
															digits:true,
															maxlength:11
														},
														 box_border_color: {
															required:true, 
															 maxlength:10
														}
														,box_shadow_color: {
															required:true, 
															 maxlength:10
														},
														box_border_size:{
															required:true,
															number:true,
															maxlength:2
														},
														slider_back_color: {
															required:true, 
															 maxlength:10
														},
														is_adaptive_height:{
															required:true,  
															digits:true,
															maxlength:1

														},
														show_arrows:{
															required:true,  
															digits:true,
															maxlength:1

														}
														,touch_enabled:{
															required:true,
															digits:true,
															maxlength:1  
														},
														resize_images:{
															required:true,
															digits:true,
															maxlength:1  
														}
														
													 

													},
													errorClass: "image_error",
													errorPlacement: function(error, element) {
														jQuery(element).closest('td').find('.error_label').html(error);


													} 


											})
									});

								</script> 

							</div>
							<div id="postbox-container-1" class="postbox-container"> 
									<div class="postbox"> 
										<h3 class="hndle"><span></span><?php echo esc_html( __( 'Access All Themes One price', 'easy-testimonial-rotator' ) ); ?></h3> 
										<div class="inside">
											<center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="<?php echo esc_url( plugins_url( 'images/300x250.gif', __FILE__ ) ); ?>" width="250" height="250"></a></center>

											<div style="margin:10px 5px">

											</div>
										</div></div>

									 <div class="postbox"> 
										<h3 class="hndle"><span></span><?php echo esc_html( __( 'Google For Business Coupon', 'easy-testimonial-rotator' ) ); ?></h3> 
											<div class="inside">
												<center><a href="https://goo.gl/OJBuHT" target="_blank">
														<img src="<?php echo esc_url( plugins_url( 'images/g-suite-promo-code-4.png', __FILE__ ) ); ?>" width="250" height="250" border="0">
													</a></center>
												<div style="margin:10px 5px">
												</div>
											</div>

										</div>
								</div> 
						</div>
					</div>  
				</div>      
			</div>
	<div class="clear"></div></div>
		<?php
	}

}

function etr_best_testimonial_management() {

	$action = 'gridview';
	global $wpdb;

	if ( isset( $_GET['action'] ) && '' != $_GET['action'] ) {

		 $action = sanitize_text_field( $_GET['action'] );
	}

	if ( strtolower( $action ) == strtolower( 'gridview' ) ) {

		$wpcurrentdir = dirname( __FILE__ );
		$wpcurrentdir = str_replace( '\\', '/', $wpcurrentdir );

		if ( ! current_user_can( 'etr_testimonial_slider_view_testimonials' ) ) {

			  wp_die( esc_html( __( 'Access Denied', 'easy-testimonial-rotator' ) ) );

		}

		?>
	
	<div class="wrap">
		   <table><tr>
				   <td>
							<div class="fb-like" data-href="https://www.facebook.com/i13websolution" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>
							<div id="fb-root"></div>
							  <script>(function(d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (d.getElementById(id)) return;
								js = d.createElement(s); js.id = id;
								js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2&appId=158817690866061&autoLogAppEvents=1';
								fjs.parentNode.insertBefore(js, fjs);
							  }(document, 'script', 'facebook-jssdk'));</script>
						</td> 
							<td>
								<a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
									<img id="help us for free plugin" height="30" width="90" src="<?php echo esc_url( plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ); ?>" border="0" alt="help us for free plugin" title="help us for free plugin">
								</a>
							</td>
						</tr>
					</table>
							 <span><h3 style="color: blue;"><a target="_blank" href="https://www.i13websolution.com/product/wordpress-easy-testimonial-slider-plugin/"><?php echo esc_html( __( 'UPGRADE TO PRO VERSION', 'easy-testimonial-rotator' ) ); ?></a></h3></span>  
		  
		<?php

		  $messages = get_option( 'best_testimonial_slider_messages' );
		  $type = '';
		  $message = '';
		if ( isset( $messages['type'] ) && '' != $messages['type'] ) {

			  $type = $messages['type'];
			  $message = $messages['message'];

		}

		if ( trim( $type ) == 'err' ) {
			echo "<div class='notice notice-error is-dismissible'><p>";
			echo esc_html( $message );
			echo '</p></div>';} else if ( trim( $type ) == 'succ' ) {
			 echo "<div class='notice notice-success is-dismissible'><p>";
			 echo esc_html( $message );
			 echo '</p></div>';}

			 update_option( 'best_testimonial_slider_messages', array() );

			 $uploads = wp_upload_dir();
			 $baseDir = $uploads ['basedir'];
			 $baseDir = str_replace( '\\', '/', $baseDir );

			 $baseurl = $uploads['baseurl'];
			 $baseurl .= '/easy-testimonial-rotator/';
			 $pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';
			?>
	   <div style="width: 100%;">  
		<div style="float:left;width:100%;" >
		<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
		<h2><?php echo esc_html( __( 'Testimonials', 'easy-testimonial-rotator' ) ); ?> <a class="button add-new-h2" href="admin.php?page=best_testimonial_slider_testimonial_management&action=addedit"><?php echo esc_html( __( 'Add New', 'easy-testimonial-rotator' ) ); ?></a> </h2>
		<br/>    
		<form method="POST" action="admin.php?page=best_testimonial_slider_testimonial_management&action=deleteselected"  id="posts-filter" onkeypress="return event.keyCode != 13;">
			  <div class="alignleft actions">
				<select name="action_upper" id="action_upper">
					<option selected="selected" value="-1"><?php echo esc_html( __( 'Bulk Actions', 'easy-testimonial-rotator' ) ); ?></option>
					<option value="delete"><?php echo esc_html( __( 'Delete', 'easy-testimonial-rotator' ) ); ?></option>
				</select>
				<input type="submit" value="<?php echo esc_html( __( 'Apply', 'easy-testimonial-rotator' ) ); ?>" class="button-secondary action" id="deleteselected" name="deleteselected" onclick="return confirmDelete_bulk(document.getElementById('action_upper'));" />
			</div>
		 <br class="clear">
		   <?php

			global $wpdb;
			$settings = get_option( 'best_testimonial_options' );
			if ( ! is_array( $settings ) ) {

				$best_testimonial_slider_messages = array();
				$best_testimonial_slider_messages['type'] = 'err';
				$best_testimonial_slider_messages['message'] = esc_html( __( 'No such slider found', 'easy-testimonial-rotator' ) );
				update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
				$location = 'admin.php?page=best_testimonial_slider';
								wp_redirect( esc_url( $location ) );
				exit;

			}
			?>
	   <br/>
		<?php

		 $setacrionpage = 'admin.php?page=best_testimonial_slider_testimonial_management';

		if ( isset( $_GET['order_by'] ) && '' != $_GET['order_by'] ) {
			  $setacrionpage .= '&order_by=' . sanitize_text_field( $_GET['order_by'] );
		}

		if ( isset( $_GET['order_pos'] ) && '' != $_GET['order_pos'] ) {
			   $setacrionpage .= '&order_pos=' . sanitize_text_field( $_GET['order_pos'] );
		}

		?>
		<?php
		 global $wpdb;

		 $order_by = 'id';
		 $order_pos = 'asc';

		if ( isset( $_GET['order_by'] ) ) {

			 $order_by = sanitize_text_field( $_GET['order_by'] );
		}

		if ( isset( $_GET['order_pos'] ) ) {

			  $order_pos = sanitize_text_field( $_GET['order_pos'] );
		}
			  $search_term = '';
		if ( isset( $_GET['search_term'] ) ) {

			$search_term = isset( $_GET['search_term'] ) ? esc_sql( sanitize_text_field( $_GET['search_term'] ) ) : '';
		}
			  $seval = $search_term;

			  $search_term_ = '';
		if ( isset( $_GET['search_term'] ) ) {

			$search_term_ = '&search_term=' . sanitize_text_field( $_GET['search_term'] );
		}

		if ( '' != $search_term ) {

						$search_term_new = '%' . $search_term . '%';
			$rowsCount = $wpdb->get_var( $wpdb->prepare( 'SELECT count(*) FROM ' . esc_sql( $wpdb->prefix . 'b_testimo_slide ' ) . ' where id like %s or testimonial like %s  or auth_name like %s ', $search_term_new, $search_term_new, $search_term_new ) );

		} else {

			$rowsCount = $wpdb->get_var( $wpdb->prepare( 'SELECT count(*) FROM ' . esc_sql( $wpdb->prefix . 'b_testimo_slide ' ) ) );
		}

		?>
		   <div style="padding-top:5px;padding-bottom:5px">
			  <b><?php echo esc_html( __( 'Search', 'easy-testimonial-rotator' ) ); ?> : </b>
				<input type="text" value="<?php echo esc_attr( $seval ); ?>" id="search_term" name="search_term">&nbsp;
				<input type='button'  value='<?php echo esc_attr( __( 'Search', 'easy-testimonial-rotator' ) ); ?>' name='searchusrsubmit' class='button-primary' id='searchusrsubmit' onclick="SearchredirectTO();" >&nbsp;
				<input type='button'  value='<?php echo esc_attr( __( 'Reset Search', 'easy-testimonial-rotator' ) ); ?>' name='searchreset' class='button-primary' id='searchreset' onclick="ResetSearch();" >
		  </div>  
		  <script type="text/javascript" >
			 
			  jQuery('#search_term').on("keyup", function(e) {
					 if (e.which == 13) {

						 SearchredirectTO();
					 }
				});   
		   function SearchredirectTO(){
			 var redirectto='<?php echo esc_url( $setacrionpage ); ?>';
			 var searchval=jQuery('#search_term').val();
			 redirectto=redirectto+'&search_term='+jQuery.trim(encodeURIComponent(searchval));  
			 window.location.href=redirectto;
		   }
		  function ResetSearch(){

			   var redirectto='<?php echo esc_url( $setacrionpage ); ?>';
			   window.location.href=redirectto;
			   exit;
		  }
		  </script>  
		<div id="no-more-tables">
			<table cellspacing="0" id="gridTbl" class="table-bordered table-striped table-condensed cf" style="">
			<thead>
			
			   <tr>
				 <th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
				 
			  <?php if ( 'id' == $order_by && 'asc' == $order_pos ) : ?>
					  <th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=id&order_pos=desc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Id', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/desc.png', __FILE__ ) ); ?>"/></a></th>
				 <?php else : ?>
					 <?php if ( 'id' == $order_by ) : ?>
				 <th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=id&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Id', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/asc.png', __FILE__ ) ); ?>"/></a></th>
					 <?php else : ?>
						 <th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=id&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Id', 'easy-testimonial-rotator' ) ); ?></a></th>
					 <?php endif; ?>    
				 <?php endif; ?> 
				 
			   <?php if ( 'auth_name' == $order_by && 'asc' == $order_pos ) : ?>
						<th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=auth_name&order_pos=desc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Author Name', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/desc.png', __FILE__ ) ); ?>"/></a></th>
				   <?php else : ?>
					   <?php if ( 'auth_name' == $order_by ) : ?>
				   <th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=auth_name&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Author Name', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/asc.png', __FILE__ ) ); ?>"/></a></th>
					   <?php else : ?>
						   <th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=auth_name&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Author Name', 'easy-testimonial-rotator' ) ); ?></a></th>
					   <?php endif; ?>    
				   <?php endif; ?> 

			   <?php if ( 'testimonial' == $order_by && 'asc' == $order_pos ) : ?>
						<th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=testimonial&order_pos=desc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Testimonial', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/desc.png', __FILE__ ) ); ?>"/></a></th>
				   <?php else : ?>
					   <?php if ( 'testimonial' == $order_by ) : ?>
				   <th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=testimonial&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Testimonial', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/asc.png', __FILE__ ) ); ?>"/></a></th>
					   <?php else : ?>
						   <th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=testimonial&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Testimonial', 'easy-testimonial-rotator' ) ); ?></a></th>
					   <?php endif; ?>    
				   <?php endif; ?> 
						   
				 
				 <th style=""  scope="col"><span></span></th>
				 
			  <?php if ( 'createdon' == $order_by && 'asc' == $order_pos ) : ?>
						<th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=createdon&order_pos=desc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Submitted On', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/desc.png', __FILE__ ) ); ?>"/></a></th>
				 <?php else : ?>
					 <?php if ( 'createdon' == $order_by ) : ?>
				<th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=createdon&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Submitted On', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/asc.png', __FILE__ ) ); ?>"/></a></th>
					<?php else : ?>
						<th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=createdon&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Submitted On', 'easy-testimonial-rotator' ) ); ?></a></th>
					<?php endif; ?>    
				<?php endif; ?> 

			   <?php if ( 'status' == $order_by && 'asc' == $order_pos ) : ?>
						<th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=status&order_pos=desc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Status', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/desc.png', __FILE__ ) ); ?>"/></a></th>
				 <?php else : ?>
					 <?php if ( 'status' == $order_by ) : ?>
				<th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=status&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Status', 'easy-testimonial-rotator' ) ); ?><img style="vertical-align:middle" src="<?php echo esc_html( plugins_url( '/images/asc.png', __FILE__ ) ); ?>"/></a></th>
					<?php else : ?>
						<th><a href="<?php echo esc_url( $setacrionpage ); ?>&order_by=status&order_pos=asc<?php echo esc_html( $search_term_ ); ?>"><?php echo esc_html( __( 'Status', 'easy-testimonial-rotator' ) ); ?></a></th>
					<?php endif; ?>    
				<?php endif; ?> 

				 <th style=""  scope="col"><span><?php echo esc_html( __( 'Edit', 'easy-testimonial-rotator' ) ); ?></span></th>
				 <th style=""  scope="col"><span><?php echo esc_html( __( 'Delete', 'easy-testimonial-rotator' ) ); ?></span></th>
			  </tr>   
			  
			  </thead>
			<tbody id="the-list">
			 <?php

				if ( $rowsCount > 0 ) {

					global $wp_rewrite;
					$rows_per_page = 20;

					$current = ( isset( $_GET['paged'] ) ) ? ( (int) $_GET['paged'] ) : 1;
					$pagination_args = array(
						'base' => @add_query_arg( 'paged', '%#%' ),
						'format' => '',
						'total' => ceil( $rowsCount / $rows_per_page ),
						'current' => $current,
						'show_all' => false,
						'type' => 'plain',
					);

					$paged = 1;
					if ( isset( $_GET['paged'] ) && (int) $_GET['paged'] > 0 ) {

						$paged = (int) $_GET['paged'];

					}

					$offset = ( $current - 1 ) * $rows_per_page;

										 $order_by = sanitize_sql_orderby( $order_by );
										 $order_pos = sanitize_sql_orderby( $order_pos );

					if ( '' != $search_term ) {

													   $search_term_new = '%' . $search_term . '%';
							$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'b_testimo_slide  where id like %s or testimonial like %s  or auth_name like %s order by ' . esc_sql( $order_by ) . ' ' . esc_sql( $order_pos ) . ' limit %d,%d', $search_term_new, $search_term_new, $search_term_new, $offset, $rows_per_page ), ARRAY_A );

					} else {

						$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'b_testimo_slide  order by ' . esc_sql( $order_by ) . ' ' . esc_sql( $order_pos ) . ' limit %d,%d', $offset, $rows_per_page ), ARRAY_A );
					}

					$delRecNonce = wp_create_nonce( 'delete_image' );
					foreach ( $rows as $row ) {

						$id = $row['id'];
						$editlink = "admin.php?page=best_testimonial_slider_testimonial_management&action=addedit&id=$id&paged=$paged";
						$deletelink = "admin.php?page=best_testimonial_slider_testimonial_management&action=delete&id=$id&nonce=$delRecNonce";

						if ( '' != $row['image_name'] || null != $row['image_name'] ) {

							// $outputimg = $baseurl.$row['image_name'];

							 $imagename = $row['image_name'];
							 $imageUploadTo = $pathToImagesFolder . '/' . $imagename;
							 $imageUploadTo = str_replace( '\\', '/', $imageUploadTo );
							 $pathinfo = pathinfo( $imageUploadTo );
							 $filenamewithoutextension = $pathinfo['filename'];
							 $imageheight = 300;
							 $imagewidth = 300;
							 $outputimg = '';

							 $outputimgmain = $baseurl . $row['image_name'];
							if ( 0 == $settings['resize_images'] ) {

								   $outputimgmain = $baseurl . $row['image_name'];

							} else {

									  $imagetoCheck = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
									  $imagetoCheckSmall = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );

								if ( file_exists( $imagetoCheck ) ) {
									$outputimgmain = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];

								} else if ( file_exists( $imagetoCheckSmall ) ) {
									$outputimgmain = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );
								} else {

									if ( function_exists( 'wp_get_image_editor' ) ) {

											$image = wp_get_image_editor( $pathToImagesFolder . '/' . $row['image_name'] );
										if ( ! is_wp_error( $image ) ) {
											$image->resize( $imagewidth, $imageheight, true );
											$image->save( $imagetoCheck );
											// $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

											if ( file_exists( $imagetoCheck ) ) {
												$outputimgmain = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
											} else if ( file_exists( $imagetoCheckSmall ) ) {
												$outputimgmain = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );
											}
										} else {
											$outputimgmain = $baseurl . $row['image_name'];
										}
									} else {

													$outputimgmain = $baseurl . $row['image_name'];
									}

										 // $url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

								}
							}
						} else if ( '' != $row['gravatar_email'] && null != $row['gravatar_email'] ) {

							$email = md5( $row['gravatar_email'] );
							$outputimgmain = "https://www.gravatar.com/avatar/$email?s=200";
						} else {

							$outputimgmain = plugins_url( 'images/no_photo.png', __FILE__ );
						}

						if ( 1 == $row['status'] ) {
							$status_val = esc_html( __( 'Published', 'easy-testimonial-rotator' ) );
						} else {
							$status_val = esc_html( __( 'Draft', 'easy-testimonial-rotator' ) );
						}
						?>
						 <tr valign="top" class="alternate author-self status-publish format-default iedit" id="post-113">
							<td  data-title="<?php echo esc_attr( __( 'Select Record', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter check-column" ><input type="checkbox" value="<?php echo intval( $row['id'] ); ?>" name="thumbnails[]" /></td>
							<td  data-title="<?php echo esc_attr( __( 'Id', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter" ><?php echo intval( $row['id'] ); ?></td>
							<td  data-title="<?php echo esc_attr( __( 'Author Name', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter" ><?php echo esc_html( $row['auth_name'] ); ?></td>
							<td  data-title="<?php echo esc_attr( __( 'Testimonial', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter"><strong><?php echo esc_html( $row['testimonial'] ); ?></strong></td>  
							<td  data-title="<?php echo esc_attr( __( 'Image', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter">
							  <img src="<?php echo esc_url( $outputimgmain ); ?>" style="width:50px" height="50px"/>
							</td> 
							<td   data-title="<?php echo esc_html( __( 'Submitted On', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter"><?php echo esc_html( $row['createdon'] ); ?></td>
							<td   data-title="<?php echo esc_html( __( 'Status', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter"><strong><?php echo esc_html( $status_val ); ?></strong></td>  
							<td   data-title="<?php echo esc_html( __( 'Edit', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter"><strong><a href='<?php echo esc_attr( esc_url( $editlink ) ); ?>' title="<?php echo esc_attr( __( 'Edit', 'easy-testimonial-rotator' ) ); ?>"><?php echo esc_html( __( 'Edit', 'easy-testimonial-rotator' ) ); ?></a></strong></td>  
							<td   data-title="<?php echo esc_html( __( 'Delete', 'easy-testimonial-rotator' ) ); ?>" class="alignCenter"><strong><a href='<?php echo esc_attr( esc_url( $deletelink ) ); ?>' onclick="return confirmDelete();"  title="<?php echo esc_attr( __( 'Delete', 'easy-testimonial-rotator' ) ); ?>"><?php echo esc_html( __( 'Delete', 'easy-testimonial-rotator' ) ); ?></a> </strong></td>  
					   </tr>
						<?php
					}
				} else {
					?>

				  <tr valign="top"  id="">
							<td colspan="10" data-title="No Record" align="center"><strong><?php echo esc_html( __( 'No Testimonials Found', 'easy-testimonial-rotator' ) ); ?></strong></td>  
				   </tr>
					<?php
				}
				?>
					  
			</tbody>
		</table>
		</div>
		<?php
		if ( $rowsCount > 0 ) {
			echo "<div class='pagination' style='padding-top:10px'>";
			echo wp_kses_post( paginate_links( $pagination_args ) );
			echo '</div>';
		}
		?>
	<br/>
	<div class="alignleft actions">
		<select name="action" id="action_bottom">
			<option selected="selected" value="-1"><?php echo esc_html( __( 'Bulk Actions', 'easy-testimonial-rotator' ) ); ?></option>
			<option value="delete"><?php echo esc_html( __( 'Delete', 'easy-testimonial-rotator' ) ); ?></option>
		</select>
		
		<?php wp_nonce_field( 'action_settings_mass_delete', 'mass_delete_nonce' ); ?>
		<input type="submit" value="<?php echo esc_html( __( 'Apply', 'easy-testimonial-rotator' ) ); ?>" class="button-secondary action" id="deleteselected" name="deleteselected"  onclick="return confirmDelete_bulk(document.getElementById('action_bottom'));"/>
	</div>

	</form>
		<script type="text/JavaScript">

			function  confirmDelete(){
			var agree=confirm("<?php echo esc_html( __( 'Are you sure you want to delete this testimonial ?', 'easy-testimonial-rotator' ) ); ?>");
			if (agree)
				 return true ;
			else
				 return false;
		}
		
		function  confirmDelete_bulk(elemnt){
			var topval=document.getElementById("action_bottom").value;
			var bottomVal=document.getElementById("action_upper").value;
			 
			 if(elemnt.value.toString()=='-1'){

				 return;
			 }
				if(jQuery('[name="thumbnails[]"]:checked').length > 0){

					if(topval=='delete' || bottomVal=='delete'){


						var agree=confirm("<?php echo esc_html( __( 'Are you sure you want to delete selected testimonials.', 'easy-testimonial-rotator' ) ); ?>");
						if (agree)
							return true ;
						else
							return false;
					}
				   }else{

					   alert('<?php echo esc_html( __( 'Please select atleast one record to delete.', 'easy-testimonial-rotator' ) ); ?>');
					   return false;
				   }
		}
	 </script>

		<br class="clear">
		</div>
		<div style="clear: both;"></div>
		<?php $url = plugin_dir_url( __FILE__ ); ?>
	  </div>  
	</div>  

	<h3><?php echo esc_html( __( 'To print this slider into WordPress Post/Page use below code', 'easy-testimonial-rotator' ) ); ?></h3>
	<input type="text" value='[print_best_testimonial_slider] ' style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
	<div class="clear"></div>
	<h3><?php echo esc_html( __( 'To print this slider into WordPress theme/template PHP files use below code', 'easy-testimonial-rotator' ) ); ?></h3>
		<?php
		$shortcode = '[print_best_testimonial_slider]';
		?>
	<input type="text" value="&lt;?php echo do_shortcode('<?php echo esc_html( $shortcode ); ?>'); ?&gt;" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
	   
	<div class="clear"></div>
	
	<h3><?php echo esc_html( __( 'To print form for this slider into WordPress Post/Page use below code', 'easy-testimonial-rotator' ) ); ?></h3>
	<input type="text" value='[print_best_testimonial_form ] ' style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
	<div class="clear"></div>
	<h3><?php echo esc_html( __( 'To print form for this slider into WordPress theme/template PHP files use below code', 'easy-testimonial-rotator' ) ); ?></h3>
		<?php
		$shortcode = '[print_best_testimonial_form]';
		?>
	<input type="text" value="&lt;?php echo do_shortcode('<?php echo esc_html( $shortcode ); ?>'); ?&gt;" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
	<div class="clear"></div>
		<?php
	} else if ( strtolower( $action ) == strtolower( 'addedit' ) ) {
		$url = plugin_dir_url( __FILE__ );
		$paged = 1;
		if ( isset( $_POST['paged'] ) ) {
			$paged = intval( $_GET['paged'] );
		}
		?>
		 <?php
			if ( isset( $_POST['btnsave'] ) ) {

				if ( ! check_admin_referer( 'action_image_add_edit', 'add_edit_image_nonce' ) ) {

					wp_die( 'Security check fail' );
				}

				$uploads = wp_upload_dir();
				$baseDir = $uploads ['basedir'];
				$baseDir = str_replace( '\\', '/', $baseDir );
				$pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';
				// edit save

				$testimonial = '';
				if ( isset( $_POST['testimonial'] ) && '' != $_POST['testimonial'] ) {
					$testimonial = trim( htmlentities( sanitize_textarea_field( $_POST['testimonial'] ), ENT_QUOTES ) );
				}
				$auth_name = '';
				if ( isset( $_POST['auth_name'] ) && '' != $_POST['auth_name'] ) {

					$auth_name = trim( htmlentities( sanitize_text_field( $_POST['auth_name'] ), ENT_QUOTES ) );
				}
				$auth_desn = '';
				if ( isset( $_POST['auth_desn'] ) && '' != $_POST['auth_desn'] ) {

					$auth_desn = trim( htmlentities( sanitize_text_field( $_POST['auth_desn'] ), ENT_QUOTES ) );
				}

				$auth_email = '';
				if ( isset( $_POST['auth_email'] ) && '' != $_POST['auth_email'] ) {

					$auth_email = trim( htmlentities( sanitize_email( $_POST['auth_email'] ), ENT_QUOTES ) );
				}

				$gravatar_email = '';
				if ( isset( $_POST['HdnMediaGrevEmail'] ) && '' != $_POST['HdnMediaGrevEmail'] ) {

					$gravatar_email = trim( htmlentities( sanitize_email( $_POST['HdnMediaGrevEmail'] ), ENT_QUOTES ) );

				}

				$status = 0;
				if ( isset( $_POST['status'] ) && '' != $_POST['status'] ) {

					$status = (int) trim( htmlentities( sanitize_text_field( $_POST['status'] ), ENT_QUOTES ) );

				}

				if ( isset( $_POST['imageid'] ) ) {

					if ( ! current_user_can( 'etr_testimonial_slider_edit_testimonial' ) ) {

						$best_testimonial_slider_messages = array();
						$best_testimonial_slider_messages['type'] = 'err';
						$best_testimonial_slider_messages['message'] = esc_html( __( 'Access Denied. Please contact your administrator', 'easy-testimonial-rotator' ) );
						update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
						$location = 'admin.php?page=best_testimonial_slider_testimonial_management';
												wp_redirect( esc_url( $location ) );
						exit;
					}

					// add new
					$imageid = intval( $_POST['imageid'] );
					$location = "admin.php?page=best_testimonial_slider_testimonial_management&paged=$paged";

					$imagename = '';
					if ( isset( $_POST['HdnMediaSelection'] ) && '' != sanitize_text_field( $_POST['HdnMediaSelection'] ) ) {

						$postThumbnailID = intval( $_POST['HdnMediaSelection'] );
						$photoMeta = wp_get_attachment_metadata( $postThumbnailID );
						if ( is_array( $photoMeta ) && isset( $photoMeta['file'] ) ) {

								$fileName = $photoMeta['file'];
								$phyPath = ABSPATH;
								$phyPath = str_replace( '\\', '/', $phyPath );

								$pathArray = pathinfo( $fileName );

								$imagename = $pathArray['basename'];

								$upload_dir_n = wp_upload_dir();
								$upload_dir_n = $upload_dir_n['basedir'];
								$fileUrl = $upload_dir_n . '/' . $fileName;
								$fileUrl = str_replace( '\\', '/', $fileUrl );

								$wpcurrentdir = dirname( __FILE__ );
								$wpcurrentdir = str_replace( '\\', '/', $wpcurrentdir );
								$imageUploadTo = $pathToImagesFolder . '/' . $imagename;

							   @copy( $fileUrl, $imageUploadTo );

						}
					}

					try {
						if ( '' != $imagename ) {

													$wpdb->query( $wpdb->prepare( 'update ' . esc_sql( $wpdb->prefix . 'b_testimo_slide ' ) . ' set testimonial=%s,image_name=%s,gravatar_email=%s,auth_name=%s,auth_desn=%s,auth_email=%s,status=%d where id=%d ', $testimonial, $imagename, '', $auth_name, $auth_desn, $auth_email, $status, $imageid ) );

						} else if ( null != $gravatar_email && '' != $gravatar_email ) {

													$wpdb->query( $wpdb->prepare( 'update ' . esc_sql( $wpdb->prefix . 'b_testimo_slide ' ) . ' set testimonial=%s,auth_name=%s,auth_desn=%s,auth_email=%s,image_name=%s,gravatar_email=%s,status=%d where id=%d', $testimonial, $auth_name, $auth_desn, $auth_email, '', $gravatar_email, $status, $imageid ) );

						} else {

														$wpdb->query( $wpdb->prepare( 'update ' . esc_sql( $wpdb->prefix . 'b_testimo_slide ' ) . ' set testimonial=%s,auth_name=%s,auth_desn=%s,auth_email=%s,status=%d where id=%d', $testimonial, $auth_name, $auth_desn, $auth_email, $status, $imageid ) );

						}

									   $best_testimonial_slider_messages = array();
									   $best_testimonial_slider_messages['type'] = 'succ';
									   $best_testimonial_slider_messages['message'] = esc_html( __( 'Testimonial updated successfully.', 'easy-testimonial-rotator' ) );
									   update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );

					} catch ( Exception $e ) {

						$best_testimonial_slider_messages = array();
						$best_testimonial_slider_messages['type'] = 'err';
						$best_testimonial_slider_messages['message'] = esc_html( __( 'Error while updating testimonial.', 'easy-testimonial-rotator' ) );
						update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
					}

										wp_redirect( esc_url( $location ) );
					exit;
				} else {

					// add new

					if ( ! current_user_can( 'etr_testimonial_slider_add_testimonial' ) ) {

						 $best_testimonial_slider_messages = array();
						 $best_testimonial_slider_messages['type'] = 'err';
						 $best_testimonial_slider_messages['message'] = esc_html( __( 'Access Denied. Please contact your administrator', 'easy-testimonial-rotator' ) );
						 update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
						 $location = 'admin.php?page=best_testimonial_slider_testimonial_management';
						 wp_redirect( esc_url( $location ) );
						 exit;
					}

					$location = "admin.php?page=best_testimonial_slider_testimonial_management&paged=$paged";

					$createdOn = wp_date( 'Y-m-d h:i:s' );
					$imagename = '';
					if ( function_exists( 'date_i18n' ) ) {

						   $createdOn = date_i18n( 'Y-m-d' . get_option( 'time_format' ), false, false );
						if ( get_option( 'time_format' ) == 'H:i' ) {
							$createdOn = wp_date( 'Y-m-d H:i:s', strtotime( $createdOn ) );
						} else {
							$createdOn = wp_date( 'Y-m-d h:i:s', strtotime( $createdOn ) );
						}
					}

					try {

						if ( isset( $_POST['HdnMediaSelection'] ) && sanitize_text_field( $_POST['HdnMediaSelection'] ) != '' ) {

							$postThumbnailID = (int) htmlentities( sanitize_text_field( $_POST['HdnMediaSelection'] ), ENT_QUOTES );
							$photoMeta = wp_get_attachment_metadata( $postThumbnailID );

							if ( is_array( $photoMeta ) && isset( $photoMeta['file'] ) ) {

								  $fileName = $photoMeta['file'];
								  $phyPath = ABSPATH;
								  $phyPath = str_replace( '\\', '/', $phyPath );

								  $pathArray = pathinfo( $fileName );

								  $imagename = $pathArray['basename'];

								  $upload_dir_n = wp_upload_dir();
								  $upload_dir_n = $upload_dir_n['basedir'];
								  $fileUrl = $upload_dir_n . '/' . $fileName;
								  $fileUrl = str_replace( '\\', '/', $fileUrl );
								  $imageUploadTo = $pathToImagesFolder . '/' . $imagename;

								  @copy( $fileUrl, $imageUploadTo );

							}
						}

						$wpdb->insert(
							$wpdb->prefix . 'b_testimo_slide',
							array(
								'testimonial' => $testimonial,
								'image_name' => $imagename,
								'auth_name' => $auth_name,
								'auth_desn' => $auth_desn,
								'auth_email' => $auth_email,
								'createdon' => $createdOn,
								'gravatar_email' => $gravatar_email,
								'status' => $status,
							),
							array(
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%d',
							)
						);

							  $best_testimonial_slider_messages = array();
							  $best_testimonial_slider_messages['type'] = 'succ';
							  $best_testimonial_slider_messages['message'] = esc_html( __( 'New testimonial added successfully.', 'easy-testimonial-rotator' ) );
							  update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );

					} catch ( Exception $e ) {

						  $best_testimonial_slider_messages = array();
						  $best_testimonial_slider_messages['type'] = 'err';
						  $best_testimonial_slider_messages['message'] = esc_html( __( 'Error while adding testimonial.', 'easy-testimonial-rotator' ) );
						  update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
					}

										wp_redirect( esc_url( $location ) );
					exit;

				}
			} else {

					$uploads = wp_upload_dir();
					$baseurl = $uploads['baseurl'];
					$baseurl .= '/easy-testimonial-rotator/';

				?>
	 <div style="width: 100%;">  
		<div style="float:left;width:100%;" >
			<div class="wrap">
			 <table><tr>
					  <td>
							<div class="fb-like" data-href="https://www.facebook.com/i13websolution" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>
							<div id="fb-root"></div>
							  <script>(function(d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (d.getElementById(id)) return;
								js = d.createElement(s); js.id = id;
								js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2&appId=158817690866061&autoLogAppEvents=1';
								fjs.parentNode.insertBefore(js, fjs);
							  }(document, 'script', 'facebook-jssdk'));</script>
						</td>
						<td>
							<a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
								<img id="help us for free plugin" height="30" width="90" src="<?php echo esc_attr( esc_url( plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ) ); ?>" border="0" alt="help us for free plugin" title="help us for free plugin">
							</a>
						</td>
						</tr>
					</table>
				 <span><h3 style="color: blue;"><a target="_blank" href="https://www.i13websolution.com/product/wordpress-easy-testimonial-slider-plugin/"><?php echo esc_html( __( 'UPGRADE TO PRO VERSION', 'easy-testimonial-rotator' ) ); ?></a></h3></span>
					  
				<?php
				if ( isset( $_GET['id'] ) && (int) $_GET['id'] > 0 ) {

					if ( ! current_user_can( 'etr_testimonial_slider_edit_testimonial' ) ) {

						$best_testimonial_slider_messages = array();
						$best_testimonial_slider_messages['type'] = 'err';
						$best_testimonial_slider_messages['message'] = esc_html( __( 'Access Denied. Please contact your administrator', 'easy-testimonial-rotator' ) );
						update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
						$location = 'admin.php?page=best_testimonial_slider_testimonial_management';
												wp_redirect( esc_url( $location ) );
						exit;
					}

					$id = intval( $_GET['id'] );

					$myrow  = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'b_testimo_slide WHERE id=%d', $id ) );

					if ( is_object( $myrow ) ) {

						$testimonial = $myrow->testimonial;
						$auth_name = $myrow->auth_name;
						$auth_desn = $myrow->auth_desn;
						$image_name = $myrow->image_name;
						$auth_email = $myrow->auth_email;
						$gravatar_email = $myrow->gravatar_email;
						$status = $myrow->status;

					}

					?>
			<h2><?php echo esc_html( __( 'Update Testimonial', 'easy-testimonial-rotator' ) ); ?></h2>
					<?php
				} else {

					$testimonial = '';
					$auth_name = '';
					$auth_desn = '';
					$image_name = '';
					$auth_email = '';
					$gravatar_email = '';
					$status = '';

					if ( ! current_user_can( 'etr_testimonial_slider_add_testimonial' ) ) {

						$best_testimonial_slider_messages = array();
						$best_testimonial_slider_messages['type'] = 'err';
						$best_testimonial_slider_messages['message'] = esc_html( __( 'Access Denied. Please contact your administrator', 'easy-testimonial-rotator' ) );
						update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
						$location = 'admin.php?page=best_testimonial_slider_testimonial_management';
												wp_redirect( esc_url( $location ) );
						exit;
					}

					?>
		  <h2><?php echo esc_html( __( 'Add Testimonial', 'easy-testimonial-rotator' ) ); ?> </h2>
				<?php } ?>
					   <?php
							$settings = get_option( 'best_testimonial_options' );
							$settings_fields = get_option( 'i13_default_form_options' );

						?>
				
								   <?php

									$imgUrl = '';
									if ( '' != $image_name && null != $image_name ) {

										$imgUrl = $baseurl . $image_name;
									} else if ( '' != $gravatar_email && null != $gravatar_email ) {

										$email = md5( $gravatar_email );
										$imgUrl = "https://www.gravatar.com/avatar/$email?s=200";
									}
									$vNonce = wp_create_nonce( 'vNonce' );
									?>
			<br/>
			<div id="poststuff">
			  <div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
				  <form method="post" action="" id="addimage" name="addimage" enctype="multipart/form-data" >
					  
								<?php if ( $settings_fields['show_author_name'] ) : ?>
						<div class="stuffbox" id="namediv" style="width:100%;">
						 <h3><label for="auth_name"><?php echo esc_html( $settings_fields['author_name_label'] ); ?>
									<?php if ( $settings_fields['is_author_name_field_required'] ) : ?>
								 <span class="required">*</span>
							  <?php endif; ?>  
							 </label></h3>
							<div class="inside">
								<input type="text" id="auth_name"  name="auth_name" value="<?php echo esc_attr( $auth_name ); ?>" style="width:300px">
								 <div style="clear:both"></div>
								 <div></div>
								 <div style="clear:both"></div>

							 </div>
						 </div>
					  <?php endif; ?>
								  <?php if ( $settings_fields['show_author_des'] ) : ?>
						<div class="stuffbox" id="namediv" style="width:100%;">
						 <h3><label for="auth_desn"><?php echo esc_html( $settings_fields['author_designation_lable'] ); ?>
										<?php if ( $settings_fields['is_author_designation_field_required'] ) : ?>
								 <span class="required">*</span>
							  <?php endif; ?> 
							 </label></h3>
							<div class="inside">
								<input type="text" id="auth_desn"  name="auth_desn" value="<?php echo esc_attr( $auth_desn ); ?>" style="width:300px">
								 <div style="clear:both"></div>
								 <div></div>
								 <div style="clear:both"></div>

							 </div>
						 </div>
					  <?php endif; ?>
								   <?php if ( $settings_fields['show_author_email'] ) : ?>
						 <div class="stuffbox" id="namediv" style="width:100%;">
						 <h3><label for="auth_website"><?php echo esc_html( $settings_fields['author_email_label'] ); ?>
										<?php if ( $settings_fields['is_author_email_field_required'] ) : ?>
								 <span class="required">*</span>
							  <?php endif; ?> 
							 </label></h3>
							<div class="inside">
								<input type="text" id="auth_email" class=""   size="30" name="auth_email" value="<?php echo esc_attr( $auth_email ); ?>">
								 <div style="clear:both"></div>
								 <div></div>
								 <div style="clear:both"></div>

							 </div>
						</div>
					  <?php endif; ?>
								  <?php if ( $settings_fields['show_photo_upload'] ) : ?>
					  
						 <div class="stuffbox" id="namediv" style="width:100%;">
						 <h3><label for="link_name"><?php echo esc_html( $settings_fields['author_photo_label'] ); ?>
										<?php if ( $settings_fields['photo_upload_field_required'] ) : ?>
								 <span class="required">*</span>
							  <?php endif; ?>
							 </label></h3>
						 <div class="inside" id="fileuploaddiv">
										<?php if ( '' != $imgUrl ) { ?>
									<div>
											<b><?php echo esc_html( __( 'Current Image :', 'easy-testimonial-rotator' ) ); ?></b>
											<br/>
											<img id="img_disp" name="img_disp"
													src="<?php echo esc_attr( esc_url( $imgUrl ) ); ?>" />
									</div>
							<?php } else { ?>      
										<img
											src="<?php echo esc_url( plugins_url( '/images/no-img.png', __FILE__ ) ); ?>"
											id="img_disp" name="img_disp" />

								 <?php } ?>    
							 <div class="uploader">
							  <br/>
								<a href="javascript:;" class="niks_media" id="myMediaUploader"><b><?php echo esc_html( __( 'Click here to upload author photo', 'easy-testimonial-rotator' ) ); ?></b></a>
								<br/>
								<b style="padding-left:50px"><?php echo esc_html( __( 'OR', 'easy-testimonial-rotator' ) ); ?></b><br/>
								<table><tr><td><a style="vertical-align: top;" href="javascript:;" class="niks_gav" id="niks_gav"><b><?php echo esc_html( $settings_fields['author_photo_link_label'] ); ?></b></a>&nbsp;&nbsp;<img id="gav_loader" class="gav_loader" style="display:none" src="<?php echo esc_html( plugins_url( 'images/ajax-loader.gif', __FILE__ ) ); ?>"  /></td></tr></table>
								<input id="HdnMediaSelection" name="HdnMediaSelection" type="hidden" value="<?php echo esc_attr( $image_name ); ?>" />
								<input id="HdnMediaGrevEmail" name="HdnMediaGrevEmail" type="hidden" value="<?php echo esc_attr( $gravatar_email ); ?>" />
								 <div style="clear:both"></div>
								 <div></div>
								 <div style="clear:both"></div>
							  <br/>
							</div>  
										<?php if ( etr_best_testimonial_slider_get_wp_version() >= 3.5 ) { ?>
							  <script>
							
							
							
							jQuery( "#niks_gav" ).click(function() {
							   var email_gav = prompt("<?php echo esc_html( __( 'Please enter your gravatar.com email', 'easy-testimonial-rotator' ) ); ?>", "");
							   if(jQuery.trim(email_gav)!='' && email_gav!=null){
								   
										   jQuery("#gav_loader").show(); 
										   var data_grav = {
														'action': 'etr_get_grav_avtar',
														'email': jQuery.trim(email_gav),
														'vNonce':'<?php echo esc_html( $vNonce ); ?>'
												};
												jQuery.post(ajaxurl, data_grav, function(data) {
													
													  jQuery("#HdnMediaGrevEmail").val(jQuery.trim(email_gav)); 
													  jQuery("#HdnMediaSelection").val(''); 
													  jQuery("#img_disp").attr('src', data);
													  jQuery("#gav_loader").hide();
											   
												});


										
								   
							   }
							   
							 });
							jQuery(document).ready(function() {
								   //uploading files variable
								   var custom_file_frame;
								   jQuery("#myMediaUploader").click(function(event) {
									  event.preventDefault();
									  //If the frame already exists, reopen it
									  if (typeof(custom_file_frame)!=="undefined") {
										 custom_file_frame.close();
									  }
								 
									  //Create WP media frame.
									  custom_file_frame = wp.media.frames.customHeader = wp.media({
										 //Title of media manager frame
										 title: "<?php echo esc_html( __( 'WP Media Uploader', 'easy-testimonial-rotator' ) ); ?>",
										 library: {
											type: 'image'
										 },
										 button: {
											//Button text
											text: "<?php echo esc_html( __( 'Set Image', 'easy-testimonial-rotator' ) ); ?>"
										 },
										 //Do not allow multiple files, if you want multiple, set true
										 multiple: false
									  });
								 
									  //callback for selected image
									  custom_file_frame.on('select', function() {
										 
										  var attachment = custom_file_frame.state().get('selection').first().toJSON();
										  
										   var validExtensions=new Array();
											validExtensions[0]='jpg';
											validExtensions[1]='jpeg';
											validExtensions[2]='png';
											validExtensions[3]='gif';
										   
														
											var inarr=parseInt(jQuery.inArray( attachment.subtype, validExtensions));
								
											if(inarr>0 && attachment.type.toLowerCase()=='image' ){
												
												 
												
												if(attachment.id!=''){
													jQuery("#HdnMediaSelection").val(attachment.id); 
													jQuery("#HdnMediaGrevEmail").val(''); 
													  jQuery("#img_disp").attr('src', attachment.url);
												}   
												
											}  
											else{
												
												alert("<?php echo esc_html( $settings_fields['invalid_photo_field_error_msg'] ); ?>");
											}  
											 //do something with attachment variable, for example attachment.filename
											 //Object:
											 //attachment.alt - image alt
											 //attachment.author - author id
											 //attachment.caption
											 //attachment.dateFormatted - date of image uploaded
											 //attachment.description
											 //attachment.editLink - edit link of media
											 //attachment.filename
											 //attachment.height
											 //attachment.icon - don't know WTF?))
											 //attachment.id - id of attachment
											 //attachment.link - public link of attachment, for example ""http://site.com/?attachment_id=115""
											 //attachment.menuOrder
											 //attachment.mime - mime type, for example image/jpeg"
											 //attachment.name - name of attachment file, for example "my-image"
											 //attachment.status - usual is "inherit"
											 //attachment.subtype - "jpeg" if is "jpg"
											 //attachment.title
											 //attachment.type - "image"
											 //attachment.uploadedTo
											 //attachment.url - http url of image, for example "http://site.com/wp-content/uploads/2012/12/my-image.jpg"
											 //attachment.width
									  });
								 
									  //Open modal
									  custom_file_frame.open();
								   });
								})
							</script>
							<?php } ?> 
						 </div>
					   </div>
					 <?php endif; ?>
					   <div class="stuffbox" id="namediv" style="width:100%;">
						 <h3><label for="testimonial"><?php echo esc_html( $settings_fields['testimonial_label'] ); ?><span class="required">*</span></label></h3>
						<div class="inside">
							 <textarea cols="90" class="" style="width:100%;" rows="3" id="testimonial" name="testimonial"><?php echo esc_html( $testimonial ); ?></textarea>
							 <div style="clear:both"></div>
							 <div></div>
							 <div style="clear:both"></div>
							<p>
						 </div>
						</div>
					  <div class="stuffbox" id="namediv" style="width:100%;">
						 <h3><label for="status"><?php echo esc_html( $settings_fields['status_label'] ); ?><span class="required">*</span></label></h3>
						<div class="inside">
							 <select id="status" name="status" class="select">
								<option value=""><?php echo esc_html( __( 'Select', 'easy-testimonial-rotator' ) ); ?></option>
								<option 
								<?php
								if ( '1' == $status ) :
									?>
									 selected="selected" <?php endif; ?>  value="1" ><?php echo esc_html( __( 'Published', 'easy-testimonial-rotator' ) ); ?></option>
								<option 
								<?php
								if ( '0' == $status ) :
									?>
									 selected="selected" <?php endif; ?>  value="0"><?php echo esc_html( __( 'Draft', 'easy-testimonial-rotator' ) ); ?></option>
							</select>   
							 <div style="clear:both"></div>
							 <div></div>
							 <div style="clear:both"></div>
							<p>
						 </div>
						</div>
										 <?php if ( isset( $_GET['id'] ) && intval( $_GET['id'] ) > 0 ) { ?> 
						   <input type="hidden" name="imageid" id="imageid" value="<?php echo intval( $_GET['id'] ); ?>">
					   <?php } ?>
										 <?php wp_nonce_field( 'action_image_add_edit', 'add_edit_image_nonce' ); ?>    
					   <input type="submit"  name="btnsave" id="btnsave" value="<?php echo esc_html( __( 'Save Changes', 'easy-testimonial-rotator' ) ); ?>" class="button-primary">&nbsp;&nbsp;<input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href='admin.php?page=best_testimonial_slider_testimonial_management'">
								  
				 </form> 
				  <script type="text/javascript">
				  
					 
					 
					 jQuery.validator.setDefaults({ 
							ignore: []
						   
						});
					 jQuery.validator.addMethod("checkImgUpload", function(value, element) {
							
							if(jQuery.trim(jQuery("#HdnMediaSelection").val())=='' && jQuery.trim(jQuery("#HdnMediaGrevEmail").val())==''){
								
								return false;
							}
							else{
								
								return true;
							}
							
						}, "<?php echo esc_html( $settings_fields['required_field_error_msg'] ); ?>");

					 jQuery(document).ready(function() {
					 
						jQuery("#addimage").validate({
							rules: {
									testimonial: {
									  required:true, 
									  maxlength: 500
									},
									auth_name: {
									  maxlength: 500,
									  <?php if ( $settings_fields['show_author_name'] && $settings_fields['is_author_name_field_required'] ) : ?>
									   required:true
									   <?php else : ?>
										required:false   
									 <?php endif; ?>  
									},
									auth_desn: {
									   maxlength: 500,
									   <?php if ( $settings_fields['show_author_des'] && $settings_fields['is_author_designation_field_required'] ) : ?>
									   required:true
									   <?php else : ?>
										required:false   
									  <?php endif; ?> 
									},
									auth_email: {
									  email:true,  
									  maxlength: 500,
									  <?php if ( $settings_fields['show_author_email'] && $settings_fields['is_author_email_field_required'] ) : ?>
									   required:true
									   <?php else : ?>
										required:false   
									  <?php endif; ?>  
									 
									},
									status:{
									  required:true
									},
									HdnMediaGrevEmail:{
									   <?php if ( $settings_fields['show_photo_upload'] && $settings_fields['photo_upload_field_required'] ) : ?>
										  checkImgUpload : true 
									   <?php else : ?>
										   checkImgUpload : false 
									   <?php endif; ?>    
										   
									}        
							   },
								 messages: {
									 
									 testimonial: {
									   required:"<?php echo esc_html( $settings_fields['required_field_error_msg'] ); ?>"
									  },
									 auth_name: {
									   required:"<?php echo esc_html( $settings_fields['required_field_error_msg'] ); ?>"
									  },
									 status: {
									   required:"<?php echo esc_html( $settings_fields['required_field_error_msg'] ); ?>"
									   
									  },
									 auth_email: {
									   required:"<?php echo esc_html( $settings_fields['required_field_error_msg'] ); ?>",
									   email:"<?php echo esc_html( $settings_fields['invalid_email_field_error_msg'] ); ?>"
									  }
								 },
								 errorClass: "image_error",
								 errorPlacement: function(error, element) {
								 error.appendTo( element.next().next().next());
							 } 
							 

						})
					});
				  
				  function validateFile(){

						
						if(jQuery('#currImg').length>0 || jQuery.trim(jQuery("#HdnMediaSelection").val())!="" ){
							return true;
						}
						else
							{
							jQuery("#err_daynamic").remove();
							jQuery("#myMediaUploader").after('<br/><label class="image_error" id="err_daynamic">Please select file.</label>');
							return false;  
						} 

					}
				
				</script> 

				</div>
				<div id="postbox-container-1" class="postbox-container"> 
					<div class="postbox"> 
						<h3 class="hndle"><span></span><?php echo esc_html( __( 'Access All Themes One price', 'easy-testimonial-rotator' ) ); ?></h3> 
						<div class="inside">
							<center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="<?php echo esc_attr( esc_url( plugins_url( 'images/300x250.gif', __FILE__ ) ) ); ?>" width="250" height="250"></a></center>

							<div style="margin:10px 5px">

							</div>
						</div></div>

					 <div class="postbox"> 
						<h3 class="hndle"><span></span><?php echo esc_html( __( 'Google For Business Coupon', 'easy-testimonial-rotator' ) ); ?></h3> 
							<div class="inside">
								<center><a href="https://goo.gl/OJBuHT" target="_blank">
										<img src="<?php echo esc_attr( esc_url( plugins_url( 'images/g-suite-promo-code-4.png', __FILE__ ) ) ); ?>" width="250" height="250" border="0">
									</a></center>
								<div style="margin:10px 5px">
								</div>
							</div>

						</div>
				</div> 
		  </div>
		</div>  
	 </div>      
		 </div>
								  <?php
			}
	} else if ( strtolower( $action ) == strtolower( 'delete' ) ) {

		 $retrieved_nonce = '';

		if ( isset( $_GET['nonce'] ) && '' != sanitize_text_field( $_GET['nonce'] ) ) {

			$retrieved_nonce = sanitize_text_field( $_GET['nonce'] );

		}
		if ( ! wp_verify_nonce( $retrieved_nonce, 'delete_image' ) ) {

			wp_die( 'Security check fail' );
		}

		if ( ! current_user_can( 'etr_testimonial_slider_delete_testimonial' ) ) {

			$best_testimonial_slider_messages = array();
			$best_testimonial_slider_messages['type'] = 'err';
			$best_testimonial_slider_messages['message'] = esc_html( __( 'Access Denied. Please contact your administrator', 'easy-testimonial-rotator' ) );
			update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
			$location = 'admin.php?page=best_testimonial_slider_testimonial_management';
						wp_redirect( esc_url( $location ) );
			exit;
		}
		   $uploads = wp_upload_dir();
		   $baseDir = $uploads ['basedir'];
		   $baseDir = str_replace( '\\', '/', $baseDir );
		   $pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';

		   $location = 'admin.php?page=best_testimonial_slider_testimonial_management';
		   $deleteId = (int) $_GET['id'];

		try {

				$myrow  = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'b_testimo_slide where id=%d', $deleteId ) );

			if ( is_object( $myrow ) ) {

				$image_name = $myrow->image_name;
				// $imagename=$_FILES["image_name"]["name"];
				$imagetoDel = $pathToImagesFolder . '/' . $image_name;
				@unlink( $imagetoDel );

				$wpdb->query( $wpdb->prepare( 'delete from  ' . $wpdb->prefix . 'b_testimo_slide where id=%d', $deleteId ) );

				$best_testimonial_slider_messages = array();
				$best_testimonial_slider_messages['type'] = 'succ';
				$best_testimonial_slider_messages['message'] = esc_html( __( 'Testimonial deleted successfully.', 'easy-testimonial-rotator' ) );
				update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
			}
		} catch ( Exception $e ) {

			$best_testimonial_slider_messages = array();
			$best_testimonial_slider_messages['type'] = 'err';
			$best_testimonial_slider_messages['message'] = esc_html( __( 'Error while deleting testimonial.', 'easy-testimonial-rotator' ) );
			update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
		}

						   wp_redirect( esc_url( $location ) );
			   exit;

	} else if ( strtolower( 'deleteselected' ) == strtolower( $action ) ) {

		if ( ! check_admin_referer( 'action_settings_mass_delete', 'mass_delete_nonce' ) ) {

			 wp_die( 'Security check fail' );
		}

		if ( ! current_user_can( 'etr_testimonial_slider_delete_testimonial' ) ) {

			$best_testimonial_slider_messages = array();
			$best_testimonial_slider_messages['type'] = 'err';
			$best_testimonial_slider_messages['message'] = esc_html( __( 'Access Denied. Please contact your administrator', 'easy-testimonial-rotator' ) );
			update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
			$location = 'admin.php?page=best_testimonial_slider_testimonial_management';
			 wp_redirect( esc_url( $location ) );
			   exit;

		}

		   $uploads = wp_upload_dir();
		   $baseDir = $uploads ['basedir'];
		   $baseDir = str_replace( '\\', '/', $baseDir );
		   $pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';

		 $location = 'admin.php?page=best_testimonial_slider_testimonial_management';

		if ( isset( $_POST ) && isset( $_POST['deleteselected'] ) && ( ( isset( $_POST['action'] ) && 'delete' == sanitize_text_field( $_POST['action'] ) ) || ( isset( $_POST['action_upper'] ) && 'delete' == sanitize_text_field( $_POST['action_upper'] ) ) ) ) {

			if ( isset( $_POST['thumbnails'] ) && count( $_POST['thumbnails'] ) > 0 ) {

					$deleteto = sanitize_text_field( $_POST['thumbnails'] );
					$implode = implode( ',', $deleteto );

				try {

					foreach ( $deleteto as $img ) {

								$img = intval( $img );
								$myrow  = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . ' b_testimo_slide WHERE id=%d', $img ) );

						if ( is_object( $myrow ) ) {

							$image_name = $myrow->image_name;
							// $imagename=$_FILES["image_name"]["name"];
							$imagetoDel = $pathToImagesFolder . '/' . $image_name;
							@unlink( $imagetoDel );

							$wpdb->query( $wpdb->prepare( 'delete from  ' . $wpdb->prefix . 'b_testimo_slide where id=%d', $img ) );

							$best_testimonial_slider_messages = array();
							$best_testimonial_slider_messages['type'] = 'succ';
							$best_testimonial_slider_messages['message'] = esc_html( __( 'selected images deleted successfully.', 'easy-testimonial-rotator' ) );
							update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
						}
					}
				} catch ( Exception $e ) {

						$best_testimonial_slider_messages = array();
						$best_testimonial_slider_messages['type'] = 'err';
						$best_testimonial_slider_messages['message'] = esc_html( __( 'Error while deleting image.', 'easy-testimonial-rotator' ) );
						update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
				}

								   wp_redirect( esc_url( $location ) );
								   exit;

			} else {

								wp_redirect( esc_url( $location ) );
								exit;

			}
		} else {

			wp_redirect( esc_url( $location ) );
						exit;
		}
	}

}

function etr_best_testimonial_preview_admin() {

		   global $wpdb;
		   $settings = get_option( 'best_testimonial_options' );
		   $settings['style'] = 'style 1';
		   $rand_Numb = uniqid( 'quotes_slider' );
		   $rand_Num_td = uniqid( 'divSliderMain' );
		   $rand_var_name = uniqid( 'rand_' );

		   $uploads = wp_upload_dir();
		   $baseDir = $uploads ['basedir'];
		   $baseDir = str_replace( '\\', '/', $baseDir );

		   $baseurl = $uploads['baseurl'];
		   $baseurl .= '/easy-testimonial-rotator/';
		   $pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';

	if ( ! current_user_can( 'etr_testimonial_slider_preview' ) ) {

		$best_testimonial_slider_messages = array();
		$best_testimonial_slider_messages['type'] = 'err';
		$best_testimonial_slider_messages['message'] = esc_html( __( 'Access Denied. Please contact your administrator', 'easy-testimonial-rotator' ) );
		update_option( 'best_testimonial_slider_messages', $best_testimonial_slider_messages );
		$location = 'admin.php?page=best_testimonial_slider_testimonial_management';
		wp_redirect( esc_url( $location ) );
				exit;
	}

	?>
		   
	
	   <?php
			$wpcurrentdir = dirname( __FILE__ );
			$wpcurrentdir = str_replace( '\\', '/', $wpcurrentdir );
		?>
	   <div style="width: 100%;">  
			<h2><?php echo esc_html( __( 'Preview Slider', 'easy-testimonial-rotator' ) ); ?></h2>
			<div style="float:left;width:100%;">
				<div class="wrap">
			   
				<?php if ( is_array( $settings ) ) { ?>
				<div id="poststuff">
				  <div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
					 <?php if ( 'style 1' == $settings['style'] ) : ?>  
					  
						<div class="class_fulldiv style1">
							
							 <style type='text/css' >
								 
								#<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-viewport{left:auto;padding: 0px;padding-bottom:10px} 
								#<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-viewport {
									background: none repeat scroll 0 0 <?php echo esc_html( $settings['slider_back_color'] ); ?> ;

								  }
								   #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et {

									  border: <?php echo esc_html( $settings['box_border_size'] ); ?>px solid <?php echo esc_html( $settings['box_border_color'] ); ?>;
									  box-shadow: 0 0 5px <?php echo esc_html( $settings['box_shadow_color'] ); ?>;
								  }
								   #<?php echo esc_html( $rand_Num_td ); ?>  .bx-wrapper-et .bx-prev {

										background: rgba(0, 0, 0, 0) url("<?php echo esc_html( plugins_url( 'images/controls.png', __FILE__ ) ); ?>") no-repeat scroll -1px -31px;
										left: 0;
										<?php if ( esc_html( $settings['show_arrows'] ) ) : ?>
										display:block;
										<?php endif; ?>
									}

									 #<?php echo esc_html( $rand_Num_td ); ?>  .bx-wrapper-et .bx-next {
										background: rgba(0, 0, 0, 0) url("<?php echo esc_html( plugins_url( 'images/controls.png', __FILE__ ) ); ?>") no-repeat scroll -42px -31px;
										right: 0;
										 <?php if ( esc_html( $settings['show_arrows'] ) ) : ?>
										display:block ;
										<?php endif; ?>
									}

									 #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-next:hover,  #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-next:focus {
										  background-position: -42px 0 ;
									 }
									 #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-prev:hover, #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-prev:focus {
										background-position: -1px 0 ;
									  }
								</style>
							<div class="childDiv_style1" id="<?php echo esc_attr( $rand_Num_td ); ?>" style="display:none;opacity: 0">  
							   
								<div class="bxsliderx rowcust" >
									
								   <?php

										$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'b_testimo_slide where  status=%d order by createdon desc', 1 ), ARRAY_A );
										$randOmeAlbName = uniqid( 'slider_' );
									if ( count( $rows ) > 0 ) {

										?>
										<?php foreach ( $rows as $row ) : ?>
											<?php
											if ( '' != $row['image_name'] || null != $row['image_name'] ) {

												   // $outputimg = $baseurl.$row['image_name'];

													$imagename = $row['image_name'];
													$imageUploadTo = $pathToImagesFolder . '/' . $imagename;
													$imageUploadTo = str_replace( '\\', '/', $imageUploadTo );
													$pathinfo = pathinfo( $imageUploadTo );
													$filenamewithoutextension = $pathinfo['filename'];
													$imageheight = 300;
													$imagewidth = 300;
													$outputimg = '';

													$outputimg = $baseurl . $row['image_name'];
												if ( 0 == $settings['resize_images'] ) {

													$outputimg = $baseurl . $row['image_name'];

												} else {

													$imagetoCheck = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
													$imagetoCheckSmall = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );

													if ( file_exists( $imagetoCheck ) ) {
															$outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];

													} else if ( file_exists( $imagetoCheckSmall ) ) {
														 $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );
													} else {

														if ( function_exists( 'wp_get_image_editor' ) ) {

															$image = wp_get_image_editor( $pathToImagesFolder . '/' . $row['image_name'] );
															if ( ! is_wp_error( $image ) ) {
																$image->resize( $imagewidth, $imageheight, true );
																$image->save( $imagetoCheck );
																// $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

																if ( file_exists( $imagetoCheck ) ) {
																	$outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
																} else if ( file_exists( $imagetoCheckSmall ) ) {
																	$outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );
																}
															} else {
																$outputimg = $baseurl . $row['image_name'];
															}
														} else {

															$outputimg = $baseurl . $row['image_name'];
														}

														// $url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

													}
												}
											} else if ( '' != $row['gravatar_email'] && null != $row['gravatar_email'] ) {

												$email = md5( $row['gravatar_email'] );
												$outputimg = "https://www.gravatar.com/avatar/$email?s=200";
											} else {

												$outputimg = plugins_url( 'images/no_photo.png', __FILE__ );
											}
											?>
											<div class="setMargin tesimonial_slider_row">

												<div class="rowupdate margin_Quotes">


													<div class="colupdate-sm-12 setmargin"  >
														<div class="setfloat floatLeft">
															<img class="imgupdate-circle imgupdate-circle-img" src="<?php echo esc_attr( esc_url( $outputimg ) ); ?>" style="">
														</div> 
														<blockquote class="open_close">

															<span class="quotes_content">
															  <?php echo esc_html( $row['testimonial'] ); ?>   
															</span>
															
															<?php if ( $settings['show_author_name'] && '' != trim( $row['auth_name'] ) ) : ?> 
																<span class="author_name"><?php echo esc_html( $row['auth_name'] ); ?></span>
															<?php endif; ?>     
															<?php if ( $settings['show_author_des'] && '' != trim( $row['auth_desn'] ) ) : ?> 
																 <span class="author_position"><?php echo esc_html( $row['auth_desn'] ); ?></span>
															<?php endif; ?>
																 
														</blockquote>


													</div>
												</div>



											</div>
											<?php endforeach; ?>
									<?php } ?>
								</div>
							</div>
							<script type="text/javascript">
							   
								jQuery(document).ready(function () {
										
									var sliderwidth=jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").width();
									if(sliderwidth<=699){

										jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bxsliderx.rowcust div div.rowupdate.margin_Quotes div.colupdate-sm-12.setmargin .setfloat').removeClass('floatLeft');
									}

									var timer;
									var width = jQuery(window).width();
									jQuery(window).bind('resize', function(){
										if(jQuery(window).width() != width){

											width = jQuery(window).width();
											timer && clearTimeout(timer);
											timer = setTimeout(resizecall, 100);

										}   
									});

									function resizecall(){

										var sliderwidth=jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").width();
										if(sliderwidth<=699){

											jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bxsliderx.rowcust div div.rowupdate.margin_Quotes div.colupdate-sm-12.setmargin .setfloat').removeClass('floatLeft');
										  
											 jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bx-wrapper-et div.bx-viewport').css('height','auto');
										 
										  

										}else{

											jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bxsliderx.rowcust div div.rowupdate.margin_Quotes div.colupdate-sm-12.setmargin .setfloat').addClass('floatLeft');
											  
											jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bx-wrapper-et div.bx-viewport').css('height','auto');

												   

											
										}

									}

									jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").show();
									var sld_<?php echo esc_html( $rand_Num_td ); ?> = jQuery('#<?php echo esc_html( $rand_Num_td ); ?> .bxsliderx').bxSliderx({
										slideMargin: 100,
										auto: <?php echo ( esc_html( $settings['auto'] ) ) == 1 ? 'true' : 'false'; ?>,
										infiniteLoop: <?php echo ( esc_html( $settings['is_circular_slider'] ) ) == 1 ? 'true' : 'false'; ?>,
										minSlides: 1,
										maxSlides: 1,
										moveSlides: 1,
										preventDefaultSwipeY:false,
										speed: <?php echo esc_html( $settings['speed'] ); ?>,
										pause: <?php echo esc_html( $settings['pause'] ); ?>,
										adaptiveHeight: <?php echo ( esc_html( $settings['is_adaptive_height'] ) ) == 1 ? 'true' : 'false'; ?>,
										controls: <?php echo ( esc_html( $settings['show_arrows'] ) ) == 1 ? 'true' : 'false'; ?>,
										pager: <?php echo ( esc_html( $settings['show_pagination'] ) ) == 1 ? 'true' : 'false'; ?>,
										touchEnabled: <?php echo ( esc_html( $settings['touch_enabled'] ) ) == 1 ? 'true' : 'false'; ?>,
										wrapperClass: 'bx-wrapper-et',
										 onSlideBefore: function(slideElement){

											jQuery(slideElement).find('img').each(function(index, elm) {

												  if(!elm.complete || elm.naturalWidth === 0){
															vsrc= jQuery(elm).attr("src");
															jQuery(elm).removeAttr("src");
															dsrc= jQuery(elm).attr("data-src");
															lsrc= jQuery(elm).attr("data-lazy-src");

															if(dsrc!== undefined && dsrc!='' && dsrc!=vsrc){
																	 jQuery(elm).attr("src",dsrc);
																}
																else if(lsrc!== undefined && lsrc!=vsrc){

																	 jQuery(elm).attr("src",lsrc);
																}
																else{

																	 jQuery(elm).attr("src",vsrc);

																}   

															elm= jQuery(elm)[0];      
															if(!elm.complete && elm.naturalHeight == 0){

																 jQuery(elm).removeAttr('loading');
																 jQuery(elm).removeAttr('data-lazy-type');


																 jQuery(elm).removeClass('lazy');

																 jQuery(elm).removeClass('lazyLoad');
																 jQuery(elm).removeClass('lazy-loaded');
																 jQuery(elm).removeClass('jetpack-lazy-image');
																 jQuery(elm).removeClass('jetpack-lazy-image--handled');
																 jQuery(elm).removeClass('lazy-hidden');

														}
													}

											});

										},
										 onSliderLoad: function(){
										  
											jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").css('opacity','1');
										 }
									});
								});
								
								
								   window.addEventListener('load', function() {


										setTimeout(function(){ 

												if(jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").find('.bx-loading').length>0){

														jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").find('img').each(function(index, elm) {

																 if(!elm.complete || elm.naturalWidth === 0){
																	var toload='';
																	var toloadval='';
																	jQuery.each(this.attributes, function(i, attrib){

																			var value = attrib.value;
																			var aname=attrib.name;

																			var pattern = /^((http|https):\/\/)/;

																			if(pattern.test(value) && aname!='src') {

																					toload=aname;
																					toloadval=value;
																			 }
																	 });

																			vsrc=jQuery(elm).attr("src");
																			jQuery(elm).removeAttr("src");
																			dsrc=jQuery(elm).attr("data-src");
																			lsrc=jQuery(elm).attr("data-lazy-src");


																			   if(dsrc!== undefined && dsrc!='' && dsrc!=vsrc){
																											 jQuery(elm).attr("src",dsrc);
																					}
																					else if(lsrc!== undefined && lsrc!=vsrc){

																									 jQuery(elm).attr("src",lsrc);
																					}
																					else if(toload!='' && toload!='srcset' && toloadval!='' && toloadval!=vsrc){

																							jQuery(elm).removeAttr(toload);
																							jQuery(elm).attr("src",toloadval);


																						} 
																					else{

																									jQuery(elm).attr("src",vsrc);

																			   }   

																			elm=jQuery(elm)[0];      
																			 if(!elm.complete && elm.naturalHeight == 0){

																							 jQuery(elm).removeAttr('loading');
																							 jQuery(elm).removeAttr('data-lazy-type');


																							 jQuery(elm).removeClass('lazy');

																							 jQuery(elm).removeClass('lazyLoad');
																							 jQuery(elm).removeClass('lazy-loaded');
																							 jQuery(elm).removeClass('jetpack-lazy-image');
																							 jQuery(elm).removeClass('jetpack-lazy-image--handled');
																							 jQuery(elm).removeClass('lazy-hidden');

																			 }

																	}

															}).promise().done( function(){ 

																	jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").find('.bx-loading').remove();
															} );

													}


										   }, 6000);

								});
							</script>
						</div> 
					
					   <?php endif; ?>
					 
				 </div>
			</div>  
		  <?php } ?>
		 </div>      
	</div>   
   </div>             
	<div class="clear"></div>
   </div>
	 <h3><?php echo esc_html( esc_html( __( 'To print this slider into WordPress Post/Page use below code', 'easy-testimonial-rotator' ) ) ); ?></h3>
	<input type="text" value='[print_best_testimonial_slider] ' style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
	<div class="clear"></div>
	<h3><?php echo esc_html( esc_html( __( 'To print this slider into WordPress theme/template PHP files use below code', 'easy-testimonial-rotator' ) ) ); ?></h3>
	<?php
		$shortcode = '[print_best_testimonial_slider]';
	?>
	<input type="text" value="&lt;?php echo do_shortcode('<?php echo esc_html( htmlentities( esc_html( $shortcode ), ENT_QUOTES ) ); ?>'); ?&gt;" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
	   
	<div class="clear"></div>
	
	<h3><?php echo esc_html( esc_html( __( 'To print form for this slider into WordPress Post/Page use below code', 'easy-testimonial-rotator' ) ) ); ?></h3>
	<input type="text" value='[print_best_testimonial_form ] ' style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
	<div class="clear"></div>
	<h3><?php echo esc_html( esc_html( __( 'To print form for this slider into WordPress theme/template PHP files use below code', 'easy-testimonial-rotator' ) ) ); ?></h3>
	<?php
		$shortcode = '[print_best_testimonial_form]';
	?>
	<input type="text" value="&lt;?php echo do_shortcode('<?php echo esc_html( htmlentities( esc_html( $shortcode ), ENT_QUOTES ) ); ?>'); ?&gt;" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
	<div class="clear"></div>
	
	<?php
}

function etr_print_best_testimonial_form_func( $atts ) {

	global $wpdb;
	$settings_main = get_option( 'best_testimonial_options' );
	$settings_main['id'] = 1;
	$settings = get_option( 'i13_default_form_options' );

	$uploads = wp_upload_dir();
	$baseurl = $uploads['baseurl'];
	$baseurl .= '/easy-testimonial-rotator/';
	$baseDir = $uploads['basedir'];
	$baseDir = str_replace( '\\', '/', $baseDir );
	$pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';
	$randNo = uniqid( 'rate_' );
	$vNonce = wp_create_nonce( 'vNonce' );
	$submitNonce = wp_create_nonce( 'SubmitNonce' );
	$etr_i13_captcha_img = new Etr_I13_Captcha_Img();
	$captchaImgName = $etr_i13_captcha_img->etr_generateI13Captcha();

	// $res=$i13_captcha_img->etr_verifyCaptcha('i13_cap_5758d902eb069','yhhhgh');
	wp_enqueue_script( 'jquery' );
	wp_enqueue_style( 'best-testimonial-bx-cols-css' );
	ob_start();

	?>
	<!-- etr_print_best_testimonial_form_func --><form id="testimonial-form-<?php echo esc_attr( $settings_main['id'] ); ?>" class="testimonial-form"  method="post">
		 <div style="display:none" class="success" id="success_<?php echo esc_attr( $settings_main['id'] ); ?>" ><?php echo esc_html( $settings['success_msg'] ); ?></div>
		 <div style="display:none" class="error" id="error_<?php echo esc_attr( $settings_main['id'] ); ?>" ><?php echo esc_html( $settings['error_msg'] ); ?></div>
		 <?php if ( $settings['show_author_name'] ) : ?>  
			<div>
				<span><?php echo esc_html( $settings['author_name_label'] ); ?> <?php
				if ( $settings['is_author_name_field_required'] ) :
					?>
					 <span class="required">*</span><?php endif; ?></span>
				<input  type="text"  name="auth_name" id="auth_name_<?php echo esc_attr( $settings_main['id'] ); ?>">
				<label id="error_auth_name_<?php echo esc_attr( $settings_main['id'] ); ?>" for="auth_name_<?php echo esc_attr( $settings_main['id'] ); ?>"  class="image_error error_<?php echo esc_attr( $settings_main['id'] ); ?>"></label>

			</div>
		 <?php endif; ?>
		 <?php if ( $settings['show_author_des'] ) : ?>     
			<div>
				<span><?php echo esc_html( $settings['author_designation_lable'] ); ?> <?php
				if ( $settings['is_author_designation_field_required'] ) :
					?>
					 <span class="required">*</span><?php endif; ?></span>
			   <input  type="text"   name="auth_desn" id="auth_desn_<?php echo esc_attr( $settings_main['id'] ); ?>">
			   <label id="error_auth_desn_<?php echo esc_attr( $settings_main['id'] ); ?>" for="auth_desn_<?php echo esc_attr( $settings_main['id'] ); ?>"  class="image_error error_<?php echo esc_attr( $settings_main['id'] ); ?>"></label>

			</div>
		 <?php endif; ?>   
		  <?php if ( $settings['show_author_email'] ) : ?>    
			<div>
					
				<span><?php echo esc_html( $settings['author_email_label'] ); ?> <?php
				if ( $settings['is_author_email_field_required'] ) :
					?>
					 <span class="required">*</span><?php endif; ?></span>
				<input  type="text"  name="auth_email" id="auth_email_<?php echo esc_attr( $settings_main['id'] ); ?>">
				<label id="error_auth_email_<?php echo esc_attr( $settings_main['id'] ); ?>" for="auth_email_<?php echo esc_attr( $settings_main['id'] ); ?>"  class="image_error error_<?php echo esc_attr( $settings_main['id'] ); ?>"></label>

			</div>
		   <?php endif; ?> 
		  <?php if ( $settings['show_photo_upload'] ) : ?>
			<div>
				   
							<span><?php echo esc_html( $settings['author_photo_label'] ); ?>
											 <?php
												if ( $settings['photo_upload_field_required'] ) :
													?>
								 <span class="required">*</span><?php endif; ?></span>
							<img src="<?php echo esc_attr( esc_url( plugins_url( '/images/no-img.png', __FILE__ ) ) ); ?>" id="img_disp_<?php echo esc_attr( $settings_main['id'] ); ?>" class="auth_photo" />
							<a style="vertical-align: auto;" href="javascript:;" class="form_link_label" id="niks_gav_<?php echo esc_attr( $settings_main['id'] ); ?>"><b><?php echo esc_html( esc_html( __( "Click here to use gravatar.com's avtar", 'easy-testimonial-rotator' ) ) ); ?></b></a>&nbsp;&nbsp;<img id="gav_loader_<?php echo esc_attr( $settings_main['id'] ); ?>" class="gav_loader_<?php echo esc_attr( $settings_main['id'] ); ?>" style="display:none" src="<?php echo esc_attr( esc_url( plugins_url( 'images/ajax-loader.gif', __FILE__ ) ) ); ?>"  />
							<input id="HdnMediaGrevEmail_<?php echo esc_attr( $settings_main['id'] ); ?>" name="HdnMediaGrevEmail" type="hidden" value="" />
							<label id="error_HdnMediaGrevEmail_<?php echo esc_attr( $settings_main['id'] ); ?>" for="HdnMediaGrevEmail_<?php echo esc_attr( $settings_main['id'] ); ?>"  class="image_error error_<?php echo esc_attr( $settings_main['id'] ); ?>"></label>
							<script>
								function retr1(fetr1){/in/.test(document.readyState)?setTimeout(retr1,9,fetr1):fetr1()}
								retr1(function(){ 
										 
										 jQuery( "#niks_gav_<?php echo esc_attr( $settings_main['id'] ); ?>" ).click(function() {
											var email_gav_<?php echo esc_attr( $settings_main['id'] ); ?> = prompt("<?php echo esc_attr( __( 'Please enter your gravatar.com email', 'easy-testimonial-rotator' ) ); ?>", "");
											if(jQuery.trim(email_gav_<?php echo esc_attr( $settings_main['id'] ); ?>)!='' && email_gav_<?php echo esc_attr( $settings_main['id'] ); ?>!=null){

														jQuery("#gav_loader_<?php echo esc_html( $settings_main['id'] ); ?>").show(); 
														var data_grav = {
																	 'action': 'etr_get_grav_avtar',
																	 'email': jQuery.trim(email_gav_<?php echo esc_html( $settings_main['id'] ); ?>),
																	 'vNonce':'<?php echo esc_html( $vNonce ); ?>'
															 };
															 jQuery.post('<?php echo esc_attr( esc_url( admin_url( 'admin-ajax.php' ) ) ); ?>', data_grav, function(data) {

																   jQuery("#HdnMediaGrevEmail_<?php echo esc_html( $settings_main['id'] ); ?>").val(jQuery.trim(email_gav_<?php echo esc_html( $settings_main['id'] ); ?>)); 
																   jQuery("#img_disp_<?php echo esc_html( $settings_main['id'] ); ?>").attr('src', data);
																   jQuery("#gav_loader_<?php echo esc_html( $settings_main['id'] ); ?>").hide();

															 });




											}

										  });
									 });
						  </script>   
				  
			</div>
			<?php endif; ?>
			 <div>
				<span><?php echo esc_html( $settings['testimonial_label'] ); ?> <span class="required">*</span></span>
				<textarea name="testimonial" id="testimonial_<?php echo esc_attr( $settings_main['id'] ); ?>"  ></textarea>
				<label id="error_testimonial_<?php echo esc_attr( $settings_main['id'] ); ?>" for="testimonial_<?php echo esc_attr( $settings_main['id'] ); ?>"  class="image_error error_<?php echo esc_attr( $settings_main['id'] ); ?>"></label>

		</div>
			 <?php if ( $settings['show_captcha'] ) : ?>
								 <div>
				 <div class="cpatchadiv">
						
					<span><img id="captcha_img_<?php echo esc_attr( $settings_main['id'] ); ?>" name="captcha_img_<?php echo esc_attr( $settings_main['id'] ); ?>" src="<?php echo esc_attr( esc_url( $baseurl . $captchaImgName . '.jpeg' ) ); ?>"/></span>
					<input class="captchaFiled" autocomplete="off" placeholder="<?php echo esc_attr( $settings['captcha_label'] ); ?>"  type="text"   name="captcha" id="captcha_<?php echo esc_attr( $settings_main['id'] ); ?>">
					<img  id="reload_captcha_<?php echo esc_attr( $settings_main['id'] ); ?>" class="reload_captcha" title="<?php echo esc_attr( $settings['new_captcha_label'] ); ?>"  src="<?php echo esc_attr( esc_url( plugins_url( '/images/reload_captcha.png', __FILE__ ) ) ); ?>" />
					<input type="hidden" name="cpatcha_name" id="cpatcha_name_<?php echo esc_attr( $settings_main['id'] ); ?>" value="<?php echo esc_attr( $captchaImgName ); ?>" />

				 </div> 
				 <label id="error_cpatcha_<?php echo esc_attr( $settings_main['id'] ); ?>" for="captcha_img_<?php echo esc_attr( $settings_main['id'] ); ?>"  class="image_error error_<?php echo esc_attr( $settings_main['id'] ); ?>"></label>
				 <script>
					<?php $intval = uniqid( 'interval_' ); ?>
			   
					var <?php echo esc_html( $intval ); ?> = setInterval(function() {

					if(document.readyState === 'complete') {

					   clearInterval(<?php echo esc_html( $intval ); ?>);
					   
						 
						  jQuery( "#reload_captcha_<?php echo esc_html( $settings_main['id'] ); ?>" ).click(function() {

							var data_captcha = {
											'action': 'etr_get_new_captcha',
											'vNonce':'<?php echo esc_html( $vNonce ); ?>',
											'oldcaptcha':jQuery('#cpatcha_name_<?php echo esc_html( $settings_main['id'] ); ?>').val()
									};

									jQuery.ajax({

										url :'<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',

										type:'post',

										dataType : "json",

										data: data_captcha,

										success:function(data) {

										  jQuery("#cpatcha_name_<?php echo esc_html( $settings_main['id'] ); ?>").val(data.cpatcha_name);
										  jQuery("#captcha_img_<?php echo esc_html( $settings_main['id'] ); ?>").attr('src',data.captcha_url);
										},

										error: function() {alert('error'); }

										});
							   });

						  }    
				}, 100);
				 </script>
			</div>
			
			<?php endif; ?>
		 <div class="btn_submit_testimonial_form">
				<input type="hidden" value="<?php echo esc_attr( $submitNonce ); ?>" name="tnonce"  id="tnonce_<?php echo esc_attr( $settings_main['id'] ); ?>" />
				<input type="hidden" value="etr_save_testimonial" name="action" id="action" />
				<input type="hidden" value="<?php echo esc_attr( $settings_main['id'] ); ?>" name="form_id" id="form_id_<?php echo esc_attr( $settings_main['id'] ); ?>" />
				<button name="submit_<?php echo esc_attr( $settings_main['id'] ); ?>" type="button" id="submit_<?php echo esc_attr( $settings_main['id'] ); ?>" class="submit"><?php echo esc_html( $settings['submit_label'] ); ?></button>&nbsp;<img src="<?php echo esc_attr( esc_url( plugins_url( '/images/ajax-loader-2.gif', __FILE__ ) ) ); ?>"  id="ajax_loader_<?php echo esc_attr( $settings_main['id'] ); ?>" style="display:none"  />
				<script>
	
					<?php $intval = uniqid( 'interval_' ); ?>
			   
					var <?php echo esc_html( $intval ); ?> = setInterval(function() {

					if(document.readyState === 'complete') {

					   clearInterval(<?php echo esc_html( $intval ); ?>);
					   
							
							  jQuery( "#submit_<?php echo esc_html( $settings_main['id'] ); ?>" ).click(function() {

								jQuery('#success_<?php echo esc_html( $settings_main['id'] ); ?>').hide();
								 jQuery('#error_<?php echo esc_html( $settings_main['id'] ); ?>').hide();
								 jQuery('#ajax_loader_<?php echo esc_html( $settings_main['id'] ); ?>').show();

								  var str = jQuery( "#testimonial-form-<?php echo esc_html( $settings_main['id'] ); ?>" ).serialize();


										jQuery.ajax({

											url :'<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',

											type:'post',

											dataType : "json",

											data: str,

											success:function(data) {

												 jQuery('#ajax_loader_<?php echo esc_html( $settings_main['id'] ); ?>').hide();
												 jQuery(".error_<?php echo esc_html( $settings_main['id'] ); ?>").html('');

												if(data.result.hasOwnProperty('fields_error')){
													var flag=true;
													var first_element='';
													jQuery.each(data.result.fields_error, function(i, item) {
														if(flag==true){
															flag=false;
															first_element="error_"+i;
														}
														jQuery("#error_"+i).html(item);
													});

													var replaceEle=first_element.replace("error_", "");
													jQuery("#"+replaceEle).focus();

													 jQuery('html, body').animate({
														   scrollTop: jQuery('#'+first_element).offset().top-150
													  }, 500);



												 }
												 else if(data.result.hasOwnProperty('error')){

												   if(data.result.hasOwnProperty('captchaRefreshed')){


														jQuery("#cpatcha_name_<?php echo esc_html( $settings_main['id'] ); ?>").val(data.result.captchaRefreshed.cpatcha_name);
														jQuery("#captcha_img_<?php echo esc_html( $settings_main['id'] ); ?>").attr('src',data.result.captchaRefreshed.captcha_url);

													}

													 jQuery('#error_<?php echo esc_html( $settings_main['id'] ); ?>').show();
														jQuery('html, body').animate({
														   scrollTop: jQuery('#error_<?php echo esc_html( $settings_main['id'] ); ?>').offset().top-100
													   }, 500);

													 jQuery("#captcha_<?php echo esc_html( $settings_main['id'] ); ?>").val('');  


												 }
												 else if(data.result.hasOwnProperty('success')){

													if(data.result.hasOwnProperty('resetFormsFields')){

														 jQuery.each(data.result.resetFormsFields, function(i, item) {

															jQuery("#"+i).val('');

														});

														<?php if ( $settings['show_photo_upload'] ) : ?>
															var no_img= '<?php echo esc_html( plugins_url( '/images/no-img.png', __FILE__ ) ); ?>';
															jQuery("#img_disp_<?php echo esc_html( $settings_main['id'] ); ?>").attr('src',no_img);
														<?php endif; ?>        

													}
													if(data.result.hasOwnProperty('captchaRefreshed')){


														jQuery("#cpatcha_name_<?php echo esc_html( $settings_main['id'] ); ?>").val(data.result.captchaRefreshed.cpatcha_name);
														jQuery("#captcha_img_<?php echo esc_html( $settings_main['id'] ); ?>").attr('src',data.result.captchaRefreshed.captcha_url);

													}
														jQuery("#<?php echo esc_html( $randNo ); ?>").html('');


														 jQuery('#success_<?php echo esc_html( $settings_main['id'] ); ?>').show();
														 jQuery('html, body').animate({
															scrollTop: jQuery('#success_<?php echo esc_html( $settings_main['id'] ); ?>').offset().top-100
														}, 500);

												 }

											},

											error: function() {
																alert('error'); 
																jQuery('#ajax_loader_<?php echo esc_html( $settings_main['id'] ); ?>').hide(); 
															}



											});


							  });
						  }    
				}, 100);       
				 </script>
			</div>
   </form><!-- end etr_print_best_testimonial_form_func -->
	<?php

	$output = ob_get_clean();
	return $output;

}
function etr_print_best_testimonial_slider_func( $atts ) {

	   global $wpdb;
	   $rand_Numb = uniqid( 'thumnail_slider' );
	   $rand_Num_td = uniqid( 'divSliderMain' );
	   $settings = get_option( 'best_testimonial_options' );
	   $settings['style'] = 'style 1';

	   $rand_var_name = uniqid( 'rand_' );
	   $wpcurrentdir = dirname( __FILE__ );
	   $wpcurrentdir = str_replace( '\\', '/', $wpcurrentdir );

	   $uploads = wp_upload_dir();
	   $baseurl = $uploads['baseurl'];
	   $baseurl .= '/easy-testimonial-rotator/';
	   $baseDir = $uploads['basedir'];
	   $baseDir = str_replace( '\\', '/', $baseDir );
	   $pathToImagesFolder = $baseDir . '/easy-testimonial-rotator';
	   wp_enqueue_script( 'jquery' );
	   wp_enqueue_style( 'best-testimonial-bx' );
	   wp_enqueue_style( 'best-testimonial-bx-cols-css' );
	   wp_enqueue_script( 'best-testimonial-slider' );
	   ob_start();

	?>
 <!-- etr_print_best_testimonial_slider_func --><?php $url = plugin_dir_url( __FILE__ ); ?>
		<?php if ( 'style 1' == $settings['style'] ) : ?>  
					  
			<div class="class_fulldiv style1">

				 <style type='text/css' >

					#<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-viewport{left:auto;padding: 0px;padding-bottom:10px} 
					#<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-viewport {
						background: none repeat scroll 0 0 <?php echo esc_html( $settings['slider_back_color'] ); ?> ;

					  }
					   #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et {

						  border: <?php echo esc_html( $settings['box_border_size'] ); ?>px solid <?php echo esc_html( $settings['box_border_color'] ); ?>;
						  box-shadow: 0 0 5px <?php echo esc_html( $settings['box_shadow_color'] ); ?>;
					  }
					   #<?php echo esc_html( $rand_Num_td ); ?>  .bx-wrapper-et .bx-prev {

							background: rgba(0, 0, 0, 0) url("<?php echo esc_url( plugins_url( 'images/controls.png', __FILE__ ) ); ?>") no-repeat scroll -1px -31px ;
							left: 0;
							 <?php if ( $settings['show_arrows'] ) : ?>
							 display:block ;
							<?php endif; ?>
						}

						 #<?php echo esc_html( $rand_Num_td ); ?>  .bx-wrapper-et .bx-next {
							background: rgba(0, 0, 0, 0) url("<?php echo esc_url( plugins_url( 'images/controls.png', __FILE__ ) ); ?>") no-repeat scroll -42px -31px ;
							right: 0;
							 <?php if ( $settings['show_arrows'] ) : ?>
							 display:block ;
							<?php endif; ?>
						}

						 #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-next:hover,  #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-next:focus {
							  background-position: -42px 0 ;
						 }
						 #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-prev:hover, #<?php echo esc_html( $rand_Num_td ); ?> .bx-wrapper-et .bx-prev:focus {
							background-position: -1px 0 ;
						  }
						  
					</style>
				<div class="childDiv_style1" id="<?php echo esc_html( $rand_Num_td ); ?>" style="display:none;opacity: 0">  

					<div class="bxsliderx rowcust" >

					   <?php

							$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'b_testimo_slide where  status=%d order by createdon desc', 1 ), ARRAY_A );
							$randOmeAlbName = uniqid( 'slider_' );
						if ( count( $rows ) > 0 ) {

							?>
							<?php foreach ( $rows as $row ) : ?>
								<?php
								if ( '' != $row['image_name'] || null != $row['image_name'] ) {

										   // $outputimg = $baseurl.$row['image_name'];

											$imagename = $row['image_name'];
											$imageUploadTo = $pathToImagesFolder . '/' . $imagename;
											$imageUploadTo = str_replace( '\\', '/', $imageUploadTo );
											$pathinfo = pathinfo( $imageUploadTo );
											$filenamewithoutextension = $pathinfo['filename'];
											$imageheight = 300;
											$imagewidth = 300;
											$outputimg = '';

											$outputimg = $baseurl . $row['image_name'];
									if ( 0 == $settings['resize_images'] ) {

										$outputimg = $baseurl . $row['image_name'];

									} else {

										$imagetoCheck = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
										$imagetoCheckSmall = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );

										if ( file_exists( $imagetoCheck ) ) {
													$outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];

										} else if ( file_exists( $imagetoCheckSmall ) ) {
											 $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );
										} else {

											if ( function_exists( 'wp_get_image_editor' ) ) {

													  $image = wp_get_image_editor( $pathToImagesFolder . '/' . $row['image_name'] );
												if ( ! is_wp_error( $image ) ) {
													$image->resize( $imagewidth, $imageheight, true );
													$image->save( $imagetoCheck );
													// $outputimg = $baseurl.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

													if ( file_exists( $imagetoCheck ) ) {
															   $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo['extension'];
													} else if ( file_exists( $imagetoCheckSmall ) ) {
														  $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . strtolower( $pathinfo['extension'] );
													}
												} else {
													$outputimg = $baseurl . $row['image_name'];
												}
											} else {

													   $outputimg = $baseurl . $row['image_name'];
											}

													// $url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

										}
									}
								} else if ( '' != $row['gravatar_email'] && null != $row['gravatar_email'] ) {

									$email = md5( $row['gravatar_email'] );
									$outputimg = "https://www.gravatar.com/avatar/$email?s=200";
								} else {

									$outputimg = plugins_url( 'images/no_photo.png', __FILE__ );
								}
								?>
								<div class="setMargin">

									<div class="rowupdate margin_Quotes tesimonial_slider_row">


										<div class="colupdate-sm-12 setmargin"  >
											<div class="setfloat floatLeft">
												<img class="imgupdate-circle imgupdate-circle-img" src="<?php echo esc_attr( esc_url( $outputimg ) ); ?>" style="">
											</div> 
											<blockquote class="open_close">

												<div class="quotes_content">
												  <?php echo esc_html( $row['testimonial'] ); ?>   
												</div>

												<?php if ( $settings['show_author_name'] && '' != trim( $row['auth_name'] ) ) : ?> 
													 <span class="author_name"><?php echo esc_html( $row['auth_name'] ); ?></span>
												<?php endif; ?>     
												<?php if ( $settings['show_author_des'] && '' != trim( $row['auth_desn'] ) ) : ?> 
													 <span class="author_position"><?php echo esc_html( $row['auth_desn'] ); ?></span>
												<?php endif; ?>

											</blockquote>


										</div>
									</div>



								</div>
								<?php endforeach; ?>
						<?php } ?>
					</div>
				</div>
				<script>
	
				 <?php $intval = uniqid( 'interval_' ); ?>
			   
					var <?php echo esc_html( $intval ); ?> = setInterval(function() {

					if(document.readyState === 'complete') {

					   clearInterval(<?php echo esc_html( $intval ); ?>);
						


							var sliderwidth=jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").width();
							if(sliderwidth<=699){

								jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bxsliderx.rowcust div div.rowupdate.margin_Quotes div.colupdate-sm-12.setmargin .setfloat').removeClass('floatLeft');
							}

							var timer;
							var width = jQuery(window).width();
							jQuery(window).bind('resize', function(){
								if(jQuery(window).width() != width){

									width = jQuery(window).width();
									<!-- -->
									timer && clearTimeout(timer);
									timer = setTimeout(resizecall, 100);

								}   
							});

							function resizecall(){

								var sliderwidth=jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").width();
								if(sliderwidth<=699){

									jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bxsliderx.rowcust div div.rowupdate.margin_Quotes div.colupdate-sm-12.setmargin .setfloat').removeClass('floatLeft');

									 jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bx-wrapper-et div.bx-viewport').css('height','auto');



								}else{

									jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bxsliderx.rowcust div div.rowupdate.margin_Quotes div.colupdate-sm-12.setmargin .setfloat').addClass('floatLeft');

									jQuery('.style1 #<?php echo esc_html( $rand_Num_td ); ?> div.bx-wrapper-et div.bx-viewport').css('height','auto');




								}

							}

							jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").show();
							var sld_<?php echo esc_html( $rand_Num_td ); ?> = jQuery('#<?php echo esc_html( $rand_Num_td ); ?> .bxsliderx').bxSliderx({
								slideMargin: 100,
								auto: <?php echo ( esc_html( $settings['auto'] ) ) == 1 ? 'true' : 'false'; ?>,
								infiniteLoop: <?php echo ( esc_html( $settings['is_circular_slider'] ) ) == 1 ? 'true' : 'false'; ?>,
								minSlides: 1,
								maxSlides: 1,
								moveSlides: 1,
								preventDefaultSwipeY:false,
								speed: <?php echo esc_html( $settings['speed'] ); ?>,
								pause: <?php echo esc_html( $settings['pause'] ); ?>,
								adaptiveHeight: <?php echo ( esc_html( $settings['is_adaptive_height'] ) ) == 1 ? 'true' : 'false'; ?>,
								controls: <?php echo ( esc_html( $settings['show_arrows'] ) ) == 1 ? 'true' : 'false'; ?>,
								pager: <?php echo ( esc_html( $settings['show_pagination'] ) ) == 1 ? 'true' : 'false'; ?>,
								touchEnabled: <?php echo ( esc_html( $settings['touch_enabled'] ) ) == 1 ? 'true' : 'false'; ?>,
								wrapperClass: 'bx-wrapper-et',
								 onSlideBefore: function(slideElement){
									
										jQuery(slideElement).find('img').each(function(index, elm) {

											  if(!elm.complete || elm.naturalWidth === 0){
														vsrc= jQuery(elm).attr("src");
														jQuery(elm).removeAttr("src");
														dsrc= jQuery(elm).attr("data-src");
														lsrc= jQuery(elm).attr("data-lazy-src");

														 <!-- -->
														 if(dsrc!== undefined && dsrc!='' && dsrc!=vsrc){
																 jQuery(elm).attr("src",dsrc);
															}
															else if(lsrc!== undefined && lsrc!=vsrc){

																 jQuery(elm).attr("src",lsrc);
															}
															else{

																 jQuery(elm).attr("src",vsrc);

															}   

														elm= jQuery(elm)[0];      
														if(!elm.complete && elm.naturalHeight == 0){

															 jQuery(elm).removeAttr('loading');
															 jQuery(elm).removeAttr('data-lazy-type');


															 jQuery(elm).removeClass('lazy');

															 jQuery(elm).removeClass('lazyLoad');
															 jQuery(elm).removeClass('lazy-loaded');
															 jQuery(elm).removeClass('jetpack-lazy-image');
															 jQuery(elm).removeClass('jetpack-lazy-image--handled');
															 jQuery(elm).removeClass('lazy-hidden');

													}
												}

										});

									},
								 onSliderLoad: function(){
									
									jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").css('opacity','1');
									
								 }

							});
					 }    
				}, 100);
				
				
				
				
				
				 window.addEventListener('load', function() {


						setTimeout(function(){ 
								
								if(jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").find('.bx-loading').length>0){
									
										jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").find('img').each(function(index, elm) {

												 if(!elm.complete || elm.naturalWidth === 0){
													var toload='';
													var toloadval='';
													jQuery.each(this.attributes, function(i, attrib){

															var value = attrib.value;
															var aname=attrib.name;

															var pattern = /^((http|https):\/\/)/;

															if(pattern.test(value) && aname!='src') {

																	toload=aname;
																	toloadval=value;
															 }
													 });

															vsrc=jQuery(elm).attr("src");
															jQuery(elm).removeAttr("src");
															dsrc=jQuery(elm).attr("data-src");
															lsrc=jQuery(elm).attr("data-lazy-src");


															   if(dsrc!== undefined && dsrc!='' && dsrc!=vsrc){
																							 jQuery(elm).attr("src",dsrc);
																	}
																	else if(lsrc!== undefined && lsrc!=vsrc){

																					 jQuery(elm).attr("src",lsrc);
																	}
																	else if(toload!='' && toload!='srcset' && toloadval!='' && toloadval!=vsrc){

																			jQuery(elm).removeAttr(toload);
																			jQuery(elm).attr("src",toloadval);


																		} 
																	else{

																					jQuery(elm).attr("src",vsrc);

															   }   

															elm=jQuery(elm)[0];      
															 if(!elm.complete && elm.naturalHeight == 0){

																			 jQuery(elm).removeAttr('loading');
																			 jQuery(elm).removeAttr('data-lazy-type');


																			 jQuery(elm).removeClass('lazy');

																			 jQuery(elm).removeClass('lazyLoad');
																			 jQuery(elm).removeClass('lazy-loaded');
																			 jQuery(elm).removeClass('jetpack-lazy-image');
																			 jQuery(elm).removeClass('jetpack-lazy-image--handled');
																			 jQuery(elm).removeClass('lazy-hidden');

															 }
															 
													}

											}).promise().done( function(){ 
												
													jQuery("#<?php echo esc_html( $rand_Num_td ); ?>").find('.bx-loading').remove();
											} );
									
									}


						   }, 6000);

				});
				
				</script>
				  
			</div><!-- end etr_print_best_testimonial_slider_func --><?php endif; ?>
	<?php
	   $output = ob_get_clean();
	   return $output;
}

function etr_best_testimonial_slider_get_wp_version() {

	global $wp_version;
	return $wp_version;
}


function etr_best_testimonial_slider_is_plugin_page() {

	$server_uri = 'http://';
	if ( isset( $_SERVER['HTTP_HOST'] ) ) {
		$server_uri .= sanitize_text_field( $_SERVER['HTTP_HOST'] );
	}

	if ( isset( $_SERVER['REQUEST_URI'] ) ) {

		$server_uri .= sanitize_text_field( $_SERVER['REQUEST_URI'] );
	}

	foreach ( array( 'best_testimonial_slider_testimonial_management', 'best_testimonial_slider' ) as $allowURI ) {

		if ( stristr( $server_uri, $allowURI ) ) {
			return true;
		}
	}

	return false;
}

function etr_best_testimonial_slider_admin_scripts_init() {

	if ( etr_best_testimonial_slider_is_plugin_page() ) {
		// double check for WordPress version and function exists
		if ( function_exists( 'wp_enqueue_media' ) && version_compare( etr_best_testimonial_slider_get_wp_version(), '3.5', '>=' ) ) {
			// call for new media manager
			wp_enqueue_media();
		}
		wp_enqueue_style( 'media' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script( 'jquery-ui-spinner' );

	}
}

function etr_remove_extra_p_tags( $content ) {

	if ( strpos( $content, 'etr_print_best_testimonial_slider_func' ) !== false ) {

		$pattern = '/<!-- etr_print_best_testimonial_slider_func -->(.*)<!-- end etr_print_best_testimonial_slider_func -->/Uis';
		$content = preg_replace_callback(
			$pattern,
			function( $matches ) {

				$altered = str_replace( '<p>', '', $matches[1] );
				$altered = str_replace( '</p>', '', $altered );

				$altered = str_replace( '&#038;', '&', $altered );
				$altered = str_replace( '&#8221;', '"', $altered );

				return @str_replace( $matches[1], $altered, $matches[0] );
			},
			$content
		);

	}

	if ( strpos( $content, 'etr_print_best_testimonial_form_func' ) !== false ) {

		$pattern = '/<!-- etr_print_best_testimonial_form_func -->(.*)<!-- end etr_print_best_testimonial_form_func -->/Uis';
		$content = preg_replace_callback(
			$pattern,
			function( $matches ) {

				$altered = str_replace( '<p>', '', $matches[1] );
				$altered = str_replace( '</p>', '', $altered );

				$altered = str_replace( '&#038;', '&', $altered );
				$altered = str_replace( '&#8221;', '"', $altered );

				return @str_replace( $matches[1], $altered, $matches[0] );
			},
			$content
		);

	}

		$content = str_replace( '<p><!-- etr_print_best_testimonial_slider_func -->', '<!-- etr_print_best_testimonial_slider_func -->', $content );
		$content = str_replace( '<!-- end etr_print_best_testimonial_slider_func --></p>', '<!-- end etr_print_best_testimonial_slider_func -->', $content );

		$content = str_replace( '<p><!-- etr_print_best_testimonial_form_func -->', '<!-- etr_print_best_testimonial_form_func -->', $content );
		$content = str_replace( '<!-- end etr_print_best_testimonial_form_func --></p>', '<!-- end etr_print_best_testimonial_form_func -->', $content );

		return $content;
}

  add_filter( 'widget_text_content', 'etr_remove_extra_p_tags' );
  add_filter( 'the_content', 'etr_remove_extra_p_tags' );


function i13_etr_render_block_defaults( $block_content, $block ) {

	$block_content = etr_remove_extra_p_tags( $block_content );
	return $block_content;

}


add_filter( 'render_block', 'i13_etr_render_block_defaults', 10, 2 );
