<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SqlDownloadController extends Controller
{
    //
    public function download()
    {
        // Configure Guzzle HTTP client
        $client = new Client();

        // phpMyAdmin export URL
        $phpMyAdminUrl = 'http://localhost/phpmyadmin/index.php'; // Replace with your phpMyAdmin URL

        // Make a GET request to the export URL
        $response = $client->get($phpMyAdminUrl);

        // Check if the request was successful
        if ($response->getStatusCode() === 200) {
            // Get the SQL content from the response
            $sqlContent = $response->getBody();

            // Set headers for file download
            $headers = [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="albasee2_agt.sql"',
            ];

            // Return the SQL content as a downloadable file
            return response($sqlContent, 200, $headers);
        }

        // Handle errors if the request fails
        return response('Failed to download SQL file', 500);
    }
}
