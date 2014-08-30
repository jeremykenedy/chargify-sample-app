<?php

$service = new Crucial_Service_ChargifyV2(array(
    'api_id'       => '{{API_ID}}',
    'api_password' => '{{API_PASSWORD}}',
    'api_secret'   => '{{API_SECRET}}',
    'format'       => 'json'
));

$direct = $service->direct();

// The redirect  URL
$direct->setRedirect('http://' . $_SERVER['HTTP_HOST'] . '/success');

// get the <form> action attribute for your form.
$formAction = $this->direct->getSignupAction();

// set tamper-proof data. https://docs.chargify.com/chargify-direct-introduction#secure-data
$direct->setData(
    array(
        'signup' => array(
            'product' => array(
                'handle' => 'pro'
            )
        ),
        'address' => array(
            'city' => 'Raleigh'
        )
    )
);

// get hidden fields for your form
$fields = $direct->getHiddenFields();

// After Chargify redirect back to your app, test if response signature is correct
if (!$direct->isValidResponseSignature()) {
    // we should throw a hard exception here because there is a good chance we are being attacked
    throw new Crucial_Service_ChargifyV2_Exception('Invalid response signature after redirect from Chargify');
}