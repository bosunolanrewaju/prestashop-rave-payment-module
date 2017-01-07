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
      $this->setTemplate('payment.tpl');
      $this->context->smarty->assign(array(
        'nb_products' => $this->context->cart->nbProducts(),
        'cart_currency' => $this->context->cart->id_currency,
        'currencies' => $this->module->getCurrency((int)$this->context->cart->id_currency),
        'total_amount'=> $this->context->cart->getOrderTotal(true, Cart::BOTH),
        'path' => $this->module->getPathUri(),
      ));
    }
  }
