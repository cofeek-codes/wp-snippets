<?php 

/* 
Plugin Name: Plugin Form1
*/

function render_form() {
   ob_start(); 
    ?>
<style>
    form {
        background: gray;
        color:#fff;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 90vw;

    }
    .f__left, .f__right {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction:column;
    } 
</style>
<form id="registration__form" method="post">
<div class="f__left">

    <label for="username">Имя пользователя</label>
    <input type="text" name="username" id="username" required>
    <label for="email">Почта</label>
    <input type="email" name="email" id="email" required>
    <label for="accept">Принимаю соглашение</label>
    <input type="checkbox" name="accept" id="accept">
</div>
<div class="f__right">
    <label for="avatar">Аватар</label>
    <input type="file" name="avatar" id="avatar">
    <label for="password">Пароль</label>
    <input type="password" name="password" id="password" required>
    <label for="repeat_password">Повторите Пароль</label>
    <input type="password" name="repeat_password" id="repeat_password" required>
    <button type="submit" name="register" id="register">Зарегистрироваться</button>
</div>


</form>
<?php
return ob_get_clean();
}


add_shortcode('plugin-form', 'render_form');

function submit_form() {
if (isset($_POST['register']) && isset($_POST['accept'])) {
$username = sanitize_user($_POST['username']);
$email = sanitize_email($_POST['email']);
$password = $_POST['password'];
$repeat_password = $_POST['repeat_password'];

$userdata = [
    'user_login' => $username,
    'user_email' => $email,
    'user_pass' => $password,
];

if (strcmp($password, $repeat_password)) {
    echo ("пароли не совполдают");
} else {
    $user_id = wp_insert_user($userdata);
    if (!is_wp_error($user_id)) {
        wp_redirect('/');
        echo "Регистрация успешна";
    
    } else {
        wp_redirect('/');
        echo "Регистрация не удалась";
    
    }
}

}
}


add_action('init', 'submit_form');
