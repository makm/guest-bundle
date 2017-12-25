<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 22.10.17 1:37
 */

namespace Makm\GuestBundle\Security;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Class Listener
 * @package Makm\GuestBundle\Security
 */
class Listener implements ListenerInterface
{
    const REMEMBER_GUEST_PARAM_KEY_COOKIE = 'cookie_guest_param_key';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var
     */
    private $secret;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $option;

    /**
     * Listener constructor.
     * @param TokenStorageInterface          $tokenStorage
     * @param                                $secret
     * @param LoggerInterface|null           $logger
     * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        $secret,
        LoggerInterface $logger = null,
        AuthenticationManagerInterface $authenticationManager = null,
        array $option = []
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->secret = $secret;
        $this->authenticationManager = $authenticationManager;
        $this->logger = $logger;
        $this->option = $option;
    }

    /**
     * @param $value
     * @return string
     * @internal param $secret
     */
    protected function encode(string $value): string
    {
        return base64_encode(\serialize([$value, crypt($value, $this->secret)]));
    }

    /**
     * @param $string
     * @return string|null
     */
    protected function decode($string): ?string
    {
        $data = unserialize(\base64_decode($string));

        if (is_array($data) && count($data)) {
            return ($data[1] === crypt($data[0], $this->secret)) ? $data[0]:null;
        }
        return null;
    }


    /**
     * Handles anonymous authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     * @throws \Symfony\Component\Security\Csrf\Exception\TokenNotFoundException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @throws \RuntimeException
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasSession()) {
            throw new \RuntimeException('This authentication method requires a session.');
        }

        if (null !== $this->tokenStorage->getToken()) {
            return;
        }

        if ($this->tokenStorage->getToken() instanceof GuestToken) {
            return;
        }

        try {
            //receive  last username
            if ($request->cookies->has($this->option['name'])) {
                $previousUsername = $this->decode($request->cookies->get($this->option['name']));
            }
            $username = $previousUsername ?? GuestToken::NEW_GUEST_MARKER;

            $token = new GuestToken($username, $this->secret, []);
            $token = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($token);

            //move through listener in Response
            $request->attributes->set(self::REMEMBER_GUEST_PARAM_KEY_COOKIE,
                new Cookie(
                    $this->option['name'],
                    $this->encode($token->getUsername()),
                    time() + $this->option['ttl']
                )
            );

            if (null !== $this->logger) {
                $this->logger->info('Populated the TokenStorage with an guest Token.');
            }
        } catch (AuthenticationException $failed) {
            if (null !== $this->logger) {
                $this->logger->info('Guest authentication failed.', ['exception' => $failed]);
            }
        }
    }
}