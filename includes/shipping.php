<?php
/**
 * GentsTime — Custom Shipping Method
 * Adds "GentsTime Shipping" as a WooCommerce shipping method
 * configurable from: WooCommerce → Settings → Shipping → GentsTime Shipping
 */

if (!defined('ABSPATH')) exit;

add_action('woocommerce_shipping_init', 'gentstime_register_shipping_method');

function gentstime_register_shipping_method()
{
    if (class_exists('GentsTime_Shipping_Method')) return;

    class GentsTime_Shipping_Method extends WC_Shipping_Method
    {
        public function __construct($instance_id = 0)
        {
            $this->id                 = 'gentstime_shipping';
            $this->instance_id        = absint($instance_id);
            $this->method_title       = __('GentsTime Shipping', 'gentstime');
            $this->method_description = __('Location-based flat rate shipping for Bangladesh. Set Dhaka-area and outside-Dhaka rates below.', 'gentstime');
            $this->supports           = ['shipping-zones', 'instance-settings'];
            $this->title              = $this->get_option('title', 'Standard Delivery');

            $this->init();
        }

        public function init()
        {
            $this->init_form_fields();
            $this->init_settings();

            $this->title        = $this->get_option('title', 'Standard Delivery');
            $this->dhaka_rate   = (float) $this->get_option('dhaka_rate', 80);
            $this->outside_rate = (float) $this->get_option('outside_rate', 130);
            $this->dhaka_districts = $this->parse_districts($this->get_option('dhaka_districts', $this->default_dhaka_districts()));

            add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
        }

        /* ── Dashboard settings fields ── */
        public function init_form_fields()
        {
            $this->instance_form_fields = [
                'title' => [
                    'title'       => __('Method Title', 'gentstime'),
                    'type'        => 'text',
                    'description' => __('Name shown to the customer at checkout.', 'gentstime'),
                    'default'     => 'Standard Delivery',
                    'desc_tip'    => true,
                ],
                'dhaka_rate' => [
                    'title'       => __('Dhaka Area Rate (৳)', 'gentstime'),
                    'type'        => 'number',
                    'description' => __('Shipping cost for Dhaka and nearby districts.', 'gentstime'),
                    'default'     => '80',
                    'desc_tip'    => true,
                    'custom_attributes' => ['min' => '0', 'step' => '1'],
                ],
                'outside_rate' => [
                    'title'       => __('Outside Dhaka Rate (৳)', 'gentstime'),
                    'type'        => 'number',
                    'description' => __('Shipping cost for all other districts.', 'gentstime'),
                    'default'     => '130',
                    'desc_tip'    => true,
                    'custom_attributes' => ['min' => '0', 'step' => '1'],
                ],
                'dhaka_districts' => [
                    'title'       => __('Dhaka-Area Districts', 'gentstime'),
                    'type'        => 'textarea',
                    'description' => __('One district name per line. These will get the Dhaka rate. All others get the Outside Dhaka rate.', 'gentstime'),
                    'default'     => $this->default_dhaka_districts(),
                    'desc_tip'    => false,
                    'css'         => 'height:140px;',
                ],
            ];
        }

        /* ── Calculate shipping ── */
        public function calculate_shipping($package = [])
        {
            $district = $this->get_district_from_package($package);

            // No district selected yet — add no rate so summary stays pending
            if (!$district) return;

            $is_dhaka = $this->is_dhaka_area($district);
            $cost     = $is_dhaka ? $this->dhaka_rate : $this->outside_rate;

            $label = $this->title . ' (' . ($is_dhaka
                ? __('Dhaka Area', 'gentstime')
                : __('Outside Dhaka', 'gentstime')) . ')';

            $this->add_rate([
                'id'    => $this->get_rate_id(),
                'label' => $label,
                'cost'  => $cost,
            ]);
        }

        /* ── Resolve district from package destination ── */
        private function get_district_from_package($package)
        {
            // Primary: billing_district saved in session (our custom field)
            $district = WC()->session ? WC()->session->get('billing_district', '') : '';

            // Fallback: destination state field
            if (!$district && !empty($package['destination']['state'])) {
                $district = $package['destination']['state'];
            }

            // Fallback: POST data (live checkout recalc)
            if (!$district && !empty($_POST['billing_district'])) {
                $district = sanitize_text_field(wp_unslash($_POST['billing_district']));
            }

            return trim($district);
        }

        /* ── Check if district is in Dhaka area ── */
        private function is_dhaka_area($district)
        {
            if (!$district) return false;
            $district_lower = strtolower($district);
            foreach ($this->dhaka_districts as $d) {
                if (strtolower(trim($d)) === $district_lower) return true;
            }
            return false;
        }

        /* ── Parse textarea districts into array ── */
        private function parse_districts($raw)
        {
            return array_filter(array_map('trim', explode("\n", str_replace("\r", '', $raw))));
        }

        /* ── Default Dhaka-area district list ── */
        private function default_dhaka_districts()
        {
            return implode("\n", [
                'Dhaka',
                'Narayanganj',
                'Gazipur',
                'Manikganj',
                'Munshiganj',
                'Narsingdi',
            ]);
        }
    }
}

/* Register the method with WooCommerce */
add_filter('woocommerce_shipping_methods', function ($methods) {
    $methods['gentstime_shipping'] = 'GentsTime_Shipping_Method';
    return $methods;
});

/* ── Save billing_district to session on checkout field update ── */
add_action('woocommerce_checkout_update_order_review', function ($post_data) {
    parse_str($post_data, $fields);
    if (!empty($fields['billing_district']) && WC()->session) {
        WC()->session->set('billing_district', sanitize_text_field($fields['billing_district']));
        WC()->shipping()->reset_shipping();
        WC()->cart->calculate_shipping();
    }
});
