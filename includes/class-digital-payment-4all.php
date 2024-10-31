<?php

include_once 'digital-payment-4all-gateway.php';

class APP_Gateway_4all extends APP_Gateway {

  /**
	 * Sets up the gateway
	 */
	public function __construct() {
		parent::__construct( 'digitalPayments4all', array(
			'dropdown' => __( 'Pagamentos digitais 4all', 'digital-payment-4all' ),
			'admin' => __( 'Pagamentos digitais 4all', 'digital-payment-4all' ),
		) );
  }

  public function form() {
    $general = array(
			'title' => __( 'General', 'digital-payment-4all' ),
			'desc' => __( 'If you do not already have 4all merchant account, <a href="https://autocredenciamento.4all.com" target="_blank">please register in Production</a>.', 'digital-payment-4all' ),
			'fields' => array(
				array(
					'title' => __( 'Merchant key', 'digital-payment-4all' ),
					'type' => 'text',
					'name' => 'merchant_key_prod',
					'desc' => __( 'Please enter your merchantKey of production. This is needed to process the payment.', 'digital-payment-4all' ),
					'tip' => __( 'This is your private key to access the 4all gateway in the production environment', 'digital-payment-4all' ),
				)
			)
    );

    $sandbox = array(
			'title' => __( 'Sandbox', 'digital-payment-4all' ),
			'desc' => __( 'If enabled, sandbox mode will send your transactions to the homolog environment.', 'digital-payment-4all' ),
			'fields' => array(
        array(
					'title' => __( 'Enable', 'digital-payment-4all' ),
					'type' => 'checkbox',
          'name' => 'sandbox_enabled',
          'desc' => __( 'Enable/Disable the sandbox mode', 'digital-payment-4all' ),
				),
        array(
					'title' => __( 'Merchant key', 'digital-payment-4all' ),
					'type' => 'text',
					'name' => 'merchant_key_homolog',
					'desc' => __( 'Please enter your merchantKey of homolog. This is needed to process the payment.', 'digital-payment-4all' ),
					'tip' => __( 'This is your private key to access the 4all gateway in the homolog environment', 'digital-payment-4all' ),
				)
			)
    );

    return array('general' => $general, 'sandbox' => $sandbox);
  }

  public function process( $order, $options ) {
		$environment = $options['sandbox_enabled'] ? 'https://gateway.homolog-interna.4all.com/' : 'https://gateway.api.4all.com/';
		$key = $options['sandbox_enabled'] ? $options['merchant_key_homolog'] : $options['merchant_key_prod'];
		$settings = array('merchantKey' => $key, 'environment' => $environment);
		$gateway_4all = new Gateway_4all($settings);
		$transactionError = false;
		$formUrl = $order->get_return_url();
		$cancelUrl = $order->get_cancel_url();

		if (isset( $_POST['completeTransaction'] )) {
			$fieldError = null;

			if (empty($_REQUEST["cardholderName"])) {
				$fieldError = __('Card holder name is a required field', 'digital-payment-4all');
			} elseif (!preg_match('/([A-z])/', $_REQUEST['cardholderName']) || strlen($_REQUEST['cardholderName']) < 2 || strlen($_REQUEST['cardholderName']) > 28) {
				$fieldError = __('Invalid holder name', 'digital-payment-4all');
			}

			if (empty( $_REQUEST['cardNumber'] )) {
				$fieldError = __('Card number is a required field', 'digital-payment-4all');
			} elseif (!preg_match('/([0-9])/', $_REQUEST['cardNumber']) || strlen($_REQUEST['cardNumber']) < 12 || strlen($_REQUEST['cardNumber']) > 19) {
				$fieldError = __('Invalid card number', 'digital-payment-4all');
			}

			if (empty( $_REQUEST['buyerDocument'] )) {
				$fieldError = __('Buyer document is a required field', 'digital-payment-4all');
			} elseif (!preg_match('/([0-9])/', $_REQUEST['buyerDocument']) || strlen($_REQUEST['buyerDocument']) < 14 || strlen($_REQUEST['buyerDocument']) > 14) {
				$fieldError = __('Invalid buyer document', 'digital-payment-4all');
			}

			if (empty( $_REQUEST['expirationDate'] )) {
				$fieldError = __('Expiration date is a required field', 'digital-payment-4all');
			} elseif (!preg_match('/([0-1]{1}[0-9]{1}[\/]{1}[0-9])/', $_REQUEST['expirationDate']) || strlen($_REQUEST['expirationDate']) != 5) {
				$fieldError = __('Invalid expiration date', 'digital-payment-4all');
			}

			if (empty( $_REQUEST['securityCode'] )) {
				$fieldError = __('Security code is a required field', 'digital-payment-4all');
			} elseif (!preg_match('/([0-9])/', $_REQUEST['securityCode']) || strlen($_REQUEST['securityCode']) < 3 || strlen($_REQUEST['securityCode']) > 4) {
				$fieldError = __('Invalid security code', 'digital-payment-4all');
			}

			if ($fieldError) {
				$order->failed();
				$transactionError = true;
				require_once 'form-template.php';
			} else {
				$paymentData = [
					"cardData" => [
						"cardholderName" => sanitize_text_field($_REQUEST["cardholderName"]),
						"buyerDocument" => sanitize_text_field(str_replace(array('.', '-', ' '), '', $_REQUEST["buyerDocument"])),
						"cardNumber" => sanitize_text_field(str_replace(array('.', '-', ' '), '', $_REQUEST["cardNumber"])),
						"expirationDate" => sanitize_text_field($_REQUEST["expirationDate"]),
						"securityCode" => sanitize_text_field($_REQUEST["securityCode"]),
					],
					"installment" => sanitize_text_field($_REQUEST['installment']),
					"total" => (float)$order->get_total() * 100,
					"metaId" => "" . $order->get_id(),
				];
	
				$tryPay = $gateway_4all->paymentFlow_4all($paymentData);
	
				if ($tryPay["error"]) {
					$order->failed();
					$transactionError = true;
					require_once 'form-template.php';
				} else {
					$order->complete();
				}
			}
		} else {
			require_once 'form-template.php';
		}
	}
}

appthemes_register_gateway( 'APP_Gateway_4all' );