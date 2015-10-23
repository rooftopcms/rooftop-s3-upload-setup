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
            <tr>
                <th scope="row">
                    Cloudfront domain

                    <br/>
                    <span class="label">
                        Bucket: <br/>
                        <span class="small"><?php echo $current_bucket; ?>.s3-eu-west-1</span>
                    </span>

                </th>
                <td>
                    <input type="text" name="cloudfront_domain" size="40" value="<?php echo (isset($cloudfront_domain) && $cloudfront_domain) ? $cloudfront_domain : '' ?>"/>
                    <br/>
                    <?php if($cloudfront_domain): ?>
                        Example url: &nbsp;&nbsp;<?php echo $cloudfront_domain;?>/wp-content/uploads/path.jpg
                    <?php endif;?>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field( 'rooftop-s3-offset-config', 'rooftop-s3-field-token' ); ?>

        <p class="submit">
            <input type="submit" value="Save" class="button button-primary" />
        </p>

    </form>
</div>
