<?php

namespace ThisMadCat;

use pocketmine\scheduler\Task;
use ThisMadCat\STMC;
use pocketmine\entity\Effect;
use pocketmine\math\Vector3;

Class Timer extends Task{

  private $p, $time;

  public function __construct(STMC $plugin) {
    $this->p = $plugin;
  }

  function onRun($currentTick): void{
    $this->time++;
    if ($this->time == 60) {
      $data = $this->p->cares->getAll();
      foreach ($this->p->getServer()->getOnlinePlayers() as $player) {
        if ($data[$player->getName()]['fuel'] > 0 && isset($this->p->inCar[$player->getName()])) {
          $data[$player->getName()]['fuel']--;
          $this->p->cares->setAll($data);
          $this->p->cares->save();
        }
      }
    }
    foreach ($this->p->getServer()->getOnlinePlayers() as $player) {
      if ($this->p->sitOn[$player->getName()] != NULL && $this->p->sitOn[$player->getName()] != $player) {
        $player->teleport($this->p->sitOn[$player->getName()]->asVector3());
      }
      if ($this->p->job[$player->getName()] == 'avto') {
        if ($player->distance($this->p->destination[$player->getName()]) <= 2) {
          $data = $this->p->avto->getAll();
          $player->sendMessage('§7Ты достиг чекпоин §3№'.$this->p->stage[$player->getName()]);
          $this->p->stage[$player->getName()]++;
          if ($this->p->stage[$player->getName()] < 22) {
            $this->p->destination[$player->getName()] = new Vector3($data['busstops'][$this->p->stage[$player->getName()]]['x'], $data['busstops'][$this->p->stage[$player->getName()]]['y'], $data['busstops'][$this->p->stage[$player->getName()]]['z']);
          } else {
            $player->sendMessage('§eТы закончил курс вождения и получил права!');
            $this->p->job[$player->getName()] = NULL;
            $datas = $this->p->prava->getAll();
            $this->p->destination[$player->getName()] = NULL;
            $this->p->stage[$player->getName()] = 0;
            $player->removeEffect(Effect::getEffect(1)->getId());
            $datas["prava"][$player->getName()] = "Есть";
            $this->p->prava->setAll($datas);
            $this->p->prava->save();
           }
        }else{$player->sendPopup('§a'.(int) $player->distance($this->p->destination[$player->getName()]).' м.');}
      }
    }
  }

}
