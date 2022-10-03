<?php

namespace W4Services\W4Payrexx\Controller\Cart;

/*
 * This file is part of the package W4Services/W4Payrexx.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Cart Shipping Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ShippingController extends ActionController
{
    /**
     * Action update
     *
     * @param int $shippingId
     */
    public function updateAction($shippingId)
    {
        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        $this->shippings = $this->parserUtility->parseServices('Shipping', $this->pluginSettings, $this->cart);

        $shipping = $this->shippings[$shippingId];

        if ($shipping) {
            if ($shipping->isAvailable($this->cart->getGross())) {
                $this->cart->setShipping($shipping);
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.cart.action.set_shipping.not_available',
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
