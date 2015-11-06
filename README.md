# r/u2f-two-factor-bundle

This Symfony2 bundle provides u2f authentication for your website using
[scheb/two-factor-bundle](https://github.com/scheb/two-factor-bundle).

## Installation

### Step 1: Download using Composer

```shell
php composer.phar require r/u2f-two-factor-bundle
```

### Step 2: Enable the bundles

Add this to you `app/AppKernel.php`:

```php
<?php

// ...
public function registerBundles()
{
    $bundles = array(
        // ...
        new Scheb\TwoFactorBundle\SchebTwoFactorBundle(),
        new R\U2FTwoFactorBundle\RU2FTwoFactorBundle(),
        // ...
    );
    // ...
}
// ...
```

### Step 3: Configure

These options are available but not required:

```yaml
r_u2f_two_factor:
    formTemplate: RU2FTwoFActorBundle:Authentication:form.html.twig
    registerTemplate: RU2FTwoFActorBundle:Registration:register.html.twig
    authCodeParameter: _auth_code
```

For the Authentication to work you User has to implement `R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface`

```php
<?php

// ...
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface as U2FTwoFactorInterface;
// ...
class User implements U2FTwoFactorInterface
{
// ...
    /**
     * @ORM\OneToMany(targetEntity="Club\BaseBundle\Entity\U2FKey", mappedBy="user")
     * @var ArrayCollection
     **/
    protected $u2fKeys;

    /**
     * isU2FAuthEnabled
     * @return boolean
     **/
    public function isU2FAuthEnabled()
    {
        // If the User has Keys associated, use U2F
        // You may use a different logic here
        return count($this->u2fKeys) > 0;
    }

    /**
     * getU2FKeys
     * @return ArrayCollection
     **/
    public function getU2FKeys()
    {
        return $this->u2fKeys;
    }

    /**
     * addU2FKey
     * @param U2FKey $key
     * @return void
     **/
    public function addU2FKey($key)
    {
        $this->u2fKeys->add($key);
    }

    /**
     * __construct
     * @return void
     **/
    public function __construct()
    {
        // ...
        $this->u2fKeys = new ArrayCollection();
        // ...
    }
}
```

For the Registration you also need an entity that implements
`R\U2FTwoFactorBundle\Model\U2F\TwoFactorKeyInterface`.
Here is an example using doctrine.

```php
<?php
// ...
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorKeyInterface;

/**
 * Class U2FKey
 * @ORM\Entity
 * @ORM\Table(name="u2f_keys",
 * uniqueConstraints={@ORM\UniqueConstraint(name="user_unique",columns={"user_id",
 * "key_handle"})})
 */
class U2FKey implements TwoFactorKeyInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     **/
    public $keyHandle;

    /**
     * @ORM\Column(type="string")
     * @var string
     **/
    public $publicKey;

    /**
     * @ORM\Column(type="string")
     * @var string
     **/
    public $certificate;

    /**
     * @ORM\Column(type="string")
     * @var int
     **/
    public $counter;

    /**
     * @ORM\ManyToOne(targetEntity="AcmeBundle\Entity\User", inversedBy="u2fKeys")
     * @var User
     **/
    protected $user;

    /**
     * @ORM\Column(type="string")
     * @var string
     **/
    protected $name;

// ...
}
```

Then you need to create an eventlistener to get and store the data of the
registered key.

```php
<?php

use AcmeBundle\Entity\U2FKey;
use R\U2FTwoFactorBundle\Event\RegisterEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class U2FRegistrationListener implements EventSubscriberInterface
{
    // ..
    /**
     * getSubscribedEvents
     * @return array
     **/
    public static function getSubscribedEvents()
    {
        return array(
            'r_u2f_two_factor.register' => 'onRegister',
        );
    }

    /**
     * onRegister
     * @param RegisterEvent $event
     * @return void
     **/
    public function onRegister(RegisterEvent $event)
    {
        $user = $event->getUser($event);
        $registrationData = $event->getRegistration();
        $newKey = new U2FKey();
        $newKey->fromRegistrationData($registrationData);
        $newKey->setUser($user);
        $newKey->setName($event->getKeyName());

        // persist the new key

        // generate new response, here we redirect the user to the fos user
        // profile
        $response = new RedirectResponse($this->router->generate('fos_user_profile_show'));
        $event->setResponse($response);
    }
}
```

Add it to your `services.yml`:

```yaml
acme.u2f_listener:
    class: AcmeBundle\EventListener\U2FRegistrationListener
    tags:
        - { name: kernel.event_subscriber }
```

Also add routing definitions to your `app/config/routing.yml`

```yaml
r_u2f:
    resource: "@RU2FTwoFactorBundle/Resources/config/routing.yml"
    prefix: /
```

The Keys can be registered visiting `/u2f_register`. It needs to be served as
https!

## License

This bundle is available under the [MIT license](LICENSE).
