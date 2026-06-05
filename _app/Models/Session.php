<?php

namespace App\Models;

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

use function date;
use function filter_input;
use function filter_var;
use function is_array;
use function is_int;
use function is_numeric;
use function is_scalar;
use function is_string;
use function mb_strtolower;
use function setcookie;
use function sprintf;
use function strip_tags;
use function stripos;
use function time;
use function trim;

class Session
{
    private readonly int $Cache;

    /** @var array<string,mixed> */
    private array $Session = [];

    private string $Agent = '';

    /** @var string[] */
    private array $Bots = [];

    private string $Url = '';

    public function __construct(?int $Cache = null)
    {

        $this->Cache = $Cache ?? 20;
        // USER SESSION START
        if ($this->isValidUser()) {
            $this->setSession();
        }

        // REMOVE EXPIRED SESSIONS
        $this->sessionClear();
    }

    // Controla a classe para iniciar a sessão ou atualizar, gerencia o tráfego do site!
    private function isValidUser(): bool
    {

        $this->Url = $this->sanitizeInputString(INPUT_GET, 'url');
        $Array = ['favicon', '.png', '.jpg', '.ico', '.gif', '.css', '.map'];
        foreach ($Array as $Sai) {
            if (false !== stripos($this->Url, $Sai)) {
                return false;
            }
        }

        $this->Agent = $this->sanitizeUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
        $this->Bots = [
            '008/',
            'accoona',
            'aghaven',
            'altavista',
            'arachmo',
            'aspseek',
            'b-l-i-t-z-b-o-t',
            'backtype',
            'baiduspider',
            'boitho.com-dc',
            'bot',
            'cerberian drtrs',
            'charlotte',
            'converacrawler',
            'cosmos',
            'covario',
            'crawler',
            'croccrawler',
            'dataparksearch',
            'embed.ly',
            'envolk[its]spider',
            'estyle',
            'facebookexternalhit',
            'fairshare',
            'fast enterprise crawler',
            'fast-webcrawler',
            'favicon',
            'fdse',
            'findlinks',
            'fyberspider',
            'g2crawler',
            'gnip',
            'google',
            'hl_ftien_spider',
            'holmes',
            'htdig',
            'ia_archiver',
            'iaskspider',
            'iccrawler',
            'ichiro',
            'igdespyder',
            'issuecrawler',
            'jaxified',
            'l.webis',
            'larbin',
            'ldspider',
            'linguee',
            'linkwalker',
            'lmspider',
            'lwp-trivial',
            'lycos',
            'mabontland',
            'magpie-crawler',
            'mediapartners-google',
            'megite',
            'metauri',
            'mnogosearch',
            'mogimogi',
            'morning paper',
            'mvaclient',
            'netresearchserver',
            'netseer crawler',
            'netvibes',
            'newsgator',
            'ng-search',
            'nusearch spider',
            'nutchcvs',
            'nymesis',
            'oegp',
            'orbiter',
            'owlin',
            'peew',
            'pompos',
            'postpost',
            'postrank',
            'pycurl',
            'qseero',
            'radian6',
            'rambler',
            'sandcrawler',
            'sbider',
            'scooter',
            'scoutjet',
            'scrubby',
            'searchsight',
            'semanticdiscovery',
            'sensis web crawler',
            'seochat',
            'shim-crawler',
            'shopwiki',
            'shoula',
            'silk',
            'sitesell',
            'skygrid',
            'snappy',
            'sogou spider',
            'sosospider',
            'soup',
            'speedy spider',
            'spider',
            'sqworm',
            'ssppiiddeerr',
            'stackrambler',
            'summify',
            'teoma',
            'thumbnail.cz',
            'tineye',
            'topix',
            'truwogps',
            'tumblr',
            'tweetbeagle',
            'tweetedtimes',
            'twitturls',
            'unwindfetchor',
            'updated',
            'urlchecker',
            'vagabondo',
            'vortex',
            'voyager',
            'vyu2',
            'webcollage',
            'websquash.com',
            'wf84',
            'wofindeich',
            'womlpefactory',
            'xaldon_webspider',
            'yacy',
            'yahoo',
            'yahooseeker',
            'yandeximages',
            'yeti',
            'yooglifetchagent',
            'zao',
            'zemanta',
            'zspider',
            'zyborg',
        ];

        foreach ($this->Bots as $Bot) {
            if (false !== stripos($this->Agent, $Bot)) {
                return false;
            }
        }

        return true;
    }

    // Inicia a sessão do usuário quando ela não existir!

    /**
     * @param 0|1|2|4|5 $inputType
     */
    private function sanitizeInputString(int $inputType, string $name): string
    {

        $value = filter_input($inputType, $name, FILTER_UNSAFE_RAW);
        if (!is_string($value)) {
            return '';
        }

        return trim(strip_tags($value));
    }

    // Atualiza a sessão do usuário de acordo com sua navegação!

    private function sanitizeUserAgent(mixed $value): string
    {

        if (!is_string($value)) {
            return '';
        }

        return mb_strtolower(trim($value));
    }

    // Limpa sessões expiradas

    private function setSession(): void
    {

        $this->viewsStart();

        $onlineSessionId = $this->sanitizeSessionId($_SESSION['userOnline'] ?? null);
        if (null === $onlineSessionId) {
            $this->sessionStart();
        } else {
            $this->sessionUpdate();
        }
    }

    /**
     * CONTROLA O TRÁFEGO DO SITE
     * Ao primeiro acesso do dia, armazena os dados de tráfego.
     * Atualiza o views_pages a cada load de página
     * Atualiza o views_views a cada nova sessão do site
     * Atualiza o views_users a cada visita única de um dispositivo.
     */
    private function viewsStart(): void
    {

        $Read = new Read();
        $Read->exeRead(DB_VIEWS_VIEWS, 'WHERE views_date = date(NOW())');

        $view = $this->firstResult($Read->getResult());
        if (null !== $view) {
            $userCookie = $this->sanitizeCookie('userView');
            $viewsPages = $this->toInt($view['views_pages'] ?? 0) + 1;
            $viewsViews = $this->toInt($view['views_views'] ?? 0);
            $viewsUsers = $this->toInt($view['views_users'] ?? 0);

            $userOnlineActive = null !== $this->sanitizeSessionId($_SESSION['userOnline'] ?? null);
            $UpdateView = [];
            $UpdateView['views_pages'] = $viewsPages;
            $UpdateView['views_views'] = $userOnlineActive ? $viewsViews : $viewsViews + 1;
            $UpdateView['views_users'] = null === $userCookie ? $viewsUsers + 1 : $viewsUsers;

            $Update = new Update();
            $Update->exeUpdate(
                DB_VIEWS_VIEWS,
                $UpdateView,
                'WHERE views_date = date(NOW()) AND views_id >= :id',
                'id=1'
            );

            // 24 HORS TO NEW USER
            setcookie('userView', Check::name(SITE_NAME), time() + 86400, '/');
        } else {
            $CreateView = [
                'views_date' => date('Y-m-d'),
                'views_users' => 1,
                'views_views' => 1,
                'views_pages' => 1,
            ];
            $Create = new Create();
            $Create->exeCreate(DB_VIEWS_VIEWS, $CreateView);
        }
    }

    // Identifica usuário ou bot

    /**
     * @param null|array<int, array<string, mixed>> $result
     *
     * @return null|array<string, mixed>
     */
    private function firstResult(?array $result): ?array
    {

        if (!is_array($result) || [] === $result) {
            return null;
        }

        // @var array<string, mixed> $row
        return $result[0];
    }

    private function sanitizeCookie(string $name): ?string
    {

        $value = filter_input(INPUT_COOKIE, $name, FILTER_UNSAFE_RAW);

        return is_string($value) && '' !== $value ? $value : null;
    }

    private function toInt(mixed $value): int
    {

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int)$value;
        }

        return 0;
    }

    private function sanitizeSessionId(mixed $value): ?string
    {

        if (!is_scalar($value)) {
            return null;
        }

        $string = (string)$value;

        return '' === $string ? null : $string;
    }

    private function sessionStart(): void
    {

        $this->Session = [];
        $this->Session['online_startview'] = date('Y-m-d H:i:s');
        $this->Session['online_endview'] = date('Y-m-d H:i:s', $this->expirationTimestamp());
        $this->Session['online_ip'] = $this->sanitizeIp($_SERVER['REMOTE_ADDR'] ?? null);
        $this->Session['online_url'] = $this->sanitizeInputString(INPUT_GET, 'url');
        $this->Session['online_agent'] = $this->sanitizeUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);

        $userLogin = $_SESSION['userLogin'] ?? null;
        if (is_array($userLogin)) {
            $userId = $this->sanitizeSessionId($userLogin['user_id'] ?? null);
            $firstName = $this->stringValue($userLogin['user_name'] ?? null);
            $lastName = $this->stringValue($userLogin['user_lastname'] ?? null);

            $this->Session['online_user'] = $userId;
            $this->Session['online_name'] = (null !== $firstName && null !== $lastName)
                ? sprintf('%s %s', $firstName, $lastName)
                : null;
        }

        $Create = new Create();
        $Create->exeCreate(DB_VIEWS_ONLINE, $this->Session);
        $_SESSION['userOnline'] = $this->sanitizeSessionId($Create->getResult());
    }

    private function expirationTimestamp(): int
    {

        return time() + ($this->Cache * 60);
    }

    private function sanitizeIp(mixed $value): ?string
    {

        if (!is_string($value)) {
            return null;
        }

        $ip = filter_var($value, FILTER_VALIDATE_IP);

        return false === $ip ? null : $ip;
    }

    private function stringValue(mixed $value): ?string
    {

        if (null === $value) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return '' === $trimmed ? null : $trimmed;
        }

        if (is_numeric($value)) {
            return (string)$value;
        }

        return null;
    }

    private function sessionUpdate(): void
    {

        $Read = new Read();
        $sessionId = $this->sanitizeSessionId($_SESSION['userOnline'] ?? null);
        if (null === $sessionId) {
            $this->sessionStart();

            return;
        }

        $Read->exeRead(DB_VIEWS_ONLINE, 'WHERE online_id = :ses', 'ses=' . $sessionId);
        $sessionRow = $this->firstResult($Read->getResult());
        if (null === $sessionRow) {
            $this->sessionStart();
        } else {
            $this->Session = $sessionRow;
            $this->Session['online_url'] = $this->sanitizeInputString(INPUT_GET, 'url');
            $this->Session['online_endview'] = date('Y-m-d H:i:s', $this->expirationTimestamp());

            $userLogin = $_SESSION['userLogin'] ?? null;
            if (is_array($userLogin)) {
                $userId = $this->sanitizeSessionId($userLogin['user_id'] ?? null);
                $firstName = $this->stringValue($userLogin['user_name'] ?? null);
                $lastName = $this->stringValue($userLogin['user_lastname'] ?? null);
                $this->Session['online_user'] = $userId;
                $this->Session['online_name'] = (null !== $firstName && null !== $lastName)
                    ? sprintf('%s %s', $firstName, $lastName)
                    : null;
            } else {
                $this->Session['online_user'] = null;
                $this->Session['online_name'] = null;
            }

            $Update = new Update();
            $Update->exeUpdate(
                DB_VIEWS_ONLINE,
                $this->Session,
                'WHERE online_id = :id',
                'id=' . $sessionId
            );
        }
    }

    private function sessionClear(): void
    {

        $Delete = new Delete();
        $Delete->exeDelete(
            DB_VIEWS_ONLINE,
            'WHERE (online_endview < NOW() OR online_startview IS NULL) AND online_id >= :id',
            'id=1'
        );
    }
}
