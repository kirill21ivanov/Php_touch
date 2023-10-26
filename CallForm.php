<?php

namespace ThisMadCat;
use ThisMadCat\STMC;
use pocketmine\Player;

class CallForm{

		    private $plug;
		    function __construct(STMC $plug){
		        $this->plug = $plug;
		    }
		    function phone(Player $pl){
		        $f = $this->plug->form->createCustomForm(function (Player $pl, $data){
						$r = $this->plug->cares->getAll();
						$gm = $r[$pl->getName()]["calldalans"];
						$player = $this->plug->getServer()->getNick[$data[0]->getName()];
					if($data[0] !== NULL){
						if($player != NULL){
							if($gm >= 4){
								if($data[0] != ""){
									if($this->plug->getcall[$data[0]->getName()] == 0){
										$pl->sendMessage("§7Вы §aуспешно §7звоните {$this->plug->getNick[$data[0]->getName()]}");
										$sen->sendMessage("§7У вас звонок от {$this->plug->getNick[$pl->getName()]}");
										$this->plug->getcall[$pl->getName()] = 1;
										$this->plug->opencall[$pl->getName()] = 0;
										$this->plug->getcall[$pl->getName()] = $player->getName();
										$this->plug->getcall[$player->getName()] = 1;
										$this->plug->getcall[$player->getName()] = $pl->getName();
									}else $pl->sendMessage("§7Данный игрок уже с кем-то общаеться");
								}else $pl->sendMessage("§7Вы нечего не написали");
							}else $pl->sendMessage("§7Пополните баланс телефона");
						}else $pl->sendMessage("§7Данный игрок не найден");
					} else $this->open($pl);
		        });
		        $f->setTitle("§3Телефон");
		        $f->addInput("§cНапишите Nick Полнустью!", "Например: Jon_Ivanov");
		        $f->sendToPlayer($pl);
		        return $f;
		    }
		}
