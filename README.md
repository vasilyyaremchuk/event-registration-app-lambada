# Lambada function SQS -> SES
Event registration application: lambada part.

## Installation

1. install the serverless command
```$ npm install -g serverless```

2. create AWS access keys

3. setup those keys by running:
```$ serverless config credentials --provider aws --key <key> --secret <secret>```

4. Install Bref in your project using Composer:
```$ composer require bref/bref```

5. Add requires dependencies to work with SQS and SES:
```$ composer require aws/aws-sdk-php```

6. Then let's start by initializing the project by running:
```$ vendor/bin/bref init```
and select [1] Event-driven function

7. On serverless.yml make chnages:
```
functions:
    participant:
        handler: app.php
        description: 'New Participant'
        layers:
            - ${bref:layer.php-74}
```

8. Deploy
```$ serverless deploy```

9. Create a new queue in SQS with basic Access policy:
Define who can send messages to the queue: Only the queue owner
Define who can receive messages from the queue: Only the queue owner

10. In Lambada console find you new function ARN: arn:aws:lambda:us-east-1:...:function:app-dev-participant and resource

11. On IAM dashboard find role that performe your Lambada function.
In my case it's custom role 'app-dev-us-east-1-lambdaRole'.
For that role you need to add Polisies:
- AmazonSESFullAccess
- AWSLambdaSQSQueueExecutionRole
To be sure that we can have access to SES and SQS in your lambada function.

12. On Amazon SQS create a Queue. In 'Lambda triggers' tab click 'Configure Lambda function trigger' button and select created Lambada function.

13. Verify your from email on amazon SES dashboard.

14. You can send messages in you new SQS Queue. See the example there
https://gist.github.com/fbrnc/396548c85ee083e32930

## Possible issues

1. When you trying to assign Lambada function in AWS console (Trigger AWS Lambda function) you can have the error:

Couldn't configure trigger for queue.
Error code: InvalidParameterValueException. Error message: The provided execution role does not have permissions to call ReceiveMessage on SQS

When you tring to make it with AWS CLI:

```$ aws lambda create-event-source-mapping --function-name app-dev-participant --batch-size 5 \
--maximum-batching-window-in-seconds 60 \
--event-source-arn arn:aws:sqs:us-east-1:780261813487:event-participant```

You can get:

An error occurred (InvalidParameterValueException) when calling the CreateEventSourceMapping operation: The provided execution role does not have permissions to call ReceiveMessage on SQS

How to fix:
You need find role of your lambada function and attached a new Policy to that role.
See #11 in the installation guide.

2. Mails don't work.

Check #11 or #13.

## References

https://bref.sh/docs/
https://gist.github.com/fbrnc/396548c85ee083e32930
https://www.eduforbetterment.com/send-email-using-amazon-ses-in-php/
https://bref.sh/docs/function/handlers.html
