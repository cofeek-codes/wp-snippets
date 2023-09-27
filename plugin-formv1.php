<?php
/*
Plugin Name: Custom Registration Form
Description: Displays a custom registration form via shortcode.
Version: 1.0
Author: Your Name
*/



// USE strcmp($password, $repeat_password) FOR false



// Register the shortcode
function custom_registration_form_shortcode() {
    ob_start();
?>

<form id="custom-registration-form" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <br>
    <input type="submit" name="register" value="Register">
</form>

<?php
    return ob_get_clean();
}
add_shortcode('custom_registration_form', 'custom_registration_form_shortcode');

// Handle form submission
function custom_registration_form_submit() {
    if (isset($_POST['register'])) {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        $userdata = array(
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password,
        );

        $user_id = wp_insert_user($userdata);
        if (!is_wp_error($user_id)) {
            // User registered successfully
            echo 'Registration successful!';
            wp_redirect(wp_login_url());
        } else {
            // Registration failed
            echo 'Registration failed. Please try again.';
            wp_redirect(home_url());
        }
    }
}
add_action('init', 'custom_registration_form_submit');
