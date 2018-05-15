<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class JwtTokenTools
{
    /**
     * @param InMemoryUserProvider $provider
     * @param JWTEncoderInterface $encoder
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param string $tokenJwtTtl
     * @param $username
     * @param $password
     * @param LoggerInterface|null $logger
     * @return string
     * @throws \Exception
     */
    public function encodeToken(
        InMemoryUserProvider $provider,
        JWTEncoderInterface $encoder,
        UserPasswordEncoderInterface $passwordEncoder,
        string $tokenJwtTtl,
        $username,
        $password,
        LoggerInterface $logger = null): string
    {
        try {
            $user = $provider->loadUserByUsername($username);

            if (!isset($user)) {
                throw new NotFoundHttpException();
            }

            $isValid = $passwordEncoder
                ->isPasswordValid($user, $password);

            if (!$isValid) {
                throw new BadCredentialsException();
            }

            return $encoder->encode([
                'username' => $username,
                'exp' => time() * $tokenJwtTtl
            ]);
        } catch (UsernameNotFoundException $e) {
            if ($logger) {
                $logger->alert(sprintf('Exception: UsernameNotFoundException: %s', $e->getMessage()));
            }

            throw $e;
        } catch (BadCredentialsException $e) {
            if ($logger) {
                $logger->alert(sprintf('Exception: BadCredentialsException: %s', $e->getMessage()));
            }

            throw $e;
        } catch (\Exception $e) {
            if ($logger) {
                $logger->alert(sprintf('Exception: \Exception: %s', $e->getMessage()));
            }

            throw $e;
        }
    }
}