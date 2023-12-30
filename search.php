<?php

// Including Database configuration file.
include "db.php";

error_log('Script started'); // Check if this script runs

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
    
// Getting value of "search" variable and "identifier" from "displayScript.js".
if (isset($_POST['search']) && isset($_POST['identifier'])) {

    // Search box value assigning to $Name variable.
    $Name = $_POST['search'];

    // Identifier to distinguish between search bars.
    $identifier = $_POST['identifier'];

    error_log('Search term: ' . $Name);
    error_log('Identifier: ' . $identifier);

    $Query = 
    "SELECT DISTINCT
                -- Concatenate room information based on conditions
                CONCAT(
                    CASE 
                        WHEN room.buildingID IS NOT NULL 
                            AND LENGTH('$Name') > 1 
                            AND room.roomNumber LIKE '$Name%' 
                            THEN CONCAT(room.roomNumber, ', ')
                        ELSE 
                            ''
                    END,
                    building.buildingName, ', ', address.aliasName
                ) AS roomInfo
    FROM building
    
    -- Join with address to get building information
    JOIN address ON address.addressID = building.addressID

     -- Left join with room to consider both buildings and rooms
    LEFT JOIN room ON room.buildingID = building.buildingID AND room.addressID = address.addressID
    
    -- Filter by building or room names containing the provided $Name
    WHERE (building.buildingName LIKE '%$Name%' OR room.roomNumber LIKE '%$Name%') 
    
    -- Order the result by building name and address alias
    ORDER BY
        building.buildingName, address.aliasName
    LIMIT 5";

    // Log the SQL query to the console.
    // echo "<script>console.log('SQL Query: " . $Query . "');</script>";

    // Query execution.
    $ExecQuery = mysqli_query($con, $Query);

    // Creating unordered list to display result.
    echo '<ul class="autocomplete-results">';

    // Fetching result from the database.
    while (($Result = mysqli_fetch_assoc($ExecQuery))) {
        // Creating unordered list items.
        // Calling JavaScript function named as "fill" found in "displayScript.js" file.
        // By passing fetched result which is a string as a parameter.
        echo '<li onclick="fill(\'' . $Result['roomInfo'] . '\', \'' . $identifier . '\', showOnMap)">';
        echo '<a>';
        // Assigning searched result in "Search box" in "search.php" file.
        echo $Result['roomInfo'];
        echo '</a>';
        echo '</li>';
    }
    // Closing unordered list
    echo '</ul>';
}

error_log('Script ended'); 
?>
