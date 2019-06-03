# r/u2f-two-factor-bundle

This Symfony2 bundle provides u2f authentication for your website using
[scheb/two-factor-bundle](https://github.com/scheb/two-factor-bundle).

## Installation

### Step 1: Download using Composer

```shell
php composer.phar require r/u2f-two-factor-bundle
```

### Step 2: Enable the bundles (skip when using Symfony Flex)

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
use Club\BaseBundle\Entity\U2FKey;
// ...
class User implements U2FTwoFactorInterface
{
// ...
    /**
     * @ORM\OneToMany(targetEntity=U2FKey::class, mappedBy="user")
     * @var Collection<TwoFactorKeyInterface>
     **/
    protected $u2fKeys;

    public function isU2FAuthEnabled(): bool
    {
        // If the User has Keys associated, use U2F
        // You may use a different logic here
        return count($this->u2fKeys) > 0;
    }

    /** @return Collection<TwoFactorKeyInterface> **/
    public function getU2FKeys(): Collection
    {
        return $this->u2fKeys;
    }

    public function addU2FKey(TwoFactorKeyInterface $key): void
    {
        $this->u2fKeys->add($key);
    }

    public function removeU2FKey(TwoFactorKeyInterface $key): void
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
use u2flib_server\Registration;

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
    protected $keyHandle;

    /**
     * @ORM\Column(type="string")
     * @var string
     **/
    protected $publicKey;

    /**
     * @ORM\Column(type="text")
     * @var string
     **/
    protected $certificate;

    /**
     * @ORM\Column(type="string")
     * @var int
     **/
    protected $counter;

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

    public function fromRegistrationData(Registration $data): void
    {
        $this->keyHandle = $data->keyHandle;
        $this->publicKey = $data->publicKey;
        $this->certificate = $data->certificate;
        $this->counter = $data->counter;
    }

    /** @inheritDoc */
    public function getKeyHandle()
    {
        return $this->keyHandle;
    }

    /** @inheritDoc */
    public function setKeyHandle($keyHandle)
    {
        $this->keyHandle = $keyHandle;
    }

    /** @inheritDoc */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /** @inheritDoc */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /** @inheritDoc */
    public function getCertificate()
    {
        return $this->certificate;
    }


    /** @inheritDoc */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;
    }

    /** @inheritDoc */
    public function getCounter()
    {
        return $this->counter;
    }

    /** @inheritDoc */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /** @inheritDoc */
    public function getName()
    {
        return $this->name;
    }

    /** @inheritDoc */
    public function setName($name)
    {
        $this->name = $name;
    }
}
```

Then you need to create an event subscriber to get and store the data of the
registered key.

```php
<?php

use AcmeBundle\Entity\U2FKey;
use R\U2FTwoFactorBundle\Event\RegisterEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class U2FRegistrationSubscriber implements EventSubscriberInterface
{
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    // ..

    /** @return string[] **/
    public static function getSubscribedEvents(): array
    {
        return array(
            'r_u2f_two_factor.register' => 'onRegister',
        );
    }

    public function onRegister(RegisterEvent $event): void
    {
        $user = $event->getUser($event);
        $registration = $event->getRegistration();
        $newKey = new U2FKey();
        $newKey->fromRegistrationData($registration);
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
