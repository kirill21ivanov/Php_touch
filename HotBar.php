<?php

namespace ThisMadCat;

use pocketmine\scheduler\Task;
use ThisMadCat\STMC;
use pocketmine\math\Vector3;
use pocketmine\command\ConsoleCommandSender;

Class HotBar extends Task{

  private $p, $time;

  public function __construct(STMC $plugin) {
    $this->p = $plugin;
  }

  function onRun($currentTick): void{
    $this->time++;
    foreach ($this->p->getServer()->getOnlinePlayers() as $player) {
      if($this->p->onlineplayer[$player->getName()] != 0){
        $ra = $this->p->ra->getAll();
        $ros = $this->p->ros->getAll();
        $data = $this->p->cares->getAll();
        $right = str_repeat(" ", 60);
        $t = time() + (3 * 60 * 60);
        $time = gmdate("H:i:s, $t");
        $tt = date("H:i:s");
        $onli = count($this->p->getServer()->getOnlinePlayers());
        $money = $ra["mon"][$this->p->getNick[$player->getName()]];
        $player->sendPopup("" . $right . "§eQweek Rоle-Plаy\n" . $right . "§eУровень Розыска: " . $ros["ros"][$this->p->getNick[$player->getName()]] . "\n" . $right . "§eБаланс: " . $money . "§e\n" . $right . "§eВремя по МСК: " . date("H:i:s") . "\n" . $right ."§eОнлайн: " . $onli . "\n" . $right . "§eУровень: " .  $data[$this->p->getNick[$player->getName()]]['lvl'] . " \n" . $right);
      }
    }
  }

}
