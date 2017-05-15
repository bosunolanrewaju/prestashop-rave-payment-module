<div class="row">
  <div class="col-xs-12">
    <p class="payment_module ">
      <span class="rave-payment-gateway">
        {l s='Pay with Rave' mod='ravepaymentgateway'}
        <button class="rave-pay-now-button pull-right">{$btntext}</button>
      </span>
      <script type="text/javascript">
        $('.rave-pay-now-button').click(function() {
          var config = {
            amount : "{$amount}",
            custom_description: "{$desc}",
            custom_logo   : "{$logo}",
            custom_title  : "{$title}",
            PBFPubKey : "{$pb_key}",
            currency  : "{$currency}",
            country   : "{$country}",
            txref     : "{$txref}",
            customer_email: "{$customer_email}",
            cbUrl : "{$link->getModuleLink('ravepaymentgateway', 'validation', [], true)}",
          };

          processPayment(config);
        });
      </script>
    </p>
  </div>
</div>
