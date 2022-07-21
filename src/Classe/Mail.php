<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail{

private $api_key='5b94d543657195dfd65369a344854dbe';
private $api_key_secret='3c4982963e8c93aba949f8cddfd5d3af';

public function send($to_email,$to_name,$subject,$content){

    $mj = new Client($this->api_key,$this->api_key_secret,true,['version' => 'v3.1']);

    // Define your request body

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => 'axelwilfried.etoundiessimi@esprit.tn',
                    'Name' => "MaBoutique"
                ],
                'To' => [
                    [
                        'Email' => $to_email,
                        'Name' => $to_name
                    ]
                ],
                'TemplateId' => 4058375,
                'TemplateLanguage' => true,
                'Subject' => $subject,
                'TextPart' => "Greetings from Mailjet!",

                'Variables' => [
                    'day' => 'friday',
                    'content' => $content,
                ],

            ]
        ]
    ];

    // All resources are located in the Resources class

    $response = $mj->post(Resources::$Email, ['body' => $body]);

    // Read the response

    $response->success();

}

}

?>