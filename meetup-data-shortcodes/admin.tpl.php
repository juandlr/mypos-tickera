<div class="wrap">
    <h1>Meetup Photo Albums</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('meetup_albums');
        $options = get_option('meetup_options');
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Group Url</th>
                <td style="padding-bottom: 40px;">
                    <input style="min-width: 400px" name="meetup_options[group_url]" type="text" value="<?= $options['group_url']; ?>" />
                    <p>
                        <em>This is the Group Name portion of your URL. For example, if your home page URL is:<br /><br />
                            https://meetup.com/houstonphotowalks/<br /><br />
                            then this setting should be "houstonphotowalks".
                        </em>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">API Key</th>
                <td style="padding-bottom: 40px;">
                    <input type="password" style="min-width: 400px" name="meetup_options[api_key]" placeholder="" value="************************" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Optional Album Disclaimer</th>
                <td>
                    <textarea style="width: 500px; min-height: 200px;" name="meetup_options[album_disclaimer]"><?= $options['album_disclaimer']; ?></textarea>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
