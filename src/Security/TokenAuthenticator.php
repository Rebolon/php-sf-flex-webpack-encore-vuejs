<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken as SymfonyCsrfToken;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var string
     */
    protected $csrfTokenParameter;

    /**
     * @var string
     */
    protected $csrfTokenId;

    /**
     * @var string
     */
    protected $loginUsernamePath;

    /**
     * @var string
     */
    protected $loginPasswordPath;

    /**
     * @var string
     */
    protected $providerKey;

    /**
     * @var string
     */
    protected $apiPlatformPrefix;

    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * TokenAuthenticator constructor.
     *
     * @param string $csrfTokenParameter
     * @param string $csrfTokenId
     * @param string $loginUsernamePath
     * @param string $loginPasswordPath
     * @param string $providerKey : don't know how to inject it from security.yaml
     * @param AuthenticationManagerInterface $authenticationManager : don't know how to inject this
     * @param CsrfTokenManagerInterface $csrfTokenManager : don't know how to inject this
     */
    public function __construct(string $csrfTokenParameter, string $csrfTokenId, string $loginUsernamePath, string $loginPasswordPath, string $providerKey, string $apiPlatformPrefix, AuthenticationManagerInterface $authenticationManager, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenParameter = $csrfTokenParameter;
        $this->csrfTokenId = $csrfTokenId;
        $this->loginUsernamePath = $loginUsernamePath;
        $this->loginPasswordPath = $loginPasswordPath;
        $this->providerKey = $providerKey;
        $this->apiPlatformPrefix = $apiPlatformPrefix;
        $this->authenticationManager = $authenticationManager;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return strtolower($request->getMethod()) !== 'get'
            && $this->csrfTokenParameter &&
            !(false === strpos($request->getRequestFormat(), 'json')
            && false === strpos($request->getContentType(), 'json'));
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        $content = json_decode($request->getContent());
        if (is_null($content)) {
            $content = [];
        }

        $json = new \ArrayObject($content, \ArrayObject::STD_PROP_LIST);

        if (!isset($json[$this->csrfTokenParameter])) {
            throw new AuthenticationException($this->csrfTokenParameter . ' mandatory', 420);
        }

        if (!isset($json[$this->loginUsernamePath])) {
            throw new \HttpInvalidParamException($this->loginUsernamePath . ' mandatory', 420);
        }

        if (!isset($json[$this->loginPasswordPath])) {
            throw new \HttpInvalidParamException($this->loginPasswordPath . ' mandatory', 420);
        }

        return array(
            'token' => $json[$this->csrfTokenParameter],
            'username' => $json[$this->loginUsernamePath],
            'password' => $json[$this->loginPasswordPath]
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (false === $this->csrfTokenManager->isTokenValid(new SymfonyCsrfToken($this->csrfTokenId, $credentials['token']))) {
            // i would prefer to throw an InvalidCsrfTokenException but i don't know how it would be catched and transformed into HTTPExceptio
            throw new AuthenticationException('Invalid CSRF token.', 423);
        }

        try {
            return $userProvider->loadUserByUsername($credentials['username']);
        } catch (AuthenticationException $e) {
            // i don't want the user to be able to get message 'Username could not be found.'
            throw new AuthenticationException('Forbidden');
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $token = new UsernamePasswordToken($credentials['username'], $credentials['password'], $this->providerKey);

        $this->authenticationManager->authenticate($token);

        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = $exception->getMessage() ? $exception->getMessage() : strtr($exception->getMessageKey(), $exception->getMessageData());
        $code = $exception->getCode() ? $exception->getCode() : Response::HTTP_FORBIDDEN;

        $data = array(
            'error' => $message,
            'code' => $code,

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'error' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
