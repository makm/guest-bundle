<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 22.10.17 1:58
 */

namespace Makm\GuestBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class GuestToken extends AbstractToken
{
    const NEW_GUEST_MARKER = '#Guest#newMarker';

    /**
     * @var string
     */
    private $secret;

    /**
     * GuestToken constructor.
     * @param string|UserInterface $user
     * @param string               $secret
     * @param array                $roles
     */
    public function __construct($user, string $secret, array $roles = [])
    {
        $this->secret  = $secret;
        $this->setUser($user);
        parent::__construct($roles);

        if ($roles) {
            $this->setAuthenticated(true);
        }
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return parent::getUsername() ?? self::NEW_GUEST_MARKER;
    }


    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * Returns the secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array($this->secret, parent::serialize()));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        [$this->secret, $parentStr] = \unserialize($serialized);
        parent::unserialize($parentStr);
    }

}
