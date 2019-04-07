<?php
/**
 * @copyright Copyright © 2017 - All rights reserved.
 * @license   GNU General Public License v2.0
 * @generator http://xdsoft/joomla-module-generator/
 */
defined('_JEXEC') or die;

class modRavePaymentFormsHelper
{

  public static function getAjax()
  {
    $input = JFactory::getApplication()->input;
    $data  = $input->get('data', '', 'raw');
    $response = json_decode($data);

    return self::saveTransactionInfo($response);
  }

  public static function saveTransactionInfo($txData)
  {
    // $sec_key = self::getParams('secret_key');
    $live_sec_key = self::getParams('live_secret_key');
    $test_sec_key = self::getParams('test_secret_key');
  
    $sec_key = self::getParams('go_live') === "1" ? $live_sec_key : $test_sec_key;
    $txn = json_decode( self::fetchTransaction($txData->flwRef, $sec_key) );
    $is_successful = !empty($txn->data) && self::isSuccessful($txn->data);
    $redirect_key = $is_successful ? 'success_url' : 'failed_url';
    $redirect_url = self::getParams($redirect_key);

    $tx = new stdClass();
    $tx->tx_ref = $txData->txRef;
    $tx->customer_email = $txData->customer->email;
    $tx->amount = $txData->amount;
    $tx->status = $is_successful ? 'successful' : 'failed';
    $tx->module = JFactory::getApplication()->input->get('title', null, 'raw');
    $tx->date = JFactory::getDate()->format('Y-m-d H:i:s');

    // Insert the object into the user profile table.
    $result = ($tx->tx_ref) ? JFactory::getDbo()->insertObject('#__ravepayments_records', $tx) : null;
    $resData = array('result' => $result, 'status' => $tx->status, 'redirect_url' => $redirect_url,);
    return $resData;
  }

  public static function getParams($key)
  {
    $rave_settings_params = JComponentHelper::getParams( 'com_ravepayments' );
    return $rave_settings_params->get($key, '');
  }

  public static function fetchTransaction($flwRef, $secretKey)
  {
    $base_url = self::getParams('go_live') === "1" ? 'https://api.ravepay.co' : 'http://flw-pms-dev.eu-west-1.elasticbeanstalk.com';

    $URL = $base_url . "/flwv3-pug/getpaidx/api/verify";
    $data = http_build_query(array(
      'flw_ref' => $flwRef,
      'SECKEY' => $secretKey
    ));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($ch);
    $failed = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    return ($failed) ? $error : $output;
  }

  public static function isSuccessful($data) {
    return $data->flwMeta->chargeResponse === '00' || $data->flwMeta->chargeResponse === '0';
  }

}
