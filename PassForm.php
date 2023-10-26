<?php

namespace ThisMadCat;
use ThisMadCat\STMC;
use pocketmine\Player;

class PassForm{

		    private $plug;
		    function __construct(STMC $plug){
		        $this->plug = $plug;
		    }
		    function openps(Player $pl){
						$f = $this->plug->form->createCustomForm(function (Player $pl, $data){
					$a = $this->plug->cares->getAll();
					$ar = $this->plug->pass->getAll();
					if($data[0] !== NULL){
							if($data[0] != ""){
							  $pl->sendMessage("§l§3Вы §l§aуспешно §l§3сделали паспорт");
								$a[$pl->getName()]["let"] = (int)$data[0];
							  $this->plug->cares->setAll($a);
							  $this->plug->cares->save();
							  $ar["pasport"][$pl->getName()] = "Есть";
							  $this->plug->pass->setAll($ar);
							  $this->plug->pass->save();
							  if($data[1] != "1"){
								  $a[$pl->getName()]["pol"] = "Мужской";
								  $this->plug->cares->setAll($a);
								 	$this->plug->cares->save();
								}elseif($data[1] != "0"){
									$a[$pl->getName()]["pol"] = "Женский";
									$this->plug->cares->setAll($a);
									$this->plug->cares->save();
							  }
							}else $pl->sendMessage("§7Вы нечего не написали");
					}
		        });
						$f->setTitle("§3Паспортный стол");
						$f->addSlider("§7Выберите возраст", 16, 27, 1, 16);
					  $f->addDropdown("§7Выберите пол", ["Мужской", "Женский"], 1);
		        $f->sendToPlayer($pl);
		        return $f;
		    }
		}
