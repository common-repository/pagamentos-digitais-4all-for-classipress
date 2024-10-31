<?php 
  $paymentMethods = $gateway_4all->getPaymentMethods_4all();
  $nonePaymentMethods = false; //variavel para o caso do merchant ainda nao ter nenhuma affiliation cadastrada

  if ($paymentMethods) {
    $brandsList = [];

    //a ordem das imagens esta de acordo com os id's retornados do gateway correspondendo a imagem
    $brands = [
      "https://4all.com/brands/visa.png", 
      "https://4all.com/brands/mastercard.png",
      "https://4all.com/brands/diners.png", 
      "https://4all.com/brands/elo.png", 
      "https://4all.com/brands/amex.png", 
      "https://4all.com/brands/discover.png", 
      "https://4all.com/brands/aura.png", 
      "https://4all.com/brands/jcb.png", 
      "https://4all.com/brands/hipercard.png"
    ];

    for ($i=0; $i < sizeof($paymentMethods["brands"]); $i++) { 
      if ($paymentMethods["brands"][$i]["brandId"] != null) {
        //o -1 é necessario, pois o gateway retorna os id's de 1 para cima
        array_push($brandsList, $paymentMethods["brands"][$i]["brandId"] -1);
      }
    }

    $brandsListString = implode(";", $brandsList);
  } else {
    $nonePaymentMethods = true;
  }
?>

<?php 
  if ($transactionError) {
?>
<div class="error-box">
  <p class="error-title"><?= __('Transaction failed:', 'digital-payment-4all') ?></p>
  <p><?= $fieldError ? $fieldError : __('Sorry, something goes wrong with your transaction. Please, try again.', 'digital-payment-4all') ?></p>
</div>
<?php
    $transactionError = false;
  }
?>
<form class="payment_method_4all" id="order_review" method="post" action="<?= $formUrl; ?>">
  <p class="form-row">
    <label><?=__('Name of the card holder (same as the card)', 'digital-payment-4all'); ?></label>
    <input type="text" name="cardholderName" maxlength="200" required>
  </p>
  <p class="form-row">
    <label><?=__('CPF of the bearer', 'digital-payment-4all'); ?></label>
    <input type="text" name="buyerDocument" maxlength="14" required>
  </p>
  <p class="form-row">
    <label><?=__('Card number', 'digital-payment-4all' ); ?></label>
    <input type="text" name="cardNumber" maxlength="19" <?php if ($nonePaymentMethods) { echo 'class="disabled" disabled'; } ?> required>
  </p>
  <input type="hidden" id="brandsList" value="<?= $brandsListString; ?>">
  <div class='form-row-brands'>
    <?php 
      if (!$nonePaymentMethods) {
        for ($i=0; $i < sizeof($brandsList); $i++) { 
          echo '<img src="' . $brands[$brandsList[$i]] . '" id="brand-' . $brandsList[$i] . '" class="">';
        }
      } else {
        echo '<p>'.__('There are no registered payment methods.', 'digital-payment-4all' ).'</p>';
      }
    ?>
  </div>
  <p class="form-row">
    <label><?=__('Expiration date', 'digital-payment-4all' ); ?></label>
    <input type="text" placeholder="MM/YY" name="expirationDate" required>
  </p>
  <p class="form-row">
    <label><?=__('Security code', 'digital-payment-4all' ); ?></label>
    <input type="text" name="securityCode" maxlength="4" required>
  </p>
  <p class="form-row form-row-installment">
    <label><?=__('Installment', 'digital-payment-4all' ); ?></label>
    <select name="installment">
    <?php
      $minInstallment = $paymentMethods['resume']['minInstallments'];
      $maxInstallments = $paymentMethods['resume']['maxInstallments'];
      $total = $order->get_total();

      for (;$minInstallment<=$maxInstallments;$minInstallment++) {
        $value = number_format($total / $minInstallment, 2, ',', '.');
        $phrase = $minInstallment . __('x of R$', 'digital-payment-4all') . $value ;

        echo '<option value="'.$minInstallment.'">'.$phrase.'</option>';
      }
    ?>
    </select>
    <p class="sub-description">
      <?=__('&#9679; Approximated values', 'digital-payment-4all'); ?>
    </p>
  </p>
  <div class="total-box">
      <p class="total-value">
        Total: R$<?= $total; ?>
      </p>
  </div>
  <div class="buttons-box">
    <input class="custom-button pay-button" type="submit" value="<?= __('Pay', 'digital-payment-4all')?>" name="completeTransaction">
  </div>
</form>
