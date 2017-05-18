<?php

  /**
   * Display Payment Controller
   */

  class RavePaymentGatewayDisplayPaymentController
  {
    public function __construct($module, $file, $path)
    {

      $this->file = $file;
      $this->module = $module;
      $this->context = Context::getContext();
      $this->_path = $path;
      $this->context->cookie->base_url = 'http://flw-pms-dev.eu-west-1.elasticbeanstalk.com';

    }

    public function run ()
    {
      $go_live = Configuration::get('RAVE_GO_LIVE');
      $btn_text = Configuration::get('RAVE_PAY_BUTTON_TEXT');

      if ( $go_live ) {
        $this->context->cookie->base_url = 'https://api.ravepay.co';
      }

      $currency_order = new Currency($this->context->cart->id_currency);
      $country = new Country((int)$address_billing->id_country);
      $customer = new Customer($this->context->cart->id_customer);
      $this->context->smarty->assign(array(
        'pb_key'  => Configuration::get('RAVE_PB_KEY'),
        'title'   => Configuration::get('RAVE_MODAL_TITLE'),
        'desc'    => Configuration::get('RAVE_MODAL_DESC'),
        'logo'    => Configuration::get('RAVE_MODAL_LOGO'),
        'btntext' => $btntext ? $btntext : 'PAY NOW',
        'currency'=> $currency_order->iso_code,
        'country' => $country->iso_code,
        'txref'   => "PS_" . $this->context->cart->id . '_' . time(),
        'amount'  => (float)$this->context->cart->getOrderTotal(true, Cart::BOTH),
        'customer_email' => $customer->email,
      ));
      $this->context->controller->addCSS($this->_path.'views/css/ravepaymentgateway.css', 'all');
      $this->context->controller->addJS($this->context->cookie->base_url . '/flwv3-pug/getpaidx/api/flwpbf-inline.js');
      $this->context->controller->addJS($this->_path.'views/js/rave.js');
      return $this->module->display($this->file, 'displayPayment.tpl');
    }
  }
