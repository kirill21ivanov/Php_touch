<?php

namespace ThisMadCat;

use pocketmine\scheduler\Task;
use ThisMadCat\STMC;
use pocketmine\math\Vector3;
use pocketmine\command\ConsoleCommandSender;

Class Forms extends Task{

  private $p, $time;

  public function __construct(STMC $plugin) {
    $this->p = $plugin;
  }

  function onRun($currentTick): void{
    $this->time++;
    $time = date('H');
    if ($time == 0){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 18000");
    }elseif ($time == 24){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 18500");
    }elseif ($time == 1){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 19000");
    }elseif ($time == 2){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 19500");
    }elseif ($time == 3){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 20000");
    }elseif ($time == 4){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 20500");
    }elseif ($time == 5){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 21000");
    }elseif ($time == 6){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 21500");
    }elseif ($time == 7){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 22500");
    }elseif ($time == 8){
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 0");
    }elseif ($time == 9) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 2000");
    }elseif ($time == 10) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 4000");
    }elseif ($time == 11) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 5000");
    }elseif ($time == 12) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 6000");
    }elseif ($time == 13) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 7000");
    }elseif ($time == 14) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 8000");
    }elseif ($time == 15) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 9000");
    }elseif ($time == 16) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 10250");
    }elseif ($time == 17) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 11250");
    }elseif ($time == 18) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 12500");
    }elseif ($time == 19) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 13500");
    }elseif ($time == 20) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 14000");
    }elseif ($time == 21) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 15000");
    }elseif ($time == 22) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 16700");
    }elseif ($time == 23) {
      $this->p->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set 17500");
    }
  }

}
