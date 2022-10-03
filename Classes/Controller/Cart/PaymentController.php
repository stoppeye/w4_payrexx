<?php

namespace W4Services\W4Payrexx\Controller\Cart;

/*
 * This file is part of the package W4Services/W4Payrexx.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Cart Payment Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class PaymentController extends ActionController
{
    /**
     * Action update
     *
     * @param int $paymentId
     */
    public function updateAction($paymentId)
    {
        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        $this->payments = $this->parserUtility->parseServices('Payment', $this->pluginSettings, $this->cart);

        $payment = $this->payments[$paymentId];

        if ($payment) {
            if ($payment->isAvailable($this->cart->getGross())) {
                $this->cart->setPayment($payment);
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.cart.action.set_payment.not_available',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR,
                    true
                );
            }
        }

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        if (isset($_GET['type'])) {
            $this->view->assign('cart', $this->cart);

            $this->parseData();
            $assignArguments = [
                'shippings' => $this->shippings,
                'payments' => $this->payments,
                'specials' => $this->specials
            ];
            $this->view->assignMultiple($assignArguments);
        } else {
            $this->redirect('show', 'Cart\Cart');
        }
    }
}
