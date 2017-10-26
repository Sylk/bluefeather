<?php

include __DIR__ . '/vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

define('CONSUMER_KEY', getenv('CONSUMER_KEY'));
define('CONSUMER_SECRET', getenv('CONSUMER_SECRET'));
define('ACCESS_TOKEN', getenv('ACCESS_TOKEN'));
define('ACCESS_TOKEN_SECRET', getenv('ACCESS_TOKEN_SECRET'));

$discord = new \Discord\Discord([
    'token' => getenv('DISCORD_TOKEN'),
]);

$discord->on('ready', function ($discord) {
    echo "Bot is ready to tweet.", PHP_EOL;

    // Listen for events here
    $discord->on('message', function ($message) {
        if ($message->author->id != getenv('DISCORD_BOT_ID')) {                   //stops it from echoing itself

            if (strpos($message->content, getenv('DISCORD_BOT_MENTION')) !== false) { // calls to an @blue-feather

                $messageSender = $message->author->user->nickname;  //establishes the users nickname

                $array = explode(' ', $message->content);    //explodes the message that was put in
                $firstValue = array_shift($array);          //pops off the front of the array which is the mention to the bot and leaves it behind in this variable
                $restOfContent = implode(' ', $array);      //combines the remaining chunks of the array
                $messageContent = $restOfContent;           //just assigning the variable to another name

                    if (is_null($message->author->user->nickname)) {      //if no nickname is given use the username
                        $messageSender = $message->author->user->username;
                    }

                $message->channel->sendMessage("I'm going to Tweet => " . $messageContent);  //send to discord to be printed out
                $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
                $content = $connection->get("account/verify_credentials");
                $statuses = $connection->post("statuses/update", ["status" => $messageContent]);
                }
            }
        }
    });
});

$discord->run();
