<?php
namespace Xaamin\WhatsapiLaravel;

use App;
use Config;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Tmv\WhatsApi\Client;
use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Entity\Phone;
use Tmv\WhatsApi\Message\Action;
use Tmv\WhatsApi\Service\LocalizationService;
use Tmv\WhatsApi\Event\MessageReceivedEvent;
use Tmv\WhatsApi\Message\Received;
use WhatsProt;

class WhatsapiLaravelServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot()
    {
        $loader  = AliasLoader::getInstance();
        $aliases = Config::get('app.aliases');

        if (empty($aliases['WA']))
        {
            $loader->alias('WA', 'Xaamin\WhatsapiLaravel\Facades\WhatsapiFacade');
        }

        $this->publishes([
            __DIR__.'/Config/whatsapi-laravel.php' => config_path('whatsapi-laravel.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //Set up how the create the Identity when one is asked to be created
        $this->app->bindShared('Tmv\WhatsApi\Entity\Identity', function ()
        {
            //Setup Account details.
            $account   = Config::get("whatsapi-laravel.account");
            $nickname  = Config::get("whatsapi-laravel.accounts.$account.nickname");
            $number    = Config::get("whatsapi-laravel.accounts.$account.number");
            $password  = Config::get("whatsapi-laravel.accounts.$account.password");
            $userIdent = Config::get("whatsapi-laravel.accounts.$account.identity");

            // Initializing client
            // Creating a service to retrieve phone info
            $localizationService = new LocalizationService();

            // Creating a phone object...
            $phone = new Phone($number);
            // Injecting phone properties
            $localizationService->injectPhoneProperties($phone);
            // Creating identity
            $identity = new Identity();

            $identity
            ->setPhone($phone)
                ->setNickname($nickname)
                ->setPassword($password)
                ->setIdentityToken($userIdent);

            return $identity;
        });


        //Set up how the create TMV's Client Object when one is asked to be created (which needs the Identity)
        $this->app->bindShared('Tmv\WhatsApi\Client', function ()
        {
            $debug             = Config::get("whatsapi-laravel.debug");
            $account           = Config::get("whatsapi-laravel.account");
            $number            = Config::get("whatsapi-laravel.accounts.$account.number");
            $nextChallengeFile = Config::get("whatsapi-laravel.challenge-dir") . "/" . $number . "-next-challenge.dat";

            $identity = App::make('Tmv\WhatsApi\Entity\Identity');
            // Initializing client
            $client = new Client($identity);
            $client->setChallengeDataFilepath($nextChallengeFile);


            // Attaching events...
            //TODO I don't want to attach events here, but this is just for demo.
            $client->getEventManager()->attach(
                'onMessageReceived',
                function (MessageReceivedEvent $e)
                {
                    $message = $e->getMessage();
                    echo str_repeat('-', 80) . PHP_EOL;
                    echo '** MESSAGE RECEIVED **' . PHP_EOL;
                    echo sprintf('From: %s', $message->getFrom()) . PHP_EOL;
                    if ($message->isFromGroup())
                    {
                        echo sprintf('Group: %s', $message->getGroupId()) . PHP_EOL;
                    }
                    echo sprintf('Date: %s', $message->getDateTime()->format('Y-m-d H:i:s')) . PHP_EOL;

                    if ($message instanceof Received\MessageText)
                    {
                        echo PHP_EOL;
                        echo sprintf('%s', $message->getBody()) . PHP_EOL;
                    } elseif ($message instanceof Received\MessageMedia)
                    {
                        echo sprintf('Type: %s', $message->getMedia()->getType()) . PHP_EOL;
                    }
                    echo str_repeat('-', 80) . PHP_EOL;
                }
            );


            // Debug events
            if ($debug)
            {
                $client->getEventManager()->attach(
                    'node.received',
                    function (Event $e)
                    {
                        $node = $e->getParam('node');
                        echo sprintf("\n--- Node received:\n%s\n", $node);
                    }
                );

                $client->getEventManager()->attach(
                    'node.send.pre',
                    function (Event $e)
                    {
                        $node = $e->getParam('node');
                        echo sprintf("\n--- Sending Node:\n%s\n", $node);
                    }
                );
            }

            return $client;
        });


        //Which concret implementation will we use when an SMSInterface is asked for? User can pick in the config file.
        $this->app->bindShared('Xaamin\WhatsapiLaravel\Repository\SMSMessageInterface', function ()
        {
            $fork = strtoupper(Config::get('whatsapi-laravel.fork'));

            switch ($fork)
            {
                case ($fork == 'MGP25'):
                    return App::make('Xaamin\WhatsapiLaravel\Clients\WhatsapiMGP25Client');
                    break;
                default:
                    return App::make('Xaamin\WhatsapiLaravel\Clients\WhatsapiTMVClient');
                    break;
            }
        });


        //Set up how the create the WhatsProt object when using MGP25 fork
        $this->app->bindShared('WhatsProt', function ()
        {
            //Setup Account details.
            $debug     = Config::get("whatsapi-laravel.debug");
            $account   = Config::get("whatsapi-laravel.account");
            $nickname  = Config::get("whatsapi-laravel.accounts.$account.nickname");
            $number    = Config::get("whatsapi-laravel.accounts.$account.number");
            $userIdent = Config::get("whatsapi-laravel.accounts.$account.identity");
            $nextChallengeFile = Config::get("whatsapi-laravel.challenge-dir") . "/" . $number . "-NextChallenge.dat";

            $whatsProt =  new WhatsProt($number, $userIdent, $nickname, $debug);
            $whatsProt->setChallengeName($nextChallengeFile);

            return $whatsProt;
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
