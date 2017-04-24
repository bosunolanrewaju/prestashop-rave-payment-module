<?php

  /**
   * Get Content Controller Class
   */

class RavePaymentGatewayGetContentController
{
  public function __construct($module, $file, $path)
  {
    $this->file = $file;
    $this->module = $module;
    $this->context = Context::getContext();
    $this->_path = $path;
  }

  public function processConfiguration()
  {
    if (Tools::isSubmit('ravepaymentgateway_form'))
    {
      Configuration::updateValue('RAVE_PB_KEY', Tools::getValue('RAVE_PB_KEY'));
      Configuration::updateValue('RAVE_SC_KEY', Tools::getValue('RAVE_SC_KEY'));
      Configuration::updateValue('RAVE_MODAL_TITLE', Tools::getValue('RAVE_MODAL_TITLE'));
      Configuration::updateValue('RAVE_MODAL_DESC', Tools::getValue('RAVE_MODAL_DESC'));
      Configuration::updateValue('RAVE_MODAL_LOGO', Tools::getValue('RAVE_MODAL_LOGO'));
      Configuration::updateValue('RAVE_PAY_BUTTON_TEXT', Tools::getValue('RAVE_PAY_BUTTON_TEXT'));
      $this->context->smarty->assign('confirmation', 'ok');
    }
  }

  public function renderForm()
  {
    $inputs = array(
      array(
        'name'  => 'RAVE_PB_KEY',
        'label' => $this->module->l('Pay Button Public Key'),
        'desc'  => 'Your Pay Button public key',
        'type'  => 'text'
      ),
      array(
        'name'  => 'RAVE_SC_KEY',
        'label' => $this->module->l('Pay Button Secret Key'),
        'desc'  => 'Your Pay Button secret key',
        'type'  => 'text'
      ),
      array(
        'name'  => 'RAVE_MODAL_TITLE',
        'label' => $this->module->l('Modal Title'),
        'desc'  => '(Optional) default: FLW PAY',
        'type'  => 'text'
      ),
      array(
        'name'  => 'RAVE_MODAL_DESC',
        'label' => $this->module->l('Modal Description'),
        'desc'  => '(Optional) default: FLW PAY MODAL',
        'type'  => 'text'
      ),
      array(
        'name'  => 'RAVE_MODAL_LOGO',
        'label' => $this->module->l('Modal Logo'),
        'desc'  => "(Optional) - Full URL (with 'http') to the custom logo. default: Rave logo",
        'type'  => 'text'
      ),
      array(
        'name'  => 'RAVE_PAY_BUTTON_TEXT',
        'label' => $this->module->l('Pay Button Text'),
        'desc'  => '(Optional) default: PAY NOW',
        'type'  => 'text',
        ),
    );

    $fields_form = array(
      'form' => array(
        'legend' => array(
          'title' => $this->module->l('Rave payment gateway module configuration'),
          'icon' => 'icon-wrench'
        ),
        'input' => $inputs,
        'submit' => array('title' => $this->module->l('Save'))
      )
    );

    $helper = new HelperForm();
    $helper->table = 'mymodpayment';
    $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
    $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
    $helper->submit_action = 'ravepaymentgateway_form';
    $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->module->name.'&tab_module='.$this->module->tab.'&module_name='.$this->module->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->tpl_vars = array(
      'fields_value' => array(
        'RAVE_PB_KEY' => Tools::getValue('RAVE_PB_KEY', Configuration::get('RAVE_PB_KEY')),
        'RAVE_SC_KEY' => Tools::getValue('RAVE_SC_KEY', Configuration::get('RAVE_SC_KEY')),
        'RAVE_MODAL_TITLE' => Tools::getValue('RAVE_MODAL_TITLE', Configuration::get('RAVE_MODAL_TITLE')),
        'RAVE_MODAL_DESC' => Tools::getValue('RAVE_MODAL_DESC', Configuration::get('RAVE_MODAL_DESC')),
        'RAVE_MODAL_LOGO' => Tools::getValue('RAVE_MODAL_LOGO', Configuration::get('RAVE_MODAL_LOGO')),
        'RAVE_PAY_BUTTON_TEXT' => Tools::getValue('RAVE_PAY_BUTTON_TEXT', Configuration::get('RAVE_PAY_BUTTON_TEXT')),
      ),
      'languages' => $this->context->controller->getLanguages()
    );

    return $helper->generateForm(array($fields_form));
  }

  public function run()
  {
    $this->processConfiguration();
    $html_confirmation_message = $this->module->display($this->file, 'getContent.tpl');
    $html_form = $this->renderForm();
    return $html_confirmation_message.$html_form;
  }
}
