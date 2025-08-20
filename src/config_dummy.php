<?php

// Reading is given here as an example, but you can use any SIRI-compliant bus stop API.
// such as Oxford and Plymouth

$endpoint = "https://reading-opendata.r2p.com/api/v1";

// You can get your API token from https://reading-opendata.r2p.com/
// and you can find the stop ID from https://<your domain>/stops.php
// or by using the api /busstops endpoint
// https://reading-opendata.r2p.com/api/v1/busstops?api_token=<your token>

$apiToken = "";

$stop = "";

$lines = [];
