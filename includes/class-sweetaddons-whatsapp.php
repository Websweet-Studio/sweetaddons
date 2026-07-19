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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_whatsapp_styles'));
    }

    public function enqueue_whatsapp_styles()
    {
        $enable_whatsapp = get_option('sweetaddons_whatsapp_enable');
        $phone_number = get_option('sweetaddons_whatsapp_phone');
        $agents = get_option('sweetaddons_whatsapp_agents', array());
        $has_agents = is_array($agents) && !empty($agents);

        if ($enable_whatsapp && !is_admin() && ($phone_number || $has_agents)) {
            wp_register_style('sweetaddons-whatsapp-css', false);
            wp_enqueue_style('sweetaddons-whatsapp-css');
            wp_add_inline_style('sweetaddons-whatsapp-css', $this->get_whatsapp_css());
        }
    }

    public function output_whatsapp_widget()
    {
        if (is_admin()) {
            return;
        }

        $enable_whatsapp = get_option('sweetaddons_whatsapp_enable');
        $phone_number = get_option('sweetaddons_whatsapp_phone');
        $agents = get_option('sweetaddons_whatsapp_agents', array());
        if (!is_array($agents)) {
            $agents = array();
        }

        if (!$enable_whatsapp || (!$phone_number && empty($agents))) {
            return;
        }

        $message = get_option('sweetaddons_whatsapp_message', 'Halo! Saya butuh bantuan.');
        $button_text = get_option('sweetaddons_whatsapp_button_text', 'Chat dengan kami');
        $position = get_option('sweetaddons_whatsapp_position', 'bottom-right');
        $show_on_mobile = get_option('sweetaddons_whatsapp_show_mobile', '1');
        $show_on_desktop = get_option('sweetaddons_whatsapp_show_desktop', '1');
        $show_text_mobile = get_option('sweetaddons_whatsapp_show_text_mobile', '');
        $animation = get_option('sweetaddons_whatsapp_animation', 'none');
        $bubble_style = get_option('sweetaddons_whatsapp_bubble_style', 'circle');
        $device_classes = '';

        if (!$show_on_mobile) {
            $device_classes .= ' sweetaddons-wa-hide-mobile';
        }
        if (!$show_on_desktop) {
            $device_classes .= ' sweetaddons-wa-hide-desktop';
        }
        if ($show_text_mobile) {
            $device_classes .= ' sweetaddons-wa-show-text-mobile';
        }

        $agents_normalized = array();
        foreach ($agents as $agent) {
            if (!is_array($agent)) {
                continue;
            }

            $agent_phone = isset($agent['phone']) ? $this->normalize_phone_number($agent['phone']) : '';
            if (empty($agent_phone)) {
                continue;
            }

            $agents_normalized[] = array(
                'name'   => isset($agent['name']) ? $agent['name'] : '',
                'phone'  => $agent_phone,
                'role'   => isset($agent['role']) ? $agent['role'] : '',
                'note'   => isset($agent['note']) ? $agent['note'] : '',
                'status' => isset($agent['status']) ? $agent['status'] : 'online',
                'avatar' => isset($agent['avatar']) ? $agent['avatar'] : '',
            );
        }

        $agent_count = count($agents_normalized);

        if ($agent_count === 0) {
            if (!$phone_number) {
                return;
            }

            $clean_phone = $this->normalize_phone_number($phone_number);
            if (empty($clean_phone)) {
                return;
            }

            $whatsapp_url = 'https://wa.me/' . $clean_phone . '?text=' . urlencode($message);
            $position_classes = $this->get_position_classes($position);
?>
            <div id="sweetaddons-whatsapp-widget" class="sweetaddons-wa-widget <?php echo esc_attr($position_classes . $device_classes); ?>" data-animation="<?php echo esc_attr($animation); ?>">
                <div class="sweetaddons-wa-bubble sweetaddons-wa-<?php echo esc_attr($bubble_style); ?>">
                    <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener" class="sweetaddons-wa-link">
                        <div class="sweetaddons-wa-icon">
                            <svg viewBox="0 0 24 24" width="24" height="24">
                                <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        </div>
                        <?php if ($bubble_style === 'extended'): ?>
                            <span class="sweetaddons-wa-text"><?php echo esc_html($button_text); ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <?php if (get_option('sweetaddons_whatsapp_show_tooltip', '')): ?>
                    <div class="sweetaddons-wa-tooltip">
                        <?php echo esc_html($button_text); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php
            return;
        }

        if ($agent_count === 1) {
            $clean_phone = $agents_normalized[0]['phone'];
            $whatsapp_url = 'https://wa.me/' . $clean_phone . '?text=' . urlencode($message);
            $position_classes = $this->get_position_classes($position);
        ?>
            <div id="sweetaddons-whatsapp-widget" class="sweetaddons-wa-widget <?php echo esc_attr($position_classes . $device_classes); ?>" data-animation="<?php echo esc_attr($animation); ?>">
                <div class="sweetaddons-wa-bubble sweetaddons-wa-<?php echo esc_attr($bubble_style); ?>">
                    <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener" class="sweetaddons-wa-link">
                        <div class="sweetaddons-wa-icon">
                            <svg viewBox="0 0 24 24" width="24" height="24">
                                <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        </div>
                        <?php if ($bubble_style === 'extended'): ?>
                            <span class="sweetaddons-wa-text"><?php echo esc_html($button_text); ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <?php if (get_option('sweetaddons_whatsapp_show_tooltip', '')): ?>
                    <div class="sweetaddons-wa-tooltip">
                        <?php echo esc_html($button_text); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php
            return;
        }

        // Position classes
        $position_classes = $this->get_position_classes($position);
        ?>
        <div id="sweetaddons-whatsapp-widget" class="sweetaddons-wa-widget sweetaddons-wa-multi <?php echo esc_attr($position_classes . $device_classes); ?>" data-animation="<?php echo esc_attr($animation); ?>">
            <div id="sweetaddons-wa-panel" class="sweetaddons-wa-panel" hidden>
                <div class="sweetaddons-wa-panel-header">
                    <div class="sweetaddons-wa-panel-header-row">
                        <div class="sweetaddons-wa-panel-title">Mulai Chat</div>
                    </div>
                </div>
                <div class="sweetaddons-wa-panel-body">
                    <?php foreach ($agents_normalized as $agent): ?>
                        <?php
                        $agent_name = !empty($agent['name']) ? $agent['name'] : $button_text;
                        $agent_role = !empty($agent['role']) ? $agent['role'] : '';
                        $agent_note = !empty($agent['note']) ? $agent['note'] : '';
                        $agent_status = !empty($agent['status']) ? $agent['status'] : 'online';
                        $agent_avatar = !empty($agent['avatar']) ? $agent['avatar'] : '';
                        $agent_url = 'https://wa.me/' . $agent['phone'] . '?text=' . urlencode($message);
                        $agent_initial = strtoupper(substr(trim($agent_name), 0, 1));
                        ?>
                        <a class="sweetaddons-wa-agent sweetaddons-wa-agent--<?php echo esc_attr($agent_status); ?>" href="<?php echo esc_url($agent_url); ?>" target="_blank" rel="noopener">
                            <div class="sweetaddons-wa-agent-left">
                                <div class="sweetaddons-wa-agent-avatar">
                                    <?php if (!empty($agent_avatar)): ?>
                                        <img src="<?php echo esc_url($agent_avatar); ?>" alt="<?php echo esc_attr($agent_name); ?>" />
                                    <?php else: ?>
                                        <span><?php echo esc_html($agent_initial); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="sweetaddons-wa-agent-meta">
                                    <div class="sweetaddons-wa-agent-name"><?php echo esc_html($agent_name); ?></div>
                                    <?php if (!empty($agent_role)): ?>
                                        <div class="sweetaddons-wa-agent-role"><?php echo esc_html($agent_role); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($agent_note)): ?>
                                        <div class="sweetaddons-wa-agent-note"><?php echo esc_html($agent_note); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="sweetaddons-wa-agent-cta">
                                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                                    <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884" />
                                </svg>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sweetaddons-wa-bubble sweetaddons-wa-<?php echo esc_attr($bubble_style); ?>">
                <button type="button" class="sweetaddons-wa-link sweetaddons-wa-trigger" aria-controls="sweetaddons-wa-panel" aria-expanded="false">
                    <div class="sweetaddons-wa-icon">
                        <svg class="sweetaddons-wa-icon-chat" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                            <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                        </svg>
                        <svg class="sweetaddons-wa-icon-close" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                            <path fill="currentColor" d="M18.3 5.71a1 1 0 0 0-1.41 0L12 10.59 7.11 5.7A1 1 0 0 0 5.7 7.11L10.59 12 5.7 16.89a1 1 0 1 0 1.41 1.41L12 13.41l4.89 4.89a1 1 0 0 0 1.41-1.41L13.41 12l4.89-4.89a1 1 0 0 0 0-1.4z" />
                        </svg>
                    </div>
                    <?php if ($bubble_style === 'extended'): ?>
                        <span class="sweetaddons-wa-text"><?php echo esc_html($button_text); ?></span>
                    <?php endif; ?>
                </button>
            </div>

            <?php if (get_option('sweetaddons_whatsapp_show_tooltip', '')): ?>
                <div class="sweetaddons-wa-tooltip">
                    <?php echo esc_html($button_text); ?>
                </div>
            <?php endif; ?>
        </div>
        <script>
            (function() {
                var widget = document.getElementById('sweetaddons-whatsapp-widget');
                if (!widget) {
                    return;
                }
                var panel = document.getElementById('sweetaddons-wa-panel');
                var trigger = widget.querySelector('.sweetaddons-wa-trigger');
                if (!panel || !trigger) {
                    return;
                }

                function openPanel() {
                    panel.hidden = false;
                    panel.classList.add('is-open');
                    widget.classList.add('sweetaddons-wa-panel-open');
                    trigger.setAttribute('aria-expanded', 'true');
                }

                function closePanel() {
                    panel.classList.remove('is-open');
                    panel.hidden = true;
                    widget.classList.remove('sweetaddons-wa-panel-open');
                    trigger.setAttribute('aria-expanded', 'false');
                }

                trigger.addEventListener('click', function() {
                    if (panel.hidden) {
                        openPanel();
                    } else {
                        closePanel();
                    }
                });

                document.addEventListener('click', function(e) {
                    if (panel.hidden) {
                        return;
                    }
                    if (widget.contains(e.target)) {
                        return;
                    }
                    closePanel();
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !panel.hidden) {
                        closePanel();
                    }
                });
            })();
        </script>
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

    private function normalize_phone_number($phone_number)
    {
        $clean_phone = preg_replace('/[^0-9]/', '', (string) $phone_number);
        if (empty($clean_phone)) {
            return '';
        }

        if (substr($clean_phone, 0, 1) === '0') {
            $clean_phone = '62' . substr($clean_phone, 1);
        }

        return $clean_phone;
    }

    private function get_whatsapp_css()
    {
        $primary_color = get_option('sweetaddons_whatsapp_color', '#25D366');
        $size = '60';
        $offset_x = '20';
        $offset_y = '20';

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
            border: none !important;
            outline: none !important;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            -webkit-tap-highlight-color: transparent;
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
            border: none;
        }

        .sweetaddons-wa-icon, .sweetaddons-wa-text {
            position: relative;
            z-index: 1;
        }

        .sweetaddons-wa-extended .sweetaddons-wa-icon {
            margin-right: 8px;
        }

        .sweetaddons-wa-extended .sweetaddons-wa-text {
            font-size: 14px;
            font-weight: 500;
        }

        .sweetaddons-wa-link:hover {
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
        }

        .sweetaddons-wa-icon svg {
            width: 24px;
            height: 24px;
        }

        .sweetaddons-wa-icon-close {
            display: none;
        }

        .sweetaddons-wa-panel-open .sweetaddons-wa-icon-chat {
            display: none;
        }

        .sweetaddons-wa-panel-open .sweetaddons-wa-icon-close {
            display: inline;
        }

        .sweetaddons-wa-panel {
            position: absolute;
            width: 320px;
            max-width: calc(100vw - 40px);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
            --wa-panel-open-transform: translateY(0);
            --wa-panel-closed-transform: translateY(10px);
            transform: var(--wa-panel-closed-transform);
        }

        .sweetaddons-wa-panel.is-open {
            opacity: 1;
            pointer-events: auto;
            transform: var(--wa-panel-open-transform);
        }

        .sweetaddons-wa-panel-header {
            background: {$primary_color};
            color: #fff;
            padding: 14px 16px;
        }

        .sweetaddons-wa-panel-title {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
        }

        .sweetaddons-wa-panel-body {
            background: #f3f5f7;
            padding: 10px;
            max-height: 360px;
            overflow: auto;
        }

        .sweetaddons-wa-agent {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 12px 12px 16px;
            background: #fff;
            border-radius: 12px;
            text-decoration: none;
            color: #111;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06);
            position: relative;
        }

        .sweetaddons-wa-agent + .sweetaddons-wa-agent {
            margin-top: 10px;
        }

        .sweetaddons-wa-agent::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 12px 0 0 12px;
            background: #2ecc71;
        }

        .sweetaddons-wa-agent--offline::before {
            background: #d0d5dd;
        }

        .sweetaddons-wa-agent-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .sweetaddons-wa-agent-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e9edf1;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            color: #475467;
            font-weight: 700;
        }

        .sweetaddons-wa-agent-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .sweetaddons-wa-agent-meta {
            min-width: 0;
        }

        .sweetaddons-wa-agent-name {
            font-weight: 700;
            line-height: 1.2;
            font-size: 14px;
        }

        .sweetaddons-wa-agent-role {
            font-size: 12px;
            color: #667085;
            margin-top: 2px;
            line-height: 1.2;
        }

        .sweetaddons-wa-agent-note {
            font-size: 12px;
            color: #f97316;
            margin-top: 4px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
        }

        .sweetaddons-wa-agent-cta {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(37, 211, 102, 0.12);
            color: {$primary_color};
            flex: 0 0 34px;
        }

        .sweetaddons-wa-bottom-right .sweetaddons-wa-panel {
            bottom: calc(100% + 12px);
            right: 0;
            --wa-panel-open-transform: translateY(0);
            --wa-panel-closed-transform: translateY(10px);
        }

        .sweetaddons-wa-bottom-left .sweetaddons-wa-panel {
            bottom: calc(100% + 12px);
            left: 0;
            --wa-panel-open-transform: translateY(0);
            --wa-panel-closed-transform: translateY(10px);
        }

        .sweetaddons-wa-top-right .sweetaddons-wa-panel {
            top: calc(100% + 12px);
            right: 0;
            --wa-panel-open-transform: translateY(0);
            --wa-panel-closed-transform: translateY(-10px);
        }

        .sweetaddons-wa-top-left .sweetaddons-wa-panel {
            top: calc(100% + 12px);
            left: 0;
            --wa-panel-open-transform: translateY(0);
            --wa-panel-closed-transform: translateY(-10px);
        }

        .sweetaddons-wa-center-right .sweetaddons-wa-panel {
            right: calc(100% + 12px);
            top: 50%;
            --wa-panel-open-transform: translateY(-50%);
            --wa-panel-closed-transform: translateY(-50%) translateX(10px);
        }

        .sweetaddons-wa-center-left .sweetaddons-wa-panel {
            left: calc(100% + 12px);
            top: 50%;
            --wa-panel-open-transform: translateY(-50%);
            --wa-panel-closed-transform: translateY(-50%) translateX(-10px);
        }

        .sweetaddons-wa-panel-open .sweetaddons-wa-tooltip {
            opacity: 0 !important;
            visibility: hidden !important;
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
            50% { transform: scale(1.02); }
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

            .sweetaddons-wa-show-text-mobile.sweetaddons-wa-widget .sweetaddons-wa-extended .sweetaddons-wa-text {
                display: inline;
            }
            
            .sweetaddons-wa-show-text-mobile.sweetaddons-wa-widget .sweetaddons-wa-extended .sweetaddons-wa-link {
                width: auto;
                height: auto;
                border-radius: 25px;
                padding: 12px 20px;
                justify-content: center;
            }
            
            .sweetaddons-wa-show-text-mobile.sweetaddons-wa-widget .sweetaddons-wa-extended .sweetaddons-wa-icon {
                margin-right: 8px;
            }

            .sweetaddons-wa-panel {
                width: 290px;
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

        .sweetaddons-wa-circle .sweetaddons-wa-link:focus,
        .sweetaddons-wa-circle .sweetaddons-wa-link:active {
            outline: none !important;
        }

        @supports selector(:focus-visible) {
            .sweetaddons-wa-circle .sweetaddons-wa-link:focus {
                outline: none !important;
            }

            .sweetaddons-wa-circle .sweetaddons-wa-link:focus-visible {
                box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.92), 0 4px 12px rgba(37, 211, 102, 0.4);
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
