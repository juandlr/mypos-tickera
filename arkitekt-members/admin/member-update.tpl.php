<div class="wrap">
    <h2>Member ID Update</h2>

    <?php if ($message) { ?>
        <div class="updated">
            <p><?= $message; ?></p>
        </div>
        <a href="<?php echo admin_url('admin.php?page=arkitekt-members') ?>">&laquo; Back to member list</a>
    <?php } elseif ($error) { ?>
        <div class="error-message">
            <p><?= $error; ?></p>
        </div>
    <?php } else { ?>
        <form method="post" action="">
            <table class='wp-list-table widefat fixed'>
                <tr>
                    <th>Member ID</th>
                    <td><input type="text" name="member_id" value="<?= $member_id; ?>" /></td>
                </tr>
            </table>
            <div style="margin-top: 10px;">
                <input type='submit' name="update" value='Save' class='button'> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button'
                       onclick="return confirm(<?= __('Are you sure of this action?', 'arkitekt-members') ?>)">
            </div>
        </form>
    <?php } ?>
</div>