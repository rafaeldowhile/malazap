<?php
namespace Xaamin\WhatsapiLaravel\Clients;

use Config;
use WhatsProt;
use Xaamin\WhatsapiLaravel\Repository\SMSMessageInterface;

class WhatsapiMGP25Client implements SMSMessageInterface{

    /**
     * @var string $password
     */
    protected $password;

    /**
     * @var WhatsProt
     */
    protected $whatsProt;

    /**
     * @param WhatsProt $whatsProt
     */
    public function __construct(WhatsProt $whatsProt)
    {
        $this->whatsProt = $whatsProt;
        $account   = Config::get("whatsapi-laravel.account");
        $this->password = Config::get("whatsapi-laravel.accounts.$account.password");
    }

    public function sendMessage($to, $message)
    {
        $this->connectAndLogin();
        $this->whatsProt->sendMessageComposing($to);
        $this->whatsProt->sendMessage($to, $message);
        $this->whatsProt->pollMessage();
        $this->logoutAndDisconnect();
    }

    public function sendMessageImage($to, $image)
    {
      $this->connectAndLogin();
      $this->whatsProt->sendMessageComposing($to);
      $this->whatsProt->sendMessageImage($to, $image);
      $this->whatsProt->pollMessage();
      $this->logoutAndDisconnect();
    }

    public function checkForNewMessages()
    {
        // TODO: Implement checkForNewMessages() method.
    }

    protected function connectAndLogin()
    {
        $this->whatsProt->connect();
        $this->whatsProt->loginWithPassword($this->password);
    }

    protected function logoutAndDisconnect()
    {
        $this->whatsProt->disconnect();
    }
}
