<?php

  /**
   * Validation controller class
   */

  class RavePaymentGatewayValidationModuleFrontController extends ModuleFrontController
  {
    public function postProcess()
    {
      $cart = $this->context->cart;
      if ($cart->id_customer == 0 ||
          $cart->id_address_delivery == 0 ||
          $cart->id_address_invoice == 0 ||
          !$this->module->active
        ) {
         Tools::redirect('index.php?controller=order&step=1');
      }

      $authorized = false;
      foreach (Module::getPaymentModules() as $module) {
        if ($module['name'] == $this->module->name) {
          $authorized = true;
          break;
        }
      }

      if (!$authorized)
        die($this->module->l('This payment method is not available.', 'validation'));

      $customer = new Customer($cart->id_customer);
      if (!Validate::isLoadedObject($customer))
        Tools::redirect('index.php?controller=order&step=1');

      // Setting data
      $message          = null;
      $currency         = $this->context->currency;
      $total            = (float)$cart->getOrderTotal(true, Cart::BOTH);
      $amount_paid      = Tools::getValue('amount');
      $payment_currency = Tools::getValue('currency');
      $payment_customer = Tools::getValue('customer');
      $payment_status   = Tools::getValue('status_code');
      $tx_ref           = Tools::getValue('tx_ref');
      $sec_key          = Configuration::get('RAVE_SC_KEY');

      $extra_vars = array(
        'transaction_id' => $tx_ref,
      );

      $txn = json_decode( $this->_fetchTransaction($tx_ref, $sec_key) );
      $is_successful = !empty($txn->data) && $this->_is_successful($txn->data);

      $message  = 'New Order Details - <br>'.
                  'Transaction Ref: ' . $extra_vars['transaction_id'] . ' - <br>'.
                  'Amount Paid: ' . $amount_paid . ' - <br>'.
                  'Payment Status: ' . $is_successful ? 'successful' : 'failed' . ' - <br>'.
                  'Payment Currency: ' . $payment_currency . ' - <br>'.
                  'Customer: ' . $payment_customer . ' - <br>';

      // Verify payment data

      if ( $is_successful ) {

        $order_status_id = 'PS_OS_PAYMENT';

        if ($amount_paid != $total) {
          $order_status_id = 'PS_OS_RAVE_PENDING';

          $message .= 'Attention: This order have been placed on hold and payment flagged because of incorrect payment amount. Please, look into it. - <br>';
          $message .= 'Amount paid: '.$currency->iso_code.' '.$amount_paid.'  - <br> Order amount: '.
                      $currency->iso_code.' '.$total.'  - <br> Reference: ' .$extra_vars['transaction_id'];

        } elseif ($payment_currency != $currency->iso_code) {

          $order_status_id = 'PS_OS_RAVE_PENDING';

          $message .= 'Attention: This order has been placed on hold and payment flagged because of incorrect payment currency. Please, look into it.  - <br>';
          $message .= 'Payment currency: '.$payment_currency.'  - <br> Order currency: '.
                      $currency->iso_code.'  - <br> Reference: ' .$extra_vars['transaction_id'];

        }

      } else {

       $order_status_id = 'PS_OS_ERROR';

      }

      // Validate Order
      $this->module->validateOrder(
        $cart->id,
        Configuration::get($order_status_id),
        $total,
        $this->module->displayName,
        $message,
        $extra_vars,
        (int)$currency->id,
        false,
        $customer->secure_key
      );

      // Redirect on order confirmation page
      $url = 'index.php?controller=order-confirmation'.
              '&id_cart='.$cart->id.
              '&id_module='.$this->module->id.
              '&id_order='.$this->module->currentOrder.
              '&key='.$customer->secure_key.
              '&txref='.$extra_vars['transaction_id'];

      Tools::redirect($url);
    }

    private function _fetchTransaction($txRef, $secretKey) {

      $URL = $this->context->cookie->base_url . "flwv3-pug/getpaidx/api/verify";
      $data = http_build_query(array(
        'tx_ref' => $txRef,
        'SECKEY' => $secretKey
      ));

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $URL);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSLVERSION, 3);
      $output = curl_exec($ch);
      $failed = curl_errno($ch);
      $error = curl_error($ch);
      curl_close($ch);
      return ($failed) ? $error : $output;

    }

    private function _is_successful($data) {

      return $data->flwMeta->chargeResponse === '00' || $data->flwMeta->chargeResponse === '0';

    }
  }
