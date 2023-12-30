function fill(Value, identifier, callback) {

    console.log('fill function called: ', identifier,Value);

    // Assigning value to the appropriate "search" div based on the identifier.
    $(`#${identifier}`).val(Value);

    // Hiding the corresponding "displayFrom" and "" div based on the identifier.
    $(`#display${identifier}`).hide();
    
    // Get Lon and Lat of the selected option and output it to the console
    // Using fetch API
    fetch('getLonLat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `coord=${encodeURIComponent(Value)}&identifier=${identifier}`, // Include the identifier in the body
    })
    .then(response => {
        // Log the SQL query to the console (skip the first response)
        if (response.headers.get('content-type') !== 'application/json') {
            console.log('SQL Query:', response.text());
            throw new Error('Skipping non-JSON response');
        }
        console.log("Before parsing  : ", response);
        return response.json(); // Parse the response as JSON
    })
    .then(data => {
        console.log('Raw data:', data);

        console.log('Longitude:', data.Longitude);
        console.log('Latitude:', data.Latitude);
        console.log('Polygon: ', data.Polygon);
        console.log('Routing Longitude:', data.routingLongitude);
        console.log('Routing Latitude: ', data.routingLatitude)           
        

        if (typeof callback === 'function') {
            // passing values to function that calls it
            callback(Value, data.Longitude, data.Latitude, data.Polygon,data.routingLongitude,data.routingLatitude)
        }
    })
    .catch(error => console.error('Error:', error));

}

$(document).ready(function() {
    function performSearch(searchId, displayId) {
        // Creating a jQuery object
        // assigning searchID to a variable
        var name = $(searchId).val();

        if (name == "") {
            $(displayId).html("").hide();
        } else {
            $.ajax({
                type: "POST",
                url: "search.php",
                data: {
                    search: name,
                    identifier: searchId.replace("#", "") // include the identifier in the data, and removing '#'
                },
                dataType: 'html',
                success: function(html) {

                    // Create a jQuery object for the display element using the provided identifier
                    // This jQuery object provides a convenient way to work with the selected element(s) using jQuery methods
                    // Can also use pure JavaScript, by using:
                    // var displayElement = document.querySelector(displayId);
                    var $displayElement = $(displayId);

                    // Display the received HTML in the display element and show it
                    $displayElement.html(html).show();

                    // Check if there are no <li> elements inside the <ul> of the display element
                    var $ulElement = $displayElement.find('ul.autocomplete-results');
                    if ($ulElement.find('li').length === 0) {
                        // If no <li> elements are found, set the HTML to display "No such thing"
                        $ulElement.html('<li class="no-match">No matching Block or Room</li>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    }

    $("#From").keyup(function() {
        performSearch("#From", "#displayFrom");
    });

    $("#To").keyup(function() {
        performSearch("#To", "#displayTo");
    });

});
