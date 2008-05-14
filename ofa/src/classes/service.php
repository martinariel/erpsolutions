<?php


	require_once('nusoap-php5/lib/nusoap.php');
	// Create the client instance
	$client = new soapclientnusoap('http://localhost/interface/ofa/src/classes/ws_interface_.php?wsdl', true);
	// Check for an error
	$err = $client->getError();
	
	
	if ($err) {
		echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	}

	// Call the SOAP method
	
	$result = $client->call('getTransactionId', 
			array('state_id' => 1)
			);
	
	// Check for a fault
	if ($client->fault) {
	    echo '<h2>Fault</h2><pre>';
	    print_r($result);
	    echo '</pre>';
	} else {
	    // Check for errors
	    $err = $client->getError();
	    if ($err) {
	        // Display the error
	        echo '<h2>Error</h2><pre>' . $err . '</pre>';
	    } else {
	        // Display the result
	        echo '<h2>Result</h2><pre>';
	        print_r($result);
	    echo '</pre>';
	    }
	}
	
	
	// Display the request and response
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
// Display the debug messages
echo '<h2>Debug</h2>';
echo '<pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';



?>