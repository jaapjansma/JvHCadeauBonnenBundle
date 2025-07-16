<?php

/**
 * Product classes
 */
\Isotope\Model\Product::registerModelType('cadeaubon', 'JvH\CadeauBonnenBundle\Model\Cadeaubon');
\Isotope\Model\Shipping::registerModelType('cadeaubon', 'JvH\CadeauBonnenBundle\Model\Shipping\Cadeaubon');
\Isotope\Model\Document::registerModelType('cadeaubon', 'JvH\CadeauBonnenBundle\Model\Document\Cadeaubon');

$GLOBALS['BE_MOD']['isotope']['iso_rules']['send_email'] = [\JvH\CadeauBonnenBundle\Backend\Email::class, 'sendEmail'];
$GLOBALS['BE_MOD']['isotope']['iso_rules']['tables'][] = 'tl_iso_rule_usage';
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

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['recipients'] = ['recipient_email'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_replyTo'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_recipient_cc'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_recipient_bcc'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_text'] = ['recipient_email', 'code', 'pin', 'discount', 'startDate', 'endDate', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*', 'billing_address_*', 'shipping_address_*', 'shipping_address', 'document'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_subject'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_html'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['file_name'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['file_content'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created']['attachment_tokens'] = ['document'];

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['recipients'] = ['recipient_email'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_replyTo'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_recipient_cc'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_recipient_bcc'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_text'] = ['recipient_email', 'code', 'pin', 'discount', 'startDate', 'endDate', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*', 'billing_address_*', 'shipping_address_*', 'shipping_address', 'document'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_subject'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_html'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['file_name'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['file_content'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_created_per_post']['attachment_tokens'] = ['document'];

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['recipients'] = ['recipient_email'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_replyTo'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_recipient_cc'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_recipient_bcc'] = &$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['recipients'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_text'] = ['recipient_email', 'code', 'pin', 'discount', 'startDate', 'endDate', 'member_*', 'product_*', 'rule_*', 'order_*', 'form_*', 'shipping_address_*', 'shipping_address', 'document'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_subject'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_html'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['file_name'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['file_content'] = $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['email_text'];
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['jvh_cadeaubon']['jvh_cadeaubon_email']['attachment_tokens'] = ['document'];