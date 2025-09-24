<?php
namespace Frontend_Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Frontend_Admin\Classes\Submit_Form' ) ) :

	class Submit_Form {


		public function check_inline_field() {
			// validate
			if ( ! acf_verify_ajax() ) {
				die();
			}
			do_action( 'frontend_admin/validate_field' );

			// vars
			$json = array(
				'valid'   => 1,
				'errors'  => 0,
				'updates' => array(),
			);

			if ( ! empty( $_POST['acff'] ) ) {
				if ( ! empty( $_POST['acff']['_validate_email'] ) ) {
					acf_add_validation_error( '', __( 'Spam Detected', 'acf' ) );
				}

				$_post = wp_kses_post_deep( $_POST['acff'] ); 

				foreach ( $_post as $source => $fields ) {

					foreach ( $fields as $key => $value ) {

						$field = acf_maybe_get_field( $key );

						if ( ! $field ) {
							continue;
						}

						if ( $field['type'] == 'fields_select' ) {
							$fields_select = $key;
						}

						$input = 'acff[' . $source . '][' . $key . ']';

						// validate
						$valid = acf_validate_value( $value, $field, $input );

						if ( $valid ) {
							//$value = $this->check_for_files( $value, $field );
							acf_update_value( $value, $source, $field );

							if ( empty( $fields_select ) ) {
								$object            = get_field_object( $key, $source );
								$json['updates'][] = array(
									'html' => fea_instance()->dynamic_values->display_field( $object ),
								);
							}
						}
					}
					if ( ! empty( $fields_select ) ) {
						$object            = get_field_object( $fields_select, $source );
						$json['updates'][] = array(
							'html' => fea_instance()->dynamic_values->display_field( $object ),
						);
					}
				}
			}

			// vars
			$errors = acf_get_validation_errors();

			// bail ealry if no errors
			if ( ! $errors ) {
				wp_send_json_success( $json );
			}

			// update vars
			$json['valid']  = 0;
			$json['errors'] = $errors;

			// return
			wp_send_json_success( $json );
		}

		public function validate_submitted_form() {
			// validate
			if ( ! acf_verify_ajax() ) {
				die();
			}

			do_action( 'acf/validate_save_post' );

			// vars
			$json = array(
				'valid'  => 1,
				'errors' => 0,
			);

			if ( ! empty( $_POST['acff'] ) ) {
				if ( ! empty( $_POST['acff']['_validate_email'] ) ) {
					acf_add_validation_error( '', __( 'Spam Detected', 'acf' ) );
				}
				$data = wp_kses_post_deep( $_POST['acff'] ); 
				$data_types = array( 'form', 'post', 'user', 'term', 'options', 'woo_product' );
				foreach ( $data_types as $type ) {
					if ( isset( $data[ $type ] ) ) {
						$this->validate_values( $data[ $type ], 'acff[' . $type . ']' );
					}
				}
			}
			// vars
			$errors = acf_get_validation_errors();

			// bail ealry if no errors
			if ( ! $errors ) {
				wp_send_json_success( $json );
			}

			// update vars
			$json['valid']  = 0;
			$json['errors'] = $errors;

			// return
			wp_send_json_success( $json );

		}

		function validate_values( $values, $input_prefix = '' ) {
			// bail early if empty
			if ( empty( $values ) ) {
				return;
			}
			foreach ( $values as $key => $value ) {
				$field = acf_maybe_get_field( $key );

				if ( ! $field ) {
					continue;
				}

				$input = $input_prefix . '[' . $key . ']';
				// bail early if not found

				if ( isset( $field['frontend_admin_display_mode'] ) && $field['frontend_admin_display_mode'] == 'hidden' ) {
					continue;
				}

				// validate
				acf_validate_value( $value, $field, $input );

			}

		}

		public function check_submit_form() {
			// verify nonce
			if ( ! feadmin_verify_nonce( 'fea_form' ) ) {
				wp_send_json_error( __( 'Nonce Error', 'acf-frontend-form-element' ) );
			}

			// bail ealry if form not submit
			if ( empty( $_POST['_acf_form'] ) ) {
				wp_send_json_error( __( 'No Form Data', 'acf-frontend-form-element' ) );
			}

			// load form
			$form = wp_kses_post_deep( json_decode( fea_decrypt( $_POST['_acf_form'] ), true ) );
			
			// bail ealry if form is corrupt
			if ( empty( $form ) ) {
				wp_send_json_error( __( 'No Form Data', 'acf-frontend-form-element' ) );
			}

			// submit
			$this->submit_form( $form );

		}

		function create_record( $form, $save = false ) {
			// Retrieve all form fields and their values
			if ( empty( $form['record'] ) ) {
				$form['record'] = array(
					'fields' => false,
				);
			}

			if ( isset( $form['field_objects'] ) ) {
				$objects = $form['field_objects'];
			} elseif ( isset( $form['fields'] ) ) {
				$objects = $form['fields'];
			} else {
				$objects = false;
			}

			$form['record'] = array();

			if ( ! empty( $form['save_all_data'] ) && in_array( 'verify_email', $form['save_all_data'] ) ) {
				$form['record']['emails_to_verify'] = array();
			}

			$types = array( 'post', 'user', 'term', 'product' );
			foreach ( $types as $type ) {
				if ( isset( $form[$type . '_id'] ) ) {
					$form['record'][ $type ] = $form[$type . '_id'];
				}
			}

			if ( isset( $_POST['_acf_taxonomy_type'] ) ) {
				$form['record']['taxonomy_type'] = sanitize_text_field( $_POST['_acf_taxonomy_type'] );
			}
			if ( isset( $_POST['_acf_status'] ) ) {
				$form['record']['status'] = sanitize_text_field( $_POST['_acf_status'] );
			}

			if ( ! empty( $form['submission_title'] ) ) {
				$form['dynamic_title'] = true;
			}

			if ( ! empty( $_POST ) ) {
				
				foreach ( array_keys( $_POST['acff'] ) as $key ) {
					$key = sanitize_text_field( $key );
					
					$inputs = $_POST['acff'][$key];
					if ( is_array( $inputs ) ) {
						foreach ( $inputs as $index => $input ) {
							$form = $this->add_value_to_record( $form, $index, $input, $key, $objects );
						}
					}
				}
					
			}

			if ( ! empty( $form['dynamic_title'] ) ) {
				$form['submission_title'] = fea_instance()->dynamic_values->get_dynamic_values( $form['submission_title'], $form );
			}

			// add global for backwards compatibility
			$GLOBALS['admin_form'] = $form;
			global $fea_form;
			$fea_form = $form;

			$form = fea_instance()->dynamic_values->get_form_dynamic_values( $form );

			if ( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'save' ) {
				$this->reload_form( $form );
			}

			if ( $save ) {
				$save = get_option( 'frontend_admin_save_submissions' );
				if ( isset( $form['no_record'] ) ) {
					$save = false;
				}
				if ( isset( $form['save_form_submissions'] ) ) {
					$save = $form['save_form_submissions'];
				}

				$save = apply_filters( 'frontend_admin/save_submission', $save, $form );

				if ( $save ) {
					$form = $this->save_record( $form, $save );
				}
			}

			return $form;
		}

		function get_sub_field( $key, $field ) {
			// Vars.
			$sub_field = false;

			// Search sub fields.
			if ( ! empty( $field['sub_fields'] ) ) {
				foreach ( $field['sub_fields'] as $sub_field ) {
					if ( $key == $sub_field['key'] ) {
						return $sub_field;
					}
				}
			}

			// return
			return $sub_field;

		}

		function upload_signature( $value ) {
			// If the user skipped the field, escape this function
			if ( empty( $value['id'] ) && empty( $value['src'] ) ) {
				return '';
			}

			// If the user didn't change the signature, leave it alone
			if ( empty( $value['changed'] ) ) {
				return $value['id'];
			}

			$upload     = wp_upload_dir();
			$upload_dir = $upload['basedir'];
			$upload_dir = $upload_dir . '/signatures/';
			if ( ! is_dir( $upload_dir ) ) {
				mkdir( $upload_dir );

				$signatures_htaccess = $upload_dir . '.htaccess';

				// Protect uploads directory for the servers that support .htaccess
				if ( ! file_exists( $signatures_htaccess ) ) {
					file_put_contents( $signatures_htaccess, 'Deny from all' . PHP_EOL ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
				}

				if ( ! file_exists( $upload_dir . 'index.php' ) ) {
					touch( $upload_dir . 'index.php' );
				}
			}

			// Upload dir.
			$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir ) . DIRECTORY_SEPARATOR;

			if ( $value['id'] ) {
				$title = $value['id'];
			} else {
				$title = wp_create_nonce( $value['src'] );
			}

			$img       = str_replace( 'data:image/png;base64,', '', $value['src'] );
			$img       = str_replace( ' ', '+', $img );
			$decoded   = base64_decode( $img );
			$filename  = $title . '.png';
			$file_type = 'image/png';

			$signature_url = $upload_path . $filename;

			if ( empty( $value['src'] ) ) {
				// If the user clear the signature, delete the file
				unlink( $signature_url );
			} else {
				// If the signature is new or is changed save the file in the protected uploads directory.
				$upload_file = file_put_contents( $signature_url, $decoded );
			}

			return $title;
		}

		function check_for_files( $input, $field, $id = false ) {
			if ( ! $id ) {
				$id = $field['key'];
			}

			$multiple = array( 'list_items', 'repeater' );
			if ( in_array( $field['type'], $multiple, true ) ) {
				if ( is_array( $input ) ) {
					$new_input = array();
					$i         = 0;
					foreach ( $input as $index => $row ) {
						$row_id = $id . '-' . $index;
						foreach ( $row as $key => $sub_value ) {
							$sub_field = $this->get_sub_field( $key, $field );
							if ( $sub_field ) {
								$new_input[ $i ][ $key ] = $this->check_for_files( $sub_value, $sub_field, $row_id . '-' . $key );
							}
						}
						$i++;
					}
					$input = $new_input;
				}
			} elseif ( in_array( $field['type'], array( 'signature', 'form_signature' ), true ) ) {
				return $this->upload_signature( $input );
			}
			return $input;
		}

		public function add_value_to_record( $form, $key, $input, $group, $objects = false ) {
			$record = $form['record'];
			$field = acf_get_field( $key );

			if ( ! $field && ! empty( $objects[ $key ] ) ) {
				$field = $objects[ $key ];
			}

			if ( $field ) {
				if( $form['kses'] ){
					// sanitize input based on field settings
					$sanitized = apply_filters( 'frontend_admin/forms/sanitize_input', false, $input, $field );
					if( ! $sanitized ){
						$input = feadmin_sanitize_input( $input, $field );
					}else{
						$input = $sanitized;
					}			
				}

				if ( $field['type'] == 'fields_select' ) {
					return $form;
				}

				if ( isset( $record['emails_to_verify'] ) && in_array( $field['type'], array( 'email', 'user_email' ) ) ) {
					if ( $input ) {
						if ( email_exists( $input ) ) {
							$user = get_user_by( 'email', $input );
							if ( isset( $user->ID ) ) {
								$verified = get_user_meta( $user->ID, 'frontend_admin_email_verified', 1 );
							}
						} else {
							$verified_emails = get_option( 'frontend_admin_verified_emails' );
							if ( $verified_emails ) {
								$verified_emails = explode( ',', $verified_emails );
								if ( in_array( $input, $verified_emails ) ) {
									$verified = true;
								};
							}
						}
						if ( empty( $verified ) ) {
							$record['emails_to_verify'][ $input ] = $input;
						} else {
							$record['verified_emails'][ $input ] = $input;
						}
					}
				}

				if ( is_string( $input ) ) {
					$input = html_entity_decode( $input );
				}

				$field['_input'] = $input;

				if ( 'user_password' == $field['type'] ) {
					$field['value'] = $field['_input'] = wp_hash_password( $field['_input'] );
				} 
				if ( is_string( $field['value'] ) && $field['type'] != 'form_step' && empty( $form['dynamic_title'] ) && empty( $form['submission_title'] ) ) {
					$form['submission_title'] = $field['value'];
				}
				$record = apply_filters( 'frontend_admin/add_to_record', $record, $group, $field );
				$record = apply_filters( 'frontend_admin/add_to_record/' . $field['type'], $record, $group, $field );
				$record = apply_filters( 'frontend_admin/add_to_record/' . $field['key'], $record, $group, $field );

				$value = array(
					'key'    => $field['key'],
					'_input' => $field['_input'],
					'value' => $field['_input'],
				);

				if ( isset( $field['default_value'] ) ) {
					$value['default_value'] = $field['default_value'];
				}

				if ( $group ) {
					if( 'file_data' == $group ){
						$record['fields'][ $group ][ $field['name'] ] = $input;
					}else{
						$record['fields'][ $group ][ $field['name'] ] = $value;
					}
				} else {
					$record['fields'][ $field['name'] ] = $value;
				}
			}

			$form['record'] = $record;
			return $form;
		}


		public function submit_form( $form ) {
			$form = $this->create_record( $form );

			if ( empty( $form['approval'] ) ) {
				do_action( 'frontend_admin/form/on_submit', $form );
			}

			$form['submission_status'] = 'approved';
			$save                      = get_option( 'frontend_admin_save_submissions' );

			foreach ( fea_instance()->local_actions as $name => $action ) {
				if ( ! $save || empty( $form['save_all_data'] ) || isset( $form['approval'] ) ) {
					$form = $action->run( $form );
				} else {
					if ( $name != 'options' && isset( $form[ "{$name}_id" ] ) ) {
						$form['record'][ $name ] = $form[ "{$name}_id" ];
					}
				}
			}
			if ( ! empty( $form['save_all_data'] ) ) {
				if ( in_array( 'verify_email', $form['save_all_data'] ) ) {
					if ( empty( $form['record']['emails_to_verify'] ) && empty( $form['record']['verified_emails'] ) ) {
						$current_user = wp_get_current_user();
						if ( isset( $current_user->ID ) ) {
							$verified = get_user_meta( $current_user->ID, 'frontend_admin_email_verified', 1 );
							if ( ! $verified ) {
								$form['record']['emails_to_verify'][ $current_user->user_email ] = $current_user->user_email;
							}
						}
					}

					if ( empty( $form['record']['emails_to_verify'] ) ) {
						unset( $form['save_all_data']['verify_email'] );
					}
				} else {
					unset( $form['record']['emails_to_verify'] );
				}
				$form['submission_status'] = implode( ',', $form['save_all_data'] );
			} else {
				unset( $form['record']['emails_to_verify'] );
			}
			$this->return_form( $form );
		}

		public function send_verification_emails( $form ) {
			foreach ( $form['record']['emails_to_verify'] as $email_address ) {
				$subject  = __( 'Please verify your email.', 'acf-frontend-form-element' );
				$message  = '<h1>' . $subject . '</h1>';
				$token    = wp_create_nonce( 'frontend-admin-verify-' . $email_address );
				$message .= '<p>' . sprintf(
					__( 'Please click <a href="%s">here</a> to verify your email. Thank you.', 'acf-frontend-form-element' ) . '</p>',
					add_query_arg(
						array(
							'submission'    => $form['submission'],
							'email-address' => $email_address,
							'token'         => $token,
						),
						$form['return']
					)
				);
				// Set the type of email to HTML.
				$headers[] = 'Content-type: text/html; charset=UTF-8';

				$header_string = implode( "\r\n", $headers );
				return wp_mail(
					$email_address,
					$subject,
					$message,
					$header_string
				);
			}
		}

		public function return_form( $form ) {
			$types = array( 'post', 'user', 'term', 'product' );
			foreach ( $types as $type ) {
				if ( isset( $form['record'][ $type ] ) ) {
					$form[ $type . '_id' ] = $form['record'][ $type ];
				}
			}

			$GLOBALS['admin_form'] = $form;

			if ( ! empty( fea_instance()->remote_actions ) ) {

				if ( empty( $form['approval'] ) ) {
					if ( ! empty( $form['submit_actions'] ) ) {
						foreach ( fea_instance()->remote_actions as $name => $action ) {
							$action->run( $form );
						}
					} elseif ( ! empty( $form['more_actions'] ) ) {
						foreach ( fea_instance()->remote_actions as $name => $action ) {
							if ( in_array( $name, $form['more_actions'] ) ) {
								$action->run( $form );
							}
						}
					}
				}
			}

			$update_message = $form['update_message'];
			if ( is_string( $update_message ) && strpos( $update_message, '[' ) !== 'false' ) {
				$update_message = fea_instance()->dynamic_values->get_dynamic_values( $update_message, $form );
			}
			$response = array(
				'to_top' => true,
			);

			if ( $form['show_update_message'] ) {
				$response['success_message'] = $update_message;
				$response['form_element']    = $form['id'];
			}

			if ( ! empty( $form['ajax_submit'] ) ) {
				$response['location'] = 'current';
			
				if ( $form['ajax_submit'] === 'submission_form' ) {
					$title  = $form['record']['submission_title'];
					if ( ! empty( $form['submission_title'] ) ) {
						$title = fea_instance()->dynamic_values->get_dynamic_values( $form['submission_title'], $form );
					}
					$response['submission']       = $form['submission'];
					$response['submission_title'] = $title;
					$response['close_modal']      = 1;
					$submission_form              = true;
				} else {
					if ( isset( $form['form_attributes']['data-field'] ) ) {
						$response['post_id']   = $form['post_id'];
						$response['field_key'] = $form['form_attributes']['data-field'];
						$response['modal'] = true;

						$host_field = acf_maybe_get_field( $response['field_key'] );

						if ( ! $host_field ) {
							wp_send_json_error( __( 'Post Added. No Field found to update.', 'acf-frontend-form-element' ) );
						}

						$field_class = acf()->fields->get_field_type( $host_field['type'] );

						$title = $field_class->get_post_title( get_post( $form['post_id'] ), $host_field );

						$response['post_info'] = array(
							'id'         => $form['post_id'],
							'text'       => $title,
							'action'     => $form['save_to_post'] == 'edit_post' ? 'edit' : 'add',
							'field_type' => $host_field['type'],
						);
					}
					if ( isset( $form['redirect_action'] ) ) {
						if ( $form['redirect_action'] == 'clear' ) {
							foreach ( $types as $type ) {
								if ( isset( $form[ "save_to_$type" ] ) && $form[ "save_to_$type" ] == "new_$type" ) {
									$form[ $type . '_id' ]   = "add_$type";
									$form[ "save_to_$type" ] = "new_$type";
								}
							}
						} else {
							foreach ( $types as $type ) {
								$form[ "save_to_$type" ] = "edit_$type";
							}
						}
					}
				}
			} else {
				$form                 = $this->get_redirect_url( $form );
				$response['location'] = $form['message_location'];
				if ( ! empty( $form['_in_modal'] ) || ! empty( $form['show_in_modal'] ) ) {
					$response['location'] = 'other';
				}
				// vars
				$return = acf_maybe_get( $form, 'return', '' );
				// redirect
				if ( $return ) {
					// update %placeholders%

					if ( isset( $form['post_id'] ) ) {
						$return = str_replace( array( '%post_id%', '[post:id]' ), $form['post_id'], $return );
						$return = str_replace( array( '%post_url%', '[post:url]' ), get_permalink( $form['post_id'] ), $return );

					}

					$return = remove_query_arg( array( 'pagename' ), $return );

					$response['redirect'] = $return;

					if ( isset( $form['redirect_action'] ) && $form['redirect_action'] == 'edit' ) {
						foreach ( $types as $type ) {
							$response[ $type ] = $form[ $type . '_id' ];
						}
					}

					$response['frontend-form-nonce'] = wp_create_nonce( 'frontend-form' );

					$expiration_time = time() + 600;
					setcookie( 'admin_form_success', json_encode( $response ), $expiration_time, '/' );
					unset($response['frontend-form-nonce']);
				}
			}

			$save = get_option( 'frontend_admin_save_submissions' );
			if ( isset( $form['save_form_submissions'] ) ) {
				$save = $form['save_form_submissions'];
			}
			if ( isset( $form['no_record'] ) ) {
				$save = false;
			}

			$save = apply_filters( 'frontend_admin/save_submission', $save, $form );

			if ( $save ) {
				$form = $this->save_record( $form, $form['submission_status'] );
			}

			if ( isset( $submission_form ) ) {
				$form = fea_instance()->submissions_handler->get_submission_form(
					$form['submission'],
					array(),
					1
				);
			}

			if ( ! empty( $form['ajax_submit'] ) ) {
				ob_start();
				$form['scripts_loaded'] = true;
				fea_instance()->form_display->render_form( $form );
				$response['reload_form'] = ob_get_clean();
			}

			do_action( 'frontend_admin/form/after_submit', $form );
			do_action( 'fea_after_submit', $form );

			wp_send_json_success( $response );
		}

		public function reload_form( $form ) {
			$types = array( 'post', 'user', 'term', 'product' );
			foreach ( $types as $type ) {
				if ( ! empty( $form['record'][ $type ] ) ) {
					$form[ $type . '_id' ]   = $form['record'][ $type ];
					$form[ "save_to_$type" ] = "edit_$type";
				}
			}

			$form = $this->save_record( $form );
			/*
			ob_start();
			$form['scripts_loaded'] = true;
			fea_instance()->form_display->render_form( $form );
			$json = [ 'reload_form' => ob_get_contents() ];
			ob_end_clean(); */
			$form                        = $this->get_redirect_url( $form );
			$json                        = array(
				'location'     => 'current',
				'redirect'     => $form['return'] . '?submission=' . $form['submission'] . '&edit=1',
				'form_element' => $form['id'],
			);
			$json['frontend-form-nonce'] = wp_create_nonce( 'frontend-form' );

			if ( isset( $_POST['_acf_message'] ) ) {
				$json['success_message'] = sanitize_textarea_field( $_POST['_acf_message'] );
			}

			$expiration_time = time() + 600;
			setcookie( 'admin_form_success', json_encode( $json ), $expiration_time, '/' );
			wp_send_json_success( $json );
		}

		public function save_record( $form, $status = 'in_progress' ) {
			if ( isset( $form['no_cookies'] ) ) {
				unset( $form['no_cookies'] );
				$no_cookie = true;
			}
			if ( ! empty( $form['approval'] ) ) {
				$status = 'approved';
			}

			global $wpdb;

			$title = $form['submission_title'];

			$args = array(
				'fields' => fea_encrypt( json_encode( $form['record'] ) ),
				'user'   => get_current_user_id(),
				'status' => $status,
				'title'  => $title,
			);

			if ( empty( $form['submission'] ) ) {
				$args['created_at'] = current_time( 'mysql' );
				$args['form']       = 'admin_form' == get_post_type( $form['ID'] ) ? $form['ID'] : $form['ID']. ':' .$form['id'];
				$form['submission'] = fea_instance()->submissions_handler->insert_submission( $args );
			} else {
				fea_instance()->submissions_handler->update_submission( $form['submission'], $args );
			}

			if ( ! empty( $form['record']['emails_to_verify'] ) ) {
				$this->send_verification_emails( $form );
			}
			if ( empty( $no_cookie ) ) {
				$expiration_time = time() + 86400;
				setcookie( $form['id'], $form['submission'], $expiration_time, '/' );
			}
			return $form;

		}


		public function form_message() {
			global $form_success;

			if ( empty( $form_success['frontend-form-nonce'] ) || ! wp_verify_nonce( $form_success['frontend-form-nonce'], 'frontend-form' ) ) {
				$user_id = get_current_user_id();
				if ( empty( $form_success['message_token'] ) || get_user_meta( $user_id, 'message_token', true ) !== $form_success['message_token'] || isset( $form_success['used'] ) ) {
					return;
				}
			}

			if ( isset( $form_success['success_message'] ) && $form_success['location'] == 'other' ) {
				$message = $form_success['success_message'];
				$return  =
				fea_instance()->frontend->enqueue_scripts( 'frontend_admin_form' );
				fea_instance()->frontend->feadata( true );
				?>
				<div class="-fixed frontend-admin-message">
				<div class="elementor-element elementor-element-<?php echo esc_attr( $form_success['form_element'] ); ?>">
					<div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg"><?php echo wp_unslash( wp_kses( $message, 'post' ) ); ?></p><span class="frontend-admin-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div>
				</div>
				</div>
				<?php
			}

		}

		public function get_redirect_url( $form ) {
			if ( ! empty( $form['return'] ) ) {
				return $form;
			}

			$redirect_url             = '';
			$form['message_location'] = 'other';
			switch ( $form['redirect'] ) {
				case 'custom_url':
					if ( is_array( $form['custom_url'] ) ) {
						 $redirect_url = $form['custom_url']['url'];
					} else {
						$redirect_url = $form['custom_url'];
					}
					break;
				case 'current':
					global $wp;
					$redirect_url = $form['current_url'];
					// $form_args['reload_current'] = true;
					$form['message_location'] = 'current';
					break;
				case 'referer_url':
					$redirect_url = $form['referer_url'];
					break;
				case 'post_url':
					$redirect_url = '%post_url%';
					break;
			}
			$form['return'] = $redirect_url;

			return $form;
		}

		public function delete_object() {
			if ( ! feadmin_verify_nonce( 'fea_form' ) ) {
				wp_send_json_error( __( 'Nonce Error', 'acf-frontend-form-element' ) );
			}
			// bail ealry if form not submit
			if ( empty( $_POST['_acf_form'] ) ) {
				wp_send_json_error( __( 'No Form Data', 'acf-frontend-form-element' ) );
			}
			// load form
			$form = json_decode( fea_decrypt( $_POST['_acf_form'] ), true );

			$form = wp_kses_post_deep( $form );
			

			// bail ealry if form is corrupt
			if ( empty( $form ) ) {
				wp_send_json_error( __( 'No Form Data', 'acf-frontend-form-element' ) );
			}

			if ( empty( $_POST['field'] ) ) {
				wp_send_json_error( __( 'Delete Button Key Not Found', 'acf-frontend-form-element' ) );
			}

			$field = acf_get_field( sanitize_key( $_POST['field'] ) );

			if ( ! $field ) {
				wp_send_json_error( __( 'Invalid Delete Button', 'acf-frontend-form-element' ) );
			}

			// add global for backwards compatibility
			$GLOBALS['admin_form'] = $form;

			if ( isset( $field['redirect'] ) ) {
				$form['redirect'] = $field['redirect'];
			}
			if ( isset( $field['custom_url'] ) ) {
				$form['custom_url'] = $field['custom_url'];
			}
			$form                     = $this->get_redirect_url( $form );
			$form['message_location'] = 'other';

			$redirect_args = array( 'redirect' => $form['return'] );

			if ( $field['show_delete_message'] ) {
				if ( ! empty( $field['delete_message'] ) ) {
					$message = $field['delete_message'];
				} else {
					$message = $form['success_message'];
				}
				if ( strpos( $message, '[' ) !== 'false' ) {
					$message = fea_instance()->dynamic_values->get_dynamic_values( $message, $form );
				}
				$redirect_args['form_element']        = $form['id'];
				$redirect_args['success_message']     = $message;
				$redirect_args['location']            = $form['message_location'];
				$redirect_args['frontend-form-nonce'] = wp_create_nonce( 'frontend-form' );
			}

			switch ( $field['type'] ) {
				case 'delete_post':
					if ( ! empty( $_POST['_acf_post'] ) ) {
						$post_id = intval( $_POST['_acf_post'] );
						if ( empty( $form['force_delete'] ) ) {
							$deleted = wp_trash_post( $post_id );
						} else {
							$deleted = wp_delete_post( $post_id, true );
						}
						$form['record']['post'] = $post_id;
					}
					break;
				case 'delete_product':
					if ( ! empty( $_POST['_acf_product'] ) ) {
						$product_id = intval( $_POST['_acf_product'] );
						if ( empty( $form['force_delete'] ) ) {
							$deleted = wp_trash_post( $product_id );
						} else {
							$deleted = wp_delete_post( $product_id, true );
						}
						$form['record']['product'] = $product_id;
					}
					break;
				case 'delete_term':
					if ( ! empty( $_POST['_acf_term'] ) ) {
						$term_id                = intval( $_POST['_acf_term'] );
						$deleted                = wp_delete_term( $term_id, sanitize_title( $_POST['_acf_taxonomy_type'] ) );
						$form['record']['term'] = $term_id;
					}
					break;
				case 'delete_user':
					if ( ! empty( $_POST['_acf_user'] ) ) {
						$user_id                = intval( $_POST['_acf_user'] );
						$deleted                = wp_delete_user( $user_id, $field['reassign_posts'] );
						$form['record']['user'] = $user_id;
					}
					break;
				default:
					wp_send_json_error( __( 'No object found to delete' ) );
			}

			$expiration_time = time() + 600;
			setcookie( 'admin_form_success', json_encode( $redirect_args ), $expiration_time, '/' );

			wp_send_json_success( $redirect_args );
		}


		public function verify_email_address() {
			if ( isset( $_GET['submission'] ) && isset( $_GET['email-address'] ) && isset( $_GET['token'] ) ) {
				$request = wp_kses_post_deep( $_GET );
			} else {
				return;
			}
			$address = $request['email-address'];

			$fea = fea_instance();

			$submission = $fea->submissions_handler->get_submission( $request['submission'] );
			if ( empty( $submission->fields ) ) {
				wp_redirect( home_url() );
			}

			$record = json_decode( fea_decrypt( $submission->fields ), true );

			if ( isset( $record['emails_to_verify'][ $address ] ) ) {
				if ( ! wp_verify_nonce( $request['token'], 'frontend-admin-verify-' . $address ) ) {
					return;
				}

				if ( email_exists( $address ) ) {
					$user     = get_user_by( 'email', $address );
					$verified = get_user_meta( $user->ID, 'frontend_admin_email_verified', 1 );

					if ( $verified ) {
						return;
					}
					if ( isset( $user->ID ) ) {
						update_user_meta( $user->ID, 'frontend_admin_email_verified', 1 );
					}
				} else {
					$verified_emails = get_option( 'frontend_admin_verified_emails', '' );
					if ( $verified_emails = '' ) {
						$verified_emails = $address;
					} else {
						$verified_emails .= ',' . $address;
					}
					update_option( 'frontend_admin_verified_emails', $verified_emails );
				}

				unset( $record['emails_to_verify'][ $address ] );
				$record['verified_emails'][ $address ] = $address;

				$args = array();
				if ( empty( $record['emails_to_verify'] ) ) {
					if ( $submission->status ) {
						$old_status = explode( ',', $submission->status );
						if ( ! in_array( 'require_approval', $old_status ) ) {
							   $form           = $fea->form_display->get_form( $submission->form );
							   $form['record'] = $record;
							foreach ( $fea->local_actions as $name => $action ) {
								$form = $action->run( $form );
							}
							$record = $form['record'];
						}
						$new_status = str_replace( 'verify_email', 'email_verified', $submission->status );
					}

					$args['status'] = $new_status;
				}

				$args['fields'] = fea_encrypt( json_encode( $record ) );
				$fea->submissions_handler->update_submission( $request['submission'], $args );
				$GLOBAL[ $address . '_verified' ] = true;
			}

		}

		public function __construct() {
			add_action( 'wp_footer', array( $this, 'form_message' ) );

			add_action( 'wp_ajax_frontend_admin/validate_form_submit', array( $this, 'validate_submitted_form' ), 2 );
			add_action( 'wp_ajax_nopriv_frontend_admin/validate_form_submit', array( $this, 'validate_submitted_form' ), 2 );
			add_action( 'wp_ajax_frontend_admin/form_submit', array( $this, 'check_submit_form' ) );
			add_action( 'wp_ajax_nopriv_frontend_admin/form_submit', array( $this, 'check_submit_form' ) );
			add_action( 'wp_ajax_frontend_admin/forms/update_field', array( $this, 'check_inline_field' ) );
			add_action( 'wp_ajax_nopriv_frontend_admin/forms/update_field', array( $this, 'check_inline_field' ) );

			add_action( 'wp_ajax_frontend_admin/delete_object', array( $this, 'delete_object' ) );
			add_action( 'wp_ajax_nopriv_frontend_admin/delete_object', array( $this, 'delete_object' ) );

			add_action( 'init', array( $this, 'verify_email_address' ) );
		}
	}

	fea_instance()->form_submit = new Submit_Form();

endif;



