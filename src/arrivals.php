<?php

/**
 * arrivals.php
 *
 * Home Assistant - Bus Stop Times
 *
 * @author     Neil Thompson <neil@spokenlikeageek.com>
 * @copyright  2025 Neil Thompson
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License v3.0
 * @link       https://github.com/williamsdb/Home-Assistant---Bus-Stop-Times Home Assistant - Bus Stop Times on GitHub
 * @see        https://www.spokenlikeageek.com/tag/bus-stop-times/ Blog post
 *
 * ARGUMENTS
 *
 */

header('Content-Type: application/json; charset=utf-8');

// turn off reporting of notices
error_reporting(0);
ini_set('display_errors', 0);

// Load parameters
try {
    require __DIR__ . '/config.php';
} catch (\Throwable $th) {
    die('config.php file not found. Have you renamed from config_dummy.php?');
}

// Fetch SIRI XML and extract ExpectedArrivalTime values.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// has a stop been specified?
if (isset($_GET['stop']) && !empty($_GET['stop'])) {
    $stop = $_GET['stop'];
}

// has a line been specified?
if (isset($_GET['line']) && !empty($_GET['line'])) {
    $lines = explode(',', $_GET['line']);
    $lines = array_map('trim', $lines); // trim whitespace
}

$url = "$endpoint/siri-sm?api_token=$apiToken&location=$stop";

// Fetch
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_HTTPHEADER => [
        'Accept: application/xml',
        'User-Agent: PHP SIRI Client'
    ],
]);
$body = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($body === false || $code < 200 || $code >= 300) {
    fwrite(STDERR, "Request failed (HTTP $code): $err<br>");
    exit(1);
}

// Parse
libxml_use_internal_errors(true);
$xml = simplexml_load_string($body);
if ($xml === false) {
    fwrite(STDERR, "XML parse error\n");
    foreach (libxml_get_errors() as $e) {
        fwrite(STDERR, trim($e->message) . "<br>");
    }
    exit(1);
}

// Register namespace and select visits
$xml->registerXPathNamespace('siri', 'http://www.siri.org.uk/siri');
$visits = $xml->xpath('//siri:StopMonitoringDelivery/siri:MonitoredStopVisit');
if ($visits === false) {
    fwrite(STDERR, "XPath evaluation failed<br>");
    exit(1);
}

// Output ExpectedArrivalTime for each MonitoredStopVisit (N/A if missing)

// Fetch and parse your SIRI XML as before
$results = [];
$now = time();

foreach ($visits as $visit) {
    $visit->registerXPathNamespace('siri', 'http://www.siri.org.uk/siri');

    // LineRef
    $line = $visit->xpath('siri:MonitoredVehicleJourney/siri:LineRef');
    $lineRef = ($line && isset($line[0])) ? (string)$line[0] : '—';

    // If $lines is provided, filter by allowed line refs (case-insensitive, string compare)
    if (isset($lines) && is_array($lines) && !empty($lines)) {
        $allowedLines = array_map('strtolower', array_map('strval', $lines));
        if (!in_array(strtolower((string)$lineRef), $allowedLines, true)) {
            continue;
        }
    }

    // ExpectedArrivalTime (real-time)
    $expected = $visit->xpath('siri:MonitoredVehicleJourney/siri:MonitoredCall/siri:ExpectedArrivalTime');
    $expectedTime = ($expected && isset($expected[0])) ? (string)$expected[0] : null;

    // AimedArrivalTime (scheduled)
    $aimed = $visit->xpath('siri:MonitoredVehicleJourney/siri:MonitoredCall/siri:AimedArrivalTime');
    $aimedTime = ($aimed && isset($aimed[0])) ? (string)$aimed[0] : null;

    // Pick sort key (expected preferred, fallback to aimed)
    $sortKey = $expectedTime ?? $aimedTime;
    $typeLabel = $expectedTime ? 'Expected' : 'Aimed';

    // Minutes from now
    $minutesFromNow = $sortKey ? round((strtotime($sortKey) - $now) / 60) : null;

    $results[] = [
        'line'     => $lineRef,
        'time'     => $sortKey ? date("H:i", strtotime($sortKey)) : null,
        'due_in'   => $minutesFromNow,
        'type'     => $typeLabel
    ];
}

// Sort by soonest due
usort($results, function ($a, $b) {
    return ($a['due_in'] ?? PHP_INT_MAX) <=> ($b['due_in'] ?? PHP_INT_MAX);
});

// Convert minutes to friendly string
foreach ($results as &$r) {
    if ($r['due_in'] === null) {
        $r['due_in_str'] = '—';
    } elseif ($r['due_in'] >= 60) {
        $hours = floor($r['due_in'] / 60);
        $mins  = $r['due_in'] % 60;
        $r['due_in_str'] = "{$hours} hr" . ($hours > 1 ? "s" : "") .
            ($mins > 0 ? " {$mins} min" : "");
    } else {
        $r['due_in_str'] = "{$r['due_in']} min";
    }
}
unset($r); // break reference

// Output JSON
$output = ['buses' => array_slice($results, 0, 4)];  // first 4 only
echo json_encode($output);
