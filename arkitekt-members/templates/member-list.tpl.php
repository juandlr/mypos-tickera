<?php
if ($_SESSION['success']) {
    echo "<h3>Welcome</h3>";
}
?>
<p class="profile-link">
    <a href="<?= PROFILE_URL; ?>"><b>My Profile</b></a><br/>
    <a href="<?= wp_logout_url(); ?>"><b>Logout</b></a>
</p>
<div class="arkitekt-members">
    <?php foreach ($users as $user) {
        $first_name = !$user->first_name_priv ? $user->first_name : '';
        $last_name = !$user->last_name_priv ? $user->last_name : '';
        $user_email = !$user->user_email_priv ? $user->user_email : '';
        $education = !$user->education_priv ? $user->education : '';
        $work_experience = !$user->work_experience_priv ? $user->work_experience : '';
        $fields_interest = !$user->fields_interest_priv ? $user->fields_interest : '';
        $year_birth = !$user->year_birth_priv ? $user->year_birth : '';
        $spoken = !$user->spoken_languages_priv ? $user->spoken_languages : '';
        $cv_id = !$user->cv_priv ? $user->cv : '';
        $cv = wp_get_attachment_url($cv_id);
        $profile_photo_id = !$user->profile_photo_priv ? $user->profile_photo : '';
        $profile_photo = wp_get_attachment_url($profile_photo_id);
        $key_comp = !$user->key_competencies_priv ? $user->key_competencies : '';
        $work_type = !$user->work_type_priv ? $user->work_type : '';
        ?>
        <div class="arkitekt-member">
            <h2>
                <?= $first_name; ?> <?= $last_name; ?>
            </h2>
            <p>
                <?php if ($profile_photo) {
                    ?>
                    <img style="max-height: 200px" src="<?= $profile_photo; ?>" alt="">
                <?php } ?>
            </p>
            <?php if ($user_email) { ?>
                <p><b><?= __('Email', 'arkitekt-members'); ?>: </b><?= $user_email; ?></p>
            <?php } ?>
            <?php if ($education) { ?>
                <p><b><?= __('Education', 'arkitekt-members'); ?>: </b> <?= $education; ?></p>
            <?php } ?>
            <?php if ($work_experience) { ?>
                <p><b><?= __('Work Experience', 'arkitekt-members'); ?>: </b><?= $work_experience; ?></p>
            <?php } ?>
            <?php if ($fields_interest) { ?>
                <p><b><?= __('Fields of Interest', 'arkitekt-members'); ?>: </b> <?= $fields_interest; ?></p>
            <?php } ?>
            <?php if ($year_birth) { ?>
                <p><b><?= __('Year of Birth', 'arkitekt-members'); ?>: </b><?= $year_birth; ?></p>
            <?php } ?>
            <?php if ($spoken) { ?>
                <p><b><?= __('Spoken Languages', 'arkitekt-members'); ?>: </b><?= $spoken; ?></p>
            <?php } ?>
            <?php if ($key_comp) { ?>
                <p><b><?= __('Key Competencies', 'arkitekt-members'); ?>: </b><?= $key_comp; ?></p>
            <?php } ?>
            <?php if ($work_type) { ?>
                <p><b><?= __('Work Type', 'arkitekt-members'); ?>: </b><?= $work_type; ?></p>
            <?php } ?>
            <p>
                <?php if ($cv_id) {
                    ?>
                    <img style="height: 20px" src="<?= plugin_dir_url(__DIR__); ?>images/pdf.svg" alt=""> <a
                            target="_blank" href="<?= $cv; ?>">Cv</a>
                <?php } ?>
            </p>
        </div>
    <?php } ?>
</div>
<div class="members-list-pagination">
    <?php if ($total_members > $per_page) { ?>
        <div class="pagination">
            <?php
            $paginate_args = array(
                'total' => ceil($total_members / $per_page),
                'current' => max(1, $paged),
            );
            echo paginate_links($paginate_args);
            ?>
        </div>
    <?php } ?>
</div>
