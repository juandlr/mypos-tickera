<?php global $success_message, $errors; ?>
<?php if (isset($success_message)) : ?>
    <div style='margin-top: 20px' class="alert alert-success"><?= $success_message; ?></div>
<?php endif; ?>
<?php if (!empty($errors)) : ?>
    <div style='margin-top: 20px' class="alert alert-danger">
        <?php
        foreach ($errors as $key =>  $error) {
            if (is_string($error)) {
                echo $error . '<br />';
            }
            else {
                echo $error[0] . '<br />';
            }
        }
        ?>
    </div>
<?php endif; ?>
<form id="aikido-login-form"  class="" action="" method="post">
    <fieldset>
        <p>
            <label for="aikido-user-login">Email</label>
            <input name="aikido_user_login" id="aikido-user-login" required type="text"/>
        </p>
        <p>
            <label for="aikido_user_pass">Password</label>
            <input name="aikido_user_pass" id="aikido-user-pass" required type="password"/>
        </p>
        <p>
            <?php wp_nonce_field('aikido_login', 'aikido_login_nonce'); ?>
            <input id="aikido-login-submit" type="submit" value="Login"/>
        </p>
    </fieldset>
</form>