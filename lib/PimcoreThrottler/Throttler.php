<?php

namespace PimcoreThrottler;

class Throttler
{

    public static $garbageCollectionLoopMax = 1000;
    public static $cpuTimeout = 5000000;

    private static $cpus = false;
    private static $loopCounter = 0;


    public static function throttleCpu($l = false) {

        if(!self::$cpus) {
            // INITIALIZE !
            self::$cpus = max(self::numCpus(), 2);  // minimum cpus = 2 for throttling
            gc_enable();
        }

        $load = sys_getloadavg();
        while ($load[0] > self::$cpus) {
            if($l) {
                $l->l("Throttler: Current CPU load = " . $load[0] . " / " . self::$cpus . " - holding a bit");
            }
            $load = sys_getloadavg();
            usleep(self::$cpuTimeout);
        }

        if(self::$loopCounter++ > self::$garbageCollectionLoopMax) {
            gc_collect_cycles();
        }

    }

    public static function numCpus()
    {

        $numCpus = 1;
        if (is_file('/proc/cpuinfo'))
        {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $numCpus = count($matches[0]);
        }
        else if ('WIN' == strtoupper(substr(PHP_OS, 0, 3)))
        {
            $process = @popen('wmic cpu get NumberOfCores', 'rb');
            if (false !== $process)
            {
                fgets($process);
                $numCpus = intval(fgets($process));
                pclose($process);
            }
        }
        else
        {
            $process = @popen('sysctl -a', 'rb');
            if (false !== $process)
            {
                $output = stream_get_contents($process);
                preg_match('/hw.ncpu: (\d+)/', $output, $matches);
                if ($matches)
                {
                    $numCpus = intval($matches[1][0]);
                }
                pclose($process);
            }
        }

        return $numCpus;

    }

}
