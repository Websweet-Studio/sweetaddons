<?php

/**
 * Floating WhatsApp functionality for Sweet Addons
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class Sweetaddons_WhatsApp
{
    public function __construct()
    {
        add_action('wp_footer', array($this, 'output_whatsapp_widget'));
        add_action('wp_head', array($this, 'print_whatsapp_styles'));
    }

    public function print_whatsapp_styles()
    {
        $enable_whatsapp = get_option('sweetaddons_whatsapp_enable');

        if ($enable_whatsapp && !is_admin()) {
            echo '<style>' . $this->get_whatsapp_css() . '</style>';
        }
    }

    public function output_whatsapp_widget()
    {
        if (is_admin()) {
            return;
        }

        $enable_whatsapp = get_option('sweetaddons_whatsapp_enable');
        $phone_number = get_option('sweetaddons_whatsapp_phone');
        $link_type = get_option('sweetaddons_whatsapp_link_type', 'phone');
        $custom_url = get_option('sweetaddons_whatsapp_custom_url');

        // Check if configured based on link type
        if (!$enable_whatsapp) {
            return;
        }

        if ($link_type === 'custom' && empty($custom_url)) {
            return;
        }

        if ($link_type !== 'custom' && empty($phone_number)) {
            return;
        }

        $message = get_option('sweetaddons_whatsapp_message', 'Halo! Saya butuh bantuan.');
        $button_text = get_option('sweetaddons_whatsapp_button_text', 'Chat dengan kami');
        $position = get_option('sweetaddons_whatsapp_position', 'bottom-right');
        $show_on_mobile = get_option('sweetaddons_whatsapp_show_mobile', '1');
        $show_on_desktop = get_option('sweetaddons_whatsapp_show_desktop', '1');
        $animation = get_option('sweetaddons_whatsapp_animation', 'pulse');
        $bubble_style = get_option('sweetaddons_whatsapp_bubble_style', 'circle');
        $custom_image = get_option('sweetaddons_whatsapp_custom_image');

        if ($link_type === 'custom') {
            $whatsapp_url = $custom_url;
        } else {
            // Clean phone number
            $clean_phone = preg_replace('/[^0-9]/', '', $phone_number);
            if (substr($clean_phone, 0, 1) !== '+') {
                $clean_phone = '+' . $clean_phone;
            }

            // replace 62 jika karakter pertama adalah 0
            if (substr($clean_phone, 0, 1) === '0') {
                $clean_phone = '62' . substr($clean_phone, 1);
            }

            // Generate WhatsApp URL
            $whatsapp_url = 'https://wa.me/' . ltrim($clean_phone, '+') . '?text=' . urlencode($message);
        }

        // Position classes
        $position_classes = $this->get_position_classes($position);
        $device_classes = '';

        if (!$show_on_mobile) {
            $device_classes .= ' sweetaddons-wa-hide-mobile';
        }
        if (!$show_on_desktop) {
            $device_classes .= ' sweetaddons-wa-hide-desktop';
        }

?>
        <div id="sweetaddons-whatsapp-widget" class="sweetaddons-wa-widget <?php echo esc_attr($position_classes . $device_classes); ?>" data-animation="<?php echo esc_attr($animation); ?>">
            <div class="sweetaddons-wa-bubble sweetaddons-wa-<?php echo esc_attr($bubble_style); ?> <?php echo $custom_image ? 'sweetaddons-wa-has-image' : ''; ?>">
                <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener" class="sweetaddons-wa-link">
                    <div class="sweetaddons-wa-icon">
                        <?php if ($custom_image) : ?>
                            <img src="<?php echo esc_url($custom_image); ?>" alt="WhatsApp" />
                        <?php else : ?>
                            <svg viewBox="0 0 24 24" width="24" height="24">
                                <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <?php if ($bubble_style === 'extended'): ?>
                        <span class="sweetaddons-wa-text"><?php echo esc_html($button_text); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <?php if (get_option('sweetaddons_whatsapp_show_tooltip', '1')): ?>
                <div class="sweetaddons-wa-tooltip">
                    <?php echo esc_html($button_text); ?>
                </div>
            <?php endif; ?>
        </div>
<?php
    }

    private function get_position_classes($position)
    {
        switch ($position) {
            case 'bottom-left':
                return 'sweetaddons-wa-bottom-left';
            case 'bottom-right':
            default:
                return 'sweetaddons-wa-bottom-right';
            case 'top-left':
                return 'sweetaddons-wa-top-left';
            case 'top-right':
                return 'sweetaddons-wa-top-right';
            case 'center-left':
                return 'sweetaddons-wa-center-left';
            case 'center-right':
                return 'sweetaddons-wa-center-right';
        }
    }

    private function get_whatsapp_css()
    {
        $primary_color = get_option('sweetaddons_whatsapp_color', '#25D366');
        $size = get_option('sweetaddons_whatsapp_size', '60');
        $offset_x = get_option('sweetaddons_whatsapp_offset_x', '20');
        $offset_y = get_option('sweetaddons_whatsapp_offset_y', '20');

        return "
        .sweetaddons-wa-widget {
            position: fixed;
            z-index: 999999;
            transition: all 0.3s ease;
        }

        .sweetaddons-wa-bottom-right {
            bottom: {$offset_y}px;
            right: {$offset_x}px;
        }

        .sweetaddons-wa-bottom-left {
            bottom: {$offset_y}px;
            left: {$offset_x}px;
        }

        .sweetaddons-wa-top-right {
            top: {$offset_y}px;
            right: {$offset_x}px;
        }

        .sweetaddons-wa-top-left {
            top: {$offset_y}px;
            left: {$offset_x}px;
        }

        .sweetaddons-wa-center-right {
            top: 50%;
            right: {$offset_x}px;
            transform: translateY(-50%);
        }

        .sweetaddons-wa-center-left {
            top: 50%;
            left: {$offset_x}px;
            transform: translateY(-50%);
        }

        .sweetaddons-wa-bubble {
            position: relative;
        }

        .sweetaddons-wa-circle .sweetaddons-wa-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: {$size}px;
            height: {$size}px;
            background: {$primary_color};
            border-radius: 50%;
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
        }

        .sweetaddons-wa-extended .sweetaddons-wa-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            background: {$primary_color};
            border-radius: 25px;
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .sweetaddons-wa-extended .sweetaddons-wa-icon {
            margin-right: 8px;
        }

        .sweetaddons-wa-extended .sweetaddons-wa-text {
            font-size: 14px;
            font-weight: 500;
        }

        .sweetaddons-wa-link:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
        }

        .sweetaddons-wa-icon svg {
            width: 24px;
            height: 24px;
        }

        .sweetaddons-wa-tooltip {
            position: absolute;
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .sweetaddons-wa-bottom-right .sweetaddons-wa-tooltip,
        .sweetaddons-wa-bottom-left .sweetaddons-wa-tooltip {
            bottom: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
        }

        .sweetaddons-wa-top-right .sweetaddons-wa-tooltip,
        .sweetaddons-wa-top-left .sweetaddons-wa-tooltip {
            top: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
        }

        .sweetaddons-wa-center-right .sweetaddons-wa-tooltip {
            right: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%);
        }

        .sweetaddons-wa-center-left .sweetaddons-wa-tooltip {
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%);
        }

        .sweetaddons-wa-widget:hover .sweetaddons-wa-tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* Animations */
        .sweetaddons-wa-widget[data-animation='pulse'] .sweetaddons-wa-link {
            animation: sweetaddons-wa-pulse 2s infinite;
        }

        .sweetaddons-wa-widget[data-animation='bounce'] .sweetaddons-wa-link {
            animation: sweetaddons-wa-bounce 2s infinite;
        }

        .sweetaddons-wa-widget[data-animation='shake'] .sweetaddons-wa-link {
            animation: sweetaddons-wa-shake 3s infinite;
        }

        @keyframes sweetaddons-wa-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes sweetaddons-wa-bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        @keyframes sweetaddons-wa-shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sweetaddons-wa-hide-mobile {
                display: none !important;
            }
            
            .sweetaddons-wa-extended .sweetaddons-wa-text {
                display: none;
            }
            
            .sweetaddons-wa-extended .sweetaddons-wa-link {
                width: {$size}px;
                height: {$size}px;
                border-radius: 50%;
                padding: 0;
                justify-content: center;
            }
            
            .sweetaddons-wa-extended .sweetaddons-wa-icon {
                margin-right: 0;
            }
        }

        @media (min-width: 769px) {
            .sweetaddons-wa-hide-desktop {
                display: none !important;
            }
        }

        /* Accessibility */
        .sweetaddons-wa-link:focus {
            outline: 2px solid #fff;
            outline-offset: 2px;
        }

        /* Custom Image Styles */
        .sweetaddons-wa-has-image .sweetaddons-wa-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sweetaddons-wa-has-image .sweetaddons-wa-icon img {
            display: block;
            border-radius: 50%;
            object-fit: cover;
        }

        .sweetaddons-wa-circle.sweetaddons-wa-has-image .sweetaddons-wa-icon {
            width: 100%;
            height: 100%;
        }

        .sweetaddons-wa-circle.sweetaddons-wa-has-image .sweetaddons-wa-icon img {
            width: 100%;
            height: 100%;
        }

        .sweetaddons-wa-extended.sweetaddons-wa-has-image .sweetaddons-wa-icon img {
            width: 24px;
            height: 24px;
        }

        @media (max-width: 768px) {
            .sweetaddons-wa-extended.sweetaddons-wa-has-image .sweetaddons-wa-icon {
                width: 100%;
                height: 100%;
                margin-right: 0;
            }
            
            .sweetaddons-wa-extended.sweetaddons-wa-has-image .sweetaddons-wa-icon img {
                width: 100%;
                height: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .sweetaddons-wa-widget * {
                animation: none !important;
                transition: none !important;
            }
        }
        ";
    }
}
