<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 */
?>

<div class="wrap">
    <h1>Setup S3 & Cloudfront keys</h1>

    <form action="?page=<?php echo $this->plugin_name."-overview"; ?>" method="post">
        <table class="form-table">
            <tr>
                <th scope="row">
                    Access Key ID *
                </th>
                <td>
                    <input type="text" name="access_key_id" size="40" value="<?php echo isset($access_key_id) ? $access_key_id : '' ?>"/>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Secret Access Key *
                </th>
                <td>
                    <input type="text" name="secret_access_key" size="40" value="<?php echo (isset($secret_access_key) && $secret_access_key) ? $secret_access_key : '' ?>"/>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field( 'rooftop-s3-offset-config', 'rooftop-s3-field-token' ); ?>

        <p class="submit">
            <input type="submit" value="Save" class="button button-primary" />
        </p>

    </form>
</div>
