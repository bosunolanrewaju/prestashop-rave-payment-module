'use strict';
var cbUrl;
var response;
var processPayment = function(config) {
  cbUrl = config.cbUrl;
  var opts = Object.assign({}, config, otherOptions());

  getpaidSetup(opts);
};

var otherOptions = function() {
  return {
    onclose: function() {
      redirectTo(cbUrl);
    },
    callback: function(res) {
      response = res.tx;
      setTimeout( redirectTo, 5000, cbUrl );
    }
  };
};

var redirectTo = function(url) {
  if (url && response) {
    var responseCode = (response.paymentType === 'account') ? response.acctvalrespcode : response.vbvrespcode;
    var txRef = response.txRef;
    var amount = response.amount;
    var currency = response.currency;
    var customerEmail = response.customer.email;
    var form = createDOMElement('form', {method: 'post', action: url});
    form.appendChild(createDOMElement('input', {name: 'status_code', value: responseCode}));
    form.appendChild(createDOMElement('input', {name: 'tx_ref', value: txRef}));
    form.appendChild(createDOMElement('input', {name: 'amount', value: amount}));
    form.appendChild(createDOMElement('input', {name: 'currency', value: currency}));
    form.appendChild(createDOMElement('input', {name: 'customer', value: customerEmail}));
    document.body.appendChild(form);
    form.submit();
  }
};

var createDOMElement = function(type, attrObj={}) {
  var element = document.createElement(type);

  if (Object.keys(attrObj).length > 0) {
    for( var attr in attrObj) {
      if (attrObj.hasOwnProperty(attr)) {
        element.setAttribute( attr, attrObj[attr] );
      }
    }
  }

  return element;
};
