var $ = jQuery;

(function($) {
  var $context = $('.payment_method_4all');

  function createExpirationMask_4all(string) {
    return string.replace(/\D/g, '').replace(/(\d{2})(\d)/, '$1/$2').replace(/(\d{2})(\d)/, '$1/$2').replace(/(\d{2})(\d{2})$/, '$1$2');
  }

  function destroyExpirationMask_4all(string) {
    return string.replace(/\D/g, '').substring(0, 3);
  }

  function createDocumentMask_4all(string) {
    return string.replace(/\D/g, '').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, "$1-$2");
  }

  function destroyDocumentMask_4all(string) {
    return string.replace(/\D/g, '').substring(0, 11);
  }

  function checkCardType_4all(number, $brands) {
    var ids = $("#brandsList").val();
    ids = ids.split(";");

    errorCardNumber = false;

    if (number.length === 0) {
      $brands.find('.active').removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^4/.test(number) && ids.includes("0")) { //VISA
      $brands.find('#brand-' + 0).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^(222[1-9]|22[3-9]|2[3-6]|27[0-1]|2720|5018|50[2-3]|506|5[6-8]|5|603689|639|6220|67)/.test(number) && ids.includes("1")) { //MASTERCARD
      $brands.find('#brand-' + 1).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^(36|301|305|309|3[8-9])/.test(number) && ids.includes("2")) { //DINERS
      $brands.find('#brand-' + 2).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^(40117[8-9]|431274|438935|451416|457393|45763[1-2]|504175|627780|636297|636368|506699|5067[0-6]|50677[0-8]|509|65003[1-3]|65003[5-9]|65004|65005[0-1]|65040[5-9]|6504[1-3]|65048[5-9]|65049|6505[0-2]|65053[0-8]|65054[1-9]|6505[5-8]|65059[0-8]|65070|65071[0-8]|65072[0-7]|65090[1-9]|65091|650920|65165[2-9]|6516[6-7]|6550[0-1]|65502[1-9]|6550[3-4]|65505[0-8]|65092[1-9]|6509[3-6]|65097[0-8])'/.test(number) && ids.includes("3")) { //ELO
      $brands.find('#brand-' + 3).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^(34|37)/.test(number) && ids.includes("4")) { //AMEX
      $brands.find('#brand-' + 4).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^(6011|62|64|65)/.test(number) && ids.includes("5")) { //DISCOVER
      $brands.find('#brand-' + 5).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^50/.test(number) && ids.includes("6")) { //AURA
      $brands.find('#brand-' + 6).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^35/.test(number) && ids.includes("7")) { //JCB
      $brands.find('#brand-' + 7).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else if (/^(384100|384140|384160|60)/.test(number) && ids.includes("8")) { //HIPERCARD
      $brands.find('#brand-' + 8).addClass('active').siblings().removeClass('active');
      $('[name=cardNumber]').removeClass('alert');
    } else {
      errorCardNumber = true;
    }
  }

  function validateCard_4all(){
    if (errorCardNumber) {
      $('.form-row-brands').find('.active').removeClass('active');
      $('[name=cardNumber]').addClass('alert');
    }
  }

  var expirationSelector = '[name=expirationDate]';
  var cardNumberSelector = '[name=cardNumber]';
  var buyerDocument = '[name=buyerDocument]';
  var errorCardNumber = false;

  $context.on('keypress', expirationSelector, function(event) {
    var v = destroyExpirationMask_4all(event.target.value);
    event.target.value = createExpirationMask_4all(v);
  });

  $context.on('keypress', buyerDocument, function(event) {
    var v = destroyDocumentMask_4all(event.target.value);
    event.target.value = createDocumentMask_4all(v);
  });

  $context.on('keyup', cardNumberSelector, function (event) {
    var v = event.target.value;
    checkCardType_4all(v, $('.form-row-brands'));
  });

  $context.on('focusout', cardNumberSelector, function () {
    validateCard_4all();
  });

}(jQuery));
