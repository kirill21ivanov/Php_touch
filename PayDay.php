<?php
namespace ThisMadCat;
use pocketmine\scheduler\Task;
use ThisMadCat\STMC;
use pocketmine\math\Vector3;

Class PayDay extends Task{

  private $p, $time;

  public function __construct(STMC $plugin) {
    $this->p = $plugin;
  }

  function onRun($currentTick): void{
    $this->time++;
    if ($this->time == 1800) {
      $data = $this->p->cares->getAll();
      $bank = $this->p->bank->getAll();
      $cfg = $this->p->cfg->getAll();
      $mon = $this->p->ra->getAll();
      foreach ($this->p->getServer()->getOnlinePlayers() as $p) {
        $pl = $p->getPlayer();
        $name = $pl->getName();
        $p->sendMessage("§f(§cQweek Role Play§f) §fЗа §e30 §fминут игры на сервере вы получили §c1 ехр§f!");
        $data[$p->getName()]['exp']++;
        $this->p->cares->setAll($data);
        $this->p->cares->save();
        $data[$p->getName()]['zak'] += 2;
        $this->p->cares->setAll($data);
        $this->p->cares->save();
        $dep = $bank[$p->getName()]["dipozit"];
        $de = $bank[$p->getName()]["kol"];
        if (isset($cfg["army"][$p->getName()])) {
          if($cfg["army"][$p->getName()] == 2){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 8.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 8000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 3){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 13.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 13000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 4){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 14.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 14000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 5){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 17.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 17000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 6){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 19.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 19000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 7){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 22.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 22000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 8){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 23.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 23000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 9){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 26.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 26000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 10){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 36.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 36000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["army"][$p->getName()] == 1){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 6.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 6000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

        }elseif(isset($cfg["hospital"][$p->getName()])) {
          if($cfg["hospital"][$p->getName()] == 2){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 8.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 8000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 3){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 13.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 13000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 4){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 14.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 14000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 5){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 17.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 17000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 6){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 19.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 19000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 7){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 22.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 22000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 8){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 23.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 23000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 9){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 26.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 26000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 10){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 36.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 36000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["hospital"][$p->getName()] == 1){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 6.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 6000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

        }elseif (isset($cfg["massmedia"][$p->getName()])) {
          if($cfg["massmedia"][$p->getName()] == 2){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 8.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 8000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 3){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 13.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 13000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 4){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 14.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 14000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 5){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 17.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 17000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 6){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 19.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 19000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 7){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 22.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 22000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 8){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 23.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 23000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 9){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 26.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 26000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 10){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 36.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 36000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["massmedia"][$p->getName()] == 1){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 6.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 6000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

        }elseif (isset($cfg["police"][$p->getName()])) {
          if($cfg["police"][$p->getName()] == 2){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 8.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 8000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 3){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 13.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 13000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 4){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 14.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 14000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 5){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 17.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 17000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 6){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 19.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 19000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 7){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 22.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 8000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 8){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 23.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 23000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 9){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 26.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 26000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 10){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 36.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 36000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["police"][$p->getName()] == 1){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 6.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 6000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

        }elseif (isset($cfg["pravo"][$p->getName()])) {
          if($cfg["pravo"][$p->getName()] == 2){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 8.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 8000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 3){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 13.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 13000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 4){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 14.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 14000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 5){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 17.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 17000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 6){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 19.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 19000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 7){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 22.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 22000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 8){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 23.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 23000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 9){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 26.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 26000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 10){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 36.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 36000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

          if($cfg["pravo"][$p->getName()] == 1){
            $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §f Ваша зарплатата состовляет: 6.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
            $bank[$p->getName()]["kol"] += 6000;
            $this->p->bank->setAll($bank);
            $this->p->bank->save();
          }

        }elseif (!isset($cfg["fsbi"][$p->getName()])){
          $p->sendMessage("§a__________Банковский чек__________ \n §f Депозит состовляет: §7" . (int)$dep . "§a$ §f \n §f Текущая сумма в банке: §7{$de}§a$ §f \n §fПодобие о безработие: 3.000$ \n §f В данный момент у вас " . $data[$p->getName()]['lvl'] ."-й уровень и " . $data[$p->getName()]['exp'] . "/8 респектов \n §a__________Банковский чек__________");
          $bank[$p->getName()]["kol"] += 3000;
          $this->p->bank->setAll($bank);
          $this->p->bank->save();
        }
      }
    }

    foreach ($this->p->getServer()->getOnlinePlayers() as $p) {
      $this->p->opencall[$p->getName()] = 0;
      if ($this->p->opencall[$p->getName()] == 1) {
        $money = $this->p->ra->getAll();

      }
    }


    foreach ($this->p->getServer()->getOnlinePlayers() as $p) {
      $data = $this->p->cares->getAll();
      $bank = $this->p->bank->getAll();
      if($data[$p->getName()]['exp'] == 7){
        $data[$p->getName()]['exp'] -= 7;
        $this->p->cares->setAll($data);
        $this->p->cares->save();
        $data[$p->getName()]['lvl']++;
        $data[$p->getName()]['let']++;
        $this->p->cares->setAll($data);
        $this->p->cares->save();
        $bank[$p->getName()]["dipozit"] *= 1.1;
        $this->p->bank->setAll($bank);
        $this->p->bank->save();
        $p->sendMessage("§f(§cQweek Role Play§f)§b Ты перешел на новый §aуровень§f!");
      }
    }
  }

}
