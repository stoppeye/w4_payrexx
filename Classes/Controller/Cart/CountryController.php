<?php

namespace W4Services\W4Payrexx\Controller\Cart;

class CountryController extends ActionController
{
    /**
     *
     */
    public function updateAction()
    {
        //ToDo check country is allowed by TypoScript

        $this->cartUtility->updateCountry($this->settings['cart'], $this->pluginSettings, $this->request);

        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        $taxClasses = $this->parserUtility->parseTaxClasses($this->pluginSettings, $this->cart->getBillingCountry());

        $this->cart->setTaxClasses($taxClasses);
        $this->cart->reCalc();

        $this->parseData();

        $paymentId = $this->cart->getPayment()->getId();
        if ($this->payments[$paymentId]) {
            $payment = $this->payments[$paymentId];
            $this->cart->setPayment($payment);
        } else {
            foreach ($this->payments as $payment) {
                if ($payment->getIsPreset()) {
                    $this->cart->setPayment($payment);
                }
            }
        }
        $shippingId = $this->cart->getShipping()->getId();
        if ($this->shippings[$shippingId]) {
            $shipping = $this->shippings[$shippingId];
            $this->cart->setShipping($shipping);
        } else {
            foreach ($this->shippings as $shipping) {
                if ($shipping->getIsPreset()) {
                    $this->cart->setShipping($shipping);
                }
            }
        }

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        $this->cartUtility->updateService($this->cart, $this->pluginSettings);

        $this->view->assign('cart', $this->cart);

        $assignArguments = [
            'shippings' => $this->shippings,
            'payments' => $this->payments,
            'specials' => $this->specials
        ];
        $this->view->assignMultiple($assignArguments);
    }
}
