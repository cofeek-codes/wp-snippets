

<?php
/*
Plugin Name: PluginForm
Author: Егор Кормышев
*/

class CustomUserRegistration {
    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('plugin-form', array($this, 'render_registration_form'));
        add_action('init', array($this, 'handle_registration_submission'));
    }

    public function render_registration_form() {
        ?>
        <style>
            /* Add to your theme's stylesheet */
.registration-form {
    max-width: 400px;
    margin: 2rem auto;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.tos-acceptance {
    margin: 1.5rem 0;
}

.tos-acceptance input[type="checkbox"] {
    margin-right: 0.5rem;
    vertical-align: middle;
}

.tos-acceptance a {
    color: #007bff;
    text-decoration: none;
}

.tos-acceptance a:hover {
    text-decoration: underline;
}


button[type="submit"] {
    background-color: #007bff;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}
        </style>
        <div class="registration-form">
            <form method="post" id="user-registration-form">
                <?php wp_nonce_field('register_user', 'registration_nonce'); ?>
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required />
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required />
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required />
                </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required />
            </div>

            <div class="form-group tos-acceptance">
                <input type="checkbox" id="accept_tos" name="accept_tos" required />
                <label for="accept_tos">
                    I accept the <a href="#">Terms of Service</a>
                </label>
            </div>
                <button type="submit" name="register">Register</button>
            </form>
        </div>
        <?php
    }

    public function handle_registration_submission() {
        if (!isset($_POST['register']) || !wp_verify_nonce($_POST['registration_nonce'], 'register_user')) {
            return;
        }

        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $passwordConfirm = $_POST['confirm_password'];
        $TOS_acceptance = $_POST['accept_tos'];

        if (!$TOS_acceptance) {
            wp_die('accept TOS');
        }

        if ($password !== $passwordConfirm) {
            wp_die('password don\'t match');
        }

        // Validate username
        if (empty($username) || strlen($username) < 4) {
            wp_die('Username must be at least 4 characters long');
        }

        // Validate email
        if (!is_email($email)) {
            wp_die('Please enter a valid email address');
        }

        // Check if user exists
        if (username_exists($username) || email_exists($email)) {
            wp_die('Username or email already exists');
        }

        // Validate password
        if (strlen($password) < 8) {
            wp_die('Password must be at least 8 characters long');
        }

        // Create user
        $user_id = wp_insert_user(array(
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password,
            'role' => 'subscriber'
        ));

        if (is_wp_error($user_id)) {
            wp_die($user_id->get_error_message());
        }

        // Send welcome email
        wp_new_user_notification($user_id);

        // Redirect to login page
        wp_safe_redirect(wp_login_url());
        exit;
    }
}

// Initialize plugin
CustomUserRegistration::getInstance();
