<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF;

use \fpoirotte\IDMEF\Types\AbstractType;
use \fpoirotte\IDMEF\Classes\AbstractIDMEFMessage;

/**
 * Implements a basic Prelude SIEM agent.
 *
 * Usage:
 *      $agent = PreludeAgent::create($profile_name);
 *      $agent->send($alert);
 *
 * Note:
 *      The profile name defaults to "php" if omitted.
 *      Also, the profile must have been registered against
 *      the Prelude SIEM manager with the "idmef:w" permission
 *      before this class can be used.
 *
 * See https://www.prelude-siem.org/projects/prelude/wiki/DevelAgentQuickly and
 * https://www.prelude-siem.org/projects/prelude/wiki/InstallingAgentRegistration
 * for more information.
 */
final class PreludeAgent
{
    const DEFAULT_PROFILE_NAME = "php";
    const PRELUDE_CLIENT_EXIT_STATUS_FAILURE = -1;
    const PRELUDE_CLIENT_EXIT_STATUS_SUCCESS = 0;

    private static $libprelude = null;
    private static $normMapping = array(
        'A' => '_a', 'B' => '_b', 'C' => '_c', 'D' => '_d', 'E' => '_e',
        'F' => '_f', 'G' => '_g', 'H' => '_h', 'I' => '_i', 'J' => '_j',
        'K' => '_k', 'L' => '_l', 'M' => '_m', 'N' => '_n', 'O' => '_o',
        'P' => '_p', 'Q' => '_q', 'R' => '_r', 'S' => '_s', 'T' => '_t',
        'U' => '_u', 'V' => '_v', 'W' => '_w', 'X' => '_x', 'Y' => '_y',
        'Z' => '_z',
    );


    private $client = null;
    private $status = self::PRELUDE_CLIENT_EXIT_STATUS_SUCCESS;

    private function __construct($profile)
    {
        if (!is_string($profile)) {
            throw new \InvalidArgumentException('Invalid profile name');
        }

        $this->client = self::$libprelude->new("prelude_client_t *");
        self::$libprelude->prelude_client_new(\FFI::addr($this->client), $profile);

        if (is_null($this->client)) {
            throw new \RuntimeException('Could not create Prelude client');
        }

        $res = self::$libprelude->prelude_client_start($this->client);
        if ($res < 0) {
            throw new \RuntimeException('Could not start Prelude client');
        }
    }

    public function __destruct()
    {
        $this->stop();
    }

    private function stop(): void
    {
        if ($this->client !== null) {
            self::$libprelude->prelude_client_destroy($this->client, $this->status);
            $this->client = null;
        }
    }

    private function __clone()
    {
        throw new \RuntimeException('Cloning this class is prohibited');
    }

    private static function adaptPath($path): string
    {
        // Turn paths like "Alert.AdditionalData(0)" into a Prelude-compatible
        // path (eg. "alert.additional_data(0)")
        $path = ltrim(str_replace('._', '.', strtr($path, self::$normMapping)), '_');

        // Prelude SIEM handles "Alert.Analyzer" as if it was a list rather than
        // a recursive class. So "Alert.Analyzer" becomes "alert.analyzer(0)",
        // "Alert.Analyzer.Analyzer" becomes "alert.analyzer(1)", and so on.
        $parts = explode('.', $path);
        $counts = array_count_values($parts);
        if (isset($counts['analyzer'])) {
            $pos = array_search('analyzer', $parts, true);
            $parts[$pos] = 'analyzer(' . ($counts['analyzer'] - 1) . ')';
            $path = str_replace('.analyzer.', '.', implode('.', $parts));
        }

        return $path;
    }

    private function send(AbstractIDMEFMessage $message): void
    {
        $idmef = self::$libprelude->new("idmef_message_t *");
        $res = self::$libprelude->idmef_message_new(\FFI::addr($idmef));
        if ($res < 0) {
            throw new \RuntimeException('Could not create IDMEF object: error #' . $res);
        }

        try {
            $message->acquireLock($message::LOCK_SHARED, true);
            try {
                if (!$message->isValid()) {
                    throw new \InvalidArgumentException('Invalid IDMEF message');
                }

                foreach ($message->getIterator('{' . AbstractType::class . '}', null, 0, -1) as $path => $value) {
                    $path = $this->adaptPath($path);
                    self::$libprelude->idmef_message_set_string($idmef, $path, (string) $value);
                }

                self::$libprelude->prelude_client_send_idmef($this->client, $idmef);
            } finally {
                $message->releaseLock($message::LOCK_SHARED, true);
            }
        } finally {
            self::$libprelude->idmef_message_destroy($idmef);
        }
    }

    public static function create($profile = self::DEFAULT_PROFILE_NAME): \Generator
    {
        if (self::$libprelude === null) {
            // We rely on class detection instead of extension detection
            // because there exists several PHP extensions named "FFI".
            if (!class_exists("\\FFI\\CType")) {
                throw new \RuntimeException("The FFI extension (https://www.php.net/FFI) " .
                                            "is required to use this feature");
            }

            self::$libprelude = \FFI::load(
                dirname(__DIR__) .
                DIRECTORY_SEPARATOR . 'data' .
                DIRECTORY_SEPARATOR . 'prelude.h'
            );

            // prelude_init() requires an argc+argv formatted array.
            // We build one based on the PHP binary's name.
            $binary = PHP_BINARY . "\0";
            $agentOption = \FFI::new('char [' . strlen($binary) . ']', false);
            register_shutdown_function('\\FFI::free', $agentOption);

            $optCount = \FFI::new('int [1]');
            $optCount[0] = 1;
            $agentOptions = \FFI::new('char *[1]');
            \FFI::memcpy($agentOption, $binary, strlen($binary));
            $agentOptions[0] = $agentOption;

            if (self::$libprelude->prelude_init($optCount, $agentOptions) < 0) {
                throw new \InvalidArgumentException('Could not initialize Prelude');
            }
        }

        $client = new self($profile);
        try {
            while (true) {
                $message = yield;
                $client->send($message);
            }
        } catch (\Exception $e) {
            $client->status = self::PRELUDE_CLIENT_EXIT_STATUS_FAILURE;
            $client->stop();
            unset($client);
            throw $e;
        }
    }
}
