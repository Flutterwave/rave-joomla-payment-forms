<?php
/**
 * @copyright	Copyright © 2017 - All rights reserved.
 * @license		GNU General Public License v2.0
 */
defined('_JEXEC') or die;

include_once __DIR__ . '/helper.php';

$doc        = JFactory::getDocument();
$loadJquery = $params->get('loadJquery', 1);

// Load jQuery
if ($loadJquery == '1') {
  $doc->addScript('//code.jquery.com/jquery-latest.min.js');
}

$rave_settings_params = JComponentHelper::getParams( 'com_ravepayments' );
$base_url = 'https://api.ravepay.co';
// $base_url = $rave_settings_params->get('go_live') === "1" ? 'https://api.ravepay.co' : 'https://ravesandboxapi.flutterwave.com';

// Include assets
$doc->addScript($base_url . "/flwv3-pug/getpaidx/api/flwpbf-inline.js");
$doc->addScript(JURI::root() . "modules/mod_rave_payment_forms/assets/js/flw.js", 'text/javascript', true);


if ( $rave_settings_params->get('disable_style') === "0" ) {
  $doc->addStyleSheet(JURI::root()."modules/mod_rave_payment_forms/assets/css/style.css");
}

$args = array(
  'amount'   => $params->get('amount', ''),
  // 'country'  => $rave_settings_params->get('country', "NG"),
  'currency' => $rave_settings_params->get('currency', ''),
  'desc'   => $rave_settings_params->get('modal_desc', ''),
  'module' => $module->title,
  // 'pbkey'  => $rave_settings_params->get('public_key', false),
  'pbkey' => $rave_settings_params->get('go_live') === "1" ? $rave_settings_params->get('live_public_key', '') : $rave_settings_params->get('test_public_key', ''),
  'title'  => $rave_settings_params->get('modal_title', ""),
  'weeklyplan'  => $rave_settings_params->get('weekly_plan', ''),
  'monthlyplan'  => $rave_settings_params->get('monthly_plan', ''),
  'quaterlyplan'  => $rave_settings_params->get('quarterly_plan', ''),
  'annualplan'  => $rave_settings_params->get('annually_plan', '')
);

$email = '';
$logo  = $rave_settings_params->get('modal_logo', "");
$recurring = 'yes';

$use_user_email = $params->get('use_user_email');
if ($use_user_email === '1') {
  $user = JFactory::getUser();
  if (!$user->guest) {
    $email = $user->email;
  }
}


$enable_recurring = $params->get('enable_recurring');
if ($enable_recurring === '1') {
  $recurring = '';
} 

if ( ! empty($logo) ) {
  $logo = JURI::root() . $logo;
}

$args['email'] = $email;
$args['logo']  = $logo;
$args['paymentplan'] = $recurring;

$data_attr = '';
foreach ($args as $att_key => $att_value) {
  $data_attr .= ' data-' . $att_key . '="' . $att_value . '"';
}

require JModuleHelper::getLayoutPath('mod_rave_payment_forms', $params->get('layout', 'default'));
