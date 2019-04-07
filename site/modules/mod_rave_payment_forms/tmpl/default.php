<?php
  /**
   * @copyright Copyright © 2017 - All rights reserved.
   * @license   GNU General Public License v2.0
   */

  defined('_JEXEC') or die;
  $form_id = bin2hex( openssl_random_pseudo_bytes( 2 ) );
  $btntext = $params->get('button_text', 'PAY NOW');
  if ( ! $args['pbkey'] ) return;
?>

<div>
  <form id="<?php echo $form_id ?>" class="flw-simple-pay-now-form" <?php echo $data_attr; ?>>
    <div id="notice"></div>

      <?php if ( empty($args['email']) ) : ?>

        <label class="pay-now">Email</label>
        <input class="flw-form-input-text" id="flw-customer-email" type="email" placeholder="Email" required /><br>

      <?php endif; ?>
      <?php if ( empty($args['amount']) ) : ?>

        <label class="pay-now">Amount</label>
        <input class="flw-form-input-text" id="flw-amount" type="text" placeholder="Amount" required /><br>

      <?php endif; ?>
      <?php if ( empty($args['currency']) ) : ?>

      <label class="pay-now">Select Currency</label>
        <select class="flw-form-input-select" id="flw-currency" required>
          <option value="NGN">NGN</option>
          <option value="KES">KES</option>
          <option value="GHS">GHS</option>
          <option value="TZS">TZS</option>
          <option value="UGX">UGX</option>
          <option value="ZMW">ZMW</option>
          <option value="SLL">SLL</option>
          <option value="USD">USD</option>
          <option value="GBP">GBP</option>
          <option value="ZAR">ZAR</option>
        
        </select><br>

      <?php endif; ?>
      <?php if ( empty($args['paymentplan']) ) : ?>

        <label class="pay-now">Select Subscription plan</label>
        <select class="flw-form-input-select" id="flw-paymentplan" required >
          <?php if ( ($args['weeklyplan']) ) : ?>
            <option id="flw-weekly-plan" value="<?php echo $args['weeklyplan'] ?>">Weekly</option>
          <?php endif; ?>
          <?php if ( ($args['monthlyplan']) ) : ?>
            <option id="flw-monthly-plan" value="<?php echo $args['monthlyplan'] ?>">Monthly</option>
          <?php endif; ?>
          <?php if ( ($args['quaterlyplan']) ) : ?>
            <option id="flw-quarterly-plan" value="<?php echo $args['quaterlyplan'] ?>">Quarterly</option>
          <?php endif; ?>
          <?php if ( ($args['annualplan']) ) : ?>
            <option id="flw-annual-plan" value="<?php echo $args['annualplan'] ?>">Annually</option>
          <?php endif; ?>
        </select><br>

      <?php endif; ?>

    <button value="submit" class='flw-pay-now-button' href='#'><?php echo $btntext; ?></button>
  </form>
</div>
