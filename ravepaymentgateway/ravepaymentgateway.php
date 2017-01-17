<?php
  /**
   * 2016 Olatunbosun Olanrewaju
   *
   * NOTICE OF LICENSE
   *
   * This source file is subject to the Academic Free License (AFL 3.0)
   * that is bundled with this package in the file LICENSE.txt.
   * It is also available through the world-wide-web at this URL:
   * http://opensource.org/licenses/afl-3.0.php
   * If you did not receive a copy of the license and are unable to
   * obtain it through the world-wide-web, please send an email
   * to license@michaeldekker.com so we can send you a copy immediately.
   *
   *  @author    Bosun Olanrewaju <bosunolanrewaju@gmail.com>
   *  @copyright 2017 Bosun Olanrewaju
   *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
   */

  if (!defined('_PS_VERSION_'))
    exit;

  /**
  * Rave Payment Gateway Class
  */
  class RavePaymentGateway extends PaymentModule
  {

    public function __construct()
    {

      $this->name = 'ravepaymentgateway';
      $this->tab  = 'payments_gateways';
      $this->version = '0.0.1';
      $this->author   = 'Bosun Olanrewaju';
      $this->bootstrap  = true;

      parent::__construct();

      $this->displayName = $this->l('Rave Payment Gateway');
      $this->description = $this->l('Accept payments on your shop with Rave');
      $this->confirmUninstall = $this->l('Are you sure you want to uninstall Rave Payment Gateway module?');

    if (!Configuration::get('RAVEPAYMENTGATEWAY_NAME'))
      $this->warning = $this->l('No name provided');

    }

    public function install()
    {

      if (!parent::install() || !$this->registerHook('displayPayment') || !$this->registerHook('displayPaymentReturn') || !Configuration::updateValue('RAVEPAYMENTGATEWAY_NAME', 'rave')) {
        return false;
      }

      if (!$this->installOrderState())
        return false;

      return true;
    }

    public function getHookController($hook_name)
    {
      require(dirname(__FILE__).'/controllers/hook/'.$hook_name.'.php');
      $controller_name = $this->name.$hook_name.'Controller';
      $controller = new $controller_name($this, __FILE__, $this->_path);
      return $controller;
    }

    public function hookDisplayPayment($params)
    {
      $controller = $this->getHookController('displayPayment');
      return $controller->run($params);
    }

    public function hookDisplayPaymentReturn($params)
    {
      $controller = $this->getHookController('displayPaymentReturn');
      return $controller->run($params);
    }

    public function getContent()
    {
      $controller = $this->getHookController('getContent');
      return $controller->run();
    }

    public function installOrderState()
    {
      if (!Configuration::get('PS_OS_RAVE_PENDING')) {
        $order_state = new OrderState();
        $order_state->send_email = false;
        $order_state->module_name = $this->name;
        $order_state->invoice = false;
        $order_state->color = '#98c3ff';
        $order_state->logable = true;
        $order_state->shipped = false;
        $order_state->unremovable = false;
        $order_state->delivery = false;
        $order_state->hidden = false;
        $order_state->paid = false;
        $order_state->deleted = false;
        $order_state->name = array(
          (int)Configuration::get('PS_LANG_DEFAULT') => pSQL($this->l('Rave Payment Gateway - Pending resolution'))
        );

        if ($order_state->add()) {
          // Save order state id in tehe config table
          Configuration::updateValue('PS_OS_RAVE_PENDING', $order_state->id);
          // Clone module logo into the order state logo dir
          copy(dirname(__FILE__).'/views/img/loading.gif', dirname(__FILE__).'/../../img/os/'.$order_state->id.'.gif');
          copy(dirname(__FILE__).'/views/img/loading.gif', dirname(__FILE__).'/../../img/tmp/order_state_mini_'.$order_state->id.'.gif');

        } else {
          return false;
        }
      }

      return true;
    }

  }
