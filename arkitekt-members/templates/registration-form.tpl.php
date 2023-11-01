<div class="ark-profile-form">
    <?php
    $first_name = isset($_POST['first_name'][0]) ? $_POST['first_name'][0] : '';
    $first_name_priv = isset($_POST['first_name'][1]) ? 'checked' : '';
    $last_name = isset($_POST['last_name'][0]) ? $_POST['last_name'][0] : '';
    $last_name_priv = isset($_POST['last_name'][1]) ? 'checked' : '';
    $user_email = isset($_POST['email'][0]) ? $_POST['email'][0] : '';
    $user_email_priv = isset($_POST['email'][1]) ? 'checked' : '';
    $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
    $member_id = isset($_POST['member_id']) ? $_POST['member_id'] : '';
    $education = isset($_POST['education'][0]) ? $_POST['education'][0] : '';
    $education_priv = isset($_POST['education'][1]) ? 'checked' : '';
    $work_exp = isset($_POST['work_exp'][0]) ? $_POST['work_exp'][0] : '';
    $work_exp_priv = isset($_POST['work_exp'][1]) ? 'checked' : '';
    $interests = isset($_POST['fields_int'][0]) ? $_POST['fields_int'][0] : '';
    $interests_priv = isset($_POST['fields_int'][1]) ? 'checked' : '';
    $work_type_post = isset($_POST['work_type'][0]) ? $_POST['work_type'][0] : '';
    $work_type_priv = isset($_POST['work_type'][1]) ? 'checked' : '';
    $year_birth = isset($_POST['year_birth'][0]) ? $_POST['year_birth'][0] : '';
    $year_birth_priv = isset($_POST['year_birth'][1]) ? 'checked' : '';
    $spoken = isset($_POST['spoken'][0]) ? $_POST['spoken'][0] : '';
    $spoken_priv = isset($_POST['spoken'][1]) ? 'checked' : '';
    $key_comp = isset($_POST['key_comp'][0]) ? $_POST['key_comp'][0] : '';
    $key_comp_priv = isset($_POST['key_comp'][1]) ? 'checked' : '';
    if ($_SESSION['errors']) {
        echo "<p style='padding: 20px; border: 1px solid red;'>{$_SESSION['errors'][0]}</p>";
        unset($_SESSION['errors']);
    }
    if ($_SESSION['success']) {
        echo "<p style='padding: 20px; border: 1px solid green;'>{$_SESSION['success']}</p>";
        unset($_SESSION['success']);
    }
    ?>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="ark-form-row">
            <p>
                <label for="name"><?= __('First Name', 'arkitekt-members'); ?>:</label>
                <input required type="text" name="first_name[]" id="first-name" value="<?= $first_name; ?>">
                <label><input type="checkbox" value="1" name="first_name[]" <?= $first_name_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <label for="last_name"><?= __('Last Name', 'arkitekt-members'); ?>:</label>
                <input type="text" name="last_name[]" id="last-name" value="<?= $last_name; ?>">
                <label><input type="checkbox" value="1" name="last_name[]" <?= $last_name_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>
        <div class="ark-form-row">
            <p>
                <label for="email"><?= __('Email', 'arkitekt-members'); ?>:</label>
                <input required type="email" name="email[]" id="email" value="<?= $user_email; ?>">
                <label><input type="checkbox" value="1" name="email[]" <?= $user_email_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <label for="user_name"><?= __('Username', 'arkitekt-members'); ?>:</label>
                <input required type="text" name="user_name" id="user-name" value="<?= $user_name; ?>">

            </p>
            <p>
                <label for="user-id"><?= __('Membership nr', 'arkitekt-members'); ?>:</label>
                <input required type="text" name="member_id" id="user-id" value="<?= $member_id; ?>">
            </p>
        </div>
        <div class="ark-form-row">
            <p>
                <label for="pass1"><?= __('Password', 'arkitekt-members'); ?>:</label>
                <input required type="password" name="pass1" id="pass1" value="">
            </p>
            <p>
                <label for="pass2"><?= __('Confirm Password', 'arkitekt-members'); ?>:</label>
                <input required type="password" name="pass2" id="pass1" value="">
            </p>
        </div>
        <div class="ark-form-row">
            <p>
                <label for="education" for=""><?= __('Education', 'arkitekt-members'); ?>:</label>
                <input type="text" name="education[]" id="education" value="<?= $education; ?>">
                <label><input type="checkbox" name="education[]" <?= $education_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <label for="work-exp"><?= __('Work Experience', 'arkitekt-members'); ?>:</label>
                <input type="text" name="work_exp[]" id="work-exp" value="<?= $work_exp; ?>">
                <label><input type="checkbox" name="work_exp[]" <?= $work_exp_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <label for="work-type"><?= __('Work Type', 'arkitekt-members'); ?>:</label>
                <select name="work_type[]" id="work-type">
                    <option value=""><?php __('--Please choose an option--', 'arkitekt-members'); ?></option>
                    <?php
                    $work_types = array(
                        'voluntary' => __('Voluntary Work', 'arkitekt-members'),
                        'paid' => __('Paid Work', 'arkitekt-members'),
                        'both' => __('Both', 'arkitekt-members')
                    );
                    foreach ($work_types as $val => $work_type): ?>
                        <option value="<?= $val; ?>"<?php if ($val == $work_type_post): ?> selected="selected"<?php endif; ?>><?= $work_type; ?></option>
                    <?php endforeach; ?>
                </select><br />
                <label><input type="checkbox" name="work_type[]" <?= $work_type_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>
        <div class="ark-form-row">
            <p>
                <label for="fields-int"><?= __('Fields of Interest', 'arkitekt-members'); ?>:</label>
                <input type="text" id="fields-int" name="fields_int[]" value="<?= $interests; ?>">
                <label><input type="checkbox" name="fields_int[]" <?= $interests_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <label for="year-birth"><?= __('Year of Birth', 'arkitekt-members'); ?>:</label>
                <input required type="text" name="year_birth[]" id="year-birth" value="<?= $year_birth; ?>">
                <label><input type="checkbox" name="year_birth[]" <?= $year_birth_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>
        <div class="ark-form-row">
            <p>
                <label for="spoken"><?= __('Spoken Languages', 'arkitekt-members'); ?>:</label>
                <input type="text" id="spoken" name="spoken[]" value="<?= $spoken; ?>">
                <label><input type="checkbox" name="spoken[]" <?= $spoken_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>

            <p>
                <label for="key-comp"><?= __('Key Competencies', 'arkitekt-members'); ?>:</label>
                <input type="text" name="key_comp[]" id="key-comp" value="<?= $key_comp; ?>">
                <label><input type="checkbox" name="key_comp[]" <?= $key_comp_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>

        <div class="ark-form-row">
            <p>
                <label for="profile-photo"><?= __('Profile Photo', 'arkitekt-members'); ?>:</label>
                <input type="file" id="profile-photo" name="profile_photo" accept="image/*"><br/>
                <label><input type="checkbox" name="profile_photo_priv"> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <label for="cv"><?= __('Upload Cv', 'arkitekt-members'); ?>:</label>
                <input type="file" id="cv" name="cv" accept="application/pdf"><br/>
                <label><input type="checkbox" name="cv_priv"> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>
        <?php wp_nonce_field('arkitekt_member', 'arkitekt_nonce'); ?>
        <input type="hidden" name="action" value="create_member">
        <div class="submit-row">
            <input type="submit" name="submit" value="Submit">
        </div>
    </form>
</div>
