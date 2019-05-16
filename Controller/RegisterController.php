<?php

namespace R\U2FTwoFactorBundle\Controller;

use R\U2FTwoFactorBundle\Event\RegisterEvent;
use R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F\U2FAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @author Nils Uliczka
 */
class RegisterController extends AbstractController
{
    /** @var U2FAuthenticator */
    private $u2fAuthenticator;
    /** @var SessionInterface */
    private $session;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var string */
    private $registerTemplate;

    public function __construct(
        U2FAuthenticator $u2fAuthenticator,
        SessionInterface $session,
        EventDispatcherInterface $eventDispatcher,
        string $registerTemplate
    ) {
        $this->u2fAuthenticator = $u2fAuthenticator;
        $this->session = $session;
        $this->eventDispatcher = $eventDispatcher;
        $this->registerTemplate = $registerTemplate;
    }

    public function u2fAction(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $registerData = json_decode($request->get('_auth_code'));
            $registrationRequest = json_decode($this->session->get('u2f_registrationRequest'));
            $registration = $this->u2fAuthenticator->doRegistration($registrationRequest[0], $registerData);

            $event = new RegisterEvent($registration, $this->getUser(), $request->get('keyName'));
            $this->eventDispatcher->dispatch('r_u2f_two_factor.register', $event);

            return $event->getResponse();
        }

        $registrationRequest = $this->u2fAuthenticator->generateRegistrationRequest($this->getUser());
        $this->session->set('u2f_registrationRequest', json_encode($registrationRequest));

        return $this->render(
            $this->registerTemplate,
            array(
                'registrationRequest' => json_encode($registrationRequest, JSON_UNESCAPED_SLASHES),
            )
        );
    }
}
