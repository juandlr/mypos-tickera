<div class="ark-profile-form">
    <?php
    $first_name = $user->first_name;
    $first_name_priv = $user->first_name_priv ? 'checked' : '';
    $last_name = $user->last_name;
    $last_name_priv = $user->last_name_priv ? 'checked' : '';
    $user_email = $user->user_email;
    $user_email_priv = $user->user_email_priv ? 'checked' : '';
    $education = $user->education;
    $education_priv = $user->education_priv ? 'checked' : '';
    $work_experience = $user->work_experience;
    $work_experience_priv = $user->work_experience_priv ? 'checked' : '';
    $fields_interest = $user->fields_interest;
    $fields_interest_priv = $user->fields_interest_priv ? 'checked' : '';
    $year_birth = $user->year_birth;
    $year_priv = $user->year_birth_priv ? 'checked' : '';
    $spoken = $user->spoken_languages;
    $member_id = $user->member_id;
    $spoken_priv = $user->spoken_languages_priv ? 'checked' : '';
    $key_comp = $user->key_competencies;
    $key_comp_priv = $user->key_competencies_priv ? 'checked' : '';
    $work_type = $user->work_type;
    $work_type_priv = $user->work_type_priv ? 'checked' : '';
    $cv_id = $user->cv;
    $cv = wp_get_attachment_url($cv_id);
    $cv_priv = $user->cv_priv ? 'checked' : '';
    $photo_id = $user->profile_photo;
    $profile_photo = wp_get_attachment_url($photo_id);
    $profile_photo_priv = $user->cv_priv ? 'checked' : '';
    if ($_SESSION['errors']) {
        echo $_SESSION['errors'][0];
        unset($_SESSION['errors']);
    }
    if ($_SESSION['success']) {
        echo $_SESSION['success'];
        unset($_SESSION['success']);
    }
    ?>
    <h2><?= __('Hello', 'arkitekt-members'); ?>, <?= $first_name; ?>!</h2>
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
            <p style="padding: 0 10px;">
                <label for="email"><?= __('Email', 'arkitekt-members'); ?>:</label>
                <input required type="email" name="email[]" id="email" value="<?= $user_email; ?>">
                <label><input type="checkbox" value="1" name="email[]" <?= $user_email_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>

        <div class="ark-form-row">
            <p>
                <label for="pass1"><?= __('Password', 'arkitekt-members'); ?>:</label>
                <input type="password" name="pass1" id="pass1" value="">
            </p>
            <p>
                <label for="pass2"><?= __('Confirm Password', 'arkitekt-members'); ?>:</label>
                <input type="password" name="pass2" id="pass1" value="">
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
                <input type="text" name="work_exp[]" id="work-exp" value="<?= $work_experience; ?>">
                <label><input type="checkbox" name="work_exp[]" <?= $work_experience_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <label for="work-type"><?= __('Work Type', 'arkitekt-members'); ?>:</label>
                <select name="work_type[]" id="work-type">
                    <option value="">--Please choose an option--</option>
                    <?php
                    $work_types_array = array(
                        'voluntary' => 'Voluntary Work',
                        'paid' => 'Paid Work',
                        'both' => 'Both'
                    );
                    foreach ($work_types_array as $val => $work_type_label): ?>
                        <option value="<?= $val; ?>"<?php if ($val == $work_type): ?> selected="selected"<?php endif; ?>><?= $work_type_label; ?></option>
                    <?php endforeach; ?>
                </select>
                <label><input type="checkbox" name="work_type[]" <?= $work_type_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>
        <div class="ark-form-row">
            <p>
                <label for="fields-int"><?= __('Fields of Interest', 'arkitekt-members'); ?>:</label>
                <input type="text" id="fields-int" name="fields_int[]" value="<?= $fields_interest; ?>">
                <label><input type="checkbox" name="fields_int[]" <?= $fields_interest_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <label for="year-birth"><?= __('Year of Birth', 'arkitekt-members'); ?>:</label>
                <input type="text" name="year_birth[]" id="year-birth" value="<?= $year_birth; ?>">
                <label><input type="checkbox" name="year_birth[]" <?= $year_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>
        <div class="ark-form-row">
            <p>
                <label for="spoken"><?= __('Spoken Languages', 'arkitekt-members'); ?>:</label>
                <input type="text" id="spoken" name="spoken[]" value="<?= $spoken; ?>">
                <label><input type="checkbox" name="spoken[]" <?= $spoken_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>

            <p>
                <label for="email"><?= __('Key Competencies', 'arkitekt-members'); ?>:</label>
                <input type="text" name="key_comp[]" id="key-comp" value="<?= $key_comp; ?>">
                <label><input type="checkbox" name="key_comp[]" <?= $key_comp_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>
        <div class="ark-form-row">
            <p>
                <?php if ($profile_photo): ?>
                    <img style="height: 20px" src="<?= $profile_photo; ?>" alt="">
                    <label style="margin-left: 10px;"> <input type="checkbox" name="delete_photo" value="1"> Delete
                        Photo </label><br/>
                <?php endif; ?>
                <label for="profile-photo"><?= __('Profile Photo', 'arkitekt-members'); ?>:</label>
                <input type="file" id="profile-photo" name="profile_photo" accept="image/*"><br/>
                <label><input type="checkbox" name="profile_photo_priv"> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
            <p>
                <?php if ($cv) {
                    ?>
                    <img style="height: 20px" src="<?= plugin_dir_url(__DIR__); ?>images/pdf.svg" alt="">
                    <a target='_blank' href="<?= $cv; ?>">Cv</a>
                    <label style="margin-left: 10px;"><input type="checkbox" name="delete_cv" value="1"> Delete
                        Cv</label><br/>
                <?php } ?>
                <label for="cv"><?= __('Upload Cv', 'arkitekt-members'); ?>:</label>
                <input type="file" id="cv" name="cv" accept=".xlsx,.xls,.doc,.docx,.ppt,.pptx,.txt,.pdf"><br/>
                <label><input type="checkbox" name="cv_priv" <?= $cv_priv; ?>> <?= __('Private', 'arkitekt-members'); ?></label>
            </p>
        </div>
        <?php wp_nonce_field('arkitekt_member', 'arkitekt_nonce'); ?>
        <div class="submit-row">
            <div class="buttons">
                <input type="submit" name="update_button" value="Update">
                <input type="submit" name="delete_button" value="Delete">
            </div>
            <a href="<?= MEMBERS_URL; ?>"><b>Back</b></a>
        </div>
    </form>

</div>
