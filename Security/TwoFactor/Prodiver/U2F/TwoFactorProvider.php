<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Prodiver\U2F;

use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContext;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class TwoFactorProvider
 * @author Nils Uliczka
 */
class TwoFactorProvider implements TwoFactorProviderInterface
{

    /**
     * @var U2FAuthenticatorInterface
     **/
    protected $authenticator;

    /**
     * @var EngineInterface
     **/
    protected $templating;

    /**
     * @var string
     **/
    protected $formTemplate;

    /**
     * @var string
     **/
    protected $authCodeParameter;

    /**
     * __construct
     * @param U2FAuthenticatorInterface $authenticator
     * @param EngineInterface           $templating
     * @param string                    $formTemplate
     * @param string                    $authCodeParameter
     * @return void
     **/
    public function __construct(U2FAuthenticatorInterface $authenticator, EngineInterface $templating, $formTemplate, $authCodeParameter)
    {
        $this->authenticator = $authenticator;
        $this->templating = $templating;
        $this->formTemplate = $formTemplate;
        $this->authCodeParameter = $authCodeParameter;
    }
    /**
     * beginAuthentication
     * @param AuthenticationContext $context
     * @return boolean
     **/
    public function beginAuthentication(AuthenticationContext $context)
    {
        $user = $context->getUser();

        return ($user instanceof TwoFactorInterface && $user->isU2FAuthEnabled());
    }

    /**
     * requestAuthenticationCode
     * @param AuthenticationContext $context
     * @return \Symfony\Component\HttpFoundation\Response|null
     **/
    public function requestAuthenticationCode(AuthenticationContext $context)
    {
        $user = $context->getUser();
        $request = $context->getRequest();
        $session = $context->getSession();

        $authData = $request->get($this->authCodeParameter);
        if (null !== $authData) {
            if ($this->authenticator->checkRequest(
                $user,
                json_decode($session->get('u2f_authentication')),
                json_decode($authData)
            )) {
                $context->setAuthenticated(true);

                return new RedirectResponse($request->getUri());
            } else {
                $session->getFlashBag()->set('two_factor', 'r_u2f_two_factor.code_invalid');
            }
        }

        $authenticationData = json_encode($this->authenticator->generateRequest($user), JSON_UNESCAPED_SLASHES);
        $session->set('u2f_authentication', $authenticationData);

        return $this->templating->renderResponse($this->formTemplate, array(
            'authenticationData' => $authenticationData,
            'useTrustedOption' => $context->useTrustedOption(),
        ));
    }
}
