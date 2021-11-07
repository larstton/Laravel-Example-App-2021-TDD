<?php

namespace App\Support\LatestData;

class DataTransformer
{
    private static $key;
    private static $value;
    private static $return = [];

    public static function customCheckMeasurement($key, $value)
    {
        self::transform($key, $value);
        self::$return['name'] = substr($key, strpos($key, '.') + 1);

        return self::$return;
    }

    public static function transform(string $key, ?string $value): array
    {
        if (is_null($value)) {
            return [
                'key'      => $key,
                'name'     => $key,
                'unit'     => null,
                'value'    => null,
                'rawValue' => null,
            ];
        }

        self::$key = $key;
        self::$value = $value;

        self::$return['key'] = self::$key;
        self::$return['rawValue'] = self::value();
        self::$return['unit'] = self::unit();
        self::$return['value'] = self::value();
        self::$return['name'] = self::name();

        return self::$return;
    }

    private static function value()
    {
        return self::cast(self::$value);
    }

    private static function cast($input)
    {
        if (is_numeric($input)) {
            return $input + 0;
        }

        return $input;
    }

    private static function unit()
    {
        if (preg_match("/^cpu\.util/", self::$key)) {
            return (string) '%';
        }
        if (strpos(self::$key, '_percent')) {
            self::$key = str_replace('_percent', '', self::$key);

            return (string) '%';
        }
        if (strpos(self::$key, '_Bps')) {
            self::$key = str_replace('_Bps', '', self::$key);

            return self::calculateByteValue('Bps');
        }
        if (strpos(self::$key, '_B')) {
            self::$key = str_replace('_B', '', self::$key);

            return self::calculateByteValue();
        }
        if (strpos(self::$key, '_bps')) {
            self::$key = str_replace('_bps', '', self::$key);

            return self::calculateByteValue('bps');
        }
        if (strpos(self::$key, 'bytesReceived')) {
            return self::calculateByteValue('B');
        }
        if (strpos(self::$key, ('_ops_per_s'))) {
            self::$key = str_replace('_ops_per_s', ' operations per second', self::$key);

            return 'ops';
        }
        if (strpos(self::$key, '_s.') or preg_match('/_s$/', self::$key)) {
            self::$key = str_replace('_s', '', self::$key);

            return self::calculateSeconds();
        }
        if (strpos(self::$key, 'Octets')) {
            return self::calculateByteValue('B');
        }

        return null;
    }

    private static function calculateByteValue($suffix = 'B')
    {
        $steps = ['', 'K', 'M', 'G', 'T', 'P'];
        foreach ($steps as $step) {
            if (self::$value < 1024) {
                return (string) $step.$suffix;
            }
            self::$value = round(self::$value / 1024, 2);
        }
    }

    private static function calculateSeconds()
    {
        if (self::$value < 1) {
            self::$value = round(self::$value * 1000, 2);

            return 'ms';
        }
        if (self::$value > 60) {
            self::$value = gmdate('H:i:s', self::$value);

            return 'h:m:s';
        }

        return 's';
    }

    private static function name()
    {
        // Beautify the CPU Measurements
        if (preg_match("/cpu\.util\.(.*)\.([0-9]+)\.(.*)/u", self::$key, $match)) {
            return 'CPU Utilization '.strtoupper($match[1]).' '.strtoupper($match[3]);
        }
        $replace = [
            'cagent.success' => 'Agent Success ',
            'cpu.load.avg.1' => 'CPU Load Average',
            'fs.'            => 'Filesystem ',
            'mem.'           => 'Memory ',
        ];
        foreach ($replace as $key => $value) {
            if (self::$key = str_replace($key, $value, self::$key, $count)) {
                if ($count > 0) {
                    break;
                }
            }
        }

        return str_replace('.', ' ', self::$key);
    }

    public static function snmpCheckMeasurement($key, $value)
    {
        self::transform($key, $value);

        //exclude interface name from metric name for snmp checks
        if (strpos(self::name(), 'if') !== false) {
            self::$return['name'] = explode(' ', self::name())[0];
        }

        return self::$return;
    }
}
