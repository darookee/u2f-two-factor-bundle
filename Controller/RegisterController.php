<?php

namespace R\U2FTwoFactorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use R\U2FTwoFactorBundle\Event\RegisterEvent;

/**
 * Class RegisterController
 * @author Nils Uliczka
 */
class RegisterController extends Controller
{
    /**
     * u2fAction
     * @param Request $request
     * @return void
     **/
    public function u2fAction(Request $request)
    {
        $u2fAuthenticator = $this->get('r_u2f_two_factor.authenticator');
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
