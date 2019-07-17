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
ru2_f_two_factor:
    formTemplate: RU2FTwoFactorBundle:Authentication:form.html.twig
    registerTemplate: RU2FTwoFactorBundle:Registration:register.html.twig
    authCodeParameter: _auth_code
```

For the Authentication to work you User has to implement `R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface`

```php
<?php

// ...
use Doctrine\Common\Collections\Collection;
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface as U2FTwoFactorInterface;
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorKeyInterface;
// ...
class User implements U2FTwoFactorInterface
{
// ...
    /**
     * @ORM\OneToMany(targetEntity="Club\BaseBundle\Entity\U2FKey", mappedBy="user")
     * @var Collection
     **/
    protected $u2fKeys;

    public function isU2FAuthEnabled(): bool
    {
        // If the User has Keys associated, use U2F
        // You may use a different logic here
        return count($this->u2fKeys) > 0;
    }

    public function getU2FKeys(): Collection
    {
        return $this->u2fKeys;
    }

    public function addU2FKey(TwoFactorKeyInterface $key)
    {
        $this->u2fKeys->add($key);
    }

    public function removeU2FKey(TwoFactorKeyInterface $key)
    {
        $this->u2fKeys->remove($key);
    }

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
 * @ORM\Entity
 * @ORM\Table(name="u2f_keys",
 * uniqueConstraints={@ORM\UniqueConstraint(name="user_unique",columns={"user_id",
 * "keyHandle"})})
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
     * @ORM\Column(type="text")
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

    public function fromRegistrationData($data)
    {
        $this->keyHandle = $data->keyHandle;
        $this->publicKey = $data->publicKey;
        $this->certificate = $data->certificate;
        $this->counter = $data->counter;
    }
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

### Step 4: Include Javascript

First you need to add the dependency [u2f-api](https://www.npmjs.com/package/u2f-api) to your `package.json`.

If you're using Webpack Encore, include this line in your `webpack.config.js`:
```
    .addEntry('ru2ftwofactor', './web/bundles/ru2ftwofactor/js/auth.js')
```

Include this entry module on pages that need u2f support:
```
{{ encore_entry_script_tags('ru2ftwofactor') }}
```

## License

This bundle is available under the [MIT license](LICENSE).
