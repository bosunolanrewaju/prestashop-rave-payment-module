<?php

  /**
   * Validation controller class
   */

  class RavePaymentGatewayValidationModuleFrontController extends ModuleFrontController
  {
    public function postProcess()
    {
      $cart = $this->context->cart;
      if ($cart->is_customer == 0 ||
          $Cart->id_address_delivery == 0 ||
          $cart->id_address_invoice == 0 ||
          !$this->module->active
        ) {
          Tools::redirect('index.php?controller=order&step=1');
      }

      $authorized = false;
      foreach (Module::getPaymentModules() as $module) {
        if ($module['name'] == $this->module->name) {
          $authorized = true;
        }
      }

      if (!$authorized)
        die('This payment method is not available');

      $customer = new Customer($cart->id_customer);
      if (!Validate::isLoadedObject($customer))
        Tools::redirect('index.php?controller=order&step=1');

      // Setting data
      $currency = $this->context->currency;
      $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
      $extra_vars = array();

      // Validate Order
      $this->module->validateOrder(
        $cart->id,
        Configuration::get('PS_OS_PREPARATION'),
        $total,
        $this->module->displayName,
        NULL,
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
              '&key='.$customer->secure_key;

      Tools::redirect($url);
    }
  }
