<?php

$xml = simplexml_load_file('report.xml');

foreach ($xml->testsuite->testcase as $testcase) {
    if (isset($testcase->failure) || isset($testcase->error)) {

        $failure = $testcase->failure ?? $testcase->error;

        $message = (string) $failure['message'];
        $details = (string) $failure;

        echo "TEST FAILURE ANALYSIS MODE\n\n";
        echo "Test Name:\n";
        echo $testcase['name'] . "\n\n";

        echo "Assertion Error:\n";
        echo "Expected / Actual:\n";
        echo "(Parse manually from message if needed)\n\n";

        echo "Error Message:\n";
        echo $message . "\n\n";

        echo "Stack Trace (max 20 lines):\n";

        $lines = explode("\n", $details);
        $limited = array_slice($lines, 0, 20);
        echo implode("\n", $limited);

        echo "\n\n------------------------------------\n\n";
    }
}
