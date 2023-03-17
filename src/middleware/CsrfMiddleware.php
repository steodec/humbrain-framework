<?php

namespace Framework\Middleware;

use ArrayAccess;
use Humbrain\Framework\middleware\MiddlewareInterface;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfMiddleware implements MiddlewareInterface
{

    /**
     * @var string
     */
    private string $formKey;

    /**
     * @var string
     */
    private string $sessionKey;

    /**
     * @var int
     */
    private int $limit;

    /**
     * @var ArrayAccess
     */
    private ArrayAccess $session;

    public function __construct(
        ArrayAccess &$session,
        int $limit = 50,
        string $formKey = '_csrf',
        string $sessionKey = 'csrf'
    ) {
        $this->validSession($session);
        $this->session = &$session;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->limit = $limit;
    }

    private function validSession(ArrayAccess $session): void
    {
        if (!is_array($session)) {
            throw new \TypeError('La session passé au middleware CSRF n\'est pas traitable comme un tableau');
        }
    }

    /**
     * @throws Exception
     */
    final public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                    return $next($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            return $next($request);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function reject(): void
    {
        throw new Exception();
    }

    /**
     * @param string $token
     * @return void
     */
    private function useToken(string $token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * @throws Exception
     */
    final public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }

    private function limitTokens(): void
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * @return string
     */
    final public function getFormKey(): string
    {
        return $this->formKey;
    }
}
