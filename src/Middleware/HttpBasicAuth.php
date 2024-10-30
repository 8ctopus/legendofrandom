<?php

declare(strict_types=1);

namespace Legend\Middleware;

use Legend\Helper;
use HttpSoft\Message\Response;
use Oct8pus\NanoRouter\RouteException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpBasicAuth
{
    protected readonly ServerRequestInterface $request;
    protected readonly string $passFile;
    protected readonly string $groupFile;
    protected readonly string $userGroup;
    protected readonly string $user;

    protected readonly array $passwords;
    protected readonly array $groups;

    protected readonly array $userGroups;

    /**
     * Constructor
     *
     * @param ServerRequestInterface $request
     * @param string                 $passFile
     * @param string                 $groupFile
     * @param string                 $userGroup - leave empty to ignore group check
     */
    public function __construct(ServerRequestInterface $request, string $passFile, string $groupFile, string $userGroup)
    {
        $this->request = $request;
        $this->passFile = $passFile;
        $this->groupFile = $groupFile;
        $this->userGroup = $userGroup;

        $this->loadPasswordFile();
        $this->loadGroupFile();
    }

    /**
     * Authenticate
     *
     * @return ?ResponseInterface
     *
     * @throws RouteException
     */
    public function run() : ?ResponseInterface
    {
        // make sure we authenticate only when https
        if ($this->request->getUri()->getScheme() !== 'https') {
            throw new RouteException('connection not https', 403);
        }

        // get login and password
        $server = $this->request->getServerParams();

        $user = $server['PHP_AUTH_USER'] ?? null;
        $pass = $server['PHP_AUTH_PW'] ?? null;

        if (!isset($user, $pass)) {
            return new Response(401, ['WWW-Authenticate' => 'Basic']);
        }

        $user = trim($user);
        $pass = trim($pass);

        // check if user exists
        if (!array_key_exists($user, $this->passwords)) {
            //throw new RouteException("no such user - {$user}", 403);
            Helper::errorLog(self::class, "no such user - {$user}", false);
            return new Response(401, ['WWW-Authenticate' => 'Basic']);
        }

        if (!$this->validatePassword($user, $pass)) {
            //throw new RouteException("invalid password - {$user}", 403);
            Helper::errorLog(self::class, "invalid password - {$user}", false);
            return new Response(401, ['WWW-Authenticate' => 'Basic']);
        }

        if (!$this->validateGroup($user)) {
            return new Response(403);
        }

        $this->user = $user;

        return null;
    }

    /**
     * Validate user group
     *
     * @param string $user
     *
     * @return bool
     *
     * @throws RouteException
     */
    protected function validateGroup(string $user) : bool
    {
        if (!array_key_exists($user, $this->groups)) {
            Helper::errorLog(self::class, "user has no groups set - {$user}", false);
            return false;
        }

        if (!isset($this->userGroups)) {
            $this->userGroups = $this->groups[$user];
        }

        // authorized if there is no group mentionned
        if ($this->userGroup === '') {
            return true;
        }

        if (!in_array($this->userGroup, $this->userGroups, true)) {
            Helper::errorLog(self::class, "user {$user} not in group {$this->userGroup}", false);
            return false;
        }

        return true;
    }

    /**
     * Check if user is authorized
     *
     * @param string $user
     * @param string $password
     *
     * @return bool
     */
    private function validatePassword(string $user, string $password) : bool
    {
        // get user salt and hash
        $saltAndHash = $this->passwords[$user];

        // split salt and hash
        $pos = strrpos($saltAndHash, '$');

        // get salt
        $salt = substr($saltAndHash, 0, $pos + 1);

        // compare passwords
        return crypt($password, $salt) === $saltAndHash;
    }

    /**
     * Load password file
     *
     * @return self
     */
    private function loadPasswordFile() : self
    {
        $handle = fopen($this->passFile, 'r');

        if ($handle === false) {
            throw new RouteException('read password file', 500);
        }

        $authorizations = [];

        while ($line = fgets($handle, 1000)) {
            // skip comments and empty lines
            if ($line[0] === '#' || $line[0] === "\n" || $line[0] === "\r") {
                continue;
            }

            // get key and value
            $authorization = explode(':', $line);

            $authorizations[$authorization[0]] = trim($authorization[1]);
        }

        fclose($handle);

        $this->passwords = $authorizations;

        return $this;
    }

    /**
     * Load group file
     *
     * @return self
     *
     * @throws RouteException
     */
    private function loadGroupFile() : self
    {
        $handle = fopen($this->groupFile, 'r');

        if ($handle === false) {
            throw new RouteException('load group file', 500);
        }

        // load groups
        $groups = [];

        while ($line = fgets($handle, 1000)) {
            // skip comments and empty lines
            if ($line[0] === '#' || $line[0] === "\n" || $line[0] === "\r") {
                continue;
            }

            // get key and value
            $group = explode(':', $line);

            $groups[trim($group[1])][] = $group[0];
        }

        $this->groups = $groups;

        return $this;
    }
}
