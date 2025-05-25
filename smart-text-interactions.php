<?php
/**
 * Plugin Name: Smart Text Interactions
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Customize text highlight and show a tooltip when content is copied.
 * Version: 1.0
 * Author: Pixelhub Media
 */


add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'settings_page_smart-text-interactions') return;
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    add_action('admin_footer', function() {
        echo '<script>jQuery(document).ready(function($){ $(".color-field").wpColorPicker(); });</script>';
    });
});


// Add settings menu under "Settings"
add_action('admin_menu', function() {
    add_options_page('Smart Text Interactions', 'Smart Text Interactions', 'manage_options', 'smart-text-interactions', 'sti_render_settings_page');
});

// Register settings and fields
// Render settings page with scoped styles
function sti_render_settings_page() {
    ?>
    <div class="sti-settings-page">
        <h1 style="font-size: 18px; font-weight: bold;">Smart Text Interactions</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('sti_settings_group');
            do_settings_sections('smart-text-interactions');
            submit_button();
            ?>
        
<div class="sti-preview" style="margin-top: 20px;">
    <strong>Live Preview:</strong>
    <p id="sti-preview-text" style="padding: 8px; border: 1px solid #ccc; user-select: all;">
        Try selecting this text to preview your highlight and text color.
    </p>
</div>
<script>
jQuery(document).ready(function($) {
    function updatePreview() {
        const bg = $('input[name="sti_settings[highlight_bg]"]').val();
        const color = $('input[name="sti_settings[highlight_color]"]').val();
        const style = `::selection { background: ${bg}; color: ${color}; } ::-moz-selection { background: ${bg}; color: ${color}; }`;
        $('#sti-preview-style').remove();
        $('<style id="sti-preview-style">').text(style).appendTo('head');
    }
    $('.color-field').on('input change', updatePreview);
    updatePreview();
});
</script>


<p style="margin-top: 20px; font-style: italic; color: #555;">When text is copied, a copy tooltip appears.</p>
</form>
    </div>
    <style>
        
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400&display=swap');
.sti-settings-page {
    font-family: 'Open Sans', sans-serif;
    font-size: 14px;
    font-weight: 400;
    max-width: 700px;

    h1 { font-size: 18px; font-weight: bold; } }
.sti-settings-page h1,
.sti-settings-page label,
.sti-settings-page th,
.sti-settings-page td,
.sti-settings-page input,
.sti-settings-page select,
.sti-settings-page textarea {
    font-family: 'Open Sans', sans-serif !important;
    font-weight: 400 !important;
    font-size: 14px !important;
}

        .sti-settings-page {
    font-family: 'Open Sans', sans-serif;
    font-size: 14px;
    font-weight: 400;
            font-family: 'Open Sans', sans-serif;
            font-size: 14px;
            font-weight: 400;
            max-width: 700px;
        
    h1 { font-size: 18px; font-weight: bold; } }
        .sti-settings-page input[type="text"],
        .sti-settings-page input[type="text" class="color-field"] {
            margin-top: 4px;
            margin-bottom: 12px;
        }
    
.sti-settings-page h1 { font-size: 18px !important; font-weight: bold !important; } </style>
    <?php
}

// Inject highlight styles into frontend
add_action('wp_head', function() {
    $opt = get_option('sti_settings');
    $bg = esc_attr($opt['highlight_bg'] ?? '#000000');
    $color = isset($opt['highlight_color']) ? esc_attr($opt['highlight_color']) : '#ffffff';
    echo "<style>::selection { background: {$bg}; color: {$color}; } ::-moz-selection { background: {$bg}; color: {$color}; }
.sti-settings-page h1 { font-size: 18px !important; font-weight: bold !important; } </style>";
});

// Show copy message on frontend
add_action('wp_footer', function() {
    $opt = get_option('sti_settings');
    $msg = esc_js($opt['copy_message'] ?? 'Copied!');
    ?>
    <script>
    let lastX = 0, lastY = 0;
    document.addEventListener('mousemove', e => {
        lastX = e.clientX;
        lastY = e.clientY;
    });

    document.addEventListener('copy', () => {
        const tooltip = document.createElement('div');
        tooltip.textContent = "<?php echo $msg; ?>";
        tooltip.style.position = 'fixed';
        tooltip.style.left = (lastX + 10) + 'px';
        tooltip.style.top = (lastY - 10) + 'px';
        tooltip.style.backgroundColor = 'rgba(0,128,0,0.8)';
        tooltip.style.color = '#fff';
        tooltip.style.padding = '6px 10px';
        tooltip.style.borderRadius = '4px';
        tooltip.style.fontSize = '14px';
        tooltip.style.zIndex = '9999';
        tooltip.style.pointerEvents = 'none';
        tooltip.style.transition = 'opacity 1000ms ease';
        tooltip.style.opacity = '1';

        document.body.appendChild(tooltip);
        setTimeout(() => tooltip.style.opacity = '0', 50);
        setTimeout(() => tooltip.remove(), 1050);
    });
    </script>
    <?php
});


// Add extra settings fields
// Update frontend copy behavior with new settings
add_action('wp_footer', function() {
    $opt = get_option('sti_settings');
    if (empty($opt['enable_copy_message'])) return;

    $msg = esc_js($opt['copy_message'] ?? 'Copied!');
    $fade = intval($opt['fade_duration'] ?? 1000);
        ?>
    <script>
    let lastX = 0, lastY = 0;
    document.addEventListener('mousemove', e => {
        lastX = e.clientX;
        lastY = e.clientY;
    });

    document.addEventListener('copy', () => {
        const tooltip = document.createElement('div');
        tooltip.textContent = "<?php echo $msg; ?>";
        tooltip.style.position = 'fixed';
        tooltip.style.backgroundColor = 'rgba(0,128,0,0.8)';
        tooltip.style.color = '#fff';
        tooltip.style.padding = '6px 10px';
        tooltip.style.borderRadius = '4px';
        tooltip.style.fontSize = '14px';
        tooltip.style.zIndex = '9999';
        tooltip.style.pointerEvents = 'none';
        tooltip.style.transition = 'opacity <?php echo $fade; ?>ms ease';
        tooltip.style.opacity = '1';

        if ("<?php echo $pos; ?>" === "cursor") {
            tooltip.style.left = (lastX + 10) + 'px';
            tooltip.style.top = (lastY - 10) + 'px';
        } else {
            tooltip.style.left = '20px';
            tooltip.style.bottom = '20px';
        }

        document.body.appendChild(tooltip);
        setTimeout(() => tooltip.style.opacity = '0', 50);
        setTimeout(() => tooltip.remove(), <?php echo $fade + 50; ?>);
    });
    </script>
    <?php
});


add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="options-general.php?page=smart-text-interactions">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
});


add_action('admin_init', function() {
    register_setting('sti_settings_group', 'sti_settings', function($input) {
    $output = [];
    $output['highlight_bg'] = sanitize_hex_color($input['highlight_bg'] ?? '#000000');
    $output['highlight_color'] = sanitize_hex_color($input['highlight_color'] ?? '#ffffff');
    return $output;
});

    add_settings_section('sti_section', '', null, 'smart-text-interactions');

    add_settings_field('highlight_bg', 'Highlight Background', function() {
        $opt = get_option('sti_settings');
        echo '<input type="text" class="color-field" name="sti_settings[highlight_bg]" value="' . esc_attr($opt['highlight_bg'] ?? '#ffee58') . '">';
    }, 'smart-text-interactions', 'sti_section');

    add_settings_field('highlight_color', 'Highlight Text Color', function() {
        $opt = get_option('sti_settings');
        echo '<input type="text" class="color-field" name="sti_settings[highlight_color]" value="' . esc_attr($opt['highlight_color'] ?? '#000000') . '">';
    }, 'smart-text-interactions', 'sti_section');

    });
