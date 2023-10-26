<?php

namespace ThisMadCat;
use ThisMadCat\STMC;
use pocketmine\Player;

class RegisterForm{

    private $p;
    function __construct(STMC $plug){
        $this->p = $plug;
    }
    function open(Player $pl){
        $f = $this->p->form->createCustomForm(function (Player $pl, $data){
			if($data[0] !== NULL && $data[1] !== NULL){
					//data[0] - ник, data[1] - пароль
					$cfg = $this->p->login->getAll();
					$all = $this->p->ol->getAll();
          $admins = $this->p->admins->getAll();
          $frac = $this->p->cfg->getAll();
          $ip = $pl->getAddress();
          $gamemode = $pl->getGamemode();
						$number = $this->p->getNumberAccount($data[0]);
						if($number == NULL){
							$all["all"] = $all["all"] + 1;
							$this->p->ol->setAll($all);
							$this->p->ol->save();
							$cfg[$all["all"]]["nick"] = $data[0];
							$cfg[$all["all"]]["password"] = $data[1];
							$this->p->login->setAll($cfg);
							$this->p->login->save();
							$pl->setDisplayName($data[0]);
							$pl->setNameTag($data[0]);
							$pl->setImmobile(false);
              $this->p->getNick[$pl->getName()] = $data[0];
              $pl->sendMessage("§aВы успешно зарегистрировались! Ваш ник §3{$this->p->getNick[$pl->getName()]}");
              $data = $this->p->cares->getAll();
              $money = $this->p->ra->getAll();
              $ros = $this->p->ros->getAll();
              if(!isset($data[$this->p->getNick[$pl->getName()]]["exp"])){
                $data[$this->p->getNick[$pl->getName()]]['exp'] = 1;
                $this->p->cares->setAll($data);
                $this->p->cares->save();
                $data[$this->p->getNick[$pl->getName()]]['lvl'] = 1;
                $this->p->cares->setAll($data);
                $this->p->cares->save();
                $money["mon"][$this->p->getNick[$pl->getName()]] = 100;
                $this->p->ra->setAll($money);
                $this->p->ra->save();
                $ros["ros"][$this->p->getNick[$pl->getName()]] = 0;
                $this->p->ros->setAll($ros);
                $this->p->ros->save();
                $data[$this->p->getNick[$pl->getName()]]['car'] = 0;
                $data[$this->p->getNick[$pl->getName()]]['fuel'] = 0;
                $data[$this->p->getNick[$pl->getName()]]['lock'] = "open";
                $data[$this->p->getNick[$pl->getName()]]['zak'] = 1;
                $data[$this->p->getNick[$pl->getName()]]['promo'] = 0;
                $data[$this->p->getNick[$pl->getName()]]['pills'] = 70;
                $data[$this->p->getNick[$pl->getName()]]['apteka'] = 0;
                $data[$this->p->getNick[$pl->getName()]]['nar'] = 0;
                $data[$this->p->getNick[$pl->getName()]]['gunlic'] = "Нету";
                $data[$this->p->getNick[$pl->getName()]]['druglic'] = "Нету";
                $data[$this->p->getNick[$pl->getName()]]['let'] = "Нету";
                $data[$this->p->getNick[$pl->getName()]]['pol'] = "Нету";
                $data[$this->p->getNick[$pl->getName()]]['calldalans'] = 0;
                $data[$this->p->getNick[$pl->getName()]]['call'] = "Нету";
                $this->p->cares->setAll($data);
                $this->p->cares->save();
                $p->sendMessage(" §7Для дальнейшего §3ознакомления§7 c сервером и его правилами пропиши §c/rules§7. Помни, не знание правил не освобождает вас от ответственности. \n §a*§7мысли§a*§7 Бр-р-р.... Холодная ночь была, даже нет §3телефона§7 позвонить родным чтобы помогли, но и денег толком нету, все сгорело в той машине, хотя стой, я нашел 100§a$ §7у себя в кармане, я могу купить себе телефон, может свяжусь с кем-то. Ближайший магазин в городе, ну тогда пойду туда.");
                $this->p->cares->setAll($data);
                $this->p->cares->save();
                $this->p->cares->setAll($data);
                $this->p->cares->save();
                $this->onlineplayer[$this->p->getNick[$pl->getName()]] = 1;
              foreach($this->p->getServer()->getOnlinePlayers() as $play){
                if (isset($admins["admin"][$play->getName()])) {
                  $play->sendMessage("§7[§eА§7]§7Приветсвуем нового игрока на сервере - §3{$this->p->getNick[$pl->getName()]}§7 - XP §3{$pl->getName()}\n §7[§cIP§7] §3" .$ip. "  §7[§eGM§7] §3" .$gamemode. "");
                }
              }
						} else $pl->kick("Такой аккаунт зарегистрирован!");
			} else $this->open($pl);
        });
        $f->setTitle("§bЗарегистрируйтесь");
        $f->addInput("§aНик", "Например: Ivan_Ivanov");
        $f->addInput("§aПароль", "Пароль к аккаунту");
        $f->sendToPlayer($pl);
        return $f;
    }
}
