<div class="wrap cartly-settings">
    <h2>Cartly Settings</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('cartly-option-group'); ?>
        <?php @do_settings_fields('cartly-option-group'); ?>
        <?php do_settings_sections('cartly'); ?>
        <?php @submit_button(); ?>
    </form>
</div>