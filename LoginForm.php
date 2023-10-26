<?php

namespace ThisMadCat;
use ThisMadCat\STMC;
use pocketmine\Player;

class LoginForm{

    private $plug;
    function __construct(STMC $plug){
        $this->plug = $plug;
    }
    function open(Player $pl){
        $f = $this->plug->form->createCustomForm(function (Player $pl, $data){
			if($data[0] !== NULL && $data[1] !== NULL){
					//data[0] - ник, data[1] - пароль
					$cfg = $this->plug->login->getAll();
          $admins = $this->plug->admins->getAll();
          $frac = $this->plug->cfg->getAll();
          $ip = $pl->getAddress();
          $gamemode = $pl->getGamemode();
						$number = $this->plug->getNumberAccount($data[0]);
					if($number !== NULL){
						if($data[0] == $cfg[$number]["nick"] && $data[1] == $cfg[$number]["password"]){
							$pl->sendMessage("§aВы успешно авторизировались!");
							$pl->setDisplayName($data[0]);
							$pl->setNameTag($data[0]);
							$pl->setImmobile(false);
              $this->plug->getNick[$pl->getName()] = $data[0];
              $this->onlineplayer[$this->plug->getNick[$pl->getName()]] = 1;
              foreach($this->plug->getServer()->getOnlinePlayers() as $play){
                if (isset($admins["admin"][$play->getName()])) {
                  $play->sendMessage("§7[§eА§7]§7Приветсвуем нового игрока на сервере - §3{$this->plug->getNick[$pl->getName()]}§7 - XP §3{$pl->getName()}\n §7[§cIP§7] §3" .$ip. "  §7[§eGM§7] §3" .$gamemode. "");
                }
              }
						} else $pl->kick("Неверный ник или пароль");
					} else $pl->kick("Такого пользователя не существует!");
			} else $this->open($pl);
        });
        $f->setTitle("§bАвторизируйтесь");
        $f->addInput("§aНик", "Например: Ivan_Ivanov");
        $f->addInput("§aПароль", "Пароль к аккаунту");
        $f->sendToPlayer($pl);
        return $f;
    }
}
