<?PHP

	include "inc.php";
	include "../include/nusoap.php";

    // Create the client instance
    $client = new soapclientW('http://www.onedaynet.co.kr/mall/nusoap/member.chk.php');

    // Check for an error
    $err = $client->getError();
    if ($err) {
        // Display the error
        echo '<p><b>Constructor error: ' . $err . '</b></p>';
        // At this point, you know the call that follows will fail
    }


    // Call the SOAP method
    $result = $client->call(
        'member_chk',array('_id_onedaynet' => $_POST[_id_onedaynet],'_pw_onedaynet' => $_POST[_pw_onedaynet])
    );


    echo $result;

?>