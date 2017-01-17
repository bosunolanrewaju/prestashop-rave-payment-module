<?php

  /**
   * Display Payment Return Controller
   */

  class RavePaymentGatewayDisplayPaymentReturnController
  {
    public function __construct($module, $file, $path)
    {

      $this->file = $file;
      $this->module = $module;
      $this->context = Context::getContext();
      $this->_path = $path;

    }

    public function run ($params)
    {
      $this->context->smarty->assign(array(
        'reference' => Tools::getValue('txref'),
        'status' => $params['objOrder']->current_state == Configuration::get('PS_OS_PAYMENT') ? 'successful' : 'failed',
      ));
      $this->context->controller->addCSS($this->_path.'views/css/ravepaymentgateway.css', 'all');
      return $this->module->display($this->file, 'displayPaymentReturn.tpl');
    }
  }
