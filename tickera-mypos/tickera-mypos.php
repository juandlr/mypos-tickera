<?php
/*
Plugin Name: MyPOS Gateway for Tickera
Plugin URI: http://admin123.net
Description: MyPOS gateway for Tickera
Author: Juan
Version: 1.0
*/

function register_tc_mypos()
{
    class TC_MyPOS_Gateway extends TC_Gateway_API
    {
        var $plugin_name = 'mypos';
        var $admin_name = '';
        var $public_name = '';
        var $method_img_url = '';
        var $admin_img_url = '';
        var $force_ssl = false;
        var $ipn_url;
        var $wallet_number, $sid, $sandboxFlag, $currency, $API_Endpoint, $version, $private_key, $public_certificate, $keyindex;
        var $currencies = array();
        var $automatically_activated = false;
        var $skip_payment_screen = true;

        function init()
        {
            global $tc;
            $this->admin_name = 'MyPOS';//Title of the plugin shown within the admin area
            $this->public_name = 'MyPOS';//Title of the gateway shown on the front-end part of the site
            $this->method_img_url = plugin_dir_url(__FILE__) . 'assets/images/mypos-front.png'; //URL of the image shown on the front-end
            $this->admin_img_url = plugin_dir_url(__FILE__) . 'assets/images/mypos.png'; //URL of the image shown within the wp-admin
            $this->skip_payment_screen = false; // whether to skip payment screen or not, you can set it to true if payment screen is not needed like for FREE Orders, forms with only hidden fields etc
            $this->version = '1.0';
            $this->wallet_number = $this->get_option('wallet_number', '', 'mypos');
            $this->sid = $this->get_option('sid', '', 'mypos');
            $this->sandboxFlag = $this->get_option('mode', 'sandbox', 'mypos');
            $this->currency = $this->get_option('currency', 'USD', 'mypos');
            if (!($this->sandboxFlag == 'sandbox')) {
                $this->sid = $this->get_option('production_sid');
                $this->wallet_number = $this->get_option('production_wallet_number');
                $this->private_key = $this->get_option('production_private_key');
                $this->public_certificate = $this->get_option('production_public_certificate');
                $this->API_Endpoint = 'https://www.mypos.eu/vmp/checkout';
                $this->keyindex = $this->get_option('production_keyindex');
            } else {
                $this->sid = $this->get_option('developer_sid');
                $this->wallet_number = $this->get_option('developer_wallet_number');
                $this->private_key = $this->get_option('developer_private_key');
                $this->public_certificate = $this->get_option('developer_public_certificate');
                $this->API_Endpoint = 'https://www.mypos.eu/vmp/checkout-test';
                $this->keyindex = $this->get_option('developer_keyindex');
            }
            $currencies = array(
                "AED" => __('AED - United Arab Emirates Dirham', 'tc'),
                "ARS" => __('ARS - Argentina Peso', 'tc'),
                "AUD" => __('AUD - Australian Dollar', 'tc'),
                "BRL" => __('BRL - Brazilian Real', 'tc'),
                "CAD" => __('CAD - Canadian Dollar', 'tc'),
                "CHF" => __('CHF - Swiss Franc', 'tc'),
                "DKK" => __('DKK - Danish Krone', 'tc'),
                "EUR" => __('EUR - Euro', 'tc'),
                "GBP" => __('GBP - British Pound', 'tc'),
                "HKD" => __('HKD - Hong Kong Dollar', 'tc'),
                "INR" => __('INR - Indian Rupee', 'tc'),
                "ILS" => __('ILS - Israeli New Shekel', 'tc'),
                "LTL" => __('LTL - Lithuanian Litas', 'tc'),
                "JPY" => __('JPY - Japanese Yen', 'tc'),
                "MYR" => __('MYR - Malaysian Ringgit', 'tc'),
                "MXN" => __('MXN - Mexican Peso', 'tc'),
                "NOK" => __('NOK - Norwegian Krone', 'tc'),
                "NZD" => __('NZD - New Zealand Dollar', 'tc'),
                "PHP" => __('PHP - Philippine Peso', 'tc'),
                "RON" => __('RON - Romanian New Leu', 'tc'),
                "RUB" => __('RUB - Russian Ruble', 'tc'),
                "SEK" => __('SEK - Swedish Krona', 'tc'),
                "SGD" => __('SGD - Singapore Dollar', 'tc'),
                "TRY" => __('TRY - Turkish Lira', 'tc'),
                "USD" => __('USD - U.S. Dollar', 'tc'),
                "ZAR" => __('ZAR - South African Rand', 'tc'),
                "AFN" => __('AFN - Afghan Afghani', 'tc'),
                "ALL" => __('ALL - Albanian Lek', 'tc'),
                "AZN" => __('AZN - Azerbaijani an Manat', 'tc'),
                "BSD" => __('BSD - Bahamian Dollar', 'tc'),
                "BDT" => __('BDT - Bangladeshi Taka', 'tc'),
                "BBD" => __('BBD - Barbados Dollar', 'tc'),
                "BZD" => __('BZD - Belizean dollar', 'tc'),
                "BMD" => __('BMD - Bermudian Dollar', 'tc'),
                "BOB" => __('BOB - Bolivian Boliviano', 'tc'),
                "BWP" => __('BWP - Botswana Pula', 'tc'),
                "BND" => __('BND - Brunei Dollar', 'tc'),
                "BGN" => __('BGN - Bulgarian Lev', 'tc'),
                "CLP" => __('CLP - Chilean Peso', 'tc'),
                "CNY" => __('CNY - Chinese Yuan Renminbi', 'tc'),
                "COP" => __('COP - Colombian Peso', 'tc'),
                "CRC" => __('CRC - Costa Rican Colon', 'tc'),
                "HRK" => __('HRK - Croatian Kuna', 'tc'),
                "CZK" => __('CZK - Czech Republic Koruna', 'tc'),
                "DOP" => __('DOP - Dominican Peso', 'tc'),
                "XCD" => __('XCD - East Caribbean Dollar', 'tc'),
                "EGP" => __('EGP - Egyptian Pound', 'tc'),
                "FJD" => __('FJD - Fiji Dollar', 'tc'),
                "GTQ" => __('GTQ - Guatemala Quetzal', 'tc'),
                "HNL" => __('HNL - Honduras Lempira', 'tc'),
                "HUF" => __('HUF - Hungarian Forint', 'tc'),
                "IDR" => __('IDR - Indonesian Rupiah', 'tc'),
                "JMD" => __('JMD - Jamaican Dollar', 'tc'),
                "KZT" => __('KZT - Kazakhstan Tenge', 'tc'),
                "KES" => __('KES - Kenyan Shilling', 'tc'),
                "LAK" => __('LAK - Laosian kip', 'tc'),
                "MMK" => __('MMK - Myanmar Kyat', 'tc'),
                "LBP" => __('LBP - Lebanese Pound', 'tc'),
                "LRD" => __('LRD - Liberian Dollar', 'tc'),
                "MOP" => __('MOP - Macanese Pataca', 'tc'),
                "MVR" => __('MVR - Maldiveres Rufiyaa', 'tc'),
                "MRO" => __('MRO - Mauritanian Ouguiya', 'tc'),
                "MUR" => __('MUR - Mauritius Rupee', 'tc'),
                "MAD" => __('MAD - Moroccan Dirham', 'tc'),
                "NPR" => __('NPR - Nepalese Rupee', 'tc'),
                "TWD" => __('TWD - New Taiwan Dollar', 'tc'),
                "NIO" => __('NIO - Nicaraguan Cordoba', 'tc'),
                "PKR" => __('PKR - Pakistan Rupee', 'tc'),
                "PGK" => __('PGK - New Guinea kina', 'tc'),
                "PEN" => __('PEN - Peru Nuevo Sol', 'tc'),
                "PLN" => __('PLN - Poland Zloty', 'tc'),
                "QAR" => __('QAR - Qatari Rial', 'tc'),
                "WST" => __('WST - Samoan Tala', 'tc'),
                "SAR" => __('SAR - Saudi Arabian riyal', 'tc'),
                "SCR" => __('SCR - Seychelles Rupee', 'tc'),
                "SBD" => __('SBD - Solomon Islands Dollar', 'tc'),
                "KRW" => __('KRW - South Korean Won', 'tc'),
                "LKR" => __('LKR - Sri Lanka Rupee', 'tc'),
                "CHF" => __('CHF - Switzerland Franc', 'tc'),
                "SYP" => __('SYP - Syrian Arab Republic Pound', 'tc'),
                "THB" => __('THB - Thailand Baht', 'tc'),
                "TOP" => __('TOP - Tonga Pa&#x27;anga', 'tc'),
                "TTD" => __('TTD - Trinidad and Tobago Dollar', 'tc'),
                "UAH" => __('UAH - Ukraine Hryvnia', 'tc'),
                "VUV" => __('VUV - Vanuatu Vatu', 'tc'),
                "VND" => __('VND - Vietnam Dong', 'tc'),
                "XOF" => __('XOF - West African CFA Franc BCEAO', 'tc'),
                "YER" => __('YER - Yemeni Rial', 'tc'),
            );

            $this->currencies = $currencies;

        }


        function process_payment($cart)
        {
            global $tc;
            $this->maybe_start_session();
            $this->save_cart_info();
            $payment_info = $this->save_payment_info();
            $this->order_id = $tc->generate_order_id();
            $paid = false;
            $tc->create_order($this->order_id, $this->cart_contents(), $this->cart_info(), $payment_info, $paid);
            $post = $this->create_post($this->order_id);
            $form = $this->generate_ipc_form($post);
            echo $form;
            exit(0);
        }

        function payment_form($cart)
        {

        }

        /**
         * Generate myPOS Checkout form
         * @param $post
         * @return string
         */
        public function generate_ipc_form($post)
        {

            foreach ($post as $key => $value) {
                $value = htmlspecialchars($value, ENT_QUOTES);
                $post_array[] = "<input type='hidden' name='$key' value='$value'/>";
            }

            // var_dump("Show payment form for order: " . $this->order_id);

            return '<form id="tcMypos" action="' . $this->API_Endpoint . '" method="post">
               ' . implode('', $post_array) . '
                    <button type="submit">' . __('Pay', 'tc') . '</button>
                </form>' .
            '<script>' .
            'window.onload = function()  {' .
            'document.getElementById("tcMypos").submit();' .
            '}' .
            '</script>';
        }


        public function create_post($order_id)
        {
            global $tc;

            $post = array();
            $countries = include("includes/countries.php");

            $post['IPCmethod'] = 'IPCPurchase';
            $post['IPCVersion'] = $this->version;
            $post['IPCLanguage'] = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : substr(get_locale(), 0, 2);
            $post['WalletNumber'] = $this->wallet_number;
            $post['SID'] = $this->sid;
            $post['keyindex'] = $this->keyindex;
            $post['Source'] = 'sc_wp_woocommerce 1.4';
            $post['Amount'] = number_format($this->total(), 2, '.', '');
            $post['Currency'] = $this->currency;
            $post['OrderID'] = $order_id;
            $post['URL_OK'] = $this->ipn_url;
            $post['URL_CANCEL'] = $this->ipn_url;
            $post['URL_Notify'] = $this->ipn_url;
            $post['CustomerIP'] = $_SERVER['REMOTE_ADDR'];
            $post['CustomerEmail'] = $this->buyer_info('email');
            $post['CustomerFirstNames'] = $this->buyer_info('first_name');
            $post['CustomerFamilyName'] = $this->buyer_info('last_name');
            $post['CustomerCountry'] = 'AUS';
            $post['CustomerCity'] = 'Sydney';
            $post['CustomerZIPCode'] = '33322';
            $post['CustomerAddress'] = '124 Old Road';
            $post['CustomerPhone'] = '+119-32-4258719';
            $post['Note'] = 'myPOS Checkout Tickera Extension';


            $index = 1;


            $post['Article_' . $index] = $this->cart_items();
            $post['Price_' . $index] = $_SESSION['tc_cart_subtotal'];
            $post['Amount_' . $index] = number_format($_SESSION['tc_cart_subtotal'], 2, '.', '');
            $post['Quantity_' . $index] = 1;
            $post['Currency_' . $index] = $this->currency;


            if (isset($_SESSION['tc_cart_subtotal']) && $_SESSION['tc_discount_code']) {
                $index++;
                $post['Article_' . $index] = 'Discount';
                $post['Price_' . $index] = $_SESSION['discount_value_total'];
                $post['Amount_' . $index] = number_format($_SESSION['discount_value_total'], 2, '.', '');
                $post['Quantity_' . $index] = 1;
                $post['Currency_' . $index] = $this->currency;
            }

            if ($this->total_fees()) {
                $index++;
                $post['Article_' . $index] = 'Fees';
                $post['Price_' . $index] = $this->total_fees();
                $post['Amount_' . $index] = number_format($this->total_fees(), 2, '.', '');
                $post['Quantity_' . $index] = 1;
                $post['Currency_' . $index] = $this->currency;
                $index++;
            }


            if ($this->total_taxes()) {
                $index++;
                $post['Article_' . $index] = 'Tax';
                $post['Price_' . $index] = $this->total_taxes();
                $post['Amount_' . $index] = number_format($this->total_taxes(), 2, '.', '');
                $post['Quantity_' . $index] = 1;
                $post['Currency_' . $index] = $this->currency;
            }


            $post['CartItems'] = $index;
            $post['Signature'] = $this->create_signature($post);

            return $post;
        }


        private function create_signature($post)
        {
            error_log("Create signature for order: " . $post['OrderID']);
            $concData = base64_encode(implode('-', $post));
            $privKeyObj = openssl_get_privatekey($this->private_key);
            openssl_sign($concData, $signature, $privKeyObj, OPENSSL_ALGO_SHA256);
            return base64_encode($signature);
        }


        private function is_valid_signature($post)
        {
            error_log(print_r($post, true));
            // Save signature
            $signature = $post['Signature'];

            // Remove signature from POST data array
            unset($post['Signature']);

            // Concatenate all values
            $concData = base64_encode(implode('-', $post));

            // Extract public key from certificate
            $pubKeyId = openssl_get_publickey($this->public_certificate);

            // Verify signature
            $result = openssl_verify($concData, base64_decode($signature), $pubKeyId, OPENSSL_ALGO_SHA256);

            //Free key resource
            openssl_free_key($pubKeyId);

            if ($result == 1) {
                return true;
            } else {
                error_log('Invalid signature. ' . (isset($post['OrderID']) ? 'Order: ' . $post['OrderID'] : ''));
                return false;
            }
        }

        function ipn()
        {
            error_log("Notify url request.");

            /**
             * @var WooCommerce $woocommerce
             */
            global $tc;
            $post = $_POST;
            $order = tc_get_order_id_by_name($post['OrderID']);
            if ($this->is_valid_signature($post)) {

                if ($post['IPCmethod'] == 'IPCPurchaseNotify') {
                    $tc->update_order_payment_status($order->ID, true);
                    echo 'OK';
                    exit;

                } else if ($post['IPCmethod'] == 'IPCPurchaseRollback') {
                    error_log($order->ID, __('Gateway has declined payment', 'tc'));
                    TC_Order::add_order_note($order->ID, __('Gateway has declined payment', 'tc'));
                    echo 'OK';
                    exit;

                } else if ($post['IPCmethod'] == 'IPCPurchaseCancel') {
                    error_log("IPCPurchaseCancel request for order: " . $post['OrderID']);
                    error_log($tc->update_order_status($order->ID, 'order_cancelled'));
                    wp_redirect($tc->get_confirmation_slug(true, $post['OrderID']));
                    exit;

                } else if ($post['IPCmethod'] == 'IPCPurchaseOK') {
                    error_log("IPCPurchaseOK request for order: " . $post['OrderID']);
                    wp_redirect($tc->get_confirmation_slug(true, $post['OrderID']));
                    exit;

                } else {
                    echo "<p style='color: red; text-align: center; padding:50px;'>INVALID METHOD</p>";
                    exit;

                }
                $tc->remove_order_session_data();
            }
            echo "<p style='color:red; text-align: center; padding:50px;'>INVALID SIGNATURE</p>";
            exit;

        }

        function gateway_admin_settings($settings, $visible)
        {
            global $tc;
            ?>
            <div id="<?php echo esc_attr($this->plugin_name); ?>" class="postbox" <?php echo(!$visible ? 'style="display:none;"' : ''); ?>>
                <h3 class='hndle'><span><?php printf(__('%s Settings', 'tc'), $this->admin_name); ?></span></h3>

                <div class="inside">
                    <?php
                    /**
                     * Settings for myPOS Checkout
                     */
                    $fields = array(
                        'title' => array(
                            'title' => __('Title', 'tc'),
                            'description' => __('This controls the title which the user sees during checkout.', 'tc'),
                            'default' => __('myPOS', 'tc'),
                            'type' => 'text',
                            'desc_tip' => true,
                        ),
                        'description' => array(
                            'title' => __('Description', 'tc'),
                            'type' => 'text',
                            'desc_tip' => true,
                            'description' => __('This controls the description which the user sees during checkout.', 'tc'),
                            'default' => __('Pay via myPOS Checkout. You can pay using your Debit/Credit card.', 'tc')
                        ),
                        'mode' => array(
                            'title' => __('Mode', 'tc'),
                            'type' => 'select',
                            'options' => array(
                                'sandbox' => __('Sandbox / Test', 'tc'),
                                'live' => __('Live', 'tc')
                            ),
                            'default' => 'sandbox',
                        ),
                        'currency' => array(
                            'title' => __('Currency', 'tc'),
                            'type' => 'select',
                            'options' => $this->currencies,
                            'default' => 'USD',
                        ),
                        'developer_sid' => array(
                            'title' => __('Developer Store ID', 'tc'),
                            'type' => 'text',
                            'description' => __('Store ID is given when you add a new online store. It could be reviewed in your online banking at www.mypos.eu > menu Online > Online stores.', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'developer_wallet_number' => array(
                            'title' => __('Developer Client Number', 'tc'),
                            'type' => 'text',
                            'description' => __('You can view your myPOS Client number in your online banking at www.mypos.eu', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'developer_private_key' => array(
                            'title' => __('Developer Private Key', 'tc'),
                            'type' => 'textarea',
                            'description' => __('The Private Key for your store is generated in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'developer_public_certificate' => array(
                            'title' => __('Developer myPOS Public Certificate', 'tc'),
                            'type' => 'textarea',
                            'description' => __('The myPOS Public Certificate is available for download in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'developer_keyindex' => array(
                            'title' => __('Developer Key Index', 'tc'),
                            'type' => 'text',
                            'description' => __('The Key Index assigned to the certificate could be reviewed in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'production_sid' => array(
                            'title' => __('Store ID', 'tc'),
                            'type' => 'text',
                            'description' => __('Store ID is given when you add a new online store. It could be reviewed in your online banking at www.mypos.eu > menu Online > Online stores.', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'production_wallet_number' => array(
                            'title' => __('Client Number', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('You can view your myPOS Client number in your online banking at www.mypos.eu', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'production_private_key' => array(
                            'title' => __('Private Key', 'tc'),
                            'type' => 'textarea',
                            'description' => __('The Private Key for your store is generated in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'production_public_certificate' => array(
                            'title' => __('myPOS Public Certificate', 'tc'),
                            'type' => 'textarea',
                            'description' => __('The myPOS Public Certificate is available for download in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'production_keyindex' => array(
                            'title' => __('Production Key Index', 'tc'),
                            'type' => 'text',
                            'description' => __('The Key Index assigned to the certificate could be reviewed in your online banking at www.mypos.eu > menu Online > Online stores > Keys.', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                    );
                    $form = new TC_Form_Fields_API($fields, 'tc', 'gateways', $this->plugin_name);
                    ?>
                    <table class="form-table">
                        <?php $form->admin_options(); ?>
                    </table>
                </div>
            </div>
            <?php
        }
    }

    tc_register_gateway_plugin('TC_MyPOS_Gateway', 'mypos', __('TC MyPOS Gateway', 'tc'));
}

add_action('tc_load_gateway_plugins', 'register_tc_mypos');

add_action('admin_head', 'tickera_mypos_css');

function tickera_mypos_css()
{
    echo '<style>
        .tc-settings #mypos textarea {
    width: 100%;
}
  </style>';
}