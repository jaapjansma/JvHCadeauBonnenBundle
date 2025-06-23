<?php

/**
 * Product classes
 */
\Isotope\Model\Product::registerModelType('cadeaubon', 'JvH\CadeauBonnenBundle\Model\Cadeaubon');
\Isotope\Model\Shipping::registerModelType('cadeaubon', 'JvH\CadeauBonnenBundle\Model\Shipping\Cadeaubon');

$GLOBALS['BE_MOD']['isotope']['iso_rules']['send_email'] = [\JvH\CadeauBonnenBundle\Backend\Email::class, 'sendEmail'];

$GLOBALS['FE_MOD']['isotope']['mod_jvh_cadeau_bonnen_checker'] = \JvH\CadeauBonnenBundle\Frontend\CadeabonChecker::class;

/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['calculatePrice'][] = array('JvH\CadeauBonnenBundle\Listener\CalculatePrice', 'run');
$GLOBALS['ISO_HOOKS']['addCollectionToTemplate'][] = array('JvH\CadeauBonnenBundle\Listener\UseCadaubon', 'addCollectionToTemplate');
$GLOBALS['ISO_HOOKS']['preCheckout'][] = array('JvH\CadeauBonnenBundle\Listener\UseCadaubon', 'preCheckout');
$GLOBALS['ISO_HOOKS']['postCheckout'][] = array('JvH\CadeauBonnenBundle\Listener\UseCadaubon', 'postCheckout');
$GLOBALS['ISO_HOOKS']['postOrderStatusUpdate'][] = array('JvH\CadeauBonnenBundle\Listener\GenerateCadaubon', 'PostOrderStatusUpdate');

$GLOBALS['SNELSTART_HOOKS']['post_book_order'][] = ['jvh.cadeabonnen.snelstart', 'BetaaltMetCadeaubon'];
$GLOBALS['SNELSTART_HOOKS']['itemToBoekingsRegels'][] = ['jvh.cadeabonnen.snelstart', 'GekochteCadeaubonnen'];

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon'] = array
(
  // Type
  'jvh_cadeaubon_created' => array
  (
    // Field in tl_nc_language
    'recipients' => array
    (
      // Valid tokens
      'recipient_email' // The email address of the recipient
    ),
    'email_text' => array('recipient_email', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*', 'shipping_address_*', 'shipping_address'),
    'email_subject' => array('recipient_email', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*'),
    'email_html' => array('recipient_email', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*', 'shipping_address_*', 'shipping_address'),
  ),
  'jvh_cadeaubon_email' => array
  (
    // Field in tl_nc_language
    'recipients' => array
    (
      // Valid tokens
      'recipient_email' // The email address of the recipient
    ),
    'email_text' => array('recipient_email', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*', 'shipping_address_*', 'shipping_address'),
    'email_subject' => array('recipient_email', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*'),
    'email_html' => array('recipient_email', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*', 'shipping_address_*', 'shipping_address'),
  ),
);