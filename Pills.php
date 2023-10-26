<?php
namespace ThisMadCat;
use pocketmine\scheduler\Task;
use ThisMadCat\STMC;
use pocketmine\math\Vector3;
use pocketmine\entity\Effect;

Class Pills extends Task{

  private $p, $time;

  public function __construct(STMC $plugin) {
    $this->p = $plugin;
  }

  function onRun($currentTick) {
    $this->time++;
    $data = $this->p->cares->getAll();
    if ($this->time == 180) {
      foreach ($this->p->getServer()->getOnlinePlayers() as $player) {
        $data[$player->getName()]['pills']--;
        $this->p->cares->setAll($data);
        $this->p->cares->save();
        if ($data[$player->getName()]['pills'] <= 40) {
          $player->sendMessage('§7У вас начинается §3головная боль§7.');
          $player->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(9), 20 * 60, 2));
        }
      }
    }
    foreach ($this->p->getServer()->getOnlinePlayers() as $player) {
      if (isset($this->p->do[$player->getName()])) {
        if ($this->p->do[$player->getName()] == 1){
          if ($this->p->times[$player->getName()] !== 0){
            $this->p->times[$player->getName()]--;
          }else{
            $player->sendMessage('§5Вам закончили делать укол.');
            $player->setImmobile(true);
            $data[$player->getName()]['pills'] = 80;
            $this->p->cares->setAll($data);
            $this->p->cares->save();
            unset($this->p->do[$player->getName()]);
            }
          }
        }
      }
    }
  }
