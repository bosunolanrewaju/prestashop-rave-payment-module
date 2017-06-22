<?php

  /**
   * Front Payment Controller
   */

  class RavePaymentGatewayPaymentModuleFrontController extends ModuleFrontController
  {
    public $ssl = true;

    public function initContent()
    {

      $this->display_column_left = false;
      $this->display_column_right = false;
      parent::initContent();

      if (!$this->checkCurrency())
        Tools::redirect('index.php?controller=order');

      $this->context->smarty->assign(array(
        'nb_products' => $this->context->cart->nbProducts(),
        'cart_currency' => $this->context->cart->id_currency,
        'currencies' => $this->module->getCurrency((int)$this->context->cart->id_currency),
        'total_amount'=> $this->context->cart->getOrderTotal(true, Cart::BOTH),
        'path' => $this->module->getPathUri(),
      ));

      $this->setTemplate('payment.tpl');
    }

    private function checkCurrency()
    {

      $currency_order = new Currency($this->context->cart->id_currency);
      $currencies_modules = $this->module->getCurrency($this->context->cart->id_currency);

      if (is_array($currencies_modules)) {
        foreach ($currencies_modules as $currency_module) {
          if ($currency_order->id == $currency_module['id_currency']) {
            return true;
          }
        }

        return false;
      }
    }
  }
