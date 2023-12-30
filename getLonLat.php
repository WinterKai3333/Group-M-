<?php

// Including Database configuration file.
include "db.php";

error_log('Script started'); // Log script started

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Getting value of "coord" and "identifier" variables from "dispalyScript.js".
if (isset($_POST['coord']) && isset($_POST['identifier'])) {

    // Value passed to 'coord' are in the form of
    // $coord = "BC10, Block B, UNMC"
    // $coordVal = "BC10, Block B, UNMC"
    $coordVal = $_POST['coord'];
    // Extracting only the value before the comma from the value of 'coordVal'
    $parts = explode(',', $coordVal);

    // Take the first part (before the comma)
    // searchedName = "BC10"
    $searchedName = trim($parts[0]); // trim to remove leading/trailing whitespaces

    // Identifier to distinguish between search bars.
    $identifier = $_POST['identifier'];


    $QueryLonLat = 
    "SELECT 
        -- Determine Longitude of Room if searching for Room, else get Building Longitude 
        CASE 
            WHEN room.buildingID IS NOT NULL
                 AND room.roomNumber LIKE '$searchedName'
            THEN room.Longitude
            ELSE building.Longitude
        END AS Longitude,
        
        -- Determine Latitude of Room if searching for Room, else get Building Latitude 
        CASE 
            WHEN room.buildingID IS NOT NULL
                 AND room.roomNumber LIKE '$searchedName'
            THEN room.Latitude
            ELSE building.Latitude
        END AS Latitude,
        
        -- Fetch building Longitude for routing
        building.Longitude AS routingLongitude, 
        
        -- Fetch building Latitude for routing
        building.Latitude AS routingLatitude,
 
        -- Retrieve building Polygon as GeoJSON format so that even when searching 
        -- for Room, Building's Polygon will appear for better UI 
        ST_AsGeoJSON(building.Polygon) AS Polygon
    
    FROM building
    -- Left join with room to consider both buildings and rooms
    LEFT JOIN room ON building.buildingID = room.buildingID
    -- Filter by building or room names containing the provided $searchedName
    WHERE (building.buildingName LIKE '$searchedName' OR room.roomNumber LIKE '$searchedName')";

    // Log SQL query if theres error.
    error_log('SQL Query: ' . $QueryLonLat);


    // Query execution.
    $ExecQueryLonLat = mysqli_query($con, $QueryLonLat);

    // Check for query execution success
    if (!$ExecQueryLonLat) {
        // Handle the query execution error
        $error_message = mysqli_error($con);
        $response = ['error' => 'Query execution error: ' . $error_message];
        echo json_encode($response);
        exit;
    }

    // Fetch the result
    $ResultLonLat = mysqli_fetch_assoc($ExecQueryLonLat);

    // Output the result as JSON
    echo json_encode($ResultLonLat);
}

error_log('Script ended'); // Log script ended
?>

