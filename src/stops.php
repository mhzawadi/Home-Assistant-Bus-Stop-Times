<?php


/**
 * stops.php
 *
 * Home Assistant - Bus Stop List
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

$url = "$endpoint/busstops?api_token=$apiToken";

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

header('Content-Type: text/html; charset=utf-8');

$data = json_decode($body, true);
if (!is_array($data)) {
    fwrite(STDERR, "Invalid JSON received from API<br>");
    exit(1);
}

function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Bus Stops</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            padding: 1rem;
        }

        table {
            width: 100%;
        }
    </style>
</head>

<body>
    <h1>List of Bus Stops</h1>
    <table id="stops" class="display">
        <thead>
            <tr>
                <th>Location Code</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= e($row['location_code'] ?? '') ?></td>
                    <td><?= e($row['description'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#stops').DataTable({
                pageLength: 25,
                order: [
                    [1, 'asc']
                ],
                stateSave: true
            });
        });
    </script>
</body>

</html>