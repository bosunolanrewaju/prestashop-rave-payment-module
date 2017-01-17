{if $status == 'successful'}
  <p>{l s='Your order on %s is complete.' sprintf=$shop_name mod='ravepaymentgateway'}
    <h2>{l s='Thank you for your order' mod='ravepaymentgateway'}</h2>
    <h3>{l s='Reference number #%s' sprintf=$reference mod='ravepaymentgateway'}</h3>
    <br />
    {l s='A confirmation email has been sent to you with the order information.' mod='ravepaymentgateway'}
    <br />
    <br />
    <strong>{l s='Your order is being processed for shipment.' mod='ravepaymentgateway'}</strong>
    <br />
    <br />
    {l s='For any questions or for further information, please contact our' mod='ravepaymentgateway'}
    <a href="{$link->getPageLink('contact', true)|escape:'html'}">
      {l s='customer service department.' mod='ravepaymentgateway'}
    </a>.
  </p>
{else}
  <p class="warning">
    <h3>{l s='Order Reference number #%s' sprintf=$reference mod='ravepaymentgateway'}</h3>
    {l s='We have noticed that there is a problem with your order.", mod='ravepaymentgateway'}
    {l s="If you think this is an error, you can contact our' mod='ravepaymentgateway'}
    <a href="{$link->getPageLink('contact', true)|escape:'html'}">
      {l s='customer service department.' mod='ravepaymentgateway'}
    </a>.
  </p>
{/if}
<br />
<hr />
