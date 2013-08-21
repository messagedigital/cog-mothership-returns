<?php

namespace Message\Mothership\OrderReturn\Controller\OrderReturn\Checkout;

use Message\Cog\Controller\Controller;

/**
 * Checkout controller for returns.
 */
class Checkout extends Controller
{
	public function view($orderID)
	{
		$user = $this->get('user.current');
		$order = $this->get('order.loader')->getByID($orderID);

		if ($order->user->id != $user->id) {
			throw new UnauthorizedHttpException('You are not authorised to view this page.', 'You are not authorised to
				view this page.');
		}

		$returns = $this->get('return.loader')->getByOrder($order);

		foreach ($returns as $key => $return) {
			if ($return->balance <= 0) {
				unset($returns[$key]);
			}
		}

		return $this->render('::return:checkout:single-payment-checkout', array(
			'amount' => $this->getPaymentAmount($returns),
			'returns' => $returns,
			'form' => $this->getPaymentForm($returns),
		));
	}

	public function getPaymentAmount($returns)
	{
		$balance = 0;

		foreach ($returns as $return) {
			if ($return->balance > 0) {
				$balance += $return->balance;
			}
		}

		if ($balance > 0) {
			return $balance;
		}

		return false;
	}

	public function getPaymentForm($returns)
	{
		$form = $this->get('form');

		return $form;
	}
}