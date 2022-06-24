<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

return function ($event) {

    $SesClient = new SesClient([
        'version' => 'latest',
        'region'  => 'us-east-1',
        "Statement" => [
        "Effect" => "Allow",
        "Action" => [
            "ses" => "*"
        ],
        "Resource" => "*"
        ],
    ]);

    // take records from event array
    if (is_array($event['Records'])) {
        foreach ($event['Records'] as $message) {
            echo "- Message: {$message['body']} (Id: {$message['messageId']})\n"; // output it in Logs
            echo email_send($message['body'], $SesClient);
        }
    }
};

function email_send($email_body = '', $SesClient) { // TBD: rebuild it with SES Lambada handlers

    //Sender email address verified in Amazon SES where we setup.
    // This address must be verified with Amazon SES.
    $sender_email = 'test@gmail.com';

    // Replace these sample addresses with the addresses of your recipients.
    $recipient_emails = ['test+test@gmail.com'];

    $subject = 'A new participant data';
    $plaintext_body = $email_body;
    $html_body =  '<h1>A new participant just registered!</h1>'.
                '<p>' . $email_body . '</p>';
    $char_set = 'UTF-8';

    try {
        $result = $SesClient->sendEmail([
            'Destination' => [
                'ToAddresses' => $recipient_emails,
            ],
            'ReplyToAddresses' => [$sender_email],
            'Source' => $sender_email,
            'Message' => [
            'Body' => [
                'Html' => [
                    'Charset' => $char_set,
                    'Data' => $html_body,
                ],
                'Text' => [
                    'Charset' => $char_set,
                    'Data' => $plaintext_body,
                ],
            ],
            'Subject' => [
                'Charset' => $char_set,
                'Data' => $subject,
            ],
            ],
        ]);
        $messageId = $result['MessageId'];
        return "Email sent! Message ID: $messageId"."\n";
    } catch (AwsException $e) {
        // output error message if fails
        echo $e->getMessage();
        return "The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n";
    }
}
