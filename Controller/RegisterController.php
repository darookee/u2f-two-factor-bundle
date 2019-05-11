<?php

namespace R\U2FTwoFactorBundle\Controller;

use R\U2FTwoFactorBundle\Event\RegisterEvent;
use R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F\U2FAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Nils Uliczka
 */
class RegisterController extends AbstractController
{
    public function u2fAction(Request $request) : Response
    {
        $u2fAuthenticator = $this->get(U2FAuthenticator::class);
        if ($request->isMethod('POST')) {
            $registerData = json_decode($request->get('_auth_code'));
            $registrationRequest = json_decode($this->get('session')->get('u2f_registrationRequest'));
            $registration = $u2fAuthenticator->doRegistration($registrationRequest[0], $registerData);

            $dispatcher = $this->get('event_dispatcher');
            $event = new RegisterEvent($registration, $this->getUser(), $request->get('keyName'));
            $dispatcher->dispatch('r_u2f_two_factor.register', $event);

            return $event->getResponse();
        }

        $registrationRequest = $u2fAuthenticator->generateRegistrationRequest($this->getUser());
        $this->get('session')->set('u2f_registrationRequest', json_encode($registrationRequest));

        return $this->render($this->container->getParameter('r_u2f_two_factor.registerTemplate'), array(
            'registrationRequest' => json_encode($registrationRequest, JSON_UNESCAPED_SLASHES),
        ));
    }
}
