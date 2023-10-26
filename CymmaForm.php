<?php

namespace ThisMadCat;
use ThisMadCat\STMC;
use pocketmine\Player;

class CymmaForm{

	    private $plug;
	    function __construct(STMC $plug){
	        $this->plug = $plug;
	    }
	    function open(Player $pl){
	        $f = $this->plug->form->createSimpleForm(function (Player $pl, $data){
					$ra = $this->plug->ra->getAll();
					$bank = $this->plug->bank->getAll();
					$gm = $ra["mon"][$pl->getName()];
				if($data[0] !== NULL){
					switch($data){
						case 0:
						  $pl->sendMessage("§7Вы §3успешно§7 вышли.");
								break;
					}
					  $pl->sendMessage("§l§3Вы §l§aуспешно §l§3проверели сумму на карте");
						$pl->sendMessage("\n §7Ваш счёт дипозита состовляет §b" . $bank[$pl->getName()]["dipozit"]);
				}
	        });
	        $f->setTitle("§3Банкомат");
					$bank = $this->plug->bank->getAll();
					$f->setContent("§7§lВаш счёт карты (§l§3qweek§7§l) состовляет  §7" . $bank[$pl->getName()]["kol"]. "§a$ \n \n§7§lВаш счёт §3§lдепозита§7§l состовляет §7" . (int)$bank[$pl->getName()]["dipozit"]. "§a$");
					$f->addButton("§l§cВыход");
	        $f->sendToPlayer($pl);
	        return $f;
	    }
	}
