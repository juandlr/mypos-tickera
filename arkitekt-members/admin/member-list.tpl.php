<div class="wrap">
    <?php if ($error) { ?>
        <div class="error-notice">
            <?= $error; ?>
        </div>
    <?php } ?>
    <h2>Arkitekt Member Ids</h2>
    <div class="tablenav top">
        <div class="alignleft actions">
            <a href="<?php echo admin_url('admin.php?page=member-id-create'); ?>">Add New</a>
        </div>
        <div class="alignright actions">
            <form method="post" action="">
                <label for="membid-search">Search</label> <input type="text" name="memberid_search" id="memberid-search">
                <input class='button' type="submit" value="Search">
            </form>
        </div>
        <br class="clear">
    </div>
    <table class='wp-list-table widefat fixed striped posts'>
        <tr>
            <th class="manage-column ss-list-width">Member ID</th>
            <th></th>
        </tr>
        <?php foreach ($rows as $row) { ?>
            <tr>
                <td class="manage-column ss-list-width"><?php echo $row->member_id; ?></td>
                <td><a href="<?php echo admin_url('admin.php?page=member-id-update&member_id=' . $row->member_id); ?>">Update</a></td>
            </tr>
        <?php } ?>
    </table>
    <?php if ($total > $per_page) {
        $paginate_args = array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'total' => ceil($total / $per_page),
            'current' => max(1, $paged),
        );
        echo paginate_links($paginate_args);
    }
    ?>
</div>