<div class="wrap">
    <h2>Add New Member Number</h2>
    <?php if (isset($message)): ?>
        <div class="updated">
            <p><?= $message; ?></p>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="error-message">
            <p><?= $error; ?></p>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <p><?= __('Insert Member', 'arkitekt-members'); ?></p>
        <table class='wp-list-table widefat fixed'>
            <tr>
                <th class="ss-th-width">Member ID</th>
                <td><input type="text" name="member_id" value="<?php echo $id; ?>" class="ss-field-width" /></td>
            </tr>
        </table>
        <input style="margin-top: 5px;" type='submit' name="insert" value='Save' class='button'>
    </form>
</div>