<?php

namespace ThisMadCat;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\command\CommandExecutor;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\block\Bed;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\UnknownBlock;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\utils\Config;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerAnimationEvent;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerEditBookEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\player\PlayerTransferEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\form\FormValidationException;
use pocketmine\inventory\CraftingGrid;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\PlayerCursorInventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\CraftingTransaction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Consumable;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pocketmine\item\Item;
use pocketmine\item\WritableBook;
use pocketmine\item\WrittenBook;
use pocketmine\lang\TextContainer;
use pocketmine\lang\TranslationContainer;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\metadata\MetadataValue;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\PlayerNetworkSessionAdapter;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\AvailableEntityIdentifiersPacket;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\network\mcpe\protocol\BlockPickRequestPacket;
use pocketmine\network\mcpe\protocol\BookEditPacket;
use pocketmine\network\mcpe\protocol\ChunkRadiusUpdatedPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\NetworkChunkPublisherUpdatePacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\network\mcpe\protocol\types\CommandData;
use pocketmine\network\mcpe\protocol\types\CommandEnum;
use pocketmine\network\mcpe\protocol\types\CommandParameter;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\VerifyLoginTask;
use pocketmine\network\SourceInterface;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionAttachmentInfo;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\Plugin;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\tile\ItemFrame;
use pocketmine\tile\Spawnable;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\timings\Timings;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;

class STMC extends PluginBase implements Listener{


    public $admins, $bans, $store, $kpp, $players, $sellgunmon, $cofig, $ud, $settings, $prava, $med, $arrest, $ram, $sellgun;

    public $sitOn, $cares, $inCar, $ao, $subjects, $tag, $ra, $gun;

    public $programmist = array(); // Клик Ган

    public $number = array(); // Клик ган

    public $programmisti = array(); // Клик наркотиков

    public $numberi = array(); // Клик наркотиков

    public $ros; // Розыск

    public $cfg; // Фракции

    public $bank; // Фракции

    public $promo; // Промокод

    private $easyforms; //Формы

    public $do, $times; //Состояние игрока

    public $job, $stage, $destination;

    public $ferma = array(); // Клик фермы

    public $numberferma = array(); // Клик фермы

    public $cuff, $tie;

    public $form;

    public $prg, $prd, $pr; //лицензии


    public $login;

    public $ol;

    public $onlineplayer;

    public $getNick = array();

    public $getcall = array();

    public $opencall = array();

    public $getPlayercall = array();

    function onEnable()
    {
        $folder = $this->getDataFolder();
        if (!is_dir($folder))
            @mkdir($folder);
        $this->saveResource('fractions.yml');
        $this->saveResource('admins.yml');
        $this->saveResource('ban.yml');
        $this->saveResource('sc.yml');
        $this->saveResource('kpp.yml');
        $this->saveResource('policeros.yml');
        $this->saveResource('players.yml');
        $this->saveResource('cofig.yml');
        $this->saveResource('users.yml');
        $this->saveResource('ra.yml');
        $this->saveResource('arrest.yml');
        $this->saveResource('sellgunmon.yml');
        $this->saveResource('med.yml');
        $this->saveResource('prava.yml');
        $this->saveResource('pass.yml');
        $this->saveResource('setcar.yml');
        $this->saveResource('cares.yml');
        $this->saveResource('promo.yml');
        $this->saveResource('bank.yml');
        $this->getLogger()->info(TextFormat::DARK_GREEN . 'Сборка сервера Qweek Role Play by ThisMadCat загружена');
        $this->getLogger()->info(TextFormat::DARK_GREEN . 'Автор плагина - vk.com/kivanov20040');
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new Forms($this), 20);
        $this->getScheduler()->scheduleRepeatingTask(new Timer($this), 20);
        $this->getScheduler()->scheduleRepeatingTask(new HotBar($this), 20);
        $this->getScheduler()->scheduleRepeatingTask(new PayDay($this), 20);
        $this->getScheduler()->scheduleRepeatingTask(new Pills($this), 20);
        $this->form = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $this->avto = new Config($folder . "avto.yml", Config::YAML);
        $this->bank = new Config($folder . "bank.yml", Config::YAML);
        $this->cfg = new Config($folder . "fractions.yml", Config::YAML);
        $this->promo = new Config($folder . "promo.yml", Config::YAML);
        $this->admins = new Config($folder . "admins.yml", Config::YAML);
        $this->bans = new Config($folder . "ban.yml", Config::YAML);
        $this->store = new Config($folder . "sc.yml", Config::YAML);
        $this->kpp = new Config($folder . "kpp.yml", Config::YAML);
        $this->ros = new Config($folder . "policeros.yml", Config::YAML);
        $this->players = new Config($folder . "players.yml", Config::YAML);
        $this->cofig = new Config($folder . "cofig.yml", Config::YAML);
        $this->ud = new Config($folder . "users.yml", Config::YAML);
        $this->ra = new Config($folder . "ra.yml", Config::YAML);
        $this->arrest = new Config($folder . "arrest.yml", Config::YAML);
        $this->sellgunmon = new Config($folder . "sellgunmon.yml", Config::YAML);
        $this->prava = new Config($folder . "prava.yml", Config::YAML);
        $this->med = new Config($folder . "med.yml", Config::YAML);
        $this->pass = new Config($folder . "pass.yml", Config::YAML);
        $this->setcar = new Config($folder . "setcar.yml", Config::YAML);
        $this->cares = new Config($folder . "cares.yml", Config::YAML);
        $this->login = new Config($this->getDataFolder()."accounts.yml");
        $this->ol = new Config($this->getDataFolder()."allaccounts.yml");           //В конфиге allaccounts не изменяй ничего!!
        $all = $this->ol->getAll();
        if(!isset($all["all"])){
          $all["all"] = 0;
          $this->ol->setAll($all);
          $this->ol->save();
        }
      }


      function onJoinRegisterLogin(PlayerJoinEvent $e){
        $p = $e->getPlayer();
        $n = $p->getName();
        $cfg = $this->login->getAll();
        $this->login($p);
        $p->setImmobile(true);
      }

      function login(Player $pl){
        $f = $this->form->createSimpleForm(function (Player $pl, $data){
          if($data !== NULL){
              //data[0] - ник, data[1] - пароль
              $cfg = $this->cfg->getAll();
                switch($data){
                  case 0:
                    $login = new LoginForm($this);
                    $login->open($pl);
                      break;
                  case 1:
                    $reg = new RegisterForm($this);
                    $reg->open($pl);
                      break;
                }
          } else $this->open($pl);
            });
            $f->setTitle("§eВыберите");
            $f->addButton("§aАвторизация");
            $f->addButton("§aРегистрация");
            $f->sendToPlayer($pl);
            return $f;
      }

      function getNumberAccount($name){
        foreach($this->login->getAll() as $key => $data){
          if($data["nick"] == $name){
            return $key;
          }
        }
      }

      function getIssetNumberAccount($number){  //0 - истина, 1 - ложь
        $cfg = $this->login->getAll();
        foreach($this->login->getAll() as $key){
          if(isset($cfg[$number])){
            return 0;
          } else return 1;
        }
      }

      function onOnlinePlayer(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        $this->onlineplayer[$player->getName()] = 0;
      }

      function onTellNoy(PlayerCommandPreprocessEvent $e){
        $cmd = $e->getMessage();
        if(strtolower($cmd) == "/tell"){
          $e->getPlayer()->sendMessage("§c♣§7Данная команда §3не рабоатет§7 на сервере§c♣");
         $e->setCancelled();
        }
      }

      function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $data = $this->cares->getAll();
        if (isset($this->inCar[$player->getName()]) && $data[$player->getName()]['fuel'] == 0) {
          $player->sendPopup('§bУ вас нету бензина.');
          $event->setCancelled();
        }
      }

      function onRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        if (isset($this->inCar[$player->getName()]))
        unset($this->inCar[$player->getName()]);
      }
      //всё

      function onGunsJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->programmist[$player->getName()] = 0;
        $this->number[$player->getName()] = 1;
      }

      function onNarkoJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->programmisti[$player->getName()] = 0;
        $this->numberi[$player->getName()] = 1;
      }

      function onCuffTieJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->tie[$player->getName()] = "off";
        $this->cuff[$player->getName()] = "off";
      }

      function onCall(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->call[$player->getName()] = 0;
        $this->callpl[$player->getName()] = 0;
        $this->calldal[$player->getName()] = 0;
      }

      function onBankDipositandKilicicic(PlayerJoinEvent $e){ //Банк
        $bank = $this->bank->getAll();
        $player = $e->getPlayer();
        if(!isset($bank[$player->getName()])){
          $bank[$player->getName()]["o"] = "Нету";
          $bank[$player->getName()]["kol"] = 0;
          $bank[$player->getName()]["dipozit"] = 0;
          $this->bank->setAll($bank);
          $this->bank->save();
        }
      }

      function onPravaJoin(PlayerJoinEvent $e){
        $ra = $this->ra->getAll();
        $player = $e->getPlayer();
        $cfg = $this->cfg->getAll();
        $ros = $this->ros->getAll();
        $med = $this->med->getAll();
        $prava = $this->prava->getAll();
        if(!isset($prava["prava"][$player->getName()])){
          $prava["prava"][$player->getName()] = "Нету";
          $this->prava->setAll($prava);
          $this->prava->save();
        }
      }

      function onPassJoin(PlayerJoinEvent $e){
        $ra = $this->ra->getAll();
        $player = $e->getPlayer();
        $cfg = $this->cfg->getAll();
        $ros = $this->ros->getAll();
        $med = $this->med->getAll();
        $prava = $this->prava->getAll();
        $pass = $this->pass->getAll();
        if(!isset($pass["pasport"][$player->getName()])){
          $pass["pasport"][$player->getName()] = "Нету";
          $this->pass->setAll($pass);
          $this->pass->save();
        }
      }

      function onMedCardJoin(PlayerJoinEvent $e){
        $ra = $this->ra->getAll();
        $player = $e->getPlayer();
        $cfg = $this->cfg->getAll();
        $ros = $this->ros->getAll();
        $med = $this->med->getAll();
        if(!isset($med[$player->getName()])){
          $med[$player->getName()]["o"] = "Нету";
          $med[$player->getName()]["sos"] = "Неопределённое";
          $this->med->setAll($med);
          $this->med->save();
        }
      }

      function onWarnJoin(PlayerJoinEvent $e){
        $ra = $this->ra->getAll();
        $player = $e->getPlayer();
        $cfg = $this->cfg->getAll();
        $ros = $this->ros->getAll();
        if(!isset($ra["warn"][$player->getName()])){
          $cfg["warn"][$player->getName()] = 0;
          $this->cfg->setAll($cfg);
          $this->cfg->save();
        }
      }

      function onSellgunmonJoin(PlayerJoinEvent $e){
        $se = $this->sellgunmon->getAll();
        $player = $e->getPlayer();
        if(!isset($se["sellgunmon"][$player->getName()])){
          $se["sellgunmon"][$player->getName()] = 0;
          $this->sellgunmon->setAll($se);
          $this->sellgunmon->save();
        }
      }



      function onLol(BlockBreakEvent $e){ //Приват
        $p = $e->getPlayer();
        $a = $this->admins->getAll();
        if(isset($a["admin"][$p->getName()])) {
          if ($a["admin"][$p->getName()] < 3){
            $p->sendMessage("§7Твой lvl мал чтобы ставить§c!");
            $e->setCancelled(true);
          }
        }else{$p->sendPopup("");}
      }

      function onKek(BlockPlaceEvent $e){ // Приват
        $p = $e->getPlayer();
        $a = $this->admins->getAll();
        if (isset($a["admin"][$p->getName()])) {
          if ($a["admin"][$p->getName()] < 3){
            $p->sendMessage("§7Твой lvl мал чтобы ставить§c!");
            $e->setCancelled(true);
          }
        }else{$p->sendPopup("");}
      }

       function Drop(PlayerDropItemEvent $e){
        $p = $e->getPlayer();
        $g = $p->getGamemode();
        if ($g == 1){$e->setCancelled(true);}
        $p->sendMessage("§7Нельзя выбрасывать вещи§c!");
      }

       function OnGameModeChange(PlayerGameModeChangeEvent $e){
        $p = $e->getPlayer();
        $p->getInventory()->clearAll();
        $p->getArmorInventory()->clearAll();
        return false;
      }

       function onCommandi(PlayerCommandPreprocessEvent $e){
        $cmd = $e->getMessage();
        if(strtolower($cmd) == "/ban"){
          $e->getPlayer()->sendMessage("§7Использование: /aban <ник> <причина>");
          $e->setCancelled();
        }

        if(strtolower($cmd) == "/kick"){
          $e->getPlayer()->sendMessage("§7Использование: /akick <ник> <причина>");
          $e->setCancelled();
        }

        if(strtolower($cmd) == "/mi"){
          $e->getPlayer()->sendMessage("§7Использование: /me <текст>");
          $e->setCancelled();
        }
      }

       function onSvet(PlayerInteractEvent $e){
        $sender = $e->getPlayer();
        $b = $e->getBlock();
        $cfg = $this->cfg->getAll();
        $bx = $b->getX();
        $by = $b->getY();
        $bz = $b->getZ();
        if($bx == 79 && $by == 72 && $bz == -194){
          if (isset($cfg["massmedia"][$sender->getName()])) {
            if ($cfg["massmedia"][$sender->getName()] > 8){
              $this->getServer()->broadcastMessage("§7[§aНовости с СМИ§7] §3" . $sender->getName() . ": §cВнимание! Внимание! §bВ городе поломка ЭС! Свет временно погаснит.");
              $sender->getLevel()->setBlock(new Vector3(-104, 75, -129), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, -127), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-102, 76, -127), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 75, -127), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 75, -143), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-118, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-120, 75, -126), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 75, -117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 75, -122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-121, 76, -118), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 75, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-119, 75, -135), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 75, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 75, -151), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-116, 75, -153), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 76, -145), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-109, 76, -145), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-116, -71, -133), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-117, 82, -139), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-117, 81, -152), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-115, 82, -140), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-115, 82, -153), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-114, 81, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 81, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 81, -121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 81, -116), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 82, -117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-109, 82, -116), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-119, 82, -123), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-119, 82, -117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 81, -132), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 81, -133), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 82, -133), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 82, -132), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 82, -125), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 82, -126), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 81, -129), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 81, -129), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 82, -126), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 82, -125), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 81, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 81, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 82, -139), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 82, -143), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 82, -142), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-127, 86, -122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-133, 86, -122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-127, 86, -117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-133, 86, -117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-44, 96, -214), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-35, 96, -214), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-28, 96, -214), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-27, 96, -214), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-22, 96, -214), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-21, 96, -214), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-16, 96, -214), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-15, 96, -214), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-144, 75, -141), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-142, 75, -139), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-145, 74, -123), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-143, 74, -121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-170, 75, -208), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-174, 74, -213), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-166, 74, -216), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-170, 79, -212), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-171, 79, -212), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-81, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-80, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-79, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-65, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-64, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-63, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-49, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-48, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-47, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-33, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-32, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-31, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-17, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-16, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-15, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-1, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(0, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(1, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(15, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(16, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(17, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(31, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(32, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(33, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(47, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(48, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(49, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(62, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(63, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(64, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(78, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(94, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(110, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(111, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(112, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(126, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(127, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(128, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(142, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(143, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(144, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(158, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(159, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(160, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(174, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(175, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(176, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(190, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(191, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(192, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(207, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(208, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(223, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(224, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(225, 83, -240), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(257, 77, -205), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(258, 77, -205), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(262, 77, -205), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(263, 77, -205), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(88, 75, -181), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 75, -181), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 75, -177), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(97, 75, -177), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 75, -180), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(97, 75, -180), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 75, -186), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 75, -187), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(88, 75, -185), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(81, 75, -190), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(89, 75, -190), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 80, -180), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(85, 80, -187), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-202, 75, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-202, 75, -118), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-193, 75, -114), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-193, 75, -118), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-192, 75, -142), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 74, -23), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-118, 74, -23), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-118, 74, -34), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-116, 74, -21), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-116, 74, -13), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(76, 74, -65), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 74, -65), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(76, 74, -72), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 74, -72), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(129, 74, -92), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(133, 74, -92), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(137, 74, -92), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(137, 74, -90), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(145, 74, -90), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(147, 74, -92), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -92), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -97), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -101), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -105), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -109), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -113), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -125), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(149, 74, -125), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(145, 74, -125), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(145, 74, -127), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(137, 74, -127), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(137, 74, -125), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(133, 74, -125), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(129, 74, -125), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(129, 74, -121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(129, 74, -117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-226, 76, 9), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-236, 76, 9), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-236, 76, 23), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-226, 76, 23), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(89, 75, 355), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(87, 75, 352), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 75, 347), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 75, 355), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 75, 363), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(89, 75, 361), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 75, 366), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(109, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 77, -107), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 77, -106), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 77, -105), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -93), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(94, 77, -93), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(94, 77, -103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -107), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -106), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -105), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 77, -107), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 77, -106), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 77, -105), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 77, -103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 76, -95), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(85, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(100, 77, -101), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(100, 77, -98), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(97, 77, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 77, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(108, 77, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(109, 77, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(119, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(111, 77, -109), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(111, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(111, 77, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(112, 77, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(118, 77, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(119, 77, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(120, 77, -105), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(120, 77, -107), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(120, 77, -106), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(121, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(125, 77, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(121, 77, -109), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(125, 77, -109), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(125, 77, -96), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(121, 77, -96), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(125, 77, -103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(127, 77, -113), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(128, 77, -113), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(127, 77, -96), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(128, 77, -96), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -102), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -101), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -100), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -101), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -102), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -100), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -95), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -93), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -95), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -93), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -108), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -108), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(97, 83, -114), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(98, 83, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(108, 83, -114), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(107, 83, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 83, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 83, -110), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(87, 83, -110), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(87, 83, -115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 83, -97), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 83, -96), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 83, -95), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(78, 83, -97), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(78, 83, -96), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(78, 83, -95), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(90, 83, -102), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(90, 83, -103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -102), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 83, -102), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 83, -103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -93), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 83, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 83, -93), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(93, 83, -94), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(93, 83, -93), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 83, -105), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 83, -106), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 83, -107), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 83, -108), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -105), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -106), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -107), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -108), Block::get(123, 0));
              //VLA
              $sender->getLevel()->setBlock(new Vector3(347, 71, 163), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(338, 81, 153), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(332, 76, 182), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(332, 76, 188), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(365, 75, 185), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 75, 196), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(347, 75, 196), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(367, 75, 192), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(385, 75, 174), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(347, 75, 174), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(370, 77, 185), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(369, 81, 176), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 173), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 174), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 185), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(359, 81, 190), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(360, 81, 190), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 195), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 196), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(371, 81, 196), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(371, 81, 195), Block::get(123, 0));
              //Военная база
              $sender->getLevel()->setBlock(new Vector3(341, 84, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(329, 74, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(334, 74, 296), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 84, 296), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(351, 74, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(361, 74, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(356, 74, 289), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(351, 74, 288), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(357, 74, 298), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(357, 74, 299), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 74, 298), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 74, 299), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(346, 71, 287), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(344, 77, 285), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 79, 292), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 79, 300), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 79, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 79, 299), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(332, 79, 299), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 79, 287), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(332, 79, 287), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(336, 79, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(329, 79, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 79, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(359, 79, 287), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 79, 299), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 79, 299), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(355, 79, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(361, 79, 293), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(346, 76, 287), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(377, 68, 371), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(377, 68, 375), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(373, 68, 375), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(322, 80, 391), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(322, 80, 392), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(322, 80, 401), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(322, 80, 400), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(319, 73, 399), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(309, 73, 399), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(312, 73, 403), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(312, 73, 389), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(309, 73, 393), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(319, 73, 393), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(319, 78, 390), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(319, 78, 402), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(310, 78, 397), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(311, 78, 396), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(310, 78, 395), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(355, 74, 356), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(355, 74, 348), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 74, 356), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 74, 348), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(343, 74, 356), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(343, 74, 348), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(335, 74, 348), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(335, 74, 356), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(369, 74, 339), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(369, 74, 338), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(369, 74, 333), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(365, 74, 333), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(359, 74, 333), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(370, 74, 349), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(361, 74, 345), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(360, 79, 338), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(360, 79, 328), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(368, 79, 329), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(360, 79, 348), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(364, 79, 350), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(364, 79, 345), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(368, 79, 347), Block::get(123, 0));
              //рынок
              $sender->getLevel()->setBlock(new Vector3(159, 80, 235), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(159, 80, 236), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(159, 80, 237), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(167, 80, 235), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(167, 80, 236), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(167, 80, 237), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(183, 80, 235), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(183, 80, 236), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(183, 80, 237), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(191, 80, 237), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(191, 80, 237), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(191, 80, 237), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 247), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 248), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 249), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 247), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 248), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 249), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 247), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 248), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 249), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 225), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 224), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 223), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 225), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 224), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 223), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 225), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 224), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 223), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(146, 75, 258), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(146, 75, 214), Block::get(123, 0));
              //Мэрия
              $sender->getLevel()->setBlock(new Vector3(-92, 74, 122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-97, 74, 122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 74, 122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-103, 74, 122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 74, 122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 74, 122), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-94, 76, 121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-95, 76, 121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 76, 121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 76, 121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 76, 121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 76, 121), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-109, 74, 117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-103, 74, 116), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 74, 116), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-92, 74, 117), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 76, 127), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 76, 127), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 76, 131), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 76, 131), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 76, 135), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 76, 135), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 149), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 152), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-95, 76, 152), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-95, 76, 149), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-109, 76, 154), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 76, 160), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 154), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 160), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 76, 160), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-94, 76, 154), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-93, 76, 157), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-93, 76, 160), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-111, 76, 164), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 76, 162), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-102, 76, 162), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 76, 168), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 76, 170), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 76, 171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-93, 76, 163), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 76, 170), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 83, 144), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 83, 143), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 83, 142), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 83, 142), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 83, 143), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 83, 144), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 144), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 143), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 142), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 144), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 143), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 142), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 144), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 143), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 142), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 144), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 143), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 142), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-95, 83, 134), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-96, 83, 134), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-103, 83, 134), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 134), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 83, 134), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-114, 83, 134), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 154), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 154), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 154), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 154), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-91, 83, 165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-91, 83, 168), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-97, 83, 171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 83, 171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 83, 171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 83, 171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 83, 170), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 83, 170), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 83, 168), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 83, 168), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 83, 167), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 83, 167), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 83, 168), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 83, 165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-94, 83, 128), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 128), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-111, 83, 128), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-111, 83, 124), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-102, 83, 124), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-92, 83, 124), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-93, 83, 116 ), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-103, 83, 115), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 83, 116), Block::get(123, 0));
              //GLA
              $sender->getLevel()->setBlock(new Vector3(-286, 44, 25), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-286, 44, 21), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-291, 45, 23), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-298, 45, 34), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-287, 44, 41), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-287, 44, 36), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-255, 69, 43), Block::get(123, 0));
              //Mexico
              $sender->getLevel()->setBlock(new Vector3(339, 71, -208), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 71, -200), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 75, -200), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 71, -193), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(342, 71, -193), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 75, -200), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(346, 75, -200), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(351, 75, -200), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(349, 71, -193), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 71, -193), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 71, -200), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 71, -208), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(349, 76, -216), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(348, 76, -216), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 76, -216), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(344, 76, -216), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(341, 76, -216), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 76, -216), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 76, -219), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 76, -213), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 81, -205), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 81, -204), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(343, 81, -205), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(343, 81, -204), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 81, -205), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 81, -204), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(338, 79, -192), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(353, 79, -192), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(348, 80, -212), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 80, -212), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 81, -211), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 81, -212), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 81, -220), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 81, -221), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 81, -221), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 81, -220), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 81, -218), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 81, -217), Block::get(123, 0));
              //фанори
              $sender->getLevel()->setBlock(new Vector3(80, 76, -171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 76, -165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(45, 76, -171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(45, 76, -165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(2, 76, -171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(2, 76, -165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-43, 76, -171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-43, 76, -165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-78, 76, -171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-78, 76, -165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-82, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-82, 76, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-69, 76, -129), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-69, 76, -129), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-20, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(-20, 76, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(15, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(15, 76, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(60, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(60, 76, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 76, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(126, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(126, 76, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(166, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(166, 76, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(201, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(246, 76, -130), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(246, 76, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(258, 80, -136), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(262, 80, -132), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(263, 84, -174), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(257, 84, -174), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(263, 84, -206), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(257, 84, -206), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(263, 84, -238), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(257, 84, -238), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(219, 76, -171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(219, 76, -165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(174, 76, -171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(174, 76, -165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(139, 76, -171), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(139, 76, -165), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, -120), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 80, -150), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 80, -151), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 80, -150), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 80, -151), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, -120), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, -87), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, -81), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, -56), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, -56), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, -25), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, -25), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 7), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 7), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(64, 76, -97), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(70, 76, -97), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(70, 76, -65), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(64, 76, -65), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(99, 76, -81), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(99, 76, -87), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(134, 76, -87), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 39), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 39), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 71), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 71), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 103), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 134), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 134), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 166), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 166), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 181), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 181), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(190, 76, 182), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(190, 76, 188), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(252, 76, 188), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(252, 76, 182), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(287, 76, 188), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(287, 76, 182), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(153, 76, 182), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(153, 76, 188), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(139, 76, 243), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(133, 76, 243), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(131, 76, 271), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(131, 76, 276), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(85, 76, 271), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(85, 76, 276), Block::get(123, 0));
              //Дома
              $sender->getLevel()->setBlock(new Vector3(206, 75, -151), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -150), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 75, -151), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 75, -150), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(213, 75, -156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(212, 75, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(203, 75, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -145), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(212, 75, -145), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(211, 75, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(203, 75, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(188, 75, -145), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(189, 75, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(195, 75, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(199, 75, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 75, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(199, 75, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(188, 75, -156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(189, 75, -156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 80, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(199, 80, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(189, 80, -156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(188, 80, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(195, 80, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(188, 80, -145), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(199, 80, -145), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 80, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(213, 80, -156), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(212, 80, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(203, 80, -155), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 80, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(203, 80, -146), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(212, 80, -145), Block::get(123, 0));
              $sender->getLevel()->setBlock(new Vector3(211, 80, -146), Block::get(123, 0));
            }else{$sender->sendMessage("§7Твой ранг §3не достаточно§7 высок§c!"); return false;}
          }else{$sender->sendMessage("§7Ты не работник §3СМИ§c!"); return false;}
        }
      }

       function offSvet(PlayerInteractEvent $e){
        $sender = $e->getPlayer();
        $b = $e->getBlock();
        $cfg = $this->cfg->getAll();
        $bx = $b->getX();
        $by = $b->getY();
        $bz = $b->getZ();
        if($bx == 81 && $by == 72 && $bz == -194){
          if (isset($cfg["massmedia"][$sender->getName()])) {
            if ($cfg["massmedia"][$sender->getName()] > 8){
              $this->getServer()->broadcastMessage("§7[§aНовости с СМИ§7] §3" . $sender->getName() . ": §cВнимание! Внимание! §b ЭС снова работает!");
              $sender->getLevel()->setBlock(new Vector3(-104, 75, -129), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, -127), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-102, 76, -127), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 75, -127), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 75, -143), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-118, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-120, 75, -126), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 75, -117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 75, -122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-121, 76, -118), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 75, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-119, 75, -135), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 75, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 75, -151), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-116, 75, -153), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 76, -145), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-109, 76, -145), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-116, -71, -133), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-117, 82, -139), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-117, 81, -152), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-115, 82, -140), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-115, 82, -153), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-114, 81, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 81, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 81, -121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 81, -116), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 82, -117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-109, 82, -116), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-119, 82, -123), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-119, 82, -117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 81, -132), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 81, -133), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 82, -133), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 82, -132), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 82, -125), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 82, -126), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 81, -129), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 81, -129), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 82, -126), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 82, -125), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 81, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 81, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 82, -139), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 82, -143), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 82, -142), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-127, 86, -122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-133, 86, -122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-127, 86, -117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-133, 86, -117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-44, 96, -214), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-35, 96, -214), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-28, 96, -214), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-27, 96, -214), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-22, 96, -214), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-21, 96, -214), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-16, 96, -214), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-15, 96, -214), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-144, 75, -141), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-142, 75, -139), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-145, 74, -123), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-143, 74, -121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-170, 75, -208), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-174, 74, -213), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-166, 74, -216), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-170, 79, -212), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-171, 79, -212), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-81, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-80, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-79, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-65, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-64, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-63, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-49, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-48, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-47, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-33, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-32, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-31, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-17, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-16, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-15, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-1, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(0, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(1, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(15, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(16, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(17, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(31, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(32, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(33, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(47, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(48, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(49, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(62, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(63, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(64, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(78, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(94, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(110, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(111, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(112, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(126, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(127, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(128, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(142, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(143, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(144, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(158, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(159, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(160, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(174, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(175, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(176, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(190, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(191, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(192, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(207, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(208, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(223, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(224, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(225, 83, -240), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(257, 77, -205), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(258, 77, -205), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(262, 77, -205), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(263, 77, -205), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(88, 75, -181), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 75, -181), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 75, -177), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(97, 75, -177), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 75, -180), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(97, 75, -180), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 75, -186), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 75, -187), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(88, 75, -185), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(81, 75, -190), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(89, 75, -190), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 80, -180), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(85, 80, -187), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-202, 75, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-202, 75, -118), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-193, 75, -114), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-193, 75, -118), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-192, 75, -142), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 74, -23), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-118, 74, -23), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-118, 74, -34), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-116, 74, -21), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-116, 74, -13), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(76, 74, -65), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 74, -65), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(76, 74, -72), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 74, -72), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(129, 74, -92), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(133, 74, -92), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(137, 74, -92), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(137, 74, -90), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(145, 74, -90), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(147, 74, -92), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -92), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -97), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -101), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -105), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -109), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -113), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(152, 74, -125), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(149, 74, -125), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(145, 74, -125), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(145, 74, -127), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(137, 74, -127), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(137, 74, -125), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(133, 74, -125), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(129, 74, -125), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(129, 74, -121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(129, 74, -117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-226, 76, 9), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-236, 76, 9), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-236, 76, 23), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-226, 76, 23), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(89, 75, 355), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(87, 75, 352), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 75, 347), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 75, 355), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 75, 363), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(89, 75, 361), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 75, 366), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(109, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 77, -107), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 77, -106), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 77, -105), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -93), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(94, 77, -93), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(94, 77, -103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -107), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -106), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(86, 77, -105), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 77, -107), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 77, -106), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 77, -105), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 77, -103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 76, -95), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(85, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(100, 77, -101), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(100, 77, -98), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(97, 77, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(96, 77, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(108, 77, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(109, 77, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(119, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(111, 77, -109), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(111, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(111, 77, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(112, 77, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(118, 77, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(119, 77, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(120, 77, -105), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(120, 77, -107), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(120, 77, -106), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(121, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(125, 77, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(121, 77, -109), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(125, 77, -109), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(125, 77, -96), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(121, 77, -96), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(125, 77, -103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(127, 77, -113), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(128, 77, -113), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(127, 77, -96), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(128, 77, -96), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -102), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -101), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -100), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -101), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -102), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -100), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -95), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -93), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -95), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -93), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(103, 83, -108), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(102, 83, -108), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(97, 83, -114), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(98, 83, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(108, 83, -114), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(107, 83, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 83, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(95, 83, -110), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(87, 83, -110), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(87, 83, -115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 83, -97), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 83, -96), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(79, 83, -95), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(78, 83, -97), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(78, 83, -96), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(78, 83, -95), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(90, 83, -102), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(90, 83, -103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -102), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 83, -102), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 83, -103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -93), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 83, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(92, 83, -93), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(93, 83, -94), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(93, 83, -93), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 83, -105), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 83, -106), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 83, -107), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(77, 83, -108), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -105), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -106), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -107), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 83, -108), Block::get(124, 0));
              //VLA
              $sender->getLevel()->setBlock(new Vector3(347, 71, 163), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(338, 81, 153), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(332, 76, 182), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(332, 76, 188), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(365, 75, 185), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 75, 196), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(347, 75, 196), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(367, 75, 192), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(385, 75, 174), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(347, 75, 174), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(370, 77, 185), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(369, 81, 176), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 173), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 174), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 185), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(359, 81, 190), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(360, 81, 190), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 195), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 81, 196), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(371, 81, 196), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(371, 81, 195), Block::get(124, 0));
              //Военная база
              $sender->getLevel()->setBlock(new Vector3(341, 84, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(329, 74, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(334, 74, 296), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 84, 296), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(351, 74, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(361, 74, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(356, 74, 289), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(351, 74, 288), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(357, 74, 298), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(357, 74, 299), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 74, 298), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 74, 299), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(346, 71, 287), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(344, 77, 285), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 79, 292), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 79, 300), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 79, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 79, 299), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(332, 79, 299), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 79, 287), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(332, 79, 287), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(336, 79, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(329, 79, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 79, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(359, 79, 287), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 79, 299), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(358, 79, 299), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(355, 79, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(361, 79, 293), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(346, 76, 287), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(377, 68, 371), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(377, 68, 375), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(373, 68, 375), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(322, 80, 391), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(322, 80, 392), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(322, 80, 401), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(322, 80, 400), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(319, 73, 399), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(309, 73, 399), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(312, 73, 403), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(312, 73, 389), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(309, 73, 393), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(319, 73, 393), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(319, 78, 390), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(319, 78, 402), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(310, 78, 397), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(311, 78, 396), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(310, 78, 395), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(355, 74, 356), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(355, 74, 348), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 74, 356), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 74, 348), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(343, 74, 356), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(343, 74, 348), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(335, 74, 348), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(335, 74, 356), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(369, 74, 339), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(369, 74, 338), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(369, 74, 333), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(365, 74, 333), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(359, 74, 333), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(370, 74, 349), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(361, 74, 345), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(360, 79, 338), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(360, 79, 328), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(368, 79, 329), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(360, 79, 348), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(364, 79, 350), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(364, 79, 345), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(368, 79, 347), Block::get(124, 0));
              //рынок
              $sender->getLevel()->setBlock(new Vector3(159, 80, 235), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(159, 80, 236), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(159, 80, 237), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(167, 80, 235), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(167, 80, 236), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(167, 80, 237), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(183, 80, 235), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(183, 80, 236), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(183, 80, 237), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(191, 80, 237), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(191, 80, 237), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(191, 80, 237), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 247), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 248), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 249), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 247), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 248), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 249), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 247), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 248), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 249), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 225), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 224), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 76, 223), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 225), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 224), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, 223), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 225), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 224), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(154, 76, 223), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(146, 75, 258), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(146, 75, 214), Block::get(124, 0));
              //Мэрия
              $sender->getLevel()->setBlock(new Vector3(-92, 74, 122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-97, 74, 122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 74, 122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-103, 74, 122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 74, 122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 74, 122), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-94, 76, 121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-95, 76, 121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 76, 121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 76, 121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 76, 121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 76, 121), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-109, 74, 117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-103, 74, 116), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 74, 116), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-92, 74, 117), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 76, 127), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 76, 127), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 76, 131), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 76, 131), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 76, 135), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-107, 76, 135), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 149), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 152), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-95, 76, 152), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-95, 76, 149), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-109, 76, 154), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 76, 160), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 154), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 76, 160), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 76, 160), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-94, 76, 154), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-93, 76, 157), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-93, 76, 160), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-111, 76, 164), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 76, 162), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-102, 76, 162), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 76, 168), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 76, 170), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 76, 171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-93, 76, 163), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 76, 170), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 83, 144), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 83, 143), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 83, 142), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 83, 142), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 83, 143), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-112, 83, 144), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 144), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 143), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 142), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 144), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 143), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 142), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 144), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 143), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 142), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 144), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 143), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 142), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-95, 83, 134), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-96, 83, 134), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-103, 83, 134), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 134), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-113, 83, 134), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-114, 83, 134), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-106, 83, 154), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-105, 83, 154), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 154), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 154), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-91, 83, 165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-91, 83, 168), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-97, 83, 171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 83, 171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 83, 171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-104, 83, 171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 83, 170), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 83, 170), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 83, 168), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 83, 168), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-100, 83, 167), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-101, 83, 167), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 83, 168), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-110, 83, 165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-94, 83, 128), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-99, 83, 128), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-111, 83, 128), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-111, 83, 124), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-102, 83, 124), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-92, 83, 124), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-93, 83, 116 ), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-98, 83, 115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-103, 83, 115), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-108, 83, 116), Block::get(124, 0));
              //GLA
              $sender->getLevel()->setBlock(new Vector3(-286, 44, 25), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-286, 44, 21), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-291, 45, 23), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-298, 45, 34), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-287, 44, 41), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-287, 44, 36), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-255, 69, 43), Block::get(124, 0));
              //Mexico
              $sender->getLevel()->setBlock(new Vector3(339, 71, -208), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 71, -200), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 75, -200), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 71, -193), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(342, 71, -193), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 75, -200), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(346, 75, -200), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(351, 75, -200), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(349, 71, -193), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 71, -193), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 71, -200), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 71, -208), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(349, 76, -216), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(348, 76, -216), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 76, -216), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(344, 76, -216), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(341, 76, -216), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 76, -216), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 76, -219), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 76, -213), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 81, -205), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(350, 81, -204), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(343, 81, -205), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(343, 81, -204), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 81, -205), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(340, 81, -204), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(338, 79, -192), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(353, 79, -192), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(348, 80, -212), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(345, 80, -212), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 81, -211), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 81, -212), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 81, -220), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(339, 81, -221), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 81, -221), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 81, -220), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 81, -218), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(352, 81, -217), Block::get(124, 0));
              //фанори
              $sender->getLevel()->setBlock(new Vector3(80, 76, -171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(80, 76, -165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(45, 76, -171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(45, 76, -165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(2, 76, -171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(2, 76, -165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-43, 76, -171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-43, 76, -165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-78, 76, -171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-78, 76, -165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-82, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-82, 76, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-69, 76, -129), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-69, 76, -129), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-20, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(-20, 76, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(15, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(15, 76, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(60, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(60, 76, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(91, 76, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(126, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(126, 76, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(166, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(166, 76, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(201, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(246, 76, -130), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(246, 76, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(258, 80, -136), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(262, 80, -132), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(263, 84, -174), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(257, 84, -174), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(263, 84, -206), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(257, 84, -206), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(263, 84, -238), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(257, 84, -238), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(219, 76, -171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(219, 76, -165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(174, 76, -171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(174, 76, -165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(139, 76, -171), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(139, 76, -165), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, -120), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 80, -150), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 80, -151), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 80, -150), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 80, -151), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, -120), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, -87), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(179, 76, -81), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, -56), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, -56), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, -25), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, -25), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 7), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 7), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(64, 76, -97), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(70, 76, -97), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(70, 76, -65), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(64, 76, -65), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(99, 76, -81), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(99, 76, -87), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(134, 76, -87), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 39), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 39), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 71), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 71), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 103), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 134), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 134), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 166), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 166), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(186, 76, 181), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(180, 76, 181), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(190, 76, 182), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(190, 76, 188), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(252, 76, 188), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(252, 76, 182), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(287, 76, 188), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(287, 76, 182), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(153, 76, 182), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(153, 76, 188), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(139, 76, 243), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(133, 76, 243), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(131, 76, 271), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(131, 76, 276), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(85, 76, 271), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(85, 76, 276), Block::get(124, 0));
              //Дома
              $sender->getLevel()->setBlock(new Vector3(206, 75, -151), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -150), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 75, -151), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 75, -150), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(213, 75, -156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(212, 75, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(203, 75, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -145), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 75, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(212, 75, -145), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(211, 75, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(203, 75, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(188, 75, -145), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(189, 75, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(195, 75, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(199, 75, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 75, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(199, 75, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(188, 75, -156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(189, 75, -156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(196, 80, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(199, 80, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(189, 80, -156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(188, 80, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(195, 80, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(188, 80, -145), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(199, 80, -145), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 80, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(213, 80, -156), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(212, 80, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(203, 80, -155), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(206, 80, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(203, 80, -146), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(212, 80, -145), Block::get(124, 0));
              $sender->getLevel()->setBlock(new Vector3(211, 80, -146), Block::get(124, 0));
            }else{$sender->sendMessage("§7Твой ранг §3не достаточно§7 высок§c!"); return false;}
          }else{$sender->sendMessage("§7Ты не работник §3СМИ§c!"); return false;}
        }
      }

       function onDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        $player->addTitle("WASTED");
        $v3 = new Vector3(107, 73, -107);
        $player->teleport($v3);
      }

       function onTapa(PlayerInteractEvent $e){
        $p = $e->getPlayer();
        $b = $e->getBlock();
        $cfg = $this->cfg->getAll();
        $item = item::get(99, 0, 1);
        $item->setCustomName("§l§bЯщик");
        $bx = $b->getX();
        $by = $b->getY();
        $bz = $b->getZ();
        if($bx == 360 && $by == 70 && $bz == 392){
          if (isset($cfg["army"][$p->getName()])) {
            if ($cfg["army"][$p->getName()] > 3){
              if(!$p->getInventory()->contains($item)){
                $p->sendMessage("§7Ты взял §eящик§7, отнеси его§c!");
                $p->getInventory()->addItem($item);
                 return false;
              }else{$p->sendMessage("§7Ты не можешь брать §e2§7 ящика§c!"); return false;}
            }else{$p->sendMessage("§7Твой §6ранг§7 не достаточно высок для того чтобы §eбрать§7 ящики§c!"); return false;}
          }else{$p->sendMessage("§7Для того чтобы взять §eящик §7надо быть §aармейцем§c!"); return false;}
        }
      }

       function onChatadmin(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "@") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($admins["admin"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $radmin) {
      if (isset($admins["admin"][$radmin->getName()])) {
      $text = $e->getMessage();
      $e->setCancelled();
      $radmin->sendMessage("§7[§eA§7]§b ".$admins["admin"][$sender->getName()] ." §a". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChatpolice(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["police"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rpo) {
      if (isset($cfg["police"][$rpo->getName()])) {
      $text = $e->getMessage();
      $rpo->sendMessage("§bДолжность: ".$cfg["police"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChathospital(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["hospital"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rhos) {
      if (isset($cfg["hospital"][$rhos->getName()])) {
      $text = $e->getMessage();
      $rhos->sendMessage("§bДолжность: ".$cfg["hospital"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChatarmy(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["army"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rarm) {
      if (isset($cfg["army"][$rarm->getName()])) {
      $text = $e->getMessage();
      $rarm->sendMessage("§bДолжность: ".$cfg["army"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChatpravo(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["pravo"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rpra) {
      if (isset($cfg["pravo"][$rpra->getName()])) {
      $text = $e->getMessage();
      $rpra->sendMessage("§bДолжность: ".$cfg["pravo"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChatsmi(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["massmedia"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rmass) {
      if (isset($cfg["massmedia"][$rmass->getName()])) {
      $text = $e->getMessage();
      $rmass->sendMessage("§bДолжность: ".$cfg["massmedia"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChatvla(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["vla"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rvl) {
      if (isset($cfg["vla"][$rvl->getName()])) {
      $text = $e->getMessage();
      $rvl->sendMessage("§bДолжность: ".$cfg["vla"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChatfsb(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["fsb"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rfs) {
      if (isset($cfg["fsb"][$rfs->getName()])) {
      $text = $e->getMessage();
      $rfs->sendMessage("§bДолжность: ".$cfg["fsb"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChattbg(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["tbg"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rtb) {
      if (isset($cfg["tbg"][$rtb->getName()])) {
      $text = $e->getMessage();
      $rtb->sendMessage("§bДолжность: ".$cfg["tbg"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChatpgsf(PlayerChatEvent $e) {
      $sender = $e->getPlayer();
      if($e->getMessage(){0} == "!") {
      $name = $sender->getName();
      $admins = $this->admins->getAll();
      $cfg = $this->cfg->getAll();
      if (isset($cfg["gsf"][$sender->getName()])) {
      $e->setCancelled();
      foreach ($this->getServer()->getOnlinePlayers() as $rgs) {
      if (isset($cfg["gsf"][$rgs->getName()])) {
      $text = $e->getMessage();
      $rgs->sendMessage("§bДолжность: ".$cfg["gsf"][$sender->getName()] ." ". $name . ": " . $text . "");
      }
      }
      }else{

      }
      }
      }

       function onChatpgsfe(PlayerChatEvent $e) {
        $sender = $e->getPlayer();
        if($e->getMessage(){0} == ")") {
          $name = $sender->getName();
          $admins = $this->admins->getAll();
          $cfg = $this->cfg->getAll();
          foreach ($this->getServer()->getOnlinePlayers() as $rgse) {
            if($sender->distance($rgse->asVector3()) > 11){
              $rgse->sendMessage("§d" . $sender->getName() . " улыбается.");
              $e->setCancelled();
            }
          }

          $sender->sendPopup("");
        }
      }

       function onChatpgsffffe(PlayerChatEvent $e) {
        $sender = $e->getPlayer();
        if($e->getMessage(){0} == "(") {
          $name = $sender->getName();
          $admins = $this->admins->getAll();
          $cfg = $this->cfg->getAll();
          foreach ($this->getServer()->getOnlinePlayers() as $pl) {
            $px = $pl->getFloorX();
            $py = $pl->getFloorY();
            $pz = $pl->getFloorZ();
            $x = $p->getFloorX();
            $y = $p->getFloorY();
            $z = $p->getFloorZ();
            $pxx = $px - $x;
            $pyy = $py - $y;
            $pzz = $pz - $z;
            if($pxx < 11 && $pyy < 11 && $pzz < 11 && $pxx > -11 && $pyy > -11 && $pzz > -11){
              $pl->sendMessage("§d" . $sender->getName() . " расстроился.");
              $e->setCancelled();
            }
          }
        }
      }


       function Qwait(PlayerQuitEvent $e){
        $player = $e->getPlayer();
        $name = $player->getName();
        $admins = $this->admins->getAll();
        $cfg = $this->cfg->getAll();
        $ip = $player->getAddress();
        $gamemode = $player->getGamemode();
        foreach($this->getServer()->getOnlinePlayers() as $play){
          if (isset($admins["admin"][$play->getName()])) {
            $play->sendMessage("§7[§eА§7]§7Игрок вышел с сервера - §3{$this->getNick[$player->getName()]}\n §7[§eIP§7] §3" .$ip. "  §7[§bGM§7] §3" .$gamemode. "");
          }
        }
      }


      public function chat(PlayerChatEvent $e){
        $s = $e->getPlayer();
        $e->setCancelled();
        $txt = $e->getMessage();
        if($txt{0} != "!" and $txt {0} != "@" and $txt {0} != ")" and $txt {0} != "(" and $txt {0} != "))" and $txt {0} != "(("){
          foreach ($this->getServer()->getOnlinePlayers() as $p){
            if($p->distance($s) <= 7){
              $p->sendMessage("{$this->getNick[$s->getName()]} сказал(-а): {$e->getMessage()}");
            }
          }
        }
      }


      function onFermaWork(PlayerInteractEvent $event){//Начать работу фермера.
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        $store = $this->store->getAll();
        $player = $event->getPlayer();
        $cfg = $this->cfg->getAll();
        if($x == -167 && $y == 72 && $z == -216){
          if (isset($cfg["army"][$player->getName()]) or isset($cfg["hospital"][$player->getName()]) or isset($cfg["massmedia"][$player->getName()]) or isset($cfg["police"][$player->getName()]) or isset($cfg["pravo"][$player->getName()])){$player->sendMessage("§7Вы находитесь в рабочей форме§c!"); return false;}
          $player->sendMessage("§7Вы начали рабочий день фермера.");
          $this->fermas[$player->getName()] = 1;
          $this->ferma[$player->getName()] = 0;
          $this->numberferma[$player->getName()] = 1;
        }
      }

      function onTapnd(PlayerInteractEvent $event) {
        $store = $this->store->getAll();
        $cfg = $this->cfg->getAll();
        $item = Item::get(99, 0, 1); // блок который надо нести
        $player = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == -110 && $y == 71 && $z == -122) {
          if (isset($cfg["army"][$player->getName()])) {
            if($player->getInventory()->contains($item)){
              $player->getInventory()->removeItem($item);
              $player->sendMessage("§7Вы §6положили§7 на склад §eящик§с!");
              $store["police"]["score"]++;
              $this->store->setAll($store);
              $this->store->save();
              $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-106, 73, -114));
              $tile->setText("§b§lСостояние склада:", "§3§l".$store["police"]["score"]." §l§bнабор.");
            }else{$player->sendMessage("§7У тебя больше нету §eящиков§c!"); return false;}
          }else{$player->sendMessage("§7Для того чтобы §6положить §7ящик, надо быть §aармейцем§с!"); return false;}
        }
      return false;}

      function onTapndarmy(PlayerInteractEvent $event) {
        $store = $this->store->getAll();
        $cfg = $this->cfg->getAll();
        $item = Item::get(99, 0, 1); // блок который надо нести
        $player = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == 377 && $y == 68 && $z == 374) {
          if (isset($cfg["army"][$player->getName()])) {
            if($player->getInventory()->contains($item)){
              $player->getInventory()->removeItem($item);
              $player->sendMessage("§7Вы §6положили§7 на склад §eящик§с!");
              $store["army"]["score"]++;
              $this->store->setAll($store);
              $this->store->save();
              $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(374, 70, 374));
              $tile->setText("§b§lСостояние склада:", "§3§l".$store["army"]["score"]." §l§bнабор.");
            }else{$player->sendMessage("§7У тебя больше нету §eящиков§c!"); return false;}
          }else{$player->sendMessage("§7Для того чтобы §6положить §7ящик, надо быть §aармейцем§с!"); return false;}
        }
      return false;}

      function onTapndpravo(PlayerInteractEvent $event) {
        $store = $this->store->getAll();
        $cfg = $this->cfg->getAll();
        $item = Item::get(99, 0, 1); // блок который надо нести
        $player = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == -92 && $y == 71 && $z == 123) {
          if (isset($cfg["army"][$player->getName()])) {
            if($player->getInventory()->contains($item)){
              $player->getInventory()->removeItem($item);
              $player->sendMessage("§7Вы §6положили§7 на склад §eящик§с!");
              $store["pravo"]["score"]++;
              $this->store->setAll($store);
              $this->store->save();
              $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-92, 73, 120));
              $tile->setText("§b§lСостояние склада:", "§3§l".$store["pravo"]["score"]." §l§bнабор.");
            }else{$player->sendMessage("§7У тебя больше нету §eящиков§c!"); return false;}
          }else{$player->sendMessage("§7Для того чтобы §6положить §7ящик, надо быть §aармейцем§с!"); return false;}
        }
      return false;}

      function onTapss (PlayerInteractEvent $event) {
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if (isset($this->progress[$event->getPlayer()->getName()])) {
          $data = $this->getConfig()->getAll();
          if ($this->progress[$event->getPlayer()->getName()] == 0) {
            $tile = $event->getPlayer()->getLevel()->getTile(new Vector3($x, $y, $z));
            $tile->setText('§aКупить', '§eСтатус: '.$data[$this->number[$event->getPlayer()->getName()]]['status'], '', '§aЦена: '.$data[$this->number[$event->getPlayer()->getName()]]['price'].'$');
            $data[$this->number[$event->getPlayer()->getName()]]['sign']['x'] = $x;
            $data[$this->number[$event->getPlayer()->getName()]]['sign']['y'] = $y;
            $data[$this->number[$event->getPlayer()->getName()]]['sign']['z'] = $z;
            $this->getConfig()->setAll($data);
            $this->getConfig()->save();
            $this->progress[$event->getPlayer()->getName()]++;
            $event->getPlayer()->sendMessage("Нажми на дверь");
          } elseif ($this->progress[$event->getPlayer()->getName()] == 1) {
            $data[$this->number[$event->getPlayer()->getName()]]['door']['x'] = $x;
            $data[$this->number[$event->getPlayer()->getName()]]['door']['y'] = $y;
            $data[$this->number[$event->getPlayer()->getName()]]['door']['z'] = $z;
            $this->getConfig()->setAll($data);
            $this->getConfig()->save();
            $this->progress[$event->getPlayer()->getName()]++;
            $event->getPlayer()->sendMessage("Нажми на место, куда будет ТП игрока, когда он будет заходить в дом.");
          } elseif ($this->progress[$event->getPlayer()->getName()] == 2) {
            $data[$this->number[$event->getPlayer()->getName()]]['join']['x'] = $x;
            $data[$this->number[$event->getPlayer()->getName()]]['join']['y'] = $y + 1;
            $data[$this->number[$event->getPlayer()->getName()]]['join']['z'] = $z;
            $this->getConfig()->setAll($data);
            $this->getConfig()->save();
            $this->progress[$event->getPlayer()->getName()]++;
            $event->getPlayer()->sendMessage("Нажми на место, куда будет ТП игрока, когда он будет выходить из дома.");
          } elseif ($this->progress[$event->getPlayer()->getName()] == 3) {
            $data[$this->number[$event->getPlayer()->getName()]]['quit']['x'] = $x;
            $data[$this->number[$event->getPlayer()->getName()]]['quit']['y'] = $y + 1;
            $data[$this->number[$event->getPlayer()->getName()]]['quit']['z'] = $z;
            $this->getConfig()->setAll($data);
            $this->getConfig()->save();
            $event->getPlayer()->sendMessage("Дом №".$this->number[$event->getPlayer()->getName()]." добавлен.");
            unset($this->progress[$event->getPlayer()->getName()], $this->number[$event->getPlayer()->getName()]);
          }
        }
        $tile = $event->getPlayer()->getLevel()->getTile(new Vector3($x, $y, $z));
        if ($tile instanceof Sign) {
          $data = $this->getConfig()->getAll();
          for ($i = 1; $i <= count($data); $i++) {
            $signX = $data[$i]['sign']['x'];
            $signY = $data[$i]['sign']['y'];
            $signZ = $data[$i]['sign']['z'];
            if ($x == $signX && $y == $signY && $z == $signZ) {
              if ($data[$i]['owner'] == 'not') {
                $ra = $this->ra->getAll();
                $p = $event->getPlayer();
                if ($this->players->get($p->getName()) == NULL) {
                  if ($ra["mon"][$p->getName()] >= $data[$i]['price']) {
                    $this->players->set($event->getPlayer()->getName(), $i);
                    $ra["mon"][$p->getName()] -= $data[$i]['price'];
                    $data[$i]['owner'] = $event->getPlayer()->getName();
                    $this->ra->setAll($ra);
                    $this->ra->save();
                    $tile = $event->getPlayer()->getLevel()->getTile(new Vector3($data[$i]['sign']['x'], $data[$i]['sign']['y'], $data[$i]['sign']['z']));
                    if ($tile instanceof Sign) {
                      $tile->setText("§aКуплено", "§eСтатус: ".$data[$i]['status'], "", "§aВладелец: ".$event->getPlayer()->getName());
                    }
                    $this->getConfig()->setAll($data);
                    $this->getConfig()->save();
                  } else $event->getPlayer()->sendMessage("У тебя не достаточно денег.");
                } else $event->getPlayer()->sendMessage("У тебя уже есть дом.");
              } else $event->getPlayer()->sendMessage("§7Данный §3дом §7уже купили");
            }
          }
        }
      }

       function onToop(PlayerInteractEvent $event) {
        $cfg = $this->cfg->getAll();
        $store = $this->store->getAll();
        $itemi = Item::get(351, 8, 50); // блок который надо нести
        $itemii = Item::get(257, 0, 1); // блок который надо нести
        $player = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == -106 && $y == 71 && $z == -115) {
          if (isset($cfg["police"][$player->getName()])) {
            if ($player->getInventory()->contains(Item::get(351, 8, 1))) {$player->sendMessage("§e§lНа данный момент у тебя есть §6вскрытый§e набор§c!"); return false;}
            $st = $store["police"]["score"];
            if ($st < 1 || $st > 45){$player->sendMessage("§cСклад не может быть меньше 0 и больше 45§4!"); return false;}
            if ($store["police"]["score"] < 1){$player->sendMessage("§e§lСклад пустой§c!");}
            $player->getInventory()->addItem($itemi);
            $player->getInventory()->addItem($itemii);
            $player->sendMessage("§b§lТы взял набор§4!");
            $store["police"]["score"]--;
            $this->store->setAll($store);
            $this->store->save();
            $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-106, 73, -114));
            $tile->setText("§b§lСостояние склада:", "§3§l".$store["police"]["score"]." §l§bнабор.");
            return false;
          }else{$player->sendMessage("§l§bТы не являешься §eполицейским§c!"); return false;}
        }
      }

       function onToopm(PlayerInteractEvent $event) {
        $cfg = $this->cfg->getAll();
        $store = $this->store->getAll();
        $itemi = Item::get(351, 8, 50); // блок который надо нести
        $itemii = Item::get(257, 0, 1); // блок который надо нести
        $player = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == -92 && $y == 71 && $z == 120) {
          if (isset($cfg["pravo"][$player->getName()])) {
            if ($player->getInventory()->contains(Item::get(351, 8, 1))) {$player->sendMessage("§e§lНа данный момент у тебя есть §6вскрытый§e набор§c!"); return false;}
            $st = $store["pravo"]["score"];
            if ($st < 1 || $st > 45){$player->sendMessage("§cСклад не может быть меньше 0 и больше 45§4!"); return false;}
            if ($store["police"]["score"] < 1){$player->sendMessage("§e§lСклад пустой§c!");}
            $player->getInventory()->addItem($itemi);
            $player->getInventory()->addItem($itemii);
            $player->sendMessage("§b§lТы взял набор§4!");
            $store["pravo"]["score"]--;
            $this->store->setAll($store);
            $this->store->save();
            $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-92, 73, 120));
            $tile->setText("§b§lСостояние склада:", "§3§l".$store["pravo"]["score"]." §l§bнабор.");
            return false;
          }else{$player->sendMessage("§l§bТы не являешься §eсотрудником правохранения§c!"); return false;}
        }
      }

       function onToopmarmy(PlayerInteractEvent $event) {
        $cfg = $this->cfg->getAll();
        $store = $this->store->getAll();
        $itemi = Item::get(351, 8, 50); // блок который надо нести
        $itemii = Item::get(257, 0, 1); // блок который надо нести
        $player = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == 374 && $y == 69 && $z == 375) {
          if (isset($cfg["army"][$player->getName()])) {
            if ($player->getInventory()->contains(Item::get(351, 8, 1))) {$player->sendMessage("§e§lНа данный момент у тебя есть §6вскрытый§e набор§c!"); return false;}
            $st = $store["army"]["score"];
            if ($st < 1 || $st > 45){$player->sendMessage("§cСклад не может быть меньше 0 и больше 45§4!"); return false;}
            if ($store["army"]["score"] < 1){$player->sendMessage("§e§lСклад пустой§c!");}
            $player->getInventory()->addItem($itemi);
            $player->getInventory()->addItem($itemii);
            $player->sendMessage("§b§lТы взял набор§4!");
            $store["army"]["score"]--;
            $this->store->setAll($store);
            $this->store->save();
            $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(374, 70, 374));
            $tile->setText("§b§lСостояние склада:", "§3§l".$store["army"]["score"]." §l§bнабор.");
            return false;
          }else{$player->sendMessage("§l§bТы не являешься §eвоеннаслужайщим§c!"); return false;}
        }
      }

       function onTsap(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        $data = $this->cofig->getAll();
        if (isset($this->settings[$player->getName()])) {
          if ($this->settings[$player->getName()] == 1) {
            $data['teleport']['x'] = $x;
            $data['teleport']['y'] = $y + 1;
            $data['teleport']['z'] = $z;
            $this->cofig->setAll($data);
            $this->cofig->save();
            $this->settings[$player->getName()] = 0;
            $player->sendMessage("Готово.");
          } elseif ($this->settings[$player->getName()] == 2) {
            $data = $this->cofig->getAll();
            $data['block']['x'] = $x;
            $data['block']['y'] = $y;
            $data['block']['z'] = $z;
            $this->cofig->setAll($data);
            $this->cofig->save();
            $this->settings[$player->getName()] = 0;
            $player->sendMessage("Готово.");
          }
        }

        if ($x == -121 && $y == 72 && $z == -118){
          if ($this->ud->exists($player->getName())) {
            $pd = $this->ud->getAll();
            $cfg = $this->cfg->getAll();
            $pd[$player->getName()]['did']++;
            $player->sendTip($pd[$player->getName()]['did']."/".$pd[$player->getName()]['must']);
            $this->ud->setAll($pd);
            $this->ud->save();
            if ($pd[$player->getName()]['did'] == $pd[$player->getName()]['must']) {
              $player->teleport(new Vector3(-105, 72, -131));
              $this->ud->remove($player->getName());
              $this->ud->save();
              $player->sendMessage("§7Ты теперь на §6свободе§7, нехотелось бы тебя §3видеть §7ещё.");
              if (isset($cfg["army"][$player->getName()])) {
                $player->setNameTag('§2Военнаслужайщий: '.$player->getName());
              }elseif (isset($cfg["hospital"][$player->getName()])) {
                $player->setNameTag('§cРаботник больницы: '.$player->getName());
              }elseif (isset($cfg["massmedia"][$player->getName()])) {
                $player->setNameTag('§6Работник CМИ: '.$player->getName());
              }elseif (isset($cfg["police"][$player->getName()])) {
                $player->setNameTag('§1Полицейский: '.$player->getName());
              }elseif (isset($cfg["pravo"][$player->getName()])) {
                $player->setNameTag('§9Правительство: '.$player->getName());
              }elseif (isset($cfg["vla"][$player->getName()])) {
                $player->setNameTag('§bVLA: '.$player->getName());
              }elseif (isset($cfg["tbg"][$player->getName()])) {
                $player->setNameTag('§aGLA: '.$player->getName());
              }elseif (isset($cfg["gsf"][$player->getName()])) {
                $player->setNameTag('§eMexico '.$player->getName());
             }elseif (isset($cfg["fsb"][$player->getName()])) {
                $player->setNameTag('§c§lИтальянская мафия: '.$player->getName());
             }elseif (!isset($cfg["fsb"][$player->getName()])){
                $player->setNameTag('Гражданин: '.$player->getName());
              }
            }
          }
        }elseif ($x == -121 && $y == 72 && $z == -118) {
          if ($this->ud->exists($player->getName())) {
            $pd = $this->ud->getAll();
            $cfg = $this->cfg->getAll();
            $pd[$player->getName()]['did']++;
            $player->sendTip($pd[$player->getName()]['did']."/".$pd[$player->getName()]['must']);
            $this->ud->setAll($pd);
            $this->ud->save();
            if ($pd[$player->getName()]['did'] == $pd[$player->getName()]['must']) {
              $player->teleport(new Vector3(-105, 72, -131));
              $this->ud->remove($player->getName());
              $player->sendMessage("§7Ты теперь на §6свободе§7, нехотелось бы тебя §3видеть §7ещё.");
              if (isset($cfg["army"][$player->getName()])) {
                $player->setNameTag('§2Военнаслужайщий: '.$player->getName());
              }elseif (isset($cfg["hospital"][$player->getName()])) {
                $player->setNameTag('§cРаботник больницы: '.$player->getName());
              }elseif (isset($cfg["massmedia"][$player->getName()])) {
                $player->setNameTag('§6Работник CМИ: '.$player->getName());
              }elseif (isset($cfg["police"][$player->getName()])) {
                $player->setNameTag('§1Полицейский: '.$player->getName());
              }elseif (isset($cfg["pravo"][$player->getName()])) {
                $player->setNameTag('§9Правительство: '.$player->getName());
              }elseif (isset($cfg["vla"][$player->getName()])) {
                $player->setNameTag('§bVLA: '.$player->getName());
              }elseif (isset($cfg["tbg"][$player->getName()])) {
                $player->setNameTag('§aGLA: '.$player->getName());
              }elseif (isset($cfg["gsf"][$player->getName()])) {
                $player->setNameTag('§eMexico '.$player->getName());
              }elseif (isset($cfg["fsb"][$player->getName()])) {
                $player->setNameTag('§c§lИтальянская мафия: '.$player->getName());
             }elseif (!isset($cfg["fsb"][$player->getName()])){
                $player->setNameTag('Гражданин: '.$player->getName());
              }
            }
          }
        }
      }

      function onTapeeeee(PlayerInteractEvent $event) {
        $cfg = $this->cfg->getAll();
        $player = $event->getPlayer();
        $itemi = Item::get(351, 8, 1);
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == 387 && $y == 65 && $z == 282){
          if (isset($cfg["vla"][$player->getName()]) or isset($cfg["tbg"][$player->getName()])){
            $player->teleport(new Vector3(390, 64, 284));
            $player ->sendmessage("§7Вы пробрались на §6военую часть§c!");
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($cfg["police"][$pl->getName()])) {
                $pl->sendMessage("§cВнимание!§e Дежурный Военнаслужайщий: Срочно! У нас бандиты бегают по Военной части.");
              }
            }
          }else $player->sendMessage("§7Ты не §6состоишь §7в §3банде");
        }elseif ($x == 389 && $y == 65 && $z == 285){
          if (isset($cfg["vla"][$player->getName()]) or isset($cfg["tbg"][$player->getName()])){
            $player->teleport(new Vector3(386, 64, 284));
            $player ->sendmessage("§7Вы убежали с §6военой части§c!");
          }else $player->sendMessage("§7Ты не §6состоишь §7в §3банде");
        }
      }

      function onTape(PlayerInteractEvent $event) {
        $cfg = $this->cfg->getAll();
        $player = $event->getPlayer();
        $itemi = Item::get(351, 8, 1);
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == 377 && $y == 69 && $z == 372){
          if (isset($cfg["vla"][$player->getName()]) or isset($cfg["tbg"][$player->getName()])){
            if ($player->getInventory()->contains(Item::get(351, 8, 50))) {$player->sendMessage("§7У тебя уже 50 пуль§c!"); return false;}
            $player->sendPopup("§b+1 Пуля");
            $player->getInventory()->addItem($itemi);
          }else $player->sendMessage("§7Ты не §6состоишь §7в §3банде");
        }elseif ($x == -110 && $y == 73 && $z == -134) {
          if(isset($cfg["police"][$player->getName()])){
            $player->teleport(new Vector3(-112, 72, -132));
            $player ->sendmessage("§7Вы §6успешно§7 зашли в §3Офис§7 Полиции§c!");
          }else $player->sendmessage("§7Вход строго только §bполицейским§4!");
        }elseif ($x == -112 && $y == 74 && $z == -133) {
          if(isset($cfg["police"][$player->getName()])){
            $player->teleport(new Vector3(-108, 72, -133));
            $player ->sendmessage("§7Вы §6успешно§7 вышли из §3Офиса§7 Полиции§c!");
          }else $player->sendmessage("§7Выход строго только §bполицейским§4!");
        }elseif ($x == -93 && $y == 73 && $z == 122) {
          if(isset($cfg["pravo"][$player->getName()])){
            $player->teleport(new Vector3(-94, 72, 119));
            $player ->sendmessage("§7Ты §aуспешно §7зашёл в офис §cМэрии§7.");
          }else $player->sendmessage("§7Вход строго только §cРаботникам Правительства§4!");
        }elseif ($x == -96 && $y == 73 && $z == 120) {
          if(isset($cfg["pravo"][$player->getName()])){
            $player->teleport(new Vector3(-95, 72, 124));
            $player ->sendmessage("§7Ты §aуспешно §7вышли из офса§b Мэрии§7.");
          }else $player->sendmessage("§7Слышь ты пидор. Шо тут делаешь ?В бане давно не был ?§c!");
        }elseif ($x == 76 && $y == 74 && $z == -190) {
          if(isset($cfg["massmedia"][$player->getName()])){
            $player->teleport(new Vector3(81, 72, -190));
            $player ->sendmessage("§7Ты §aуспешно §7зашёл в кабинет §3Главного Директора СМИ§7.");
          }else $player->sendmessage("§7Вход строго только §cРаботникам СМИ§4!");
        }elseif ($x == 79 && $y == 74 && $z == -190) {
          if(isset($cfg["massmedia"][$player->getName()])){
            $player->teleport(new Vector3(75, 71, -189));
            $player ->sendmessage("§7Ты §aуспешно §7вышл из кабинета§3 Главного Директора СМИ§7.");
          }else $player->sendmessage("§7Вход строго только §cРаботникам СМИ§4!");
        }elseif ($x == -254 && $y == 67 && $z == 48) {
          if(isset($cfg["tbg"][$player->getName()])){
            $player->teleport(new Vector3(-296, 42, 39));
            $player ->sendmessage("§7Ты §aуспешно §7спустился в подземную бухту §aGLA§7.");
          }else $player->sendmessage("§7Пройти в подземную бухту могут только банда §aGLA§4!");
        }elseif ($x == -295 && $y == 43 && $z == 41) {
          if(isset($cfg["tbg"][$player->getName()])){
            $player->teleport(new Vector3(-254, 66, 44));
            $player ->sendmessage("§7Ты §aуспешно §7выбрался из подземной бухты§7.");
          }else $player->sendmessage("§7Ты что тут делаешь?");
        }
      }

      function onTapblock(PlayerInteractEvent $event){
        if($event->getBlock()->getId() == "71"){$event->setCancelled();}
      }

      function onTapCoffee(PlayerInteractEvent $event){
        $p = $event->getPlayer();
        $ra = $this->ra->getAll();
        $cfg = $this->cfg->getAll();
        $b = $event->getBlock();
        $gm = $ra["mon"][$p->getName()];
        $bx = $b->getX();
        $by = $b->getY();
        $bz = $b->getZ();
        if ($bx == -173 && $by == 72 && $bz == -69){
          if($gm >= 4){
            if($p->getHealth() != 20){
              $p->sendPopup("§c-20$");
              $p->setHealth(20);
              $p->sendMessage("§7Вы купили §3кофе§7 тем же §aвыпили§7 его§7.");
              $ra["mon"][$p->getName()] -= 20;
              $p->sendMessage("§7У вас теперь §e" . $ra["mon"][$p->getName()] . "§a$");
              $this->ra->setAll($ra);
              $this->ra->save();
            }else{$p->sendMessage("§7Вы нехотите §cпить§7 кофе"); return false;}
          }else{$p->sendMessage("§7У вас §cнехватает§7 деняг"); return false;}
        }
      return false;}

      function onFerma(PlayerInteractEvent $event){
        $p = $event->getPlayer();
        $store = $this->store->getAll();
        $mon = $this->ra->getAll();
        $b = $event->getBlock();
        $bx = $b->getX();
        $by = $b->getY();
        $bz = $b->getZ();
        if ($bx == 4 && $by == 71 && $bz == -34) {
          $numberferma = $this->numberferma[$p->getName()];
          if ($this->fermas[$p->getName()] != 0){
            $this->ferma[$p->getName()]++;
            if($numberferma == 1){
              $p->sendPopup("§b" . $this->ferma[$p->getName()] . "/20");
              if ($this->ferma[$p->getName()] == 20) {
                $p->sendMessage("§7Ты начал собирать §3пшено, кликли 20 раз чтобы сорвать.");
                $event->setCancelled();
              }
              if ($this->ferma[$p->getName()] == 1) {
                $numberferma = $this->numberferma[$p->getName()];
                $this->numberferma[$p->getName()] = 2;
                $this->ferma[$p->getName()] = 0;
                $p->sendMessage("§7Далее тапни §e5§7 раз");
              }
            }
          }
        }
        if ($bx == -174 && $by == 71 && $bz == -212) {
          $numberferma = $this->numberferma[$p->getName()];
          if($numberferma == 2){
            $p->sendPopup("§b" . $this->ferma[$p->getName()] . "/5");
            if ($this->ferma[$p->getName()] == 150) {
              $this->ferma[$p->getName()] = 25;
              $this->numberferma[$p->getName()] = 3;
              $p->sendMessage("§7А теперь положи на конвеер.");
            }
          }
        }
        if ($bx == -167 && $by == 71 && $bz == -207) {
          $numberferma = $this->numberferma[$p->getName()];
          if($numberferma == 3){
            $p->sendPopup("§b" . $this->ferma[$p->getName()] . "/25");
            if ($this->ferma[$p->getName()] == 15) {
              $this->ferma[$p->getName()] = 0;
              $this->numberferma[$p->getName()] = 4;
              $p->sendMessage("§7Теперь забери с конвеера продукт");
            }
          }
        }
        if ($bx == -149 && $by == 72 && $bz == -216) {
          $numberferma = $this->numberferma[$p->getName()];
          if($numberferma == 4){
            $p->sendPopup("§b" . $this->ferma[$p->getName()] . "/15");
            if ($this->ferma[$p->getName()] == 15) {
              $this->ferma[$p->getName()] = 0;
              $this->numberferma[$p->getName()] = 1;
              $p->sendMessage("Молодец хорошая работа.");
              $store["ferma"][$p->getName()]++;
              $this->store->setAll($store);
              $this->store->save();
              $mon["mon"][$p->getName()] += 90;
              $this->ra->setAll($mon);
              $this->ra->save();
            }
          }
        }
      }


      function onTapnarko(PlayerInteractEvent $event){
        $p = $event->getPlayer();
        $cfg = $this->cfg->getAll();
        $b = $event->getBlock();
        $bx = $b->getX();
        $by = $b->getY();
        $bz = $b->getZ();
        if ($bx == 4 && $by == 71 && $bz == -34) {
          if (isset($cfg["fsb"][$p->getName()]) or isset($cfg["gsf"][$p->getName()])){
            $this->programmisti[$p->getName()]++;
            $numberi = $this->numberi[$p->getName()];
            if($numberi == 1){
              $p->sendPopup("§b" . $this->programmisti[$p->getName()] . "/25");
              if ($this->programmisti[$p->getName()] == 1) {
                $p->sendMessage("§7Ты начал собирать §3травы§7, для этого тапни §b25§7 раз");
                $event->setCancelled();
              }
              if ($this->programmisti[$p->getName()] == 25) {
                $this->numberi[$p->getName()] = 2;
                $this->programmisti[$p->getName()] = 0;
                $p->sendMessage("§7Далее тапни §b150§7 раз что-бы изготовить §3детали§7 для гироина");
              }
            }
            if($numberi == 2){
              $p->sendPopup("§b" . $this->programmisti[$p->getName()] . "/150");
              if ($this->programmisti[$p->getName()] == 150) {
                $this->programmisti[$p->getName()] = 0;
                $this->numberi[$p->getName()] = 3;
                $p->sendMessage("§7Теперь тапни §b200§7 раз что-бы перемешать гироин с марихуаной");
              }
            }
            if($numberi == 3){
              $p->sendPopup("§b" . $this->programmisti[$p->getName()] . "/200");
              if ($this->programmisti[$p->getName()] == 200) {
                $this->programmisti[$p->getName()] = 0;
                $this->numberi[$p->getName()] = 1;
                $p->sendMessage("§cМолодец§4! §7Ты сделал наркотическое вещество!");
                $cfg["nar"][$p->getName()]++;
                $this->cfg->setAll($cfg);
                $this->cfg->save();
              }
            }
          }else{$p->sendMessage("§7Ты не состоишь в мафии§3!"); return false;}
        }
      }

      function onTapGun(PlayerInteractEvent $event){
        $p = $event->getPlayer();
        $cfg = $this->cfg->getAll();
        $b = $event->getBlock();
        $bx = $b->getX();
        $by = $b->getY();
        $bz = $b->getZ();
        if ($bx == 78 && $by == 60 && $bz == 95) {
          if (isset($cfg["vla"][$p->getName()]) or isset($cfg["tbg"][$p->getName()])){
            $this->programmist[$p->getName()]++;
            $number = $this->number[$p->getName()];
            if($number == 1){
              $p->sendPopup("§b" . $this->programmist[$p->getName()] . "/25");
              if ($this->programmist[$p->getName()] == 1) {
                $p->sendMessage("§7Ты начал собирать §3инструменты§7, для этого тапни §b25§7 раз");
                $event->setCancelled();
              }
              if ($this->programmist[$p->getName()] == 25) {
                $this->number[$p->getName()] = 2;
                $this->programmist[$p->getName()] = 0;
                $p->sendMessage("§7Отлично! Далее тапни §b200§7 раз что-бы изготовить §3детали§7 для оружия");
              }
            }
            if($number == 2){
              $p->sendPopup("§b" . $this->programmist[$p->getName()] . "/200");
              if ($this->programmist[$p->getName()] == 200) {
                $this->programmist[$p->getName()] = 0;
                $this->number[$p->getName()] = 3;
                $p->sendMessage("§7Отлично! Теперь тапни §b100§7 раз что-бы собрать все §3детали§7 в оружие");
              }
            }
            if($number == 3){
              $p->sendPopup("§b" . $this->programmist[$p->getName()] . "/100");
              if ($this->programmist[$p->getName()] == 100) {
                $this->programmist[$p->getName()] = 0;
                $this->number[$p->getName()] = 1;
                $p->sendMessage("§cМолодец§4! §7Ты сделал оружие!");
                $item = item::get(291, 0, 1);
                $item->setCustomName("§l§cПистолет");
                $inventory = $p->getInventory();
                $inventory->addItem($item);
              }
            }
          }else{$p->sendMessage("§7Ты не состоишь в банде§3!"); return false;}
        }
      }

      function onJoin(PlayerJoinEvent $e){
        $players = $e->getPlayer();
        $cfg = $this->cfg->getAll();
        $ud = $this->ud->getAll();
        $arrest = $this->arrest->getAll();
        $data = $this->cofig->getAll();
        $ban = $this->bans->getAll();
        if (isset($ban[$players->getName()])) {
          $players->kick("§c" . $players->getName() . "§7 Вы забаненны§c!");
        }elseif (isset($ban["ban"][$players->getName()])) {
          $players->kick("§c" . $players->getName() . "§7 Вы забаненны§c!");
        }elseif (isset($cfg["agent"][$players->getName()])) {
          $players->setNameTag('§7♥§c§lAgent Cherry§7♥: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(-177, 63, -79));
        }elseif (isset($ud[$players->getName()])) {
          $players->setNameTag('§6Заключенный: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(-119, 72, -122));
        }elseif (isset($arrest["arrest"][$players->getName()])) {
          $players->teleport(new Vector3(-119, 72, -122));
          $players->removeEffect(Effect::INVISIBILITY);
          $players->setNameTag('§6Заключенный: '.$players->getName());
          $sender->removeEffect(Effect::INVISIBILITY);
        }elseif (isset($cfg["army"][$players->getName()])) {
          $players->setNameTag('§2Военнаслужайщий: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(345, 71, 293));
        }elseif (isset($cfg["hospital"][$players->getName()])) {
          $players->setNameTag('§cРаботник больницы: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(98, 73, -112));
        }elseif (isset($cfg["massmedia"][$players->getName()])) {
          $players->setNameTag('§6Работник CМИ: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(98, 77, -181));
        }elseif (isset($cfg["police"][$players->getName()])) {
          $players->setNameTag('§1Полицейский: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(-113, 72, -127));
        }elseif (isset($cfg["pravo"][$players->getName()])) {
          $players->setNameTag('§9Правительство: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(-98, 78, 143));
        }elseif (isset($cfg["vla"][$players->getName()])) {
          $players->setNameTag('§bVLA: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(374, 71, 185));
        }elseif (isset($cfg["tbg"][$players->getName()])) {
          $players->setNameTag('§aGLA: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(-227, 71, 3));
        }elseif (isset($cfg["gsf"][$players->getName()])) {
          $players->setNameTag('§eMexico '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(341, 78, -213));
        }elseif (isset($cfg["fsb"][$players->getName()])) {
          $players->setNameTag('§c§lИтальянская мафия: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(-181, 72, 279));
        }elseif (!isset($cfg["fsb"][$players->getName()])){
          $players->setNameTag('Гражданин: '.$players->getName());
          $players->removeEffect(Effect::INVISIBILITY);
          $players->teleport(new Vector3(-214, 73, -208));
        }
      }

      function onTapCar(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        $data = $this->setcar->getAll();
        if (isset($this->ao[$player->getName()])) {
          if ($this->ao[$player->getName()] == 1) {
            $player->sendMessage('§7Ты добавил автомобиль '.$this->subjects[$player->getName()]['number'].'-го уровня за $'.$this->subjects[$player->getName()]['price']);
            $data[$this->subjects[$player->getName()]['number']]['price'] = $this->subjects[$player->getName()]['price'];
            $data[$this->subjects[$player->getName()]['number']]['x'] = $x;
            $data[$this->subjects[$player->getName()]['number']]['y'] = $y;
            $data[$this->subjects[$player->getName()]['number']]['z'] = $z;
            $this->setcar->setAll($data);
            $this->setcar->save();
            unset($this->subjects[$player->getName()], $this->ao[$player->getName()]);
          } elseif ($this->ao[$player->getName()] == 2) {
            $player->sendMessage('§7Ты обозначил место заправки. Цена одного литра - $'.$this->subjects[$player->getName()]['price']);
            $data['fuel']['price'] = $this->subjects[$player->getName()]['price'];
            $data['fuel']['x'] = $x;
            $data['fuel']['y'] = $y;
            $data['fuel']['z'] = $z;
            $this->setcar->setAll($data);
            $this->setcar->save();
            unset($this->subjects[$player->getName()], $this->ao[$player->getName()]);
          }elseif ($this->ao[$player->getName()] == 3) {
            $dataa = $this->avto->getAll();
            $player->sendMessage('Ты добавил/изменил автобусную остановку №'.$this->subjects[$player->getName()]['number'].'.');
            $dataa['busstops'][$this->subjects[$player->getName()]['number']]['x'] = $x;
            $dataa['busstops'][$this->subjects[$player->getName()]['number']]['y'] = $y;
            $dataa['busstops'][$this->subjects[$player->getName()]['number']]['z'] = $z;
            $this->avto->setAll($dataa);
            $this->avto->save();
            unset($this->subjects[$player->getName()], $this->setting[$player->getName()]);
          }
        } else {

          foreach ($data as $lvl => $array) {
            if ($lvl > 0 && $lvl <= 4) {
              $ra = $this->ra->getAll();
              $pdata = $this->cares->getAll();
              if ($array['x'] == $x && $array['y'] == $y && $array['z'] == $z) {
                if($ra["mon"][$player->getName()] >= $array['price']){
                  if($pdata[$player->getName()]['car'] <= 1){
                    $m = $array['price'];
                    $player->sendMessage('§7Ты купил автомобиль §с'.$lvl.'§7-го уровня за $'.$array['price']."\n§b/car §7- завести/заглушить двигатель.");
                    $ra["mon"][$player->getName()] -= $m;
                    $this->ra->setAll($ra);
                    $this->ra->save();
                    $pdata[$player->getName()]['car'] = $lvl;
                    $pdata[$player->getName()]['fuel'] = 20;
                    $this->cares->setAll($pdata);
                    $this->cares->save();
                  }else{$player->sendMessage("§7У вас уже есть машина"); return false;}
                }else{$player->sendMessage('§7У тебя не достаточно денег.'); return false;}
              }
            }
          }
        }
      }

      function onMoves(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        if (isset($this->do[$player->getName()])) {
          if ($this->do[$player->getName()] == 1){
            $player->sendPopup('§7Подожди ещё §3'.$this->times[$player->getName()].' §7сек.');
            $event->setImmobile();
          }elseif ($this->do[$player->getName()] == 2){
            $player->sendPopup('§7Подожди ещё §3'.$this->times[$player->getName()].' §7сек.');
            $event->setImmobile();
          }
        }
      }

      function onTapesds(PlayerInteractEvent $event) {
        $date = $this->cares->getAll();
        $data = $this->avto->getAll();
        $datas = $this->prava->getAll();
        $ra = $this->ra->getAll();
        $p = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == -115 && $y == 71 && $z == -32){ //координаты покупки
          if($datas["prava"][$p->getName()] == "Нету"){
            if($ra["mon"][$p->getName()] >= 1500){
              $p->sendMessage("§7Вы начали здавать §3права§7 за 1.500§a$");
              $this->job[$p->getName()] = 'avto';
              $this->stage[$p->getName()] = 1;
              $this->destination[$p->getName()] = new Vector3($data['busstops'][1]['x'], $data['busstops'][1]['y'], $data['busstops'][1]['z']);
              $p->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 1));
              $p->teleport(new Vector3(-89, 71, -18));
              $ra["mon"][$p->getName()] -= 1500;
              $this->ra->setAll($ra);
              $this->ra->save();
            }else{
              $p->sendMessage("§7У вас не достаточно денег.");
              return false;
            }
          }else{
            $p->sendMessage("§7У вас уже есть водительские права.");
            return false;
          }
        }
      }

      function onTapeapteka(PlayerInteractEvent $event) { //аптечка
        $date = $this->cares->getAll();
        $ra = $this->ra->getAll();
        $p = $event->getPlayer();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == -158 && $y == 72 && $z == 316){ //координаты покупки
          if($date[$p->getName()]["apteka"] != 2){
            if($ra["mon"][$p->getName()] >= 3000){
              $p->sendMessage("§7Вы приобрели §3аптечку§7 за 3.000§a$");
              $ra["mon"][$p->getName()] -= 3000;
              $this->ra->setAll($ra);
              $this->ra->save();
              $date[$p->getName()]["apteka"]++;
              $this->cares->setAll($date);
              $this->cares->save();
            }else{
              $p->sendMessage("§7У вас не достаточно денег.");
              return false;
            }
          }else{
            $p->sendMessage("§7У вас уже есть §35§7 аптечек.");
            return false;
          }
        }
      }

      function onTapeaptekaaaaaaa(PlayerInteractEvent $event) { //аптечка
        $date = $this->cares->getAll();
        $ra = $this->ra->getAll();
        $p = $event->getPlayer();
        $bank = $this->bank->getAll();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == -109 && $y == 73 && $z == 148){
          if($bank[$p->getName()]["o"] == "Есть"){
            $this->opens($p);
          }else $p->sendMessage("§7У вас нет карты (§3qweek§7)");
        }
      }

       function opens(Player $pl){
        $f = $this->form->createSimpleForm(function (Player $pl, $data){
          if($data !== NULL){
              //data[0] - ник, data[1] - пароль
                switch($data){
                  case 0:
                    $login = new CartTOForm($this);
                    $login->open($pl);
                      break;
                  case 1:
                    $login = new CartForm($this);
                    $login->open($pl);
                      break;
                  case 2:
                    $login = new DipozitTOForm($this);
                    $login->open($pl);
                      break;
                  case 3:
                    $login = new DipozitForm($this);
                    $login->open($pl);
                     break;
                  case 4:
                    $login = new CymmaForm($this);
                    $login->open($pl);
                     break;
                  case 5:
                    $pl->sendMessage("§7Вы §3успешно§7 вышли.");
                      break;
                }
          }
            });
            $f->setTitle("§l§3Банкомат");
            $f->addButton("§l§bПополнить счёт");
            $f->addButton("§l§bСнять с счёта");
            $f->addButton("§l§bПополнить депозит");
            $f->addButton("§l§bСнять с депозита");
            $f->addButton("§l§bУзнать счёт");
            $f->addButton("§l§cВыход");
            $f->sendToPlayer($pl);
            return $f;
      }

      function onPass(PlayerInteractEvent $event) { //аптечка
        $date = $this->cares->getAll();
        $ra = $this->ra->getAll();
        $p = $event->getPlayer();
        $pass = $this->pass->getAll();
        $x = $event->getBlock()->getFloorX();
        $y = $event->getBlock()->getFloorY();
        $z = $event->getBlock()->getFloorZ();
        if ($x == -99 && $y == 72 && $z == 122){
          if($pass["pasport"][$p->getName()] != "Есть"){
            $this->openpass($p);
           }else $p->sendMessage("§7У вас есть паспорта города §3§lQweek");
        }
      }

       function openpass(Player $pl){
        $f = $this->form->createSimpleForm(function (Player $pl, $data){
          if($data !== NULL){
              //data[0] - ник, data[1] - пароль
                switch($data){
                  case 0:
                    $login = new PassForm($this);
                    $login->openps($pl);
                      break;
                  case 1:
                    $pl->sendMessage("§7Вы §3успешно§7 вышли.");
                      break;
                }
          }
            });
            $f->setTitle("§l§3Паспортный стол");
            $f->addButton("§l§bСделать паспорт");
            $f->addButton("§l§cВыход");
            $f->sendToPlayer($pl);
            return $f;
      }

       function call(Player $pl){
        $f = $this->form->createSimpleForm(function (Player $pl, $data){
          if($data !== NULL){
              //data[0] - ник, data[1] - пароль
                switch($data){
                  case 0:
                    $login = new CallForm($this);
                    $login->phone($pl);
                      break;
                  case 1:
                    $sms = new SmsForm($this);
                    $sms->openps($pl);
                      break;
                  case 2:
                    $pl->sendMessage("§e---------------------");
                    $tt = date("H:i:s");
                    $pl->sendMessage("§bВремя: {$tt}");
                    $pl->sendMessage("§bХорошего времяни на сервере§с♥");
                    $pl->sendMessage("§e---------------------");
                    foreach($this->getServer()->getOnlinePlayers() as $pls){
                      if($pl->distance($pls->asVector3()) < 11){
                        $pl->sendMessage("§d" . $pl->getName() ." §dпосмотрел(-а) на часы");
                      }
                    }

                    $pl->sendPopup("");
                      break;
                  case 3:
                    $pl->sendMessage("§7Вы §3успешно§7 вышли.");
                      break;
                }
          }
            });
            $f->setTitle("§l§aТелефон");
            $f->addButton("§l§3Позвонить");
            $f->addButton("§l§3Написать СМС");
            $f->addButton("§l§3Посмотреть время");
            $f->addButton("§l§cВыход");
            $f->sendToPlayer($pl);
            return $f;
      }


       function onCommand(CommandSender $sender, Command $command, String $label, array $args) :bool{
        $folder = $this->getDataFolder();
        $cfg = $this->cfg->getAll();
        $setcar = $this->setcar->getAll();
        $cares = $this->cares->getAll();
        $ra = $this->ra->getAll();
        $med = $this->med->getAll();
        $prava = $this->prava->getAll();
        $admins = $this->admins->getAll();
        $sellgunmon = $this->sellgunmon->getAll();
        $ban = $this->bans->getAll();
        $pass = $this->pass->getAll();
        $arrest = $this->arrest->getAll();
        $ros = $this->ros->getAll;
        if ($command->getName() == "invite") {
          $sende = $sender->getDisplayName();
          if (count($args) != 1){$sende->sendMessage("§7Использование: /invite <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          $playe = $player->getDisplayName();
          if ($playe == NULL){$sende->sendMessage("§7Данный игрок не найден."); return false;}
          if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
          if (isset($cfg["army"][$sende->getName()])) {
            if ($cfg["army"][$sende->getName()] < 9){$sende->sendMessage("§7Твой ранг не достаточно высок для принятия игроков в §aАрмию."); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный Игрок Уже Где То Работает§4!"); return false;}
            $sende->sendMessage("§7Ты принял §6" . $playe->getName() . "§7 на службу в §aАрмию.");
            $playe->setNameTag('§2Военнаслужайщий: '.$playe->getName());
            $playe->sendMessage("§6" . $sende->getName() . "§7 принял тебя на службу в §aАрмию.");
            $cfg["army"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["hospital"][$sende->getName()])) {
            if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if ($cfg["hospital"][$sende->getName()] < 9){$sende->sendMessage("§7Твой ранг не достаточно высок для принятия игроков в больницу."); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный игрок уже где-то работает§4!"); return false;}
            $sende->sendMessage("§7Ты принял §6" . $playe->getName() . "§7 на работу в §cбольнице.");
            $this->tag[$sende->getName()] = $sende->getNameTag();
            $playe->setNameTag('§cРаботник Больницы: '.$playe->getName());
            $playe->sendMessage("§6" . $sende->getName() . " §7принял тебя на работу в §cбольнице.");
            $cfg["hospital"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["massmedia"][$sende->getName()])) {
            if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if ($cfg["massmedia"][$sende->getName()] < 9){$sende->sendMessage("§7Твой ранг не достаточно высок для принятия игроков в радиоцентр."); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный игрок уже где-то работает."); return false;}
            $sende->sendMessage("§7Ты принял §6" . $playe->getName() . " §7на работу в §6радиоцентре.");
            $playe->setNameTag('§6Работник СМИ: '.$playe->getName());
            $playe->sendMessage("§6" . $sende->getName() . "§7 принял тебя на работу в §6радиоцентре.");
            $cfg["massmedia"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["police"][$sende->getName()])) {
            if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if ($cfg["police"][$sende->getName()] < 9){$sende->sendMessage("§7Твой ранг не достаточно высок для принятия игроков в Полицию."); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный игрок уже где-то работает."); return false;}
            $sende->sendMessage("§7Ты принял§6 " . $playe->getName() . " §7на работу в §bПолицию.");
            $playe->setNameTag('§1Полицейский: '.$playe->getName());
            $playe->sendMessage("§6" . $sende->getName() . " §7принял тебя на работу в  §bПолицию.");
            $cfg["police"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["pravo"][$sende->getName()])) {
            if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if ($cfg["pravo"][$sende->getName()] < 9){$sende->sendMessage("§Твой ранг не достаточно высок для принятия игроков в Правительство."); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный игрок уже где-то работает."); return false;}
            $sende->sendMessage("§7Ты принял §6" . $playe->getName() . " §7на работу в §9Правительстве.");
            $playe->setNameTag('§9Правительство: '.$playe->getName());
            $playe->sendMessage("§6" . $sende->getName() . " §7принял тебя на работу в§9 Правительстве.");
            $cfg["pravo"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["tbg"][$sende->getName()])) {
            if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if ($cfg["tbg"][$sende->getName()] < 9){$sende->sendMessage("§7Твой ранг не достаточно высок для принятия в семью §aGLA§7."); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный игрок уже где-то работает.");return false;}
            $sende->sendMessage("§7Ты принял§6 " . $playe->getName() . "§7в семью §aGLA§7.");
            $playe->setNameTag('§aGLA: '.$playe->getName());
            $playe->sendMessage("§6" . $sende->getName() . "§7 принял тебя в семью в §aGLA§7.");
            $cfg["tbg"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["vla"][$sende->getName()])) {
            if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if ($cfg["vla"][$sende->getName()] < 9){$sende->sendMessage("§7Твой ранг не достаточно высок для принять в семью §bVLA"); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный игрок уже где-то работает."); return false;}
            $sende->sendMessage("§7Ты принял§6 " . $playe->getName() . "§7 в семью §bVLA§7.");
            $playe->setNameTag('§bVLA: '.$playe->getName());
            $playe->sendMessage("§6" . $sende->getName() . " §7принял тебя в семью §bVLA§7.");
            $cfg["vla"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["gsf"][$sende->getName()])) {
            if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if ($cfg["gsf"][$sende->getName()] < 9){$sende->sendMessage("§7Твой ранг не достаточно высок для принятия в семью §eMexico§7."); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный игрок уже где-то работает."); return false;}
            $sende->sendMessage("§7Ты принял §6" . $playe->getName() . "§7в семью §eMexico§7.");
            $playe->setNameTag('§eMexico: '.$playe->getName());
            $playe->sendMessage("§6" . $sende->getName() . " §7 принял тея в семью §eМexico§7.");
            $cfg["gsf"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["fsb"][$sende->getName()])) {
            if($sende->distance($playe->asVector3()) > 11){$sende->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if ($cfg["fsb"][$sende->getName()] < 9){$sende->sendMessage("§7Твой ранг не достаточно высок для принятия в семью §сИтальянская мафия§7."); return false;}
            if($cfg["warn"][$playe->getName()] != 0){$sende->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$playe->getName()]) or isset($cfg["hospital"][$playe->getName()]) or isset($cfg["massmedia"][$playe->getName()]) or isset($cfg["police"][$playe->getName()]) or isset($cfg["pravo"][$playe->getName()]) or isset($cfg["tbg"][$playe->getName()]) or isset($cfg["vla"][$playe->getName()]) or isset($cfg["gst"][$playe->getName()]) or isset($cfg["fsb"][$playe->getName()])){$sende->sendMessage("§7Данный игрок уже где-то работает."); return false;}
            $sende->sendMessage("§7Ты принял §6" . $playe->getName() . "§7 в семью §cИтальянская мафия§7.");
            $playe->setNameTag('§c§lИтальянская мафия: '.$playes->getName());
            $playe->sendMessage("§6" . $sende->getName() . " §7принял тебя в семью§c Итальянской мафии§7.");
            $cfg["fsb"][$playe->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          }else{$sende->sendMessage("§7Ты нигде §cне работаеш§c!"); return false;}
        }

        if($command->getName() == "dy"){
          $sender->sendMessage("Ваш ник §3" . $sender->getDisplayName());
          $sender->sendMessage("2xВаш ник §3" .$this->getNick[$sender->getName()]);
        return false;}

        if($command->getName() == "phone"){
          $this->call($sender);
        return false;}

        if($command->getName() == "d"){
          if(isset($args[0])){
              if(isset($cfg["hospital"][$sender->getName()])){
                $ms = implode(" ", $args);
                if ($cfg["hospital"][$sender->getName()] >= 5){
                  foreach ($this->getServer()->getOnlinePlayers() as $ph) {
                    $ms = implode(" ", $args);
                    if (isset($cfg["hospital"][$ph->getName()])) {
                      $ph->sendMessage("§e" . $sender->getName() . " " . $cfg["hospital"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["police"][$ph->getName()])) {
                      $ph->sendMessage("§e" . $sender->getName() . " " . $cfg["hospital"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["pravo"][$ph->getName()])) {
                      $ph->sendMessage("§e" . $sender->getName() . " " . $cfg["hospital"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["army"][$ph->getName()])) {
                      $ph->sendMessage("§e" . $sender->getName() . " " . $cfg["hospital"][$sender->getName()] . ": §7{$ms}");
                    }
                  }
                }else{$sender->sendMessage("§7Ваш §aранг§7 не достаточно высок."); return false;}
              }elseif(isset($cfg["police"][$sender->getName()])){
                if ($cfg["police"][$sender->getName()] >= 5){
                  foreach ($this->getServer()->getOnlinePlayers() as $pp) {
                    $ms = implode(" ", $args);
                    if (isset($cfg["hospital"][$pp->getName()])) {
                      $pp->sendMessage("§e" . $sender->getName() . " " . $cfg["police"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["police"][$pp->getName()])) {
                      $pp->sendMessage("§e" . $sender->getName() . " " . $cfg["police"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["pravo"][$pp->getName()])) {
                      $pp->sendMessage("§e" . $sender->getName() . " " . $cfg["police"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["army"][$pp->getName()])) {
                      $pp->sendMessage("§e" . $sender->getName() . " " . $cfg["police"][$sender->getName()] . ": §7{$ms}");
                    }
                  }
                }else{$sender->sendMessage("§7Ваш §aранг§7 не достаточно высок."); return false;}
              }elseif(isset($cfg["pravo"][$sender->getName()])){
                if ($cfg["pravo"][$sender->getName()] >= 5){
                  $ms = implode(" ", $args);
                  $pra = "Правительство";
                  foreach ($this->getServer()->getOnlinePlayers() as $pr) {
                    if (isset($cfg["hospital"][$pr->getName()])) {
                      $pr->sendMessage("§e" . $sender->getName() . " {$pra} " . $cfg["pravo"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["police"][$pr->getName()])) {
                      $pr->sendMessage("§e" . $sender->getName() . " {$pra} " . $cfg["pravo"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["pravo"][$pr->getName()])) {
                      $pr->sendMessage("§e" . $sender->getName() . " {$pra} " . $cfg["pravo"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["army"][$pr->getName()])) {
                      $pr->sendMessage("§e" . $sender->getName() . " {$pra} " . $cfg["pravo"][$sender->getName()] . ": §7{$ms}");
                    }
                  }
                }else{$sender->sendMessage("§7Ваш §aранг§7 не достаточно высок."); return false;}
              }elseif(isset($cfg["army"][$sender->getName()])){
                if ($cfg["army"][$sender->getName()] >= 5){
                  foreach ($this->getServer()->getOnlinePlayers() as $ppa) {
                    $ms = implode(" ", $args);
                    if (isset($cfg["hospital"][$ppa->getName()])) {
                      $ppa->sendMessage("§e" . $sender->getName() . " " . $cfg["army"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["police"][$ppa->getName()])) {
                      $ppa->sendMessage("§e" . $sender->getName() . " " . $cfg["army"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["pravo"][$ppa->getName()])) {
                      $ppa->sendMessage("§e" . $sender->getName() . " " . $cfg["army"][$sender->getName()] . ": §7{$ms}");
                    }elseif (isset($cfg["army"][$ppa->getName()])) {
                      $ppa->sendMessage("§e" . $sender->getName() . " " . $cfg["army"][$sender->getName()] . ": §7{$ms}");
                    }
                  }
                }else{$sender->sendMessage("§7Ваш §aранг§7 не достаточно высок."); return false;}
              }else{$sender->sendMessage("§7Загрузка...§c     Доступ ограничен§4!"); return false;}
          }else{$sender->sendMessage("§7Использование: /d <to [ArmyQ], to [PravoQ], to [MCQ], to [PDQ]"); return false;}
        return false;}

        if($command->getName() == "visabank"){
          $bank = $this->bank->getAll();
          if($bank[$sender->getName()]["o"] == "Есть"){
            $sender->sendMessage("§7Ваш счёт состовляет §b" . $bank[$sender->getName()]["kol"]);
            $sender->sendMessage("§7Ваш счёт дипозита состовляет §b" . $bank[$player->getName()]["dipozit"]);
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if($sender->distance($pl->asVector3()) < 11){
                $text = implode(" ", $args);
                $pl->sendMessage("§d" . $sender->getName() ." §dдостал(-а) телефон ");
              }
            }

            $sender->sendPopup("");
          }else{$sender->sendMessage("§cУ вас нету карты Visa"); return false;}
        return false;}

        if($command->getName() == "takelic"){
          if(isset($cfg["police"][$sender->getName()])){
            if($cfg["police"][$sender->getName()] >= 6){
              if(isset($args[0])){
                $p = $this->getServer()->getPlayer($args[0]);
                $data = $this->cares->getAll();
                $ti = $args[1];
                if($p != NULL){
                  if($sender->distance($p->asVector3()) < 6){
                    if($args[1] == "оружие"){
                      if($data[$p->getName()]["gunlic"] != "Нету"){
                        unset($args[0]);
                        unset($args[1]);
                        $ms = implode(" ", $args);
                        $sender->sendMessage("§7Вы отобрали лицензию на §3{$ti}§7 по причине: §b" . $ms ."");
                        $p->sendMessage("§7У вас отобрали лицензию на §3{$ti}§7 по причине: §b" . $ms ."");
                        $data[$p->getName()]["gunlic"] = "Нету";
                        $this->cares->setAll($data);
                        $this->cares->save();
                        foreach($this->getServer()->getOnlinePlayers() as $pl){
                          if (isset($cfg["police"][$pl->getName()])) {
                            $pl->sendMessage("§7Сотрудник Полиции: ".$sender->getName()." забрал лицензию на §3{$ti}§7 по причине: §b" . $ms ."");
                          }
                        }
                      }else{
                        $sender->sendMessage("§9 *Даза Данных* §cУ данного гражданина нету такой лицензии!");
                        return false;
                      }
                    }elseif($args[1] == "наркотики"){
                      if($data[$p->getName()]["druglic"] != "Нету"){
                        unset($args[0]);
                        unset($args[1]);
                        $ms = implode(" ", $args);
                        $sender->sendMessage("§7Вы отобрали лицензию на §3{$ti}§7 по причине: §b" . $ms ."");
                        $p->sendMessage("§7У вас отобрали лицензию на §3{$ti}§7 по причине: §b" . $ms ."");
                        $data[$p->getName()]["druglic"] = "Нету";
                        $data->cares->setAll($data);
                        $this->cares->save();
                        foreach($this->getServer()->getOnlinePlayers() as $pl){
                          if (isset($cfg["police"][$pl->getName()])) {
                            $pl->sendMessage("§7Сотрудник Полиции: ".$sender->getName()." забрал лицензию на §3{$ti}§7 по причине: §b" . $ms ."");
                          }
                        }
                      }else{
                        $sender->sendMessage("§9 *Даза Данных* §c У данного гражданина нету такой лицензии!");
                        return false;
                      }
                    }else{
                      $sender->sendMessage("§7Использование: /takelic <nicke> <наркотики/оружие> <причина>");
                      return false;
                    }
                  }else{
                    $sender->sendMessage("§7Данный игрок далеко от вас");
                    return false;
                  }
                }else{
                  $sender->sendMessage("§7Данный игрок не найден");
                  return false;
                }
              }else{
                $sender->sendMessage("§7Использование: /takelic <nicke> <наркотики/оружие> <причина>");
                return false;
              }
            }else{
              $sender->sendMessage("§7Ваш ранг не достаточно высок§4!");
              return false;
            }
          }else{
            $sender->sendMessage("§7Вы не работаете §3полицеским");
            return false;
          }
        return false;}

        if($command->getName() == "showlic"){
          $player = $this->getServer()->getPlayer($args[0]);
          $date = $this->cares->getAll();
          if(isset($args[0])){
            if($player != NULL){
              if($sender->distance($player->asVector3()) < 6){
                $player->sendMessage("§c| §3Лицензии §3" . $sender->getName());
                $player->sendMessage("§c| §3Лицензия на хранение наркотических веществ: §3" . $date[$sender->getName()]["druglic"]);
                $player->sendMessage("§c| §3Лицензия на хранение оружия: §3" .$date[$sender->getName()]["gunlic"] . "");
              }else{
                $sender->sendMessage("§7Данный игрок далеко от вас.");
                return false;
              }
            }else{
              $sender->sendMessage("§7Данный игрок не найден.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Использование: /showmc <nick>");
            return false;
          }
        return false;}

        if($command->getName() == "slic"){
          if(isset($cfg["pravo"][$sender->getName()])){
            if($cfg["pravo"][$sender->getName()] >= 5){
              if(isset($args[0])){
                $player = $this->getServer()->getPlayer($args[0]);
                if($player != NULL){
                  if($sender->distance($player->asVector3()) < 5){
                    if($this->pr[$sender->getName()] != 1){
                      if($args[1] == "gun"){
                        $sender->sendMessage("§7Вы отправили §6предложение§7 на покупку лицензии §b" . $player->getName() . "§7 Чтобы отменить преложение наберите /selldeny <nick>");
                        $player->sendMessage("§7--Предложение-- Покупка лицензии на оружие за §a25.000$ от §b" . $sender->getName() . "§7\n §7Чтобы купить пропишите - §3/accept <ник игрока который вам предложил> \n §7Чтобы отказатся от запроса, пропишите - §3/deny <nick>");
                        $this->prg[$player->getName()] = 1;
                        $this->pr[$sender->getName()] = 1;
                      }elseif($args[1] == "drug"){
                        $sender->sendMessage("§7Вы отправили §6предложение§7 на покупку лицензии §b" . $player->getName());
                        $player->sendMessage("§7--Предложение-- Покупка лицензии на наркотики за §a150.000$ от §b" . $sender->getName() . "§7\n §7Чтобы купить пропишите - §3/accept <ник игрока который вам предложил> \n §7Чтобы отказатся от запроса, пропишите - §3/deny <nick>");
                        $this->prd[$player->getName()] = 1;
                        $this->pr[$sender->getName()] = 1;
                      }else{
                        $sender->sendMessage("§7Использование: /slic <nick> <gun/drug>");
                        return false;
                      }
                    }else{
                      $sender->sendMessage("§7Вы уже делали предложение на покупку.");
                      return false;
                    }
                  }else{
                    $sender->sendMessage("§7Данный игрок далико от вас.");
                    return false;
                  }
                }else{
                  $sender->sendMessage("§7Данный игрок не найден.");
                  return false;
                }
              }else{
                $sender->sendMessage("§7Использование: /slic <nick> <gun/drug>");
                return false;
              }
            }else{
              $sender->sendMessage("§7Ваш ранг не достаточно высок для выдачи лицензии.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Вы не работаете в правительстве§4!");
            return false;
          }
        return false;}

        if($command->getName() == "selldeny"){
          if($this->pr[$sender->getName()] == 1){
            if(isset($args[0])){
              $p = $this->getServer()->getPlayer($args[0]);
              if($p != NULL){
                if($this->prg[$p->getName()] == 1){
                  $sender->sendMessage("§7Вы отменили предложение на покупку лицензии§b " . $p->getName());
                  $p->sendMessage("§b" . $sender->getName() . "§7 отменил предложение на продажу лицензии.");
                  $this->pr[$sender->getName()] = 0;
                  $this->prg[$p->getName()] = 0;

                }elseif($this->prd[$p->getName()] == 1){
                  $sender->sendMessage("§7Вы отменили предложение на покупку лицензии§b " . $p->getName());
                  $p->sendMessage("§b" . $sender->getName() . "§7 отменил предложение на продажу лицензии.");
                  $this->pr[$sender->getName()] = 0;
                  $this->prd[$p->getName()] = 0;
                }else{
                  $sender->sendMessage("§7Данному игроку не предлогали покупать лицензию");
                  return false;
                }
              }else{
                $sender->sendMessage("§7Данный игрок не найден.");
                return false;
              }
            }else{
              $sender->sendMessage("§7Использование: /selldeny <nick>");
              return false;
            }
          }else{
            $sender->sendMessage("§7Вы не кому не предлогали покупать лицензию.");
            return false;
          }
        return false;}

        if ($command->getName() == "gun"){
          if(isset($args[2])){
            $player = $this->getServer()->getPlayer($args[1]);
            $mon = $args[2];
            if($player != NULL){
              $this->gun[$player->getName()] = 1;
              if($sender->distance($player->asVector3()) < 5){
                if ($args[0] == "sell") {
                  if($sender->getInventory()->contains(Item::get(257, 0, 1))){
                    $sender->sendMessage("§7Вы отправили §6запрос§7 на продажу gun §b" . $player->getName());
                    $player->sendMessage("§7Запрос на продажу gun §b" . $sender->getName() . "§7 за §e{$mon}§a$ \n §7Чтобы купить пропишите - §3/accept <nick> \n §7Чтобы отказатся от запроса, пропишите - §3/deny <nick>");
                    $sellgunmon["sellgunmon"][$player->getName()] = $mon;
                    $this->sellgunmon->setAll($mon);
                    $this->sellgunmon->save();
                  }else{$sender->sendMessage("§7У вас нету данного товара."); return false;}
                }else{$sender->sendMessage("§7Использование: /gun <sell> <nick> <деньги>"); return false;}
              }else{$sender->sendMessage("§7Данный игрок далеко от тебя."); return false;}
            }else{$sender->sendMessage("§7Данный игрок не найден."); return false;}
          }else{$sender->sendMessage("§7Использование: /gun <sell> <nick> <деньги>"); return false;}
        return false;}

        if($command->getName() == "accept"){
          if(isset($args[0])){
            $ra = $this->ra->getAll();
            $cares = $this->cares->getAll();
            $sellgunmon = $this->sellgunmon->getAll();
            $player = $this->getServer()->getPlayer($args[0]);
            if($player != NULL){
              if($this->gun[$sender->getName()] == $player->getName()){
                if ($sender->distance($player->asVector3()) < 5){
                  if($player->getInventory()->contains(Item::get(257, 0, 1))){
                    if($ra["mon"][$sender->getName()] >= $sellgunmon["sellgunmon"][$sender->getName()]){
                      $sender->sendMessage("§7Вы купили нелицензионное оружие.");
                      $player->sendMessage("§7Вы§3 продали§7 оружие §c" . $sender->getName());
                      $player->getInventory()->removeItem(Item::get(257, 0, 1));
                      $sender->getInventory()->addItem(Item::get(257, 0, 1));
                      $monsell = $sellgunmon["sellgunmon"][$sender->getName()];
                      $ra["mon"][$sender->getName()] -= $monsell;
                      $this->ra->setAll($monsell);
                      $this->ra->save();
                      $onli = 1;
                      unset($this->gun[$sender->getName()]);
                      unset($sellgunmon["sellgunmon"][$player->getName()]);
                      $this->sellgunmon->setAll();
                      $this->sellgunmon->save();
                    }else{
                      $sender->sendMessage("§7У вас недостаточно деняг");
                      return false;
                    }
                  }else{
                    $sender->sendMessage("§7Данный игрок не имеет оружие.");
                    return false;
                  }
                }else{
                  $sender->sendMessage("§7Данный игрок далеко от тебя.");
                  return false;
                }
              }elseif($this->prg[$sender->getName()] == 1){
                if ($sender->distance($player->asVector3()) < 5){
                  if($ra["mon"][$sender->getName()] >= 25000){
                    $sender->sendMessage("§7Вы согласились покупать §3лицензию§7 на оружие.");
                    $player->sendMessage("§a" . $sender->getName() . "§7 согласился покупать у вас §3лицензию§7 на оружие.");
                    $this->prg[$sender->getName()] = 0;
                    $ra["mon"][$player->getName()] += 5000;
                    $ra["mon"][$sender->getName()] -= 25000;
                    $this->ra->setAll($ra);
                    $this->ra->save();
                    $cares[$sender->getName()]["gunlic"] = "Есть";
                    $this->cares->setAll($cares);
                    $this->cares->save();
                    $this->pr[$player->getName()] = 0;
                  }else{
                    $sender->sendMessage("§7У вас не хватает денег оплатить лицензию");
                    return false;
                  }
                }else{
                  $sender->sendMessage("§7Данный игрок далеко от тебя");
                  return false;
                }
              }elseif($this->prd[$sender->getName()] == 1){
                if ($sender->distance($player->asVector3()) < 5){
                  if($ra["mon"][$sender->getName()] >= 150000){
                    $sender->sendMessage("§7Вы согласились покупать §3лицензию§7 на хранение наркоты.");
                    $player->sendMessage("§a" . $sender->getName() . "§7 согласился покупать у вас §3лицензию§7 на хранение наркоты.");
                    $this->prg[$sender->getName()] = 0;
                    $ra["mon"][$player->getName()] += 50000;
                    $ra["mon"][$sender->getName()] -= 150000;
                    $this->ra->setAll($ra);
                    $this->ra->save();
                    $cares[$sender->getName()]["druglic"] = "Есть";
                    $this->cares->setAll($cares);
                    $this->cares->save();
                    $this->pr[$player->getName()] = 0;
                  }else{
                    $sender->sendMessage("§7У вас не хватает денег оплатить лицензию");
                    return false;
                  }
                }else{
                  $sender->sendMessage("§7Данный игрок далеко от тебя");
                  return false;
                }
              }else{
                $sender->sendMessage("§a" . $player->getName() . "§7 данный игрок нечего не предлогал вам.");
                return false;
              }
            }else{
              $sender->sendMessage("§7Данного игрока не найдено.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Использование: /accept <nick>");
            return false;
          }
        return false;}

        if($command->getName() == "rules"){
          $sender->sendMessage("\n§a1§7. Неадекватное поведение. \n§a2§7. Оскорбление родных, веру, национальность человека. \n§a3§7. Нанесение урона без причины - Дм. \n§a4§7. Использование программ дающие преимущества перед другими. \n§a5§7. Использование багов сервера. \n§a6§7. Оскорбление администрации. \n§a7§7. Использование мульти-аккаунтов. \n§a8§7. Угрозы дружескими/родственными связями в администрации.");

        return false;}

        if($command->getName() == "deny"){
          if(isset($args[0])){
            $mon = $this->ra->getAll();
            $player = $this->getServer()->getPlayer($args[0]);
            if($player != NULL){
              $monsell = $sellgunmon["sellgunmon"][$sender->getName()];
              if ($this->gun[$sender->getName()] == $player->getName()){
                if ($sender->distance($player->asVector3()) < 5){
                  $sender->sendMessage("§7Вы отказались покупать оружие.");
                  $player->sendMessage("§a" . $sender->getName() . "§7 отказался покупать у вас оружие.");
                  unset($this->gun[$sender->getName()]);
                  unset($sellgunmon["sellgunmon"][$player->getName()]);
                  $this->sellgunmon->getAll();
                  $this->sellgunmon->save();
                  $se = $this->sellgunmon->getAll();
                  $se["sellgunmon"][$sender->getName()] = 0;
                  $this->sellgunmon->setAll($se);
                  $this->sellgunmon->save();
                }else{
                  $sender->sendMessage("§7Данный игрок далеко от тебя");
                  return false;
                }
              }elseif($this->prg[$player->getName()] == 1){
                if ($sender->distance($player->asVector3()) < 5){
                  $sender->sendMessage("§7Вы отказались покупать §3лицензию§7 на оружие.");
                  $player->sendMessage("§a" . $sender->getName() . "§7 отказался покупать у вас §3лицензию§7 на  оружие.");
                  $this->prg[$sender->getName()] = 0;
                }else{
                  $sender->sendMessage("§7Данный игрок далеко от тебя");
                  return false;
                }
              }elseif($this->prd[$player->getName()] == 1){
                if ($sender->distance($player->asVector3()) < 5){
                  $sender->sendMessage("§7Вы отказались покупать §3лицензию§7 на наркотики.");
                  $player->sendMessage("§a" . $sender->getName() . "§7 отказался покупать у вас §3лицензию§7 на  наркотики.");
                  $this->prg[$sender->getName()] = 0;
                }else{
                  $sender->sendMessage("§7Данный игрок далеко от тебя");
                  return false;
                }
              }else{
                $sender->sendMessage("§7Данный игрок вам нечего не предлогал");
                return false;
              }
            }else{
              $sender->sendMessage("§7Данного игрока не найдено");
              return false;
            }
          }else{
            $sender->sendMessage("§7Использование: /deny <nick>");
            return false;
          }
        return false;}


        if ($command->getName() == 'shot') {
          $date = $this->cares->getAll();
          $med = $this->med->getAll();
          $p = $this->getServer()->getPlayer($args[0]);
          if(isset($cfg["hospital"][$sender->getName()])){
            if($cfg["hospital"][$sender->getName()] >= 6){
              if(isset($args[0])){
                if($p != NULL){
                  if($p->distance($sender->asVector3()) < 6){
                    if ($date[$p->getName()]['pills'] <= 50) {
                      $this->do[$p->getName()] = 1;
                      $p->sendMessage('§7Доктор:§3 '. $sender->getName() . '§7 начал делать вам укол.');
                      $sender->sendMessage('§7Вы начали делать §aукол§7 пациенту §3' . $p->getName() . '§7, подождите 15секунд.');
                      $this->times[$p->getName()] = 15;
                      $med[$p->getName()]["sos"] = "Неопределённое";
                      $this->med->setAll($med);
                      $this->med->save();
                    }else{
                      $sender->sendMessage("§7Данный игрок не нуждается в уколе.");
                      return false;
                    }
                  }else{
                    $sender->sendMessage("§7Данный игрок далеко от вас.");
                    return false;
                  }
                }else{
                  $sender->sendMessage("Данный игрок не найден.");
                  return false;
                }
              }else {
                $sender->sendMessage("§7Использование: /shot <nick>");
                return false;
              }
            }else{
              $sender->sendMessage("§7Ваш ранг не достаточно высок.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Вы не доктор.");
            return false;
          }
        return false;}


        if($command->getName() == "medcart"){
          if(isset($cfg["hospital"][$sender->getName()])){
            if($cfg["hospital"][$sender->getName()] >= 4){
              if(isset($args[0])){
                $player = $this->getServer()->getPlayer($args[1]);
                if($player != NULL){
                  $med = $this->med->getAll();
                  if($player->distance($sender->asVector3()) < 6){
                    if($args[0] == "1"){
                      $player->sendMessage("§7Вам выдали/обновили §aМед.Карту врачь §7- §b" . $sender->getName() ."§7.\n§7Теперь у вас в Мед.Карте - Состояние Неопределённое.");
                      $sender->sendMessage("§7Вы выдали/обновили §aМед.Карту§7 гражданину - §b" . $player->getName());
                      $med[$player->getName()]["o"] = "Есть";
                      $med[$player->getName()]["sos"] = "Неопределённое";
                      $this->med->setAll($med);
                      $this->med->save();
                    }elseif($args[0] == "2"){
                      $player->sendMessage("§7Вам выдали/обновили §aМед.Карту врачь §7-§b " . $sender->getName() ."§7.\n§7Теперь у вас в Мед.Карте - Состояние Психически больной.");
                      $sender->sendMessage("§7Вы выдали/обновили §aМед.Карту§7 гражданину - §b" . $player->getName());
                      $med[$player->getName()]["o"] = "Есть";
                      $med[$player->getName()]["sos"] = "Психически больной";
                      $this->med->setAll($med);
                      $this->med->save();
                    }elseif($args[0] == "3"){
                      $player->sendMessage("§7Вам выдали/обновили §aМед.Карту врачь §7-§и " . $sender->getName() ."§7.\n§7Теперь у вас в Мед.Карте - Состояние Полнустью Здоровый");
                      $sender->sendMessage("§7Вы выдали/обновили §aМед.Карту§7 гражданину - §b" . $player->getName());
                      $med[$player->getName()]["o"] = "Есть";
                      $med[$player->getName()]["sos"] = "Полнустью Здоровый";
                      $this->med->setAll($med);
                      $this->med->save();
                    }
                  }else{$sender->sendMessage("§7Данный игрок далеко от вас."); return false;}
                }else{$sender->sendMessage("§7Данного игрока не найденно"); return false;}
              }else{$sender->sendMessage("§7Использование: /medcart <1-3> <nick> \n §b---§eНазначение§b--- \n §e1 - Неопределённый. \n §e2 - Психический больной. \n §e3 - Полнустью здоровый."); return false;}
            }else{$sender->sendMessage("§7Ваш ранг не высокий для обновление/выдачи карты."); return false;}
          }else{$sender->sendMessage("§7Вы не врачь."); return false;}
        return false;}

        if($command->getName() == "showmc"){
        $player = $this->getServer()->getPlayer($args[0]);
        $playe = $player->getDisplayName();
        $sende = $sender->getDisplayName();
        $med = $this->med->getAll();
        $date = $this->cares->getAll();
        if(isset($args[0])){
          foreach($this->getServer()->getOnlinePlayers() as $check){
            if($med[$playe->getName()]["o"] == "Есть"){
              if($check == $playe){
                if($sende->distance($playe->asVector3()) < 6){
                  $playe->sendMessage("§b| §cМедицинская Карта §3" . $sende->getName());
                  $playe->sendMessage("§b| §cСостояние человека: §3" . $med[$sende->getName()]["sos"]);
                  $playe->sendMessage("§b| §cСостояние здоровья: §3" .$date[$sende->getName()]["pills"] . "%");
                }else{
                  $sende->sendMessage("§7Данный игрок далеко от вас.");
                  return false;
                }
              }else{
                $sende->sendMessage("§7Данный игрок не на севрере.");
                return false;
              }
            }else{
              $sende->sendMessage("§7У вас не имеется медицинской карты");
              return false;
            }
          }
        }else{
          $sende->sendMessage("§7Использование: /showmc <nick>");
          return false;
        }
      return false;}

        if($command->getName() == "promo"){
          $data = $this->cares->getAll();
          $p = $this->promo->getAll();
          $ra = $this->ra->getAll();
          if($data[$sender->getName()]['promo'] != 1){
            if($data[$sender->getName()]['lvl'] >= 4){
              if(isset($args[0])){
                $cd = $args[0];
                if(isset($p["promo"][$cd])){
                  $s = $p["promo"][$cd];
                  $sender->sendMessage("§7Вы актевировали промокод и получили деньги - {$s}§a$");
                  $sender->sendPopup("+ {$s}");
                  $ra["mon"][$sender->getName()] += $s;
                  $this->ra->setAll($ra);
                  $this->ra->save();
                  $data[$sender->getName()]["promo"]++;
                  $this->cares->setAll($data);
                  $this->cares->save();
                }else{$sender->sendMessage("§7Данный промокод не существует"); return false;}
              }else{$sender->sendMessage("§7Использование: /promo <...>"); return false;}
            }else{$sender->sendMessage("§7У вас маленький уровень."); return false;}
          }else{$sender->sendMessage("§7Вы уже §cиспользовали§7 промокод§4!"); return false;}
        return false;}

        if($command->getName() == "usedrugs"){
          $date = $this->cares->getAll();
          if($date[$sender->getName()]["nar"] != 0){
            $sender->sendMessage("§3Вы закурили косяк");
            $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(2), 20 * 100, 1));
            $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(9), 20 * 100, 1));
            $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(15), 20 * 100, 3));
            $date["nar"][$sender->getName()]--;
            $this->cares->setAll($date);
            $this->cares->save();
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if($sender->distance($pl->asVector3()) < 11){
                $text = implode(" ", $args);
                $pl->sendMessage("§d" . $sender->getName() ." §dзакурил(-а) косяк");
              }
            }

            $sender->sendPopup("");
          }else{$sender->sendMessage("§7У вас нету наркотиков"); return false;}
        return false;}

        if($command->getName() == "healme"){
          $date = $this->cares->getAll();
          if($date[$sender->getName()]["apteka"] != 0){
            if ($date[$sender->getName()]['pills'] != 100) {
              $sender->sendMessage("§3Вы использовали шприц и бинты из аптечки.");
              $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(10), 20 * 1, 1));
              $date[$sender->getName()]["apteka"]--;
              $this->cares->setAll($date);
              $this->cares->save();
              $date[$sender->getName()]["pills"] += 10;
              $this->cares->setAll($date);
              $this->cares->save();
              foreach($this->getServer()->getOnlinePlayers() as $pl){
                if($sender->distance($pl->asVector3()) < 11){
                  $text = implode(" ", $args);
                  $pl->sendMessage("§d" . $sender->getName() ." §dиспользовал(-а) аптечку первой помощи.");
                }
              }

              $sender->sendPopup("");
            }else{$sender->sendMessage("§7Вы больше не можете использовать аптечки."); return false;}
          }else{$sender->sendMessage("§7У вас нету аптечек"); return false;}
        return false;}

        if ($command->getName() == 'carsystem') {
          if ($sender->isOp()) {
            if (count($args) == 3 or count($args) == 2) {
              if ($args[0] == 'setcar') {
                $number = $args[1];
                if (is_numeric($number) && $number > 0 && $number <= 4) {
                  $price = $args[2];
                  if (is_numeric($price) && $price > 0) {
                    $sender->sendMessage('Нажми на блок, при нажатии на который будет покупаться автомобиль.');
                    $this->subjects[$sender->getName()]['number'] = $number;
                    $this->subjects[$sender->getName()]['price'] = $price;
                    $this->ao[$sender->getName()] = 1;
                  } else{ $sender->sendMessage('Введи корректную цену.'); return false;}
                } else{ $sender->sendMessage('Введи корректный номер автомобиля (1-4).'); return false;}
              } elseif ($args[0] == 'setfuel') {
                $price = $args[1];
                if (is_numeric($price) && $price > 0) {
                  $sender->sendMessage('Нажми на блок, возле которого будет покупаться бензин.');
                  $this->subjects[$sender->getName()]['price'] = $price;
                  $this->ao[$sender->getName()] = 2;
                }else{ $sender->sendMessage('Введи цену за 1 литр.'); return false;}
              } elseif ($args[0] == 'setbusstop') {
                $number = $args[1];
                if (is_numeric($number) && $number > 0) {
                  $sender->sendMessage('Нажми на место остановки автобуса №'.$number.'.');
                  $this->setting[$sender->getName()] = 3;
                  $this->subjects[$sender->getName()]['number'] = $number;
                  $this->ao[$sender->getName()] = 3;
                }else{$sender->sendMessage('Введи номер автобусной остановки.'); return false;}
              }
            } else{ $sender->sendMessage('Использование: /carsystem setcar <номер автомобиля(1-4)> <цена> \ setfuel <цена за 1 литр>'); return false;}
          } else{ $sender->sendMessage('Доступно только ОПераторам сервера.'); return false;}
        return false;}

        if ($command->getName() == 'car') {
          $car = $this->cares->get($sender->getName())['car'];
          if ($car > 0) {
            if (!isset($this->inCar[$sender->getName()])) {
              $sender->sendMessage('§7Ты завёл автомобиль §b'.$car.'§7-го уровня.');
              $this->inCar[$sender->getName()] = true;
              $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999,$car + 1));
              $this->tag[$sender->getName()] = $sender->getNameTag();
              $sender->setNameTag('§7(§4В автомобиле§7) '.$sender->getName());
            } else {
              $sender->sendMessage('§7Ты заглушил автомобиль §b'.$car.'§7-го уровня.');
              $this->inCar[$sender->getName()] = NULL;
              $sender->removeEffect(Effect::getEffect(1)->getId());
              $sender->setNameTag($this->tag[$sender->getName()]);
              return false;
            }
          } else {$sender->sendMessage('§7У тебя нету автомобиля.'); return false;}
        return false;}

        if($command->getName() == "lock"){
          $carqq = $this->cares->getAll();
          $car = $this->cares->get($sender->getName())['car'];
          $carl = $this->cares->get($sender->getName())['lock'];
          if ($car > 0) {
            if (isset($this->inCar[$sender->getName()])) {
              if($carl == "open"){
                $sender->sendTip("CAR §aUNLOCK \n  §a/LOCK");
                $carqq[$sender->getName()]['lock'] = "close";
                $this->cares->setAll($carqq);
                $this->cares->save();
              }else{
                $sender->sendTip("CAR §cLOCK \n  §c/LOCK");
                $carqq[$sender->getName()]['lock'] = "open";
                $this->cares->setAll($carqq);
                $this->cares->save();
              }
            }else{$sender->sendMessage("§7Для начала сядь в автомобиль."); return false;}
          }else{$sender->sendMessage("§7У тебя нету автомобиля."); return false;}
        return false;}

        if ($command->getName() == 'sit') {
          if (count($args) == 1) {
            $data = $this->cares->getAll();
            $player = $this->getServer()->getPlayer($args[0]);
            if ($player != NULL) {
              $car = $this->cares->get($player->getName());
              if ($car['car'] > 0) {
                if (isset($this->inCar[$player->getName()])) {
                  if ($this->sitOn[$player->getName()] != $sender) {
                    if($sender->distance($player->asVector3()) < 5){
                      if($data[$player->getName()]['lock'] == "open"){
                        $sender->sendMessage('§7Ты сел в автомобиль к §e'.$player->getName().'§7 как пассажир.');
                        $player->sendMessage('§b' . $sender->getName().'§7 сел к тебе в автомобиль как §3пассажир§7.');
                        $this->sitOn[$sender->getName()] = $player;
                        $this->inCar[$sender->getName()] = true;
                      }else{$sender->sendMessage('§7Данная машина §cзакрта'); return false;}
                    }else{$sender->sendMessage('§7Ты далеко от данного игрока'); return false;}
                  }else{$sender->sendMessage('§7Данный игрок не водитель автомобиля.'); return false;}
                }else{$sender->sendMessage('§7Данный игрок не в автомобиле.'); return false;}
              }else{$sender->sendMessage('§7Данный игрок не имеет автомобиль.'); return false;}
            }else{$sender->sendMessage('§7Данный игрок не найден.'); return false;}
          }else{$sender->sendMessage('§7Использование: /sit <ник>'); return false;}
        return false;}

        if ($command->getName() == 'unsit') {
          $data = $this->cares->getAll();
          if ($this->sitOn[$sender->getName()] != NULL) {
            if ($this->sitOn[$sender->getName()] != $sender) {
              if($data[$player->getName()]['lock'] == "open"){
                $sender->sendMessage('§7Ты вышел из автомобиля.');
                $this->sitOn[$sender->getName()] = NULL;
                $this->inCar[$sender->getName()] = NULL;
              }else{$sender->sendMessage("§7Ты не можешь открыть дверь т.к она закрыта на замок"); return false;}
            }else{$sender->sendMessage('§7Ты водитель автомобиля.'); return false;}
          }else{$sender->sendMessage('§7Ты не сидишь в автомобиле.'); return false;}
        return false;}

        if ($command->getName() == 'fuel') {
          $car = $this->cares->get($sender->getName());
          if ($car['car'] > 0) {
            if (count($args) == 1) {
              $count = $args[0];
              if (is_numeric($count)) {
                if ($count > 0) {
                  $data = $this->setcar->getAll();
                  $price = $count * $data['fuel']['price'];
                  if ($ra["mon"][$sender->getName()] >= $price) {
                    $pdata = $this->cares->getAll();
                    if (($pdata[$sender->getName()]['fuel'] + $count) <= 100) {
                      $x = $sender->getFloorX() - $data['fuel']['x'];
                      $y = $sender->getFloorY() - $data['fuel']['y'];
                      $z = $sender->getFloorZ() - $data['fuel']['z'];
                      if ($x < 10 && $y < 10 && $z < 10 && $x > -10 && $y > -10 && $z > -10) {
                        $pdata[$sender->getName()]['fuel'] += $count;
                        $this->cares->setAll($pdata);
                        $this->cares->save();
                        $ra["mon"][$sender->getName()] =- $price;
                        $ra->ra->setAll($ra);
                        $ra->ra->save();
                        $sender->sendMessage('§7Ты купил '.$count.' литров бензина за $'.$price);
                      } else {$sender->sendMessage('§7Ты слишком далеко от заправки.'); return false;}
                    } else {$sender->sendMessage('§7Ты не можеш заполнить бак на более чем 100 литров.'); return false;}
                  } else {$sender->sendMessage('§7У тебя не достаточно денег.'); return false;}
                } else {$sender->sendMessage('§7Ты шо, ебобо? Нельзя ниже 0.'); return false;}
              } else {$sender->sendMessage('§7Введи кол-во литров.'); return false;}
            } else {$sender->sendMessage('§7/fuel <кол-во литров>'); return false;}
          } else {$sender->sendMessage('§7У тебя нету автомобиля.'); return false;}
        return false;}

        if($command->getName() == "policecar"){
          if(isset($cfg["police"][$sender->getName()])){
            if($cfg["police"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл автомобильный - §bпатруль§7.');
                $this->inCar[$sender->getName()] = true;
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 3));
                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§4(§1В патруле§4)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты завёл автомобильный - §bпатруль§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Ты §3не работаешь§7 в полицейском депортаминте.");
            return false;
          }
        return false;}

        if($command->getName() == "hospitalcar"){
          if(isset($cfg["hospital"][$sender->getName()])){
            if($cfg["hospital"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл §bскорая помощь§7.');
                $this->inCar[$sender->getName()] = true;
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 2));
                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§1(§cСкорая помощь§1)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты заглушил §bскороую помощь§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Ты §3не работаешь§7 в больнице.");
            return false;
          }
        return false;}

        if($command->getName() == "armycar"){
          if(isset($cfg["army"][$sender->getName()])){
            if($cfg["army"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл §bвоенный Jeep§7.');
                $this->inCar[$sender->getName()] = true;
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 2));
                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§2(§cВ Jeep§2)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты заглушил §bJeep§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Ты §3не работаешь§7 военнаслужайщим.");
            return false;
          }
        return false;}

        if($command->getName() == "massmediacar"){
          if(isset($cfg["massmedia"][$sender->getName()])){
            if($cfg["massmedia"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл §bфургон "СМИ"§7.');
                $this->inCar[$sender->getName()] = true;
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 2));
                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§2(§cВ Фургоне§2)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты заглушил §bфургон "СМИ"§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Ты §3не работаешь§7 в СМИ.");
            return false;
          }
        return false;}

        if($command->getName() == "gangcar"){
          if(isset($cfg["fsb"][$sender->getName()])){
            if($cfg["fsb"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл §bмашину§7.');
                $this->inCar[$sender->getName()] = true;
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 2));
                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§2(§cВ Автомабиле§2)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты заглушил §bмашину§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }elseif(isset(["gsf"][$sender->getName()])){
            if($cfg["gsf"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл §bмашину§7.');
                $this->inCar[$sender->getName()] = true;
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 2));
                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§2(§cВ Автомабиле§2)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты заглушил §bмашину§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Ты §3не из мафии§7.");
            return false;
          }
        return false;}

        if($command->getName() == "meryacar"){
          if(isset($cfg["prava"][$sender->getName()])){
            if($cfg["pravo"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл §bмашину правительства§7.');
                $this->inCar[$sender->getName()] = true;

                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§2(§cВ Автомабиле§2)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты заглушил §bмашину правительства§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Ты §3не работник§7 правительства.");
            return false;
          }
        return false;}

        if($command->getName() == "gangcar"){
          if(isset($cfg["vla"][$sender->getName()])){
            if($cfg["vla"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл §bмашину§7.');
                $this->inCar[$sender->getName()] = true;
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 2));
                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§2(§cВ Автомабиле§2)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты заглушил §bмашину§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }elseif(isset(["tbg"][$sender->getName()])){
            if($cfg["tbg"][$sender->getName()] > 2){
              if (!isset($this->inCar[$sender->getName()])) {
                $sender->sendMessage('§7Ты завёл §bмашину§7.');
                $this->inCar[$sender->getName()] = true;
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(1), 20 * 9999, 2));
                $this->tag[$sender->getName()] = $sender->getNameTag();
                $sender->setNameTag('§2(§cВ Автомабиле§2)§7 '.$sender->getName());
              } else {
                $sender->sendMessage('§7Ты заглушил §bмашину§7.');
                $this->inCar[$sender->getName()] = NULL;
                $sender->removeEffect(Effect::getEffect(1)->getId());
                $sender->setNameTag($this->tag[$sender->getName()]);
                return false;
              }
            }else{
              $sender->sendMessage("§7Твой §bранг§7 не достаточно высок.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Ты §3не бандит§7.");
            return false;
          }
        return false;}

        if($command->getName() == "fly"){
          if(isset($admins["admin"][$sender->getName()])){
            $sender->sendMessage("§7Вы §3включили§7 флай.");
            $sender->setAllowFlight(true);
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§7[§eА§7]§b".$sender->getName()." §7включил флай.");
              }
            }
          }
        return false;}

        if($command->getName() == "unfly"){
          if(isset($admins["admin"][$sender->getName()])){
            $sender->sendMessage("§7Вы §3выключили§7 флай.");
            $sender->setAllowFlight(false);
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§7[§eА§7]§b".$sender->getName()." §7выключил флай.");
              }
            }
          }
        return false;}

        if($command->getName() == "setnick"){
          if(isset($args[0])){
            if(isset($admins["admin"][$sender->getName()])){
              $nick = $args[0];
              $sender->setDisplayName($nick);
              $sender->sendMessage("Вы устоновили новый ник нейм " . $nick);
            }
          }
        return false;}

        if($command->getName() == "removitem"){
          if(isset($args[0])){
            if(isset($admins["admin"][$sender->getName()])){
              $p = $this->getServer()->getPlayer($args[0]);
              if($p != NULL){
                $sender->sendMessage("§7Вы отобрали вещи у " . $p->getName());
                $p->sendMessage("§3" . $sender->getName() . " §7отобрал у §3вас§7 все вещи§4!");
                $p->getInventory()->clearAll();
                $p->getArmorInventory()->clearAll();
              }else{$sender->sendMessage("§7Данного игрока не найдено."); return false;}
            }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          }else{$sender->sendMessage("§7Использование: /removitem <nick>");return false;}
        return false;}

        if($command->getName() == "invis"){
          if(isset($args[0])){
            if(isset($admins["admin"][$sender->getName()])){
              if($admins["admin"][$sender->getName()] > 2){
                $p = $this->getServer()->getPlayer($args[0]);
                if($p != NULL){
                  if($sender->getName() != $p->getName()){
                    $p->setNameTag("");
                    $p->sendMessage("§7Админ§e " . $sender->getName() . "§7 скрыл вам §3Nick-name");
                    $sender->sendMessage("§aВы скрыли Nick-name §a" . $p->getName());
                  }else{$sender->sendMessage("§7Нелься самому себе убирать префикс"); return false;}
                }else{$sender->sendMessage("§7Данного игрока не найдено"); return false;}
              }else{$sender->sendMessage("§7Твой lvl не достаточно высок."); return false;}
            }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          }else{$sender->sendMessage("§7Использование: /invis <nick>"); return false;}
        return false;}

        if($command->getName() == "uninvis"){
          if(isset($args[0])){
            if(isset($admins["admin"][$sender->getName()])){
              if($admins["admin"][$sender->getName()] > 2){
                $p = $this->getServer()->getPlayer($args[0]);
                if($p != NULL){
                  if (isset($cfg["agent"][$p->getName()])) {
                    $p->setNameTag('§7♥§c§lAgent Cherry§7♥: '.$p->getName());
                  }elseif (isset($ud[$p->getName()])) {
                    $p->setNameTag('§6Заключенный: '.$p->getName());
                  }elseif (isset($arrest["arrest"][$p->getName()])) {
                    $p->setNameTag('§6Заключенный: '.$p->getName());
                  }elseif (isset($cfg["army"][$p->getName()])) {
                    $p->setNameTag('§2Военнаслужайщий: '.$p->getName());
                  }elseif (isset($cfg["hospital"][$p->getName()])) {
                    $p->setNameTag('§cРаботник больницы: '.$p->getName());
                  }elseif (isset($cfg["massmedia"][$p->getName()])) {
                    $p->setNameTag('§6Работник CМИ: '.$p->getName());
                  }elseif (isset($cfg["police"][$p->getName()])) {
                    $p->setNameTag('§1Полицейский: '.$p->getName());
                  }elseif (isset($cfg["pravo"][$p->getName()])) {
                    $p->setNameTag('§9Правительство: '.$p->getName());
                  }elseif (isset($cfg["vla"][$p->getName()])) {
                    $p->setNameTag('§bVLA: '.$p->getName());
                  }elseif (isset($cfg["tbg"][$p->getName()])) {
                    $p->setNameTag('§aGLA: '.$p->getName());
                  }elseif (isset($cfg["gsf"][$p->getName()])) {
                    $p->setNameTag('§eMexico '.$p->getName());
                  }elseif (isset($cfg["fsb"][$p->getName()])) {
                    $p->setNameTag('§c§lИтальянская мафия: '.$p->getName());
                  }elseif (!isset($cfg["fsb"][$p->getName()])){
                    $p->setNameTag('Гражданин: '.$p->getName());
                  }
                }else{$sender->sendMessage("§7Данного игрока не найдено"); return false;}
              }else{$sender->sendMessage("§7Твой lvl не достаточно высок."); return false;}
            }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          }else{$sender->sendMessage("§7Использование: /uninvis <nick>"); return false;}
        return false;}

        if($command->getName() == "warn"){
          if(isset($args[1])){
            $ban = $this->bans->getAll();
            if (isset($admins["admin"][$sender->getName()])) {
              if ($admins["admin"][$sender->getName()] > 3){
                $player = $this->getServer()->getPlayer($args[0]);
                $ms = $args[1];
                if($player != NULL){
                  if($args[1] == "ТК"){
                    $this->getServer()->broadcastMessage("§cАдминистратор ".$sender->getName()." выдал варн ".$player->getName()."  по причине: ".$ms);
                    $ud[$player->getName()]['did'] = 0;
                    $ud[$player->getName()]['must'] = 1500;
                    $this->ud->setAll($ud);
                    $this->ud->save();
                    $player->teleport(new Vector3(-119, 72, -122));
                    if (isset($cfg["army"][$player->getName()])) {
                      unset($cfg["army"][$player->getName()]);
                      $player->setNameTag('Гражданин: '.$player->getName());
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["hospital"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["hospital"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["massmedia"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["massmedia"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["police"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["police"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["pravo"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["pravo"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["vla"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["vla"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["tbg"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["tbg"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["gsf"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["gsf"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                   }elseif (isset($cfg["fsb"][$player->getName()])) {
                     $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["fsb"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();

                    }
                    $player->setNameTag('§6Заключенный: '.$player->getName());
                    $cfg["warn"][$player->getName()]++;
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                    if($cfg["warn"][$player->getName()] == 4){
                      $sender->sendMessage("§7У данного игрока уже §3+3/3 §7(§cВыдан бан§7");
                      $ban = $this->bans->getAll();
                      $ban["ban"][$player->getName()] = 1;
                      $this->bans->getAll($ban);
                      $this->bans->save();
                    }else{$sender->sendMessage("§7У данного игрока уже §3" . $cfg["warn"][$player->getName()] ."/3 §7(§aБан не выдан)§7"); return false;}
                  }elseif($args[1] == "СК"){
                    $this->getServer()->broadcastMessage("§cАдминистратор ".$sender->getName()." выдал варн ".$player->getName()."  по причине: ".$ms);
                    $ud[$player->getName()]['did'] = 0;
                    $ud[$player->getName()]['must'] = 1000;
                    $this->ud->setAll($ud);
                    $this->ud->save();
                    $player->teleport(new Vector3(-119, 72, -122));
                    if (isset($cfg["army"][$player->getName()])) {
                      unset($cfg["army"][$player->getName()]);
                      $player->setNameTag('Гражданин: '.$player->getName());
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["hospital"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["hospital"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["massmedia"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["massmedia"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["police"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["police"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["pravo"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["pravo"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["vla"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["vla"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["tbg"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["tbg"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["gsf"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["gsf"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["fsb"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["fsb"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();

                    }
                    $player->setNameTag('§6Заключенный: '.$player->getName());
                    $cfg["warn"][$player->getName()]++;
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                    if($cfg["warn"][$player->getName()] == 4){
                      $sender->sendMessage("§7У данного игрока уже §3+3/3 §7(§cВыдан бан§7)");
                      $ban = $this->bans->getAll();
                      $ban["ban"][$player->getName()] = 1;
                      $this->bans->getAll($ban);
                      $this->bans->save();
                    }else{$sender->sendMessage("§7У данного игрока уже §3" . $cfg["warn"][$player->getName()] ."/3 §7(§aБан не выдан)§7"); return false;}
                  }elseif($args[1] == "РК"){
                    $this->getServer()->broadcastMessage("§cАдминистратор ".$sender->getName()." выдал варн ".$player->getName()."  по причине: ".$ms);
                    $ud[$player->getName()]['did'] = 0;
                    $ud[$player->getName()]['must'] = 2200;
                    $this->ud->setAll($ud);
                    $this->ud->save();
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                    $player->teleport(new Vector3(-119, 72, -122));
                    if (isset($cfg["army"][$player->getName()])) {
                      unset($cfg["army"][$player->getName()]);
                      $player->setNameTag('Гражданин: '.$player->getName());
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["hospital"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["hospital"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["massmedia"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["massmedia"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["police"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["police"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["pravo"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["pravo"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["vla"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["vla"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["tbg"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["tbg"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["gsf"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["gsf"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["fsb"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["fsb"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();

                    }
                    $player->setNameTag('§6Заключенный: '.$player->getName());
                    $cfg["warn"][$player->getName()]++;
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                    if($cfg["warn"][$player->getName()] == 4){
                      $sender->sendMessage("§7У данного игрока уже §3+3/3 §7(§cВыдан бан§7");
                      $ban = $this->bans->getAll();
                      $ban["ban"][$player->getName()] = 1;
                      $this->bans->getAll($ban);
                      $this->bans->save();
                    }else{$sender->sendMessage("§7У данного игрока уже §3" . $cfg["warn"][$player->getName()] ."/3 §7(§aБан не выдан)§7"); return false;}
                  }elseif($args[1] == "НонРП"){
                    $this->getServer()->broadcastMessage("§cАдминистратор ".$sender->getName()." выдал варн ".$player->getName()."  по причине: ".$ms);
                    $ud[$player->getName()]['did'] = 0;
                    $ud[$player->getName()]['must'] = 2000;
                    $this->ud->setAll($ud);
                    $this->ud->save();
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                    $player->teleport(new Vector3(-119, 72, -122));
                    if (isset($cfg["army"][$player->getName()])) {
                      unset($cfg["army"][$player->getName()]);
                      $player->setNameTag('Гражданин: '.$player->getName());
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["hospital"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["hospital"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["massmedia"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["massmedia"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["police"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["police"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["pravo"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["pravo"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["vla"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["vla"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["tbg"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["tbg"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["gsf"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["gsf"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();
                    }elseif (isset($cfg["fsb"][$player->getName()])) {
                      $player->setNameTag('Гражданин: '.$player->getName());
                      unset($cfg["fsb"][$player->getName()]);
                      $this->cfg->setAll($cfg);
                      $this->cfg->save();

                    }
                    $player->setNameTag('§6Заключенный: '.$player->getName());
                    $cfg["warn"][$player->getName()]++;
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                    if($cfg["warn"][$player->getName()] == 4){
                      $sender->sendMessage("§7У данного игрока уже §3+3/3 §7(§cВыдан бан)§7");
                      $ban = $this->bans->getAll();
                      $ban["ban"][$player->getName()] = 1;
                      $this->bans->getAll($ban);
                      $this->bans->save();
                    }else{$sender->sendMessage("§7У данного игрока уже §3" . $cfg["warn"][$player->getName()] ."/3 §7(§aБан не выдан)§7"); return false;}
                  }
                }else{$sender->sendMessage("§7Данного игрока не найдено"); return false;}
              }else{$sender->sendMessage("§7Твой lvl не достаточно высок."); return false;}
            }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          }else{$sender->sendMessage("§7Использование: /warn <nick> <ТК/СК/НонРП/РК> Русские буквы"); return false;}
        return false;}

        if($command->getName() == "unwarn"){
          if(isset($args[1])){
            $player = $this->getServer()->getPlayer($args[0]);
            if(isset($admins["admin"][$sender->getName()])){
              if($admins["admin"][$sender->getName()] > 5){
                if($player != NULL){
                  if($cfg["warn"][$player->getName()] != 0){
                    $ms = $args[1];
                    $cfg["warn"][$player->getName()]--;
                    $this->getServer()->broadcastMessage("§cАдминистратор ".$sender->getName()." снял варн с ".$player->getName()."  по причине: ".$ms);
                    $sender->sendMessage("§7Вы §cсняли§7 варн с §3" . $player->getName() . "§7 у данного игрока теперь §b" . $cfg["warn"][$player->getName()] ."/3");
                    $player->setNameTag('Гражданин: '.$player->getName());
                    $player->sendMessage("§7С вас §aсняли §7варн " . $sender->getName());
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                  }else{$sender->sendMessage("§7Данный игрок без варна"); return false;}
                }else{$sender->sendMessage("§7Данного игрока не найдено"); return false;}
              }else{$sender->sendMessage("§7Твой lvl не достаточно высок."); return false;}
            }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          }else{$sender->sendMessage("§7Использование: /unwarn <nick> <причина>"); return false;}
        return false;}

        if ($command->getName() == "agent") {
          if (count($args) != 1){$sender->sendMessage("§7Использование: /agent <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if($sender->distance($player->asVector3()) > 11){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 6){$sender->sendMessage("§7Твой lvl не достаточно высок."); return false;}
            $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 в агенты.");
            $player->setNameTag('§7♥§c§lAgent Cherry§7♥: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . "§7 принял тебя в агенты <Qweek Role Play>");
            $cfg["agent"][$player->getName()] = 1;
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "unagent") {
          if (count($args) != 1){$sender->sendMessage("§7Использование: /unagent <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if($sender->distance($player->asVector3()) > 11){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 6){$sender->sendMessage("§7Твой lvl не достаточно высок."); return false;}
            $sender->sendMessage("§7Ты снял агента §b" . $player->getName() . "§7.");
            $player->sendMessage("§b" . $sender->getName() . "§7 снял тебя с агентов <§cQweek Role Play§7>§7.");
            if (isset($cfg["army"][$player->getName()])) {
              $player->setNameTag('§2Военнаслужайщий: '.$player->getName());
            }elseif (isset($cfg["hospital"][$player->getName()])) {
              $player->setNameTag('§cРаботник больницы: '.$player->getName());
            }elseif (isset($cfg["massmedia"][$player->getName()])) {
              $player->setNameTag('§6Работник CМИ: '.$player->getName());
            }elseif (isset($cfg["police"][$player->getName()])) {
              $player->setNameTag('§1Полицейский: '.$player->getName());
            }elseif (isset($cfg["pravo"][$player->getName()])) {
              $player->setNameTag('§9Правительство: '.$player->getName());
            }elseif (isset($cfg["vla"][$player->getName()])) {
              $player->setNameTag('§bVLA: '.$player->getName());
            }elseif (isset($cfg["tbg"][$player->getName()])) {
              $player->setNameTag('§aGLA: '.$player->getName());
            }elseif (isset($cfg["gsf"][$player->getName()])) {
              $player->setNameTag('§eMexico '.$player->getName());
           }elseif (isset($cfg["fsb"][$player->getName()])) {
              $player->setNameTag('§c§lИтальянская мафия: '.$player->getName());
           }elseif (!isset($cfg["fsb"][$player->getName()])){
              $player->setNameTag('Гражданин: '.$player->getName());
              unset($cfg["agent"][$player->getName()]);
              $this->cfg->setAll($cfg);
              $this->cfg->save();
            }
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        return false;}

        if ($command->getName() == "setmon"){
          $ra = $this->ra->getAll();
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /setmon <nick> <1-30.000>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if(isset($admins["admin"][$sender->getName()])){
            if($admins["admin"][$sender->getName()] > 4){
              if($player != NULL){
                $mon = $args[1];
                if ($mon > 1 || $mon < 30000){
                  $sender->sendMessage("§7Вы выдали §b{$mon}§a$ §7для §e" . $player->getName());
                  $player->sendMessage("§6Администратор " . $sender->getName() . ": §7Выдал вам §b{$mon}§a$");
                  $player->sendPopup("§a +{$mon}");
                  $ra["mon"][$player->getName()] += $mon;
                  $this->ra->setAll($ra);
                  $this->ra->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($admins["admin"][$pl->getName()])) {
                      $pl->sendMessage("§7[§eА§7]((§b".$sender->getName()." §7выдал §e " . $player->getName() ." §a{$mon}$ §7))");
                    }
                  }
                }else{$sender->sendMessage("§7Использование: /setmon <nick> <1-30.000>"); return false;}
              }else{$sender->sendMessage("§7Данный игрок не найден."); return false;}
            }else{$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        return false;}

        if ($command->getName() == "takemon"){
          $ra = $this->ra->getAll();
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /takemon <nick> <1-30.000>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if(isset($admins["admin"][$sender->getName()])){
            if($admins["admin"][$sender->getName()] > 5){
              if($player != NULL){
                $mon = $args[1];
                if ($mon > 1 || $mon < 30000){
                  if ($ra["mon"][$player->getName()] >= $mon){
                    $sender->sendMessage("§7Вы забрали §b{$mon}§a$ §7у §e" . $player->getName());
                    $player->sendMessage("§6Администратор " . $sender->getName() . ": §7Забрал у вас §b{$mon}§a$");
                    $player->sendPopup("§c -{$mon}");
                    $ra["mon"][$player->getName()] -= $mon;
                    $this->ra->setAll($ra);
                    $this->ra->save();
                    foreach($this->getServer()->getOnlinePlayers() as $pl){
                      if (isset($admins["admin"][$pl->getName()])) {
                        $pl->sendMessage("§7[§eА§7]((§b" . $sender->getName() . " §7забрал у§e " . $player->getName() ." §a{$mon}$ §7))");
                      }
                    }
                  }else{$sender->sendMessage("§7У данного игрока нету столько деняг."); return false;}
                }else{$sender->sendMessage("§7Использование: /setmon <nick> <1-30.000>"); return false;}
              }else{$sender->sendMessage("§7Данный игрок не найден."); return false;}
            }else{$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        return false;}

        if ($command->getName() == "pay"){
          if(!isset($args[1])){$sender->sendMessage("§7Использование: /pay <nick> <1-3.000>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if($player != NULL){
            $mon = $args[1];
            if(is_numeric($args[1])){
              if ($mon > 1 || $mon < 3000){
                if($sender->distance($player->asVector3()) < 5){
                  if ($ra["mon"][$sender->getName()] >= $mon){
                    $sender->sendMessage("§7Вы передал §b{$mon}§a$  §e" . $player->getName());
                    $player->sendMessage("§6" . $sender->getName() . "§7 передал вам §b{$mon}§a$");
                    $player->sendPopup("§a +{$mon}");
                    $sender->sendPopup("§c -{$mon}");
                    $ra["mon"][$player->getName()] += $mon;
                    $this->ra->setAll($ra);
                    $this->ra->save();
                    $ra["mon"][$sender->getName()] -= $mon;
                    $this->ra->setAll($ra);
                    $this->ra->save();
                    foreach($this->getServer()->getOnlinePlayers() as $pl){
                      if (isset($admins["admin"][$pl->getName()])) {
                        $pl->sendMessage("§7[§eА§7]((§b".$sender->getName()." §7передал§e " . $player->getName() ." §a{$mon}$ §7))");
                      }
                    }
                  }else{$sender->sendMessage("§7У тебя нету столько деняг."); return false;}
                }else{$sender->sendMessage("§7Данный игрок далеко от тебя."); return false;}
              }else{$sender->sendMessage("§7Использование: /pay <nick> <1-3.000>"); return false;}
            }else{$sender->sendMessage("§7Использование: /pay <nick> <1-3.000>"); return false;}
          }else{$sender->sendMessage("§7Данный игрок не найден."); return false;}
        return false;}

        if($command->getName() == "mon"){
          $ra = $this->ra->getAll();
          $sender->sendMessage("§7Ваш бюджет: §a" . $ra["mon"][$sender->getName()] . "$");
        return false;}

        if ($command->getName() == "drugs"){
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /drugs <take/put> <1-2>"); return false;}
          $store = $this->store->getAll();
          if (isset($cfg["fsb"][$sender->getName()])) {
            if($sender->distance(new Vector3(-210, 72, 274)) < 5){
              if ($args[0] == "take") {
                if($store["fsbdrugs"]["lock"] != "1"){
                  if($store["fsbdrugs"]["score"] < 1){$sender->sendMessage("§bСлад на данный момент пустой§c!"); return false;}
                  $s = $args[1];
                  if ($s > 1 || $s < 2){
                    $sender->sendMessage("§7Ты взял §b{$s}§7 марихуаны со склада");
                    $store["fsbdrugs"]["score"] -= $s;
                    $cfg["nar"][$sender->getName()] += $s;
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                    $this->store->setAll($store);
                    $this->store->save();
                    $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-210, 72, 274));
                    $tile->setText("§b§lСостояние склада:", "§3§l".$store["fsbdrugs"]["score"]." §l§bнарк.");
                    foreach($this->getServer()->getOnlinePlayers() as $pl){
                      if (isset($cfg["fsb"][$pl->getName()])) {
                        $pl->sendMessage("§bКореш: §7".$sender->getName()." взял §e{$s} §7нарко. со склад");
                      }
                    }
                  }else{$sender->sendMessage("§7Использование: /drugs <take/put> <1-2>"); return false;}
                }else{$sender->sendMessage("§cСклад закрыт"); return false;}
              }elseif ($args[0] == "put") {
                $s = $args[1];
                if ($cfg["nar"][$sender->getName()] <= $s){$sender->sendMessage("§eУ тебя нет столько пуль§c!"); return false;}
                $sender->sendMessage("§7Ты положил §b{$s}§7 марихуаны на склад");
                $cfg["nar"][$sender->getName()] -= $s;
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                $store["fsbdrugs"]["score"] += $s;
                $this->store->setAll($store);
                $this->store->save();
                $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-210, 72, 274));
                $tile->setText("§b§lСостояние склада:", "§3§l".$store["fsbdrugs"]["score"]." §l§bнарко.");
                foreach($this->getServer()->getOnlinePlayers() as $pl){
                  if (isset($cfg["fsb"][$pl->getName()])) {
                    $pl->sendMessage("§bКореш: §7".$sender->getName()." положил §e{$s} §7нарко. на склад");
                  }
                }
              }elseif ($args[0] == "close") {
                if ($cfg["fsb"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["fsbdrugs"]["lock"] == "0"){
                  $sender->sendMessage("§7Ты закрыл склад");
                  $store["fsbdrugs"]["lock"]++;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["fsb"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." закрыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был закрыт."); return false;}
              }elseif ($args[0] == "open") {
                if ($cfg["fsb"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["fsbdrugs"]["lock"] == "1"){
                  $sender->sendMessage("§7Ты открыл склад");
                  $store["fsbdrugs"]["lock"]--;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["fsb"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." открыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был открыт."); return false;}
              }
            }else{$sender->sendMessage("§7Ты слишком далеко от склада§4!"); return false;}
          }elseif (isset($cfg["gsf"][$sender->getName()])) {
            if($sender->distance(new Vector3(351, 79, -221)) < 5){
              if ($args[0] == "take") {
                if($store["gsfdrugs"]["lock"] != "1"){
                  if($store["gsfdrugs"]["score"] < 1){$sender->sendMessage("§bСлад на данный момент пустой§c!"); return false;}
                  $s = $args[1];
                  if ($s > 1 || $s < 2){
                    $sender->sendMessage("§7Ты взял §b{$s}§7 марихуаны со склада");
                    $store["gsfdrugs"]["score"] -= $s;
                    $cfg["nar"][$sender->getName()] += $s;
                    $this->cfg->setAll($cfg);
                    $this->cfg->save();
                    $this->store->setAll($store);
                    $this->store->save();
                    $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(351, 79, -221));
                    $tile->setText("§b§lСостояние склада:", "§3§l".$store["gsfdrugs"]["score"]." §l§bпт.");
                    foreach($this->getServer()->getOnlinePlayers() as $pl){
                      if (isset($cfg["gsf"][$pl->getName()])) {
                        $pl->sendMessage("§bКореш: §7".$sender->getName()." взял §e{$s} §7пт. со склад");
                      }
                    }
                  }else{$sender->sendMessage("§7Использование: /drugs <take/put> <1-2>"); return false;}
                }else{$sender->sendMessage("§cСклад закрыт"); return false;}
              }elseif ($args[0] == "put") {
                $s = $args[1];
                if ($cfg["nar"][$sender->getName()] <= $s){$sender->sendMessage("§eУ тебя нет столько пуль§c!"); return false;}
                $sender->sendMessage("§7Ты положил §b{$s}§7 марихуаны на склад");
                $cfg["nar"][$sender->getName()] -= $s;
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                $store["gsfdrugs"]["score"] += $s;
                $this->store->setAll($store);
                $this->store->save();
                $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(351, 79, -221));
                $tile->setText("§b§lСостояние склада:", "§3§l".$store["gsfdrugs"]["score"]." §l§bпт.");
                foreach($this->getServer()->getOnlinePlayers() as $pl){
                  if (isset($cfg["gsf"][$pl->getName()])) {
                    $pl->sendMessage("§bКореш: §7".$sender->getName()." положил §e{$s} §7пт. на склад");
                  }
                }
              }elseif ($args[0] == "close") {
                if ($cfg["gsf"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["gsfdrugs"]["lock"] == "0"){
                  $sender->sendMessage("§7Ты закрыл склад");
                  $store["gsfdrugs"]["lock"]++;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["gsf"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." закрыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был закрыт."); return false;}
              }elseif ($args[0] == "open") {
                if ($cfg["gsf"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["gsfdrugs"]["lock"] == "1"){
                  $sender->sendMessage("§7Ты открыл склад");
                  $store["gsfdrugs"]["lock"]--;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["gsf"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." открыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был открыт."); return false;}
              }
            }else{$sender->sendMessage("§7Ты слишком далеко от склада§4!"); return false;}
          }else{$sender->sendMessage("§7Вы не состоите в §3мафии"); return false;}
        }

        if ($command->getName() == "store"){
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /store <take/put> <1-30>"); return false;}
          $store = $this->store->getAll();
          if (isset($cfg["vla"][$sender->getName()])) {
            if($sender->distance(new Vector3(347, 78, 197)) < 5){
              if ($args[0] == "take") {
                if($store["vla"]["lock"] != "1"){
                  if($store["vla"]["score"] < 1){$sender->sendMessage("§bСлад на данный момент пустой§c!"); return false;}
                  $s = $args[1];
                  if ($s > 1 || $s < 30){
                    if ($sender->getInventory()->contains(Item::get(351, 8, 10))) {$sender->sendMessage("§eУ тебя есть уже пули§c!"); return false;}
                    $sender->sendMessage("§7Ты взял §b{$s}§7 патроны со склада");
                    $store["vla"]["score"] -= $s;
                    $sender->getInventory()->addItem(Item::get(351, 8, $s));
                    $this->store->setAll($store);
                    $this->store->save();
                    $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(347, 78, 197));
                    $tile->setText("§b§lСостояние склада:", "§3§l".$store["vla"]["score"]." §l§bпт.");
                    foreach($this->getServer()->getOnlinePlayers() as $pl){
                      if (isset($cfg["vla"][$pl->getName()])) {
                        $pl->sendMessage("§bКореш: §7".$sender->getName()." взял §e{$s} §7пт. со склад");
                      }
                    }
                  }else{$sender->sendMessage("§7Использование: /store <take/put> <1-30>"); return false;}
                }else{$sender->sendMessage("§cСклад закрыт"); return false;}
              }elseif ($args[0] == "put") {
                $s = $args[1];
                if (!$sender->getInventory()->contains(Item::get(351, 8, $s))){$sender->sendMessage("§eУ тебя нет столько пуль§c!"); return false;}
                $sender->sendMessage("§7Ты положил §b{$s}§7 патроны на склад");
                $sender->getInventory()->removeItem(Item::get(351, 8, $s));
                $store["vla"]["score"] += $s;
                $this->store->setAll($store);
                $this->store->save();
                $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(347, 78, 197));
                $tile->setText("§b§lСостояние склада:", "§3§l".$store["vla"]["score"]." §l§bпт.");
                foreach($this->getServer()->getOnlinePlayers() as $pl){
                  if (isset($cfg["vla"][$pl->getName()])) {
                    $pl->sendMessage("§bКореш: §7".$sender->getName()." положил §e{$s} §7пт. на склад");
                  }
                }
              }elseif ($args[0] == "close") {
                if ($cfg["vla"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["vla"]["lock"] == "0"){
                  $sender->sendMessage("§7Ты закрыл склад");
                  $store["vla"]["lock"]++;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["vla"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." закрыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был закрыт."); return false;}
              }elseif ($args[0] == "open") {
                if ($cfg["vla"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["vla"]["lock"] == "1"){
                  $sender->sendMessage("§7Ты открыл склад");
                  $store["vla"]["lock"]--;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["vla"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." открыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был открыт."); return false;}
              }
            }else{$sender->sendMessage("§7Ты слишком далеко от склада§4!"); return false;}
          }elseif (isset($cfg["tbg"][$sender->getName()])) {
            if($sender->distance(new Vector3(-216, 72, 33)) < 5){
              if ($args[0] == "take") {
                if($store["tbg"]["lock"] != "1"){
                  if($store["tbg"]["score"] < 1){$sender->sendMessage("§bСлад на данный момент пустой§c!"); return false;}
                  $s = $args[1];
                  if ($s > 1 || $s < 30){
                    if ($sender->getInventory()->contains(Item::get(351, 8, 10))) {$sender->sendMessage("§eУ тебя есть уже пули§c!"); return false;}
                    $sender->sendMessage("§7Ты взял §b{$s}§7 патроны со склада");
                    $store["tbg"]["score"] -= $s;
                    $sender->getInventory()->addItem(Item::get(351, 8, $s));
                    $this->store->setAll($store);
                    $this->store->save();
                    $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-216, 72, 33));
                    $tile->setText("§b§lСостояние склада:", "§3§l".$store["tbg"]["score"]." §l§bпт.");
                    foreach($this->getServer()->getOnlinePlayers() as $pl){
                      if (isset($cfg["tbg"][$pl->getName()])) {
                        $pl->sendMessage("§bКореш: §7".$sender->getName()." взял §e{$s} §7пт. со склад");
                      }
                    }
                  }else{$sender->sendMessage("§7Использование: /store <take/put> <1-30>"); return false;}
                }else{$sender->sendMessage("§cСклад закрыт"); return false;}
              }elseif ($args[0] == "put") {
                $s = $args[1];
                if (!$sender->getInventory()->contains(Item::get(351, 8, $s))){$sender->sendMessage("§eУ тебя нет столько пуль§c!"); return false;}
                $sender->sendMessage("§7Ты положил §b{$s}§7 патроны на склад");
                $sender->getInventory()->removeItem(Item::get(351, 8, $s));
                $store["tbg"]["score"] += $s;
                $this->store->setAll($store);
                $this->store->save();
                $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-216, 72, 33));
                $tile->setText("§b§lСостояние склада:", "§3§l".$store["tbg"]["score"]." §l§bпт.");
                foreach($this->getServer()->getOnlinePlayers() as $pl){
                  if (isset($cfg["tbg"][$pl->getName()])) {
                    $pl->sendMessage("§bКореш: §7".$sender->getName()." положил §e{$s} §7пт. на склад");
                  }
                }
              }elseif ($args[0] == "close") {
                if ($cfg["tbg"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["tbg"]["lock"] == "0"){
                  $sender->sendMessage("§7Ты закрыл склад");
                  $store["tbg"]["lock"]++;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["tbg"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." закрыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был закрыт."); return false;}
              }elseif ($args[0] == "open") {
                if ($cfg["tbg"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["tbg"]["lock"] == "1"){
                  $sender->sendMessage("§7Ты открыл склад");
                  $store["tbg"]["lock"]--;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["tbg"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." открыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был открыт."); return false;}
              }
            }else{$sender->sendMessage("§7Ты слишком далеко от склада§4!"); return false;}
          }elseif (isset($cfg["gsf"][$sender->getName()])) {
            if($sender->distance(new Vector3(341, 79, -221)) < 5){
              if ($args[0] == "take") {
                if($store["gsf"]["lock"] != "1"){
                  if($store["gsf"]["score"] < 1){$sender->sendMessage("§bСлад на данный момент пустой§c!"); return false;}
                  $s = $args[1];
                  if ($s > 1 || $s < 30){
                    if ($sender->getInventory()->contains(Item::get(351, 8, 10))) {$sender->sendMessage("§eУ тебя есть уже пули§c!"); return false;}
                    $sender->sendMessage("§7Ты взял §b{$s}§7 патроны со склада");
                    $store["gsf"]["score"] -= $s;
                    $sender->getInventory()->addItem(Item::get(351, 8, $s));
                    $this->store->setAll($store);
                    $this->store->save();
                    $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(341, 79, -221));
                    $tile->setText("§b§lСостояние склада:", "§3§l".$store["gsf"]["score"]." §l§bпт.");
                    foreach($this->getServer()->getOnlinePlayers() as $pl){
                      if (isset($cfg["gsf"][$pl->getName()])) {
                        $pl->sendMessage("§bКореш: §7".$sender->getName()." взял §e{$s} §7пт. со склад");
                      }
                    }
                  }else{$sender->sendMessage("§7Использование: /store <take/put> <1-30>"); return false;}
                }else{$sender->sendMessage("§cСклад закрыт"); return false;}
              }elseif ($args[0] == "put") {
                $s = $args[1];
                if (!$sender->getInventory()->contains(Item::get(351, 8, $s))){$sender->sendMessage("§eУ тебя нет столько пуль§c!"); return false;}
                $sender->sendMessage("§7Ты положил §b{$s}§7 патроны на склад");
                $sender->getInventory()->removeItem(Item::get(351, 8, $s));
                $store["gsf"]["score"] += $s;
                $this->store->setAll($store);
                $this->store->save();
                $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(341, 79, -221));
                $tile->setText("§b§lСостояние склада:", "§3§l".$store["gsf"]["score"]." §l§bпт.");
                foreach($this->getServer()->getOnlinePlayers() as $pl){
                  if (isset($cfg["gsf"][$pl->getName()])) {
                    $pl->sendMessage("§bКореш: §7".$sender->getName()." положил §e{$s} §7пт. на склад");
                  }
                }
              }elseif ($args[0] == "close") {
                if ($cfg["gsf"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["gsf"]["lock"] == "0"){
                  $sender->sendMessage("§7Ты закрыл склад");
                  $store["gsf"]["lock"]++;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["gsf"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." закрыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был закрыт."); return false;}
              }elseif ($args[0] == "open") {
                if ($cfg["gsf"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["gsf"]["lock"] == "1"){
                  $sender->sendMessage("§7Ты открыл склад");
                  $store["gsf"]["lock"]--;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["gsf"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." открыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был открыт."); return false;}
              }
            }else{$sender->sendMessage("§7Ты слишком далеко от склада§4!"); return false;}
          }elseif (isset($cfg["fsb"][$sender->getName()])) {
            if($sender->distance(new Vector3(-208, 73, 271)) < 5){
              if ($args[0] == "take") {
                if($store["fsb"]["lock"] != "1"){
                  if($store["fsb"]["score"] < 1){$sender->sendMessage("§bСлад на данный момент пустой§c!"); return false;}
                  $s = $args[1];
                  if ($s > 1 || $s < 30){
                    if ($sender->getInventory()->contains(Item::get(351, 8, 10))) {$sender->sendMessage("§eУ тебя есть уже пули§c!"); return false;}
                    $sender->sendMessage("§7Ты взял §b{$s}§7 патроны со склада");
                    $store["fsb"]["score"] -= $s;
                    $sender->getInventory()->addItem(Item::get(351, 8, $s));
                    $this->store->setAll($store);
                    $this->store->save();
                    $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-208, 73, 271));
                    $tile->setText("§b§lСостояние склада:", "§3§l".$store["fsb"]["score"]." §l§bпт.");
                    foreach($this->getServer()->getOnlinePlayers() as $pl){
                      if (isset($cfg["fsb"][$pl->getName()])) {
                        $pl->sendMessage("§bКореш: §7".$sender->getName()." взял §e{$s} §7пт. со склад");
                      }
                    }
                  }else{$sender->sendMessage("§7Использование: /store <take/put> <1-30>"); return false;}
                }else{$sender->sendMessage("§cСклад закрыт"); return false;}
              }elseif ($args[0] == "put") {
                $s = $args[1];
                if (!$sender->getInventory()->contains(Item::get(351, 8, $s))){$sender->sendMessage("§eУ тебя нет столько пуль§c!"); return false;}
                $sender->sendMessage("§7Ты положил §b{$s}§7 патроны на склад");
                $sender->getInventory()->removeItem(Item::get(351, 8, $s));
                $store["fsb"]["score"] += $s;
                $this->store->setAll($store);
                $this->store->save();
                $tile = $this->getServer()->getLevelByName("world")->getTile(new Vector3(-208, 73, 271));
                $tile->setText("§b§lСостояние склада:", "§3§l".$store["fsb"]["score"]." §l§bпт.");
                foreach($this->getServer()->getOnlinePlayers() as $pl){
                  if (isset($cfg["fsb"][$pl->getName()])) {
                    $pl->sendMessage("§bКореш: §7".$sender->getName()." положил §e{$s} §7пт. на склад");
                  }
                }
              }elseif ($args[0] == "close") {
                if ($cfg["fsb"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["fsb"]["lock"] == "0"){
                  $sender->sendMessage("§7Ты закрыл склад");
                  $store["fsb"]["lock"]++;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["fsb"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." закрыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был закрыт."); return false;}
              }elseif ($args[0] == "open") {
                if ($cfg["fsb"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для §cзакрытие§7 склада§4!"); return false;}
                if($store["fsb"]["lock"] == "1"){
                  $sender->sendMessage("§7Ты открыл склад");
                  $store["fsb"]["lock"]--;
                  $this->store->setAll($store);
                  $this->store->save();
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if (isset($cfg["fsb"][$pl->getName()])) {
                      $pl->sendMessage("§bКореш: §7".$sender->getName()." открыл склад.");
                    }
                  }
                }else{$sender->sendMessage("§7Склад до этого был открыт."); return false;}
              }
            }else{$sender->sendMessage("§7Ты слишком далеко от склада§4!"); return false;}
          }else{$sender->sendMessage("§7Чтобы §bбрать/ложить§7 патроны надо быть в соучастии §3vla§7/§emexico§7/§agla§7/§cital"); return false;}
        return false;}

        if($command->getName() == "sms"){
          $ra = $this->ra->getAll();
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /sms <nick> <txt>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          unset($args[0]);
          if($player == NULL){$sender->sendMessage("§cТакого игрока не найдено"); return false;}
          $text = implode(" ", $args);
          $gm = $ra["mon"][$sender->getName()];
          foreach($this->getServer()->getOnlinePlayers() as $pl){
            if (isset($admins["admin"][$pl->getName()])) {
              $pl->sendMessage("§7[§eА§7]((".$sender->getName()." отправил смс для: ".$player->getName().": ".$text."))");
            }
          }
          if($gm >= 1){
            $sender->sendPopup("§c-1$");
            $sender->sendMessage("§6Сообщение для: §b".$player->getName().": ".$text."");
            $player->sendMessage("§6Сообщение от: §b".$sender->getName().": ".$text."");
            $ra["mon"][$sender->getName()] -= 1;
            $this->ra->setAll($ra);
            $this->ra->save();
          }else{$sender->sendMessage("§7У вас нехватает денег§c!"); return false;}
        return false;}

        if ($command->getName() == "report") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /report <text>"); return false;}
          if(isset($admins["admin"][$sender->getName()])){$sender->sendMessage("§7Ты не можешь использопавь эту команду§c!"); return false;}
          $name = $sender->getName();
          $reportei = implode(" ", $args);
          foreach($this->getServer()->getOnlinePlayers() as $adminreport){
            if(isset($admins["admin"][$adminreport->getName()])){
              $adminreport->sendMessage("§a[§eА§a]§7" . $name . " Отправил репорт:§e " . $reportei . "");
              $right = str_repeat(" ", 45);
              $t = time() + (3 * 60 * 60);
              $time = gmdate("H:i:s, $t");
              $adminreport->sendMessage("" . $right . "§bВремя по МСК:§3 " . date("H:i:s") . " \n " . $right . "§e§l§l§lREPORT++");
            }
          }
          $sender->sendMessage("§bЗаявка от вас:§7 " . $reportei . "");
        return false;}


        if ($command->getName() == "members"){
          if (isset($cfg["army"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн Армии§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $membersarmy){
              if (isset($cfg["army"][$membersarmy->getName()])) {
                $listarm = $cfg["army"][$membersarmy->getName()];
                $sender->sendMessage("§bРаботник: §7" . $membersarmy->getName() . " §с" . $listarm . "-ый Ранг§7.");
              }
            }
          }elseif (isset($cfg["hospital"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн Больницы§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $membershos){
              if (isset($cfg["hospital"][$membershos->getName()])) {
                $listhos = $cfg["hospital"][$membershos->getName()];
                $sender->sendMessage("§bРаботник: §7" . $membershos->getName() . " §c" . $listhos . "-ый Ранг§7.");
              }
            }
          }elseif (isset($cfg["police"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн Полиции§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $memberspolice){
              if (isset($cfg["police"][$memberspolice->getName()])) {
                $listpolice = $cfg["police"][$memberspolice->getName()];
                $sender->sendMessage("§bРаботник: §7" . $memberspolice->getName() . " §c" . $listpolice . "-ый Ранг§7.");
              }
            }
          }elseif (isset($cfg["pravo"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн Правительства§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $memberspravo){
              if (isset($cfg["pravo"][$memberspravo->getName()])) {
                $listpravo = $cfg["pravo"][$memberspravo->getName()];
                $sender->sendMessage("§bРаботник: §7" . $memberspravo->getName() . " §c" . $listpravo . "-ый Ранг§7.");
              }
            }
          }elseif (isset($cfg["massmedia"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн СМИ§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $membersmass){
              if (isset($cfg["massmedia"][$membersmass->getName()])) {
                $listmass = $cfg["massmedia"][$membersmass->getName()];
                $sender->sendMessage("§bРаботник: §7" . $membersmass->getName() . " §c" . $listmass . "-ый Ранг§7.");
              }
            }
          }elseif (isset($cfg["vla"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн §3VLA§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $membersgsfi){
              if (isset($cfg["vla"][$membersgsfi->getName()])) {
                $listgsfv = $cfg["vla"][$membersgsfi->getName()];
                $sender->sendMessage("§bКореш: §7" . $membersgsfi->getName() . " §с" . $listgsfv . "-ый Ранг§7.");
              }
            }
          }elseif (isset($cfg["gsf"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн Mexico§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $membersgsf){
              if (isset($cfg["gsf"][$membersgsf->getName()])) {
                $listgsf = $cfg["gsf"][$membersgsf->getName()];
                $sender->sendMessage("§bКореш: §7" . $membersgsf->getName() . " §с" . $listgsf . "-ый Ранг§7.");
              }
            }
          }elseif (isset($cfg["fsb"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн Итальянская мафия§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $membersgs){
              if (isset($cfg["fsb"][$membersgs->getName()])) {
                $listgs = $cfg["fsb"][$membersgs->getName()];
                $sender->sendMessage("§bКореш: §7" . $membersgs->getName() . " §c" . $listgs . "-ый Ранг§7.");
              }
            }
          }elseif (isset($cfg["tbg"][$sender->getName()])) {
            $sender->sendMessage("§7-----§eОнлайн GLA§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $membersg){
              if (isset($cfg["tbg"][$membersg->getName()])) {
                $listg = $cfg["tbg"][$membersg->getName()];
                $sender->sendMessage("§bКореш: §7" . $membersg->getName() . " §c" . $listg . "-ый Ранг§7.");
              }
            }
          }else{$sender->sendMessage("§7Ты негде не работаешь§c!"); return false;}
        return false;}

        if ($command->getName() == "admmem"){
          if (isset($admins["admin"][$sender->getName()])) {
            $sender->sendMessage("§7-----§bОнлайн Администрации§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $adm){
              if (isset($admins["admin"][$adm->getName()])) {
                $listaadmin = $admins["admin"][$adm->getName()];
                $sender->sendMessage("§cАдминистратор: §3" . $adm->getName() . " §7" . $listaadmin . "-ый §eLVL§7.");
              }
            }
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        return false;}

        if($command->getName() == "do"){
          if(isset($args[0])){
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if($sender->distance($pl->asVector3()) < 11){
                $text = implode(" ", $args);
                $pl->sendMessage("§9" . $text . " | -" . $sender->getName() . "");
              }
            }

            $text = implode(" ", $args);
            $sender->sendPopup("");
          }else{$sender->sendMessage("§7Использование: /do <действие>"); return false;}
        return false;}

        if($command->getName() == "try"){
          if(isset($args[0])){
            $try = mt_rand(1, 2);
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if($sender->distance($pl->asVector3()) < 11){
                $text = implode(" ", $args);
                if($try == "1"){
                  $pl->sendMessage("§d" . $sender->getName() . ": " . $text    . " §a| Удачно");
                }elseif($try == "2"){
                  $pl->sendMessage("§d" . $sender->getName() . ": " . $text . " §c| Неудачно");
                }
              }
            }

            $text = implode(" ", $args);
            $sender->sendPopup("");
          }else{$sender->sendMessage("§7Использование: /try <действие>"); return false;}
        return false;}

        if($command->getName() == "b"){
          if(isset($args[0])){
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if($sender->distance($pl->asVector3()) < 11){
                $text = implode(" ", $args);
                $pl->sendMessage("§f((" . $sender->getName() . " сказал-(а): " . $text . "))");
              }
            }

            $text = implode(" ", $args);
            $sender->sendPopup("");
          }else{$sender->sendMessage("§7Использование: /b <действие>"); return false;}
        return false;}

        if($command->getName() == "s"){
          if(isset($args[0])){
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if($sender->distance($pl->asVector3()) < 20){
                $text = implode(" ", $args);
                $pl->sendMessage("§f" . $sender->getName() . " крикнул-( а): " . $text . "");
              }
            }

            $text = implode(" ", $args);
            $sender->sendPopup("");
          }else{$sender->sendMessage("§7Использование: /s <слова>"); return false;}
        return false;}

        if ($command->getName() == "sulist"){
          $ros = $this->ros->getAll();
          if (isset($cfg["police"][$sender->getName()])) {
            $sender->sendMessage("§7-----§bРозыск§7-----");
            foreach($this->getServer()->getOnlinePlayers() as $su){
              $listaadmi = $ros["ros"][$su->getName()];
              if ($listaadmi >= 1){
                $sender->sendMessage("§cРозыск: §3" . $su->getName() . " §7" . $listaadmi . "-ый розыск§7.");
              }else{$sender->sendMessage("§7В розыске никого нету"); return false;}
            }
          }else{$sender->sendMessage("§cВы не полицейский§3!"); return false;}
        return false;}

        if ($command->getName() == "police") {
          if(isset($cfg["police"][$sender->getName()])){
            $sender->sendMessage("§e——Полиция—— \n §e/cuff - §7Наручники. \n §e/su - §7Выдать розыск. \n §e/unsu - §7Забрать розыск. \n §e/sulist - §7Розыскиваемые. \n §e/camera - §7Камеры. \n §e/arrest - §7Посадить В Тюрьму. \n §e/leave - §7Уйти Из Фракции. \n §e/uninvite - §7Кикнуть Игрока Из Фракции. \n §e/invite - §7Принять Игрока В Полицию \n §e/gov - §7Государственные Новости Полиции. \n§e——Полиция——");
            return false;
          }else{$sender->sendMessage("§aТы не полицейский"); return false;}
        }

        if ($command->getName() == "army") {
          if(isset($cfg["army"][$sender->getName()])){
            $sender->sendMessage("§a——Армия—— \n §a/cuff - §7Наручники. \n §a/kpp - §7Главные ворота. \n §a/leave - §7Уйти Из Фракции. \n §a/uninvite - §7Кикнуть Игрока Из Фракции. \n §a/invite - §7Принять Игрока В Армию \n §a/gov - §7Государственные Новости Армии. \n§a——Армия——");
            return false;
          }else{$sender->sendMessage("§aТы не служишь в армии"); return false;}
        }

        if ($command->getName() == "hospital") {
          if (isset($cfg["hospital"][$sender->getName()])) {
            $sender->sendMessage("§c——Больница—— \n §c/heal - §7Вылечить Игрока \n §c/invite - §7Принять Игрока В Больницу \n §c/uninvite - §7Кикнуть Игрока Из Больницы \n §c/gov - §7Государственные Новости Больницы \n §c/leave - §7Уйти Из Фракции \n §c——Больница——");
            return false;
          }else{$sender->sendMessage("§7Ты не работник §cбольницы§7."); return false;}
        }

        if ($command->getName() == "gang") {
          if (isset($cfg["tbg"][$sender->getName()]) or isset($cfg["vla"][$sender->getName()])){
            $sender->sendMessage("§e——Банды—— \n §b/store - §7Склад \n §b/invite - §7Принять Игрока В Банду \n §b/uninvite - §7Кикнуть Игрока Из Банды \n §b/tie - §7Цепи \n §b/untie - §7Снять цепь с игрока \n §b/leave - §7Уйти Из Фракции \n §e——Банды——");
            return false;
          }else{$sender->sendMessage("§7Ты не §3бандит§7."); return false;}
        }

        if($command->getName() == "cc"){
          if (isset($admins["admin"][$sender->getName()])){
            for($cc = 0; $cc < 100; $cc++){
              $this->getServer()->broadcastMessage(" ");
            }
            $this->getServer()->broadcastMessage("§6Администратор ".$sender->getName()." очистил чат");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if($command->getName() == "cuff") {
          $data = $this->cares->getAll();
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /cuff <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player != NULL){
            if($this->tie[$player->getName()] == "off"){
              if (isset($cfg["army"][$sender->getName()])) {
                if ($cfg["army"][$sender->getName()] > 5){
                  if($sender->distance($player->asVector3()) > 5){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
                  $player->sendMessage("§7На тебя надели наручники§6 ".$sender->getName()."§c!");
                  $sender->sendMessage("§7Ты надел наручники на §6" . $player->getName() . "§c!");
                  $player->setImmobile();
                  $this->cuff[$player->getName()] = "on";
                }else{$sender->sendMessage("§7Твой §6ранг§7 не настолько высокий"); return false;}
              }elseif (isset($cfg["police"][$sender->getName()])) {
                if ($cfg["police"][$sender->getName()] > 4){
                  if($sender->distance($player->asVector3()) > 5){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
                  $player->sendMessage("§7На тебя надели наручники§6 ".$sender->getName()."§c!");
                  $sender->sendMessage("§7Ты надел наручники на §6" . $player->getName() . "§c!");
                  $player->setImmobile();
                  $this->cuff[$player->getName()] = "on";
                }else{$sender->sendMessage("§7Твой §6ранг§7 не настолько высокий"); return false;}
              }else{$sender->sendMessage("§7Ты должен работать в армии или в полиции."); return false;}
            }else{
              $sender->sendMessage("§7Данный игрок перевязан, вы не сможете надеть наручники.");
              return false;
            }
          }else{$sender->sendMessage("§7Данный игрок не найден."); return false;}
        return false;}

        if($command->getName() == "uncuff") {
          $data = $this->cares->getAll();
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /uncuff <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if($sender->distance($player->asVector3()) > 5){ $sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false; }
          if ($player != NULL){
            if($this->tie[$player->getName()] == "off"){
              if (isset($cfg["army"][$sender->getName()])) {
                if ($cfg["army"][$sender->getName()] > 5){
                  $sender->sendMessage("§7Ты снял наручники с §b " . $player->getName());
                  $player->setImmobile(false);
                  $player->sendMessage("§b" . $sender->getName() . "§7: Снял с тебя наручники");
                  $this->cuff[$player->getName()] = "off";
                  return false;
                }else{
                  $sender->sendMessage("§7Твой §6ранг§7 не настолько высокий");
                  return false;
                }
              }elseif (isset($cfg["police"][$sender->getName()])) {
                if ($cfg["police"][$sender->getName()] > 4){
                  $sender->sendMessage("§7Ты снял наручники с §b " . $player->getName());
                  $player->setImmobile(false);
                  $player->sendMessage("§b" . $sender->getName() . "§7: Снял с тебя наручники");
                  $this->cuff[$player->getName()] = "off";
                }else{$sender->sendMessage("§7Твой §6ранг§7 не настолько высокий"); return false;}
              }else{$sender->sendMessage("§7Ты должен работаь в армии или в полиции."); return false;}
            }else{
              $sender->sendMessage("§7Данный игрок не в наручниках");
              return false;
            }
          }else{$sender->sendMessage("§7Данный игрок не найден."); return false;}
        return false;}

        if($command->getName() == "tie") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /tie <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          $data = $this->cares->getAll();
          if ($player != NULL){
            if($this->cuff[$player->getName()] == "off"){
              if (isset($cfg["tbg"][$sender->getName()])){
                if ($cfg["tbg"][$sender->getName()] > 3){
                  if($sender->distance($player->asVector3()) > 5){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
                  $player->sendMessage("§c".$sender->getName()."§7 Данный игрок теперь связан железной цепью§c!");
                  $sender->sendMessage("§c" . $player->getName() . " §7Данный игрок связал тебя цепью§c!");
                  $player->setImmobile();
                  $this->tie[$player->getName()] = "on";
                }else{$sender->sendMessage("§7Твой §6ранг§7 не настолько высокий"); return false;}
              }elseif (isset($cfg["vla"][$sender->getName()])){
                if ($cfg["vla"][$sender->getName()] > 3){
                  if($sender->distance($player->asVector3()) > 5){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
                  $player->sendMessage("§c".$sender->getName()."§7 Данный игрок теперь связан железной цепью§c");
                  $sender->sendMessage("§c" . $player->getName() . " §7Данный игрок связал тебя цепью§c!");
                  $player->setImmobile();
                  $this->tie[$player->getName()] = "on";
                }else{$sender->sendMessage("§7Твой §6ранг§7 не настолько высокий"); return false;}
              }else{$sender->sendMessage("§7Ты не §6состоишь §7в §3банде"); return false;}
            }else{
              $sender->sendMessage("§cТы не сможешь надеть на него цепь §7*данный игрок в наручниках*");
              return false;
            }
          }else{$sender->sendMessage("§7Данный игрок не найден."); return false;}
        return false;}

        if($command->getName() == "untie") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /uncuff <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player != NULL){
            if($this->cuff[$player->getName()] == "on"){
              if (isset($cfg["tbg"][$sender->getName()])) {
                if ($cfg["tbg"][$sender->getName()] > 5){
                  if($sender->distance($player->asVector3()) > 5){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
                  $sender->sendMessage("§7Ты снял цепь с §e" . $player->getName());
                  $player->setImmobile(false);
                  $player->sendMessage("§e" . $sender->getName() . "§7: Снял с тебя цепь");
                  $this->tie[$player->getName()] = "off";
                  return false;
                }else{$sender->sendMessage("§7Твой §6ранг§7 не настолько высокий"); return false;}
              }elseif (isset($cfg["vla"][$sender->getName()])) {
                if ($cfg["vla"][$sender->getName()] > 4){
                  if($sender->distance($player->asVector3()) > 5){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
                  $sender->sendMessage("§7Ты снял цепь с §e" . $player->getName());
                  $player->setImmobile(false);
                  $player->sendMessage("§e" . $sender->getName() . "§7: Снял с тебя цепь");
                  $this->tie[$player->getName()] = "off";
                }else{$sender->sendMessage("§7Твой §6ранг§7 не настолько высокий"); return false;}
              }else{$sender->sendMessage("§7Ты не §6состоишь §7в §3банде"); return false;}
            }else{
              $sender->sendMessage("§7Данный игрок не связан цепями");
              return false;
            }
          }else{$sender->sendMessage("§7Данный игрок не найден."); return false;}
        return false;}

        if ($command->getName() == "camera") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /camra <1-7>§с или§7 <off>"); return false;}
          if (isset($cfg["police"][$sender->getName()])){
            if ($cfg["police"][$sender->getName()] < 5){$sender->sendMessage("§7Твой ранг §3не дастаточно§7 высок чтобы §6просматривать§7 камеры§с!"); return false;}
            if ($args[0] == "1") {
              if ($sender->distance(new Vector3(-114, 79, -145)) > 5){$sender->sendMessage("§7Ты слишком §cдалеко§7 от компютера с §6данными§7 камер§c!"); return false;}
              $sender->sendMessage("§7Ты §3начал §7просматривать §6камеру #1§7.");
              $sender->teleport(new Vector3(-66, 77, 156));
              $sender->setImmobile();
              $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(14), 20 * 9999, 1));
              $sender->setNameTagVisible();
              $sender->addTitle("§eСостояние камеры: §aОтличная");
              return false;
            }elseif ($args[0] == "2") {
              if ($sender->distance(new Vector3(-114, 79, -145)) > 5){$sender->sendMessage("§7Ты слишком §cдалеко§7 от компютера с §6данными§7 камер§c!"); return false;}
                $sender->sendMessage("§7Ты §3начал §7просматривать §6камеру #2§7.");
                $sender->teleport(new Vector3(-82, 77, -133));
                $sender->setImmobile();
                $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(14), 20 * 9999, 1));
                $sender->setNameTagVisible(true);
                $sender->addTitle("§eСостояние камеры: §aОтличная");
                return false;
            }elseif ($args[0] == "3") {
              if ($sender->distance(new Vector3(-114, 79, -145)) > 5){$sender->sendMessage("§7Ты слишком §cдалеко§7 от компютера с §6данными§7 камер§c!"); return false;}
              $sender->sendMessage("§7Ты §3начал §7просматривать §6камеру #3§7.");
              $sender->teleport(new Vector3(106, 81, -240));
              $sender->setImmobile();
              $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(14), 20 * 9999, 1));
              $sender->setNameTagVisible(true);
              $sender->addTitle("§eСостояние камеры: §aОтличная");
              return false;
            }elseif ($args[0] == "4") {
              if ($sender->distance(new Vector3(-114, 79, -145)) > 5){$sender->sendMessage("§7Ты слишком §cдалеко§7 от компютера с §6данными§7 камер§c!"); return false;}
              $sender->sendMessage("§7Ты §3начал §7просматривать §6камеру #4§7.");
              $sender->teleport(new Vector3(183, 78, -128));
              $sender->setImmobile();
              $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(14), 20 * 9999, 1));
              $sender->setNameTagVisible(true);
              $sender->addTitle("§eСостояние камеры: §aОтличная");
              return false;
            }elseif ($args[0] == "5") {
              if ($sender->distance(new Vector3(-114, 79, -145)) > 5){$sender->sendMessage("§7Ты слишком §cдалеко§7 от компютера с §6данными§7 камер§c!"); return false;}
              $sender->sendMessage("§7Ты §3начал §7просматривать §6камеру #5§7.");
              $sender->teleport(new Vector3(190, 77, 185));
              $sender->setImmobile();
              $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(14), 20 * 9999, 1));
              $sender->setNameTagVisible(true);
              $sender->addTitle("§eСостояние камеры: §aОтличная");
              return false;
            }elseif ($args[0] == "6") {
              if ($sender->distance(new Vector3(-114, 79, -145)) > 5){$sender->sendMessage("§7Ты слишком §cдалеко§7 от компютера с §6данными§7 камер§c!"); return false;}
              $sender->sendMessage("§7Ты §3начал §7просматривать §6камеру #6§7.");
              $sender->teleport(new Vector3(-66, 77, 156));
              $sender->setImmobile();
              $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(14), 20 * 9999, 1));
              $sender->setNameTagVisible(true);
              $sender->addTitle("§eСостояние камеры: §aОтличная");
              return false;
            }elseif ($args[0] == "7") {
              if ($sender->distance(new Vector3(-114, 79, -145)) > 5){$sender->sendMessage("§7Ты слишком §cдалеко§7 от компютера с §6данными§7 камер§c!"); return false;}
              $sender->sendMessage("§7Ты §3начал §7просматривать §6камеру #7§7.");
              $sender->teleport(new Vector3(-129, 77, 339));
              $sender->setImmobile();
              $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(14), 20 * 9999, 1));
              $sender->setNameTagVisible(true);
              $sender->addTitle("§eСостояние камеры: §aОтличная");
              return false;
            }elseif ($args[0] == "off") {
              if ($sender->distance(new Vector3(113, 10, 29)) < 5){$sender->sendMessage("§7Ты слишком §cдалеко§7 от камеры§c!"); return false;}
              $sender->teleport(new Vector3(-113, 78, -143));
              $sender->sendMessage("§7Вы §aотключились§7 от §3камеры§c!");
              $sender->removeEffect(Effect::INVISIBILITY);
              $sender->setImmobile(false);
              $sender->setNameTagVisible(false);
              return false;
            }
          }else{$sender->sendMessage("§7Ты не §6работаешь§7 в полиции§c!"); return false;}
        }

        if ($command->getName() == "rang") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /rang <nick> <1-9>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          $rang = $args[1];
          if(is_numeric($rang) and strlen($rang) == 1 and $rang <= 9){
            if (isset($cfg["army"][$sender->getName()])) {
              if ($cfg["army"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["army"][$player->getName()])){$sender->sendMessage("§7Данный игрок не служит в §a Армии."); return false;}
              if ($cfg["army"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["army"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            } elseif (isset($cfg["hospital"][$sender->getName()])) {
              if ($cfg["hospital"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["hospital"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в больнице."); return false;}
              if ($cfg["hospital"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["hospital"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            } elseif (isset($cfg["massmedia"][$sender->getName()])) {
              if ($cfg["massmedia"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["massmedia"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в радиоцентре."); return false;}
              if ($cfg["massmedia"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["massmedia"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            } elseif (isset($cfg["police"][$sender->getName()])) {
              if ($cfg["police"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["police"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в полиции."); return false;}
              if ($cfg["police"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["police"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            } elseif (isset($cfg["pravo"][$sender->getName()])) {
              if ($cfg["pravo"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["pravo"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в Правительстве."); return false;}
              if ($cfg["pravo"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["pravo"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            } elseif (isset($cfg["tbg"][$sender->getName()])) {
              if ($cfg["tbg"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["tbg"][$player->getName()])){$sender->sendMessage("§7Данный игрок не в семье §aGLA§7."); return false;}
              if ($cfg["tbg"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["tbg"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            } elseif (isset($cfg["vla"][$sender->getName()])) {
              if ($cfg["vla"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["vla"][$player->getName()])){$sender->sendMessage("§7Данный игрок не в семье §bVLA§7."); return false;}
              if ($cfg["vla"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["vla"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            } elseif (isset($cfg["gsf"][$sender->getName()])) {
              if ($cfg["gsf"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["gsf"][$player->getName()])){$sender->sendMessage("§7Данный игрок не в семье §eMixico§7."); return false;}
              if ($cfg["gsf"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["gsf"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            } elseif (isset($cfg["fsb"][$sender->getName()])) {
              if ($cfg["fsb"][$sender->getName()] < 10){$sender->sendMessage("§7Твой ранг не достаточно высок для повышения/пониженя других игроков."); return false;}
              if (!isset($cfg["fsb"][$player->getName()])){$sender->sendMessage("§7Данный игрок не в семье §7Итальянской мафии."); return false;}
              if ($cfg["fsb"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь понизить §6ранг§3 себе§c!"); return false;}
              $sender->sendMessage("§7Ты выдал §b{$rang}-й§7 ранг для §c" . $player->getName() . "§7.");
              $player->sendMessage("§b" . $sender->getName() . " §7выдал тебе §c{$rang}-й §7ранг.");
              $cfg["fsb"][$player->getName()] = $rang;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }else{$sender->sendMessage("§7Ты не состоишь в какой-небудь организации§4!"); return false;}
          }else{$sender->sendMessage("§7Использование: /rang <nick> <1-9>"); return false;}
        }

        if ($command->getName() == "jail") {
          if ($sender->isOp()) {
            if ($args[0] == "settp") {
              $sender->sendMessage("Нажми на блок, над которым будут спавниться игроки, которые в тюрьме.");
              $this->settings[$sender->getName()] = 1;
              return false;
            } elseif ($args[0] == "setblock") {
              $sender->sendMessage("Нажми на блок, по которому должны нажимать игроки, которые в тюрьме.");
              $this->settings[$sender->getName()] = 2;
              return false;
            }else{$sender->sendMessage("Использование: /jail <settp \ setblock>"); return false;}
          }else{$sender->sendMessage("Доступно только ОПераторам сервера."); return false;}
        }

        if ($command->getName() == "unjail") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /unjail <nick> <причина>"); return false;}
          if (!isset($admins["admin"][$sender->getName()])){$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          $ud = $this->ud->getAll();
          $data = $this->cofig->getAll();
          $player = $this->getServer()->getPlayer($args[0]);
          if(!isset($ud[$player->getName()])){$sender->sendMessage("§cДанный игрок не находится в КПЗ"); return false;}
          if($args[0] == ""){$sender->sendMessage("§7Использование: /unjail <Ник> <Причина>"); return false;}
          unset($args[0]);
          $msg = implode(" ", $args);
          if ($player == NULL){$sender->sendMessage("§cДанный игрок не находится в КПЗ"); return false;}
          $player->teleport(new Vector3(-105, 72, -131));
          $player->sendMessage("§7Ты теперь на §6свободе§7, нехотелось бы тебя §3видеть §7ещё.");
          $this->getServer()->broadcastMessage("§cАдминистратор ".$sender->getName()." освободил с КПЗ игрока ".$player->getName()." по причине: ".$msg);
          $this->ud->remove($player->getName());
          $this->ud->save();
          if (isset($cfg["army"][$player->getName()])) {
            $player->setNameTag('§2Военнаслужайщий: '.$player->getName());
            return false;
          }elseif (isset($cfg["hospital"][$player->getName()])) {
            $player->setNameTag('§cРаботник больницы: '.$player->getName());
            return false;
          }elseif (isset($cfg["massmedia"][$player->getName()])) {
            $player->setNameTag('§6Работник CМИ: '.$player->getName());
            return false;
          }elseif (isset($cfg["police"][$player->getName()])) {
            $player->setNameTag('§1Полицейский: '.$player->getName());
            return false;
          }elseif (isset($cfg["pravo"][$player->getName()])) {
            $player->setNameTag('§9Правительство: '.$player->getName());
            return false;
          }elseif (isset($cfg["vla"][$player->getName()])) {
            $player->setNameTag('§bVLA: '.$player->getName());
            return false;
          }elseif (isset($cfg["tbg"][$player->getName()])) {
            $player->setNameTag('§aGLA: '.$player->getName());
            return false;
          }elseif (isset($cfg["gsf"][$player->getName()])) {
            $player->setNameTag('§eMexico '.$player->getName());
            return false;
          }elseif (isset($cfg["fsb"][$player->getName()])) {
            $player->setNameTag('§c§lИтальянская мафия: '.$player->getName());
            return false;
          }elseif (!isset($cfg["fsb"][$player->getName()])){
            $player->setNameTag('Гражданин: '.$player->getName());
            return false;
          }
        }

        if ($command->getName() == "ajail") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /ajail <nick> <кликов> <причина>"); return false;}
          $ud = $this->ud->getAll();
          if (!isset($admins["admin"][$sender->getName()])){$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if(isset($ud[$player->getName()])){$sender->sendMessage("§cДанный игрок уже находится в КПЗ."); return false;}
          if ($player == NULL) {$sender->sendMessage("§7Игрок не найден."); return false;}
          $count = $args[1];
          if (!is_numeric($count)){$sender->sendMessage("§7Использование: /ajail <ник> <кол-во кликов> <Причина>"); return false;}
          if (!$count > 0) {$sender->sendMessage("§7Использование: /ajail <ник> <кол-во кликов> <Причина>"); return false;}
          if($msg == ""){$sender->sendMessage("§7Использование: /ajail <ник> <кол-во кликов> <Причина>"); return false;}
          $this->getServer()->broadcastMessage("§cАдминистратор ".$sender->getName()." посадил в КПЗ игрока ".$player->getName()." на ".$count." клик(-ов)"." по причине: ".$msg);
          $ud[$player->getName()]['did'] = 0;
          $ud[$player->getName()]['must'] = $count;
          $player->setNameTag('§6Заключенный: '.$player->getName());
          $this->ud->setAll($ud);
          $this->ud->save();
          $player->teleport(new Vector3(-119, 72, -122));
          return false;
        }

        if ($command->getName() == "arrest"){
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /arrest <nick>"); return false;}
          $arrest = $this->arrest->getAll();
          $ros = $this->ros->getAll();
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player != NULL){
            if($sender->distance($player->asVector3()) > 11){$sender->sendMessage("§7Данный игрок §6далеко§7 находится от тебя§c!"); return false;}
            if (isset($cfg["police"][$sender->getName()])) {
              if ($cfg["police"][$sender->getName()] > 4){
                if($sender->distance(new Vector3(-97, 70, -125)) > 15){$sender->sendMessage("§7Надо быть §6ближе §7к §3Полицейскому Депортаминту§c!"); return false;}
                if(!isset($ros["ros"][$player->getName()])){$sender->sendMessage("§7Данный игрок не в розыске§c!"); return false;}
                if (!isset($arrest["arrest"][$player->getName()])) {
                  $sender->sendMessage("§7Ты посадил §b" . $player->getName() . "§7 в §3обезьянник§c!");
                  $player->setNameTag('§6Заключенный: '.$player->getName());
                  $player->teleport(new Vector3(-119, 72, -122));
                  $player->sendMessage("§b" . $sender->getName() . "§7 посадил тебя в §3обезбянник§c!");
                  $arrest["arrest"][$player->getName()] = 1;
                  $this->arrest->setAll($arrest);
                  $this->arrest->save();
                  unset($ros["ros"][$player->getName()]);
                  $this->ros->setAll($ros);
                  $this->ros->save();
                }else{$sender->sendMessage("§7Данный игрок уже в тюрьме"); return false;}
              }else{$sender->sendMessage("§7Твой ранг не достаточно высок"); return false;}
            }else{$sender->sendMessage("§7Ты не полицейский"); return false;}
          }else{$sender->sendMessage("§7Данный игрок не найден"); return false;}
        return false;}

        if ($command->getName() == "unarrest"){
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /unarrest <nick>"); return false;}
          $arrest = $this->arrest->getAll();
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (!isset($cfg["police"][$sender->getName()])){$sender->sendMessage("§7Ты не §6работаешь §7в§3 Полицейском Депортаминте§c!"); return false;}
          if ($cfg["police"][$sender->getName()] < 5){$sender->sendMessage("§7Твой ранг не достаточно высок §6чтобы§7 выпустить из §3обезьянника§c!"); return false;}
          if($sender->distance(new Vector3(-119, 72, -122)) > 15){$sender->sendMessage("§7Надо быть §6ближе §7к §3обезьяннику§c!"); return false;}
          if (!isset($arrest["arrest"][$player->getName()])) {$sender->sendMessage("§7Данный игрок §6не§7 в обезьяннике§c!"); return false;}
          $sender->sendMessage("§7Ты выпустил §b" . $player->getName() . "§7 из §3обезьянника§c!");
          $player->teleport(new Vector3(-105, 72, -131));
          $player->sendMessage("§b" . $sender->getName() . "§7 выпустил тебя из §3обезбянника§c!");
          unset($arrest["arrest"][$player->getName()]);
          $this->arrest->setAll($arrest);
          $this->arrest->save();
          if (isset($cfg["army"][$player->getName()])) {
            $player->setNameTag('§2Военнаслужайщий: '.$player->getName());
          }elseif (isset($cfg["hospital"][$player->getName()])) {
            $player->setNameTag('§cРаботник больницы: '.$player->getName());
          }elseif (isset($cfg["massmedia"][$player->getName()])) {
            $player->setNameTag('§6Работник CМИ: '.$player->getName());
          }elseif (isset($cfg["police"][$player->getName()])) {
            $player->setNameTag('§1Полицейский: '.$player->getName());
          }elseif (isset($cfg["pravo"][$player->getName()])) {
            $player->setNameTag('§9Правительство: '.$player->getName());
          }elseif (isset($cfg["vla"][$player->getName()])) {
            $player->setNameTag('§bVLA: '.$player->getName());
          }elseif (isset($cfg["tbg"][$player->getName()])) {
            $player->setNameTag('§aGLA: '.$player->getName());
          }elseif (isset($cfg["gsf"][$player->getName()])) {
            $player->setNameTag('§eMexico '.$player->getName());
          }elseif (isset($cfg["fsb"][$player->getName()])) {
            $player->setNameTag('§c§lИтальянская мафия: '.$player->getName());
          }elseif (!isset($cfg["fsb"][$player->getName()])){
            $player->setNameTag('Гражданин: '.$player->getName());
          }
        return false;}

        if ($command->getName() == "setro") {
          if ($sender->isOp()) {
            if(count($args) == 2) {
              $number = $args[0];
              $price = $args[1];
              if (is_numeric($number) && is_numeric($price)) {
                if ($number > 0 && $price > 0) {
                  $data = $this->getConfig()->getAll();
                  if (!$this->getConfig()->exists($number)) {
                    $data[$number]['owner'] = 'not';
                    $data[$number]['price'] = $price;
                    $data[$number]['status'] = 'home';
                    $data[$number]['sign']['x'] = 1;
                    $data[$number]['sign']['y'] = 1;
                    $data[$number]['sign']['z'] = 1;
                    $data[$number]['door']['x'] = 2;
                    $data[$number]['door']['y'] = 2;
                    $data[$number]['door']['z'] = 2;
                    $data[$number]['join']['x'] = 3;
                    $data[$number]['join']['y'] = 3;
                    $data[$number]['join']['z'] = 3;
                    $data[$number]['quit']['x'] = 4;
                    $data[$number]['quit']['y'] = 4;
                    $data[$number]['quit']['z'] = 4;
                    $this->progress[$sender->getName()] = 0;
                    $this->number[$sender->getName()] = $number;
                    $this->getConfig()->setAll($data);
                    $this->getConfig()->save();
                    $sender->sendMessage("Нажми на табличку.");
                    return false;
                  }else{$sender->sendMessage("Данный дом уже создан."); return false;}
                }else{$sender->sendMessage("Цена и номер дома не могут быть ниже нуля."); return false;}
              }else{$sender->sendMessage("Использование: /sethome <номер> <цена>"); return false;}
            }else{$sender->sendMessage("Использование: /sethome <номер> <цена>"); return false;}
          }else{$sender->sendMessage("Доступно только операторам сервера."); return false;}
        }

        if ($command->getName() == "delro") {
          if ($sender->isOp()) {
            if (count($args) == 1) {
              $number = $args[0];
              if (is_numeric($number)) {
                if ($this->getConfig()->exists($number)) {
                  $this->getConfig()->remove($number);
                  $sender->sendMessage("Удалён.");
                  return false;
                }else{$sender->sendMessage("Данный номер дома не найден."); return false;}
              }else{$sender->sendMessage("Использование: /delro <номер>"); return false;}
            }else{$sender->sendMessage("Использование: /delro <номер>"); return false;}
          }else{$sender->sendMessage("Доступно только операторам сервера."); return false;}
        }

        if ($command->getName() == "join") {
          if ($this->players->get($sender->getName()) != NULL) {
            $data = $this->getConfig()->getAll();
            $x = $sender->getFloorX() - $data[$this->players->get($sender->getName())]['door']['x'];
            $y = $sender->getFloorY() - $data[$this->players->get($sender->getName())]['door']['y'];
            $z = $sender->getFloorZ() - $data[$this->players->get($sender->getName())]['door']['z'];
            if ($x < 5 && $y < 5 && $z < 5 && $x > -5 && $y > -5 && $z > -5) {
              $sender->sendMessage("Ты вошёл внутрь своего дома.");
              $join = $data[$this->players->get($sender->getName())]['join'];
              $sender->teleport(new Vector3($join['x'], $join['y'], $join['z']));
              return false;
            }
          }
        }

        if ($command->getName() == "quit") {
          if ($this->players->get($sender->getName()) != NULL) {
            $data = $this->getConfig()->getAll();
            $x = $sender->getFloorX() - $data[$this->players->get($sender->getName())]['door']['x'];
            $y = $sender->getFloorY() - $data[$this->players->get($sender->getName())]['door']['y'];
            $z = $sender->getFloorZ() - $data[$this->players->get($sender->getName())]['door']['z'];
            if ($x < 5 && $y < 5 && $z < 5 && $x > -5 && $y > -5 && $z > -5) {
              $sender->sendMessage("Ты вышёл наружу со своего дома.");
              $quit = $data[$this->players->get($sender->getName())]['quit'];
              $sender->teleport(new Vector3($quit['x'], $quit['y'], $quit['z']));
              return false;
            }
          }
        }

        if($command->getName() == "unwatch"){
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 2){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            $sender->setGamemode(0);
            $sender->sendMessage("§cВы §7прекратили слежку");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if($command->getName() == "suad"){
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 4){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Поменял игровой режим на Выживание.");
              }
            }
            $sender->setGamemode(0);
            $sender->sendMessage("§eВы сминили игровой режим для себя на §6Выживание§c!");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if($command->getName() == "crad"){
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 4){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Сменил игровой режим на Креатив.");
              }
            }
            $sender->setGamemode(1);
            $sender->sendMessage("§eВы сминили себе игровой режим на §3Креатив§c!");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if($command->getName() == "srad"){
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 3){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Включил невидимость.");
              }
            }
            $sender->addEffect(new \pocketmine\entity\EffectInstance(Effect::getEffect(14), 20 * 9999, 1));
            $sender->sendMessage("§eВы включили невидимость§c!");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if($command->getName() == "sradoff"){
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 3){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Выкключил невидимость.");
              }
            }
            $sender->removeEffect(Effect::INVISIBILITY);
            $sender->sendMessage("§eВы выключили невидимость§c!");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "kpp") {
          $cfg = $this->cfg->getAll();
          $kpp = $this->kpp->getAll();
          if($sender->distance(new Vector3(299, 72, 342)) < 11){
            if (isset($cfg["army"][$sender->getName()])) {
              if($kpp["oc"] == 1){
                $sender->sendMessage("§e§lТы §cзакрыл§e главное §6KPP§e военнаслужайщих§c!");
                $sender->getLevel()->setBlock(new Vector3(299, 74, 342), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 342), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 342), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 342), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 341), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 341), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 341), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 341), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 340), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 340), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 340), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 340), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 339), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 339), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 339), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 339), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 338), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 338), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 338), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 338), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 337), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 337), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 337), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 337), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 336), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 336), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 336), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 336), Block::get(101, 0));
                $kpp["oc"] = 2;
                $this->kpp->setAll($kpp);
                $this->kpp->save();
                return false;
              }elseif($kpp["oc"] == 2){
                $sender->sendMessage("§e§lТы §aоткрыл§e главное §6KPP§e военнаслужайщих§c!");
                $sender->getLevel()->setBlock(new Vector3(299, 74, 342), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 342), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 342), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 342), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 341), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 341), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 341), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 341), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 340), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 340), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 340), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 340), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 339), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 339), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 339), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 339), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 338), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 338), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 338), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 338), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 337), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 337), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 337), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 337), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 74, 336), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 73, 336), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 72, 336), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(299, 71, 336), Block::get(0, 0));
                $kpp["oc"] = 1;
                $this->kpp->setAll($kpp);
                return false;
              }
            }else{$sender->sendMessage("§7Для того чтобы открыть§6 КПП, §7надо быть §aармейцем§c!"); return false;}
          }else{$sender->sendMessage("§l§c Для того чтобы открыть КПП надо быть плизко к ним"); return false;}
        }

        if ($command->getName() == "mafiakpp") {
          $cfg = $this->cfg->getAll();
          $kpp = $this->kpp->getAll();
          if($sender->distance(new Vector3(11, 72, -47)) < 11){
            if (isset($cfg["fsb"][$sender->getName()]) or isset($cfg["gsf"][$sender->getName()])){
              if($kpp["mafia"] == 1){
                $sender->sendMessage("§e§lТы §cзакрыл§e вороты§c!");
                $sender->getLevel()->setBlock(new Vector3(11, 74, -47), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 74, -46), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 73, -48), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 73, -47), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 73, -46), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 73, -45), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 72, -48), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 72, -47), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 72, -46), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 72, -45), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 71, -48), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 71, -47), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 71, -46), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 71, -45), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 74, -47), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 74, -46), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 73, -48), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 73, -47), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 73, -46), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 73, -45), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 72, -48), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 72, -47), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 72, -46), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 72, -45), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 71, -48), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 71, -47), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 71, -46), Block::get(101, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 71, -45), Block::get(101, 0));
                $kpp["mafia"] = 2;
                $this->kpp->setAll($kpp);
                $this->kpp->save();
                return false;
              }elseif($kpp["mafia"] == 2){
                $sender->sendMessage("§e§lТы §aоткрыл§e вороты§c!");
                $sender->getLevel()->setBlock(new Vector3(11, 74, -47), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 74, -46), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 73, -48), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 73, -47), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 73, -46), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 73, -45), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 72, -48), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 72, -47), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 72, -46), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 72, -45), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 71, -48), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 71, -47), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 71, -46), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(11, 71, -45), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 74, -47), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 74, -46), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 73, -48), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 73, -47), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 73, -46), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 73, -45), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 72, -48), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 72, -47), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 72, -46), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 72, -45), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 71, -48), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 71, -47), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 71, -46), Block::get(0, 0));
                $sender->getLevel()->setBlock(new Vector3(6, 71, -45), Block::get(0, 0));
                $kpp["mafia"] = 1;
                $this->kpp->setAll($kpp);
                return false;
              }
            }else{$sender->sendMessage("§7Ты не §6состоишь §7в §3мафии"); return false;}
          }else{$sender->sendMessage("§l§c Для того чтобы открыть КПП надо быть плизко к ним"); return false;}
        }

        if ($command->getName() == "su"){
          if(!isset($args[2])){$sender->sendMessage("§7Использование: /su <ник> <1-6> <причина>."); return false;}
          $cfg = $this->cfg->getAll();
          $ros = $this->ros->getAll();
          $player = $this->getServer()->getPlayer($args[0]);
          if($player != null){
            if(isset($cfg["police"][$sender->getName()])){
              if($cfg["police"][$sender->getName()] >= 3){
                $rosi = $args[1];
                unset($args[0]);
                unset($args[1]);
                $er = implode(" ", $args);
                if(is_numeric($rosi) and strlen($rosi) == 1 and $rosi <= 6){
                  if(strlen($er) != 0){
                    if(!isset($ros[$player->getName()])){
                      $sender->sendMessage("§7Ты выдал §6розыск§7 для §b" . $player->getName() . " §7с §3{$rosi}-ым§7 уровнем, по причине: " . $er);
                      $player->sendMessage("§7Тебе выдал §6розыск §b" . $sender->getName() . "§7 с §3{$rosi}-ым §7уровнем, по причине: " . $er);
                      $ros["ros"][$player->getName()] = $rosi;
                      $this->ros->setAll($ros);
                      $this->ros->save();
                      foreach($this->getServer()->getOnlinePlayers() as $adminrepor){
                        if(isset($cfg["police"][$adminrepor->getName()])){
                          $adminrepor->sendMessage("§3[§bD§3] Диспечер: Подозриваемый гражданин -§e ". $player->getName() . "§3, уровень розыска §6{$rosi}§3. Подал: §a". $sender->getName() . "§3. Причина: " . $er);
                        }
                      }
                    }else{$sender->sendMessage("§7Данный игрок в розыске."); return false;}
                  }else{$sender->sendmessage("§7Использование: /su <ник> <1-6> <причина>."); return false;}
                }else{$sender->sendmessage("§7Использование: /su <ник> <1-6> <причина>."); return false;}
              }else{$sender->sendmessage("§cРазрешено выдавать розыск с 3-го уровня!"); return false;}
            }else{$sender->sendmessage("§cТы не полицейский!"); return false;}
          }else{$sender->sendmessage("§cИгрок не в сети"); return false;}
        return false;}

        if($command->getName() == "unsu"){
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /unsu <nick>"); return false;}
          $cfg = $this->cfg->getAll();
          $ros = $this->ros->getAll();
          $player = $this->getServer()->getPlayer($args[0]);
          if($player != null){
            if(!isset($ros[$player->getName()])){
              if(isset($cfg["police"][$sender->getName()])){
                if($cfg["police"][$sender->getName()] >= 3){
                  $sender->sendMessage("§7Ты снял розыск с §b" . $player->getName() ."§c!");
                  $player->sendMessage("§c" . $sender->getName() . "§7 снял с тебя §6розыск§c!");
                  $ros["ros"][$player->getName()] = 0;
                  $this->ros->setAll($ros);
                  $this->ros->save();
                  foreach($this->getServer()->getOnlinePlayers() as $adminrepor){
                    if(isset($cfg["police"][$adminrepor->getName()])){
                      $adminrepor->sendMessage("§3[§bD§3] Диспечер: §c" . $sender->getName() . " §3снял розыск с §6" . $player->getName() ."");
                    }
                  }
                  return false;
                }else{$sender->sendMessage("§7Твой §3ранг§7 не дастаточно §2высок§c!"); return false;}
              }else{$sender->sendMessage("§7Ты не §3работаешь§7 с §6полиции§c!"); return false;}
            }else{$sender->sendMessage("§7Даный игрок не в §6розыске§c!"); return false;}
          }else{$sender->sendMessage("§7Данный игрок не найден§c!"); return false;}
        }

        if($command->getName() == "slap"){
          if (count($args) != 1){$sender->sendMessage("§7Использование: /slap <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 2){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Пнул игрока §e" . $player->getName() . "");
              }
            }
            $direct = $player->getDirectionVector();
            $player->setMotion($direct->add(1, 1, 1));
            $player->sendMessage("§7§lАдминистратор §a" . $sender->getName() . "§7§l Дал Вам Поджопник§c!");
            $sender->sendMessage("§7§lВы дали поджопник игроку §a" . $player->getName() . "§c!");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}

        }

        if($command->getName() == "stats"){
          $balance = $this->eco->mymoney($sender);
          $nametag = $sender->getNameTag();
          $sender->sendMessage("§l§a---Статистика игрока §l§eQweek Role Play§l§a--- \n §bНик:§e ".$sender->getName()." \n §bБюджет:§e ".$balance." \n §bФракция: §e".$nametag." \n§a---Статистика игрока §eQweek Role Play§a§l---");
          return false;
        }

        if($command->getName() == "pass"){
          if(isset($args[0])){
            if($pass["pasport"][$sender->getName()] == 0){
              $p = $this->getServer()->getPlayer($args[0]);
              if($p != NULL){
                if($sender->distance($p->asVector3()) < 11){
                  foreach($this->getServer()->getOnlinePlayers() as $pl){
                    if($sender->distance($pl->asVector3()) < 9){
                      $pl->sendMessage("§d" . $sender->getName() . " показал(-а) паспорт " . $p->getName());
                    }
                  }

                  $sender->sendPopup("");
                  $ros = $this->ros->getAll();
                  $carlll = $this->cares->getAll();
                  $p->sendMessage("| §aПаспорт гражданина: §3" . $sender->getName());
                  $p->sendMessage("| §aПол: §3Женский/Мурской");
                  $p->sendMessage("| §aВозраст: §318-35");
                  $p->sendMessage("| §aПроживание в городе: §3" . $carlll[$sender->getName()]["lvl"]);
                  $p->sendMessage("| §aУровень розыска: §3" . $ros["ros"][$sender->getName()]);
                  $p->sendMessage("| §aУдостоверение Водителя Автомобиля: §3" . $prava["prava"][$sender->getName()]);
                  $p->sendMessage("| §aМедицинская карта: §3" . $med[$sender->getName()]["o"]);
                  $p->sendMessage("| §aЗаконопослушность: §3" . $carlll[$sender->getName()]["zak"]);
                  if (isset($cfg["army"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Военнаслужайщий");
                  }elseif (isset($cfg["hospital"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Врачь");
                  }elseif (isset($cfg["massmedia"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Средства Массовой Информации");
                  }elseif (isset($cfg["police"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Полицейский Депортанимнт");
                  }elseif (isset($cfg["pravo"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Правохранительные Органы");
                  }elseif (isset($cfg["vla"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Безработный");
                  }elseif (isset($cfg["tbg"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Безработный");
                  }elseif (isset($cfg["gsf"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Безработный");
                  }elseif (isset($cfg["fsb"][$sender->getName()])) {
                    $p->sendMessage("§aОрганизация (Работа): §3Безработный");
                  }elseif (!isset($cfg["fsb"][$sender->getName()])){
                    $p->sendMessage("§aОрганизация (Работа): §3Безработный");
                  }
                }else{
                  $sender->sendMessage("Данный игрок далеко от тебя.");
                  return false;
                }
              }else{
                $sender->sendMessage("§7Данного игрока не найдено.");
                return false;
              }
            }else{
              $sender->sendMessage("§7У вас нету паспорта.");
              return false;
            }
          }else{
            $sender->sendMessage("§7Использование: /pass <nick>");
            return false;
          }
        return true;}

        if ($command->getName() == "check") {
          if (count($args) != 1){$sender->sendMessage("§7Использование: /check <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 2){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Посмотрел Данные §e" . $player->getName() . "");
              }
            }
            $ip = $player->getAddress();
            $gamemode = $player->getGamemode();
            $health = $player->getHealth();
            $x = $player->getX();
            $y = $player->getY();
            $carlll = $this->cares->getAll();
            $z = $player->getZ();
            $t = time() + (3 * 60 * 60);
            $times = date("h:ia");
            $time = gmdate("H:i:s, $t");
            $w = $cfg["warn"][$player->getName()];
            $nametag = $player->getNameTag();
            $sender->sendMessage("§3------§a".$player->getName()."§3------ \n §bВремя по МСК:§c " . date("H:i:s") ." §b" . date("d.m.Y") ." \n §bДеньги:§c " . $ra["mon"][$player->getName()] . "\n §bIP:§c ".$ip." \n §bЛвл:§c ". $carlll[$sender->getName()]["lvl"] ."\n §bГеймод:§c ".$gamemode." \n §bВарны:§c ".$w."/3  \n §bHP: §c".$health." \n §bКоординаты: §c".$x." ".$y." ".$z." \n §bФракция: §c".$nametag);
            if($cfg["warn"][$player->getName()] >= 1){$sender->sendMessage("§7[§cАккаунт с Варном§7] \n §3------§a".$player->getName()."§3------"); return false;}
            $sender->sendMessage("§7[§aАккаунт без варнов§7] \n §3------§a".$player->getName()."§3------");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "ad") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /ad <text>"); return false;}
          $name = $sender->getName();
          $text = implode(" ", $args);
          foreach ($this->getServer()->getOnlinePlayers() as $admassmedia){
            if (isset($cfg["massmedia"][$admassmedia->getName()])) {
              if ($admassmedia == NULL){$sender->sendMessage("§7На данный момент нету работников §3СМИ"); return false;}
              $admassmedia->sendMessage("§7Объявление От §a" . $name . ":§e " . $text . "");
            }
          }
          $sender->sendMessage("§7Запрос Отправлен: §a" . $text . " ");
        return false;}

        if ($command->getName() == "adgo") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /adgo <text>"); return false;}
          $name = $sender->getName();
          if (isset($cfg["massmedia"][$sender->getName()])) {
            if ($cfg["massmedia"][$sender->getName()] < 3){$sender->sendMessage("§7Твой ранг не дастаточно высок§c!"); return false;}
            $adgoe = implode(" ", $args);
            $this->getServer()->broadcastMessage("§2[Новости с редакции] СМИ " . $name . ": " . $adgoe . "");
            return false;
          }else{$sender->sendMessage("§7Ты не работаешь в §6СМИ§c!"); return false;}
        }

        if($command->getName() == "watch"){
          if (count($args) != 1){$sender->sendMessage("§7Использование: /watch <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == null){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 2){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            foreach($this->getServer()->getOnlinePlayers() as $pl){
              if (isset($admins["admin"][$pl->getName()])) {
                $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Начал слежку за §e" . $player->getName() . "");
              }
            }
            unset($args[0]);
            $sender->setGamemode(3);
            $sender->teleport(new Vector3($player->x, $player->y, $player->z));
            $sender->sendMessage("§cТы §7начал слежку за игроком§c ".$player->getName()."§7!");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "ask") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /ask <nick> <txt>"); return false;}
        $player = $this->getServer()->getPlayer($args[0]);
        if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
        $name = $sender->getName();
        unset($args[0]);
        $aske = implode(" ", $args);
        if($aske != ""){
        if (isset($admins["admin"][$sender->getName()])) {
        if ($admins["admin"][$sender->getName()] < 2){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
        foreach($this->getServer()->getOnlinePlayers() as $pl){
          if (isset($admins["admin"][$pl->getName()])) {
            $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Ответил игроку на репорт §e" . $player->getName() . " §b" . $aske . "");
          }
        }
        $player->sendMessage("§a[§cТех.Поддержка§a] §7" . $name . ": §e" . $aske . "");
        $sender->sendMessage("§a[§eА§a]§a Ты ответил на репорт: " . $player->getName() . " §e" . $aske . "");
        return false;
        }
        }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "uninvite") {
          if (count($args) != 1){$sender->sendMessage("§7Использование: /uninvite <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (isset($cfg["army"][$sender->getName()])) {
            if ($cfg["army"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для увольнения игроков с §aармии."); return false;}
            if (!isset($cfg["army"][$player->getName()])){$sender->sendMessage("§7Данный игрок не служит в армии."); return false;}
            if ($cfg["army"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!"); return false;}
            $sender->sendMessage("§7Ты уволил §6" . $player->getName() . "§7 с§a армии.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . "§7 уволил тебя с §aармии.");
            unset($cfg["army"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          }elseif (isset($cfg["hospital"][$sender->getName()])) {
            if ($cfg["hospital"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для увольнения игроков с больницы."); return false;}
            if (!isset($cfg["hospital"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в больнице."); return false;}
            if ($cfg["hospital"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!"); return false;}
            $sender->sendMessage("§7Ты уволил §6" . $player->getName() . " §7с §cбольницы.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . " §7уволил тебя с §cбольници");
            unset($cfg["hospital"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["massmedia"][$sender->getName()])) {
            if ($cfg["massmedia"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для увольнения игроков с радиоцентра."); return false;}
            if (!isset($cfg["massmedia"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в радиоцентре."); return false;}
            if ($cfg["massmedia"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!"); return false;}
            $sender->sendMessage("§7Ты уволил §6" . $player->getName() . " §7с §6радиоцентра.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . " §7уволил тебя с §6радиоцентра.");
            unset($cfg["massmedia"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["police"][$sender->getName()])) {
            if ($cfg["police"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для увольнения игроков с полиции.");  return false;}
            if (!isset($cfg["police"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в полиции.");  return false;}
            if ($cfg["police"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!");  return false;}
            $sender->sendMessage("§7Ты уволил §6" . $player->getName() . "§7 с§1 полиции.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . " §7уволил тебя с§1 полиции.");
            unset($cfg["police"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["pravo"][$sender->getName()])) {
            if ($cfg["pravo"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для увольнения игроков с Правительства.");  return false;}
            if (!isset($cfg["pravo"][$player->getName()])){return $sender->sendMessage("§7Данный игрок не работает в Правительстве.");  return false;}
            if ($cfg["pravo"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!");  return false;}
            $sender->sendMessage("§7Ты уволил §6" . $player->getName() . "§7 с §9Правительства.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . " §7уволил тебя с§9 Правительства.");
            unset($cfg["pravo"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["tbg"][$sender->getName()])) {
            if ($cfg["tbg"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для выговора из семьи §aGLA§7."); return false;}
            if (!isset($cfg["tbg"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в §aGLA§7."); return false;}
            if ($cfg["tbg"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!");  return false;}
            $sender->sendMessage("§7Ты выгнал §6" . $player->getName() . "§7 из §aGLA§7.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . " §7выгнал тебя из семьи §aGLA§7.");
            unset($cfg["tbg"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["vla"][$sender->getName()])) {
            if ($cfg["vla"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для выговора из семьи §bVLA§7."); return false;}
            if (!isset($cfg["vla"][$player->getName()])){$sender->sendMessage("§7Данный игрок не в семье §bVLA§7."); return false;}
            if ($cfg["vla"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!"); return false;}
            $sender->sendMessage("§7Ты выгнал §6" . $player->getName() . " §7 из §bGLA§7.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . " §7выгнал тебя из семьи §bVLA§7.");
            unset($cfg["vla"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["gsf"][$sender->getName()])) {
            if ($cfg["gsf"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для выговора из семьи §eMexico§7."); return false;}
            if (!isset($cfg["gsf"][$player->getName()])){$sender->sendMessage("§7Данный игрок не в §eMexico."); return false;}
            if ($cfg["gsf"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!"); return false;}
            $sender->sendMessage("§7Ты выгнал §6" . $player->getName() . " §7с §eMexico§7.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . " §7выгнал тебя из семьи §eMexico§7.");
            unset($cfg["gsf"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["fsb"][$sender->getName()])) {
            if ($cfg["fsb"][$sender->getName()] < 9){$sender->sendMessage("§7Твой ранг не достаточно высок для выговора из семьи §bИтальянская мафия§7."); return false;}
            if (!isset($cfg["fsb"][$player->getName()])){$sender->sendMessage("§7Данный игрок не работает в §bИтальянской мафии§7."); return false;}
            if ($cfg["fsb"][$player->getName()] == 10){$sender->sendMessage("§7Ты не можешь уволить §3лидера§c!"); return false;}
            $sender->sendMessage("§7Ты выгнал " . $player->getName() . " из §bИтальянской мафии§7.");
            $player->setNameTag('Гражданин: '.$player->getName());
            $player->sendMessage("§6" . $sender->getName() . " §7выгнал тебя из семьи §bИтальянкой мафии§7.");
            unset($cfg["fsb"][$player->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          }
        }

        if ($command->getName() == "adlvl") {
          if(count($args) == 2) {
            $player = $this->getServer()->getPlayer($args[0]);
            if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
            if (isset($admins["admin"][$sender->getName()])) {
              if ($admins["admin"][$sender->getName()] < 5){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
              if (!isset($admins["admin"][$player->getName()])){$sender->sendMessage("§7Данный игрок не §6Админ§4!."); return false;}
              $rangi = $args[1];
              if(is_numeric($rangi) and strlen($rangi) == 1 and $rangi <= 5){
                if ($admins["admin"][$player->getName()] == 6){$sender->sendMessage("§7Ты не можешь понизить §6LVL§3 Кириллу §7-_-§c!"); return false;}
                $sender->sendMessage("§7Ты выдал §a{$rangi}-й§7 lvl§7 для §c" . $player->getName() . "§7.");
                $player->sendMessage("§c" . $sender->getName() . "§7 выдал тебе §a{$rangi}-й§7 lvl.");
                $admins["admin"][$player->getName()] = $rangi;
                $this->admins->setAll($admins);
                $this->admins->save();
                return false;
              }else{$sender->sendMessage("§7Использование: /adlvl <nick> <1-5>"); return false;}
            }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          }else{$sender->sendMessage("§7Использование: /adlvl <nick> <1-5>"); return false;}
        }

        if($command->getName() == "goto") {
          $player = $this->getServer()->getPlayer($args[0]);
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /goto nick."); return false;}
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (!isset($admins["admin"][$sender->getName()])) {$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          if ($admins["admin"][$sender->getName()] < 2){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
          foreach($this->getServer()->getOnlinePlayers() as $pl){
            if (isset($admins["admin"][$pl->getName()])) {
              $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Телепортировался к §e" . $player->getName() . "");
            }
          }
          unset($args[0]);
          $sender->teleport(new Vector3($player->x, $player->y, $player->z));
          $player->sendMessage("§3Администратор ".$sender->getName()."§c телепортировался к Вам§4!");
          $sender->sendMessage("§cТы телепортировался к игроку §3".$player->getName()."§4!");
          return false;
        }

        if($command->getName() == "gotoh"){
          $player = $this->getServer()->getPlayer($args[0]);
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /gotoh nick."); return false;}
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (!isset($admins["admin"][$sender->getName()])) {$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          if ($admins["admin"][$sender->getName()] < 2){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
          foreach($this->getServer()->getOnlinePlayers() as $pl){
            if (isset($admins["admin"][$pl->getName()])) {
              $pl->sendMessage("§6Администратор " . $sender->getName() . ":§7 Телепортировал к себе §e" . $player->getName() . "");
            }
          }
          unset($args[0]);
          $player->teleport(new Vector3($sender->x, $sender->y, $sender->z));
          $player->sendMessage("§3Администратор ".$sender->getName()."§c: телепортировал Вас к себе§4!");
          $sender->sendMessage("§cТы телепортировал игрока §3".$player->getName()."§c к себе§4!");
          return false;
        }

        if ($command->getName() == "ao") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /ao text."); return false;}
          $name = $sender->getName();
          if (!isset($admins["admin"][$sender->getName()])) {$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
          if ($admins["admin"][$sender->getName()] < 2){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
          $text = implode(" ", $args);
          $this->getServer()->broadcastMessage("§3Администратор " . $name . ":§6 " . $text . "");
          return false;
        }

        if ($command->getName() == "adin") {
          if (count($args) != 1){$sender->sendMessage("§7Использование: /adin <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 5){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            if (isset($admins["admin"][$player->getName()])){$sender->sendMessage("§7Данный игрок уже §6админ§7."); return false;}
            $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на админский пост§4!");
            $player->sendMessage("§6" . $sender->getName() . "§7 принял тебя на админку§4!");
            $admins["admin"][$player->getName()] = 1;
            $this->admins->setAll($admins);
            $this->admins->save();
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "adun") {
          if (count($args) != 1){$sender->sendMessage("§7Использование: /adun <nick>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 5){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            if (!isset($admins["admin"][$player->getName()])){$sender->sendMessage("§7Данный игрок не администратор§c!");}
            if ($admins["admin"][$player->getName()] == 6){$sender->sendMessage("§7Ты не можешь выгнать §3Кирилла §6-_-§c!"); return false;}
            $sender->sendMessage("§7Ты выгнал его §6" . $player->getName() . "§7, чтобы выгонять надо до-ки§4!");
            $player->sendMessage("§6" . $sender->getName() . "§7 он выкинул тебя с админов§4!");
            unset($admins["admin"][$player->getName()]);
            $this->admins->setAll($admins);
            $this->admins->save();
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "admins") {
          if (isset($admins["admin"][$sender->getName()])) {
            $sender->sendPopup("§l§fАдмин инфармация§c!");;
            $sender->sendMessage("§e---§a\n§aQweek Role-Play\n §bЛидерки: makeleader <Ник> <1-9>\n §e Гос.Фракции 1-Больница, 2-Полициия, 3-Армиия, 4-СМИ, 5-Правительство\n 6-Mexico, 7-VLA, 8-GLA, 9-Итальянская мафия \n §b/check <nick> - §eбаза данных игрока \n §b/akick <nick> - §eкикнуть игрока из сервера \n §b/slap <nick> -§e пнуть игрока \n §b/adin <nick> - §eпринять игрока в администрацию \n §b/adun <nick> - §eвыгнать с админки игрока \n §b/adlvl <nick> - §eвыдать/забрать лвл \n §e---§a");
            return false;
          }else{$sender->sendMessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "unmakeleader") {
          $folder = $this->getDataFolder();
          $cfg = $this->cfg->getAll();
          $admins = $this->admins->getAll();
          $ban = $this->bans->getAll();
          if (count($args) != 2){$sender->sendMessage("§7Использование: /unmakeleader <nick> <1-9>"); return false;}
            $player = $this->getServer()->getPlayer($args[0]);
            if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
            if (isset($admins["admin"][$sender->getName()])) {
                if ($admins["admin"][$sender->getName()] < 5){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
                if($args[1] == "1"){
                if ($cfg["hospital"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер больницы§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 с поста лидера §cбольницы§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage("§b" . $sender->getName() . "§7: Вас снял вас с поста лидера §bбольницы§7.");
                unset($cfg["hospital"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }elseif($args[1] == "2"){
                if ($cfg["police"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер полиции§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 на пост лидера в §bПолиции§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage("§b" . $sender->getName() . "§7: Вас снял вас с поста лидера §bПолиции§7.");
                unset($cfg["police"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }elseif($args[1] == "3"){
                if ($cfg["army"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер армии§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 с поста лидера §bАрмии§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage("§b" . $sender->getName() . "§7: Вас снял вас с поста лидера §bАрмиии§7.");
                unset($cfg["army"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }elseif($args[1] == "4"){
                if ($cfg["massmedia"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер СМИ§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 с поста лидера в §bСМИ§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage("§b" . $sender->getName() . "§7: Вас снял вас с поста лидера §bСМИ§7.");
                unset($cfg["massmedia"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }elseif($args[1] == "5"){
                if ($cfg["pravo"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер Правительства§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 с поста лидера в §bПравительства§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage("§b" . $sender->getName() . "§7: Вас снял вас с поста лидера §bПравительства§7.");
                unset($cfg["pravo"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }elseif($args[1] == "6"){
                if ($cfg["gsf"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер Mexico§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 с поста лидера в мафии §bMexico§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage("§b" . $sender->getName() . "§7: Вас сняли поста лидера §bMexico§7.");
                unset($cfg["gsf"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }elseif($args[1] == "7"){
                if ($cfg["vla"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер VLA§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 с поста лидера в банду §bVLA§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage("§b" . $sender->getName() . "§7: Вас снял вас с поста лидера §bVLA§7.");
                unset($cfg["vla"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }elseif($args[1] == "8"){
                if ($cfg["tbg"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер GLA§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 с поста лидера в  банду §bGLA§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage($sender->getName() . "§7: Вас снял с поста лидера §BGLA§7.");
                unset($cfg["tbg"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }elseif($args[1] == "9"){
                if ($cfg["fsb"][$player->getName()] != 10){$sender->sendMessage("§7Данный игрок не лидер Итальянской мафии§c!"); return false;}
                $sender->sendMessage("§7Ты снял §6" . $player->getName() . "§7 на пост лидера в мафию §bИталия§7.");
                $player->setNameTag('Гражданин: '.$player->getName());
                $player->sendMessage("§b" . $sender->getName() . "§7: Вас снял с поста лидера §bИтальянская мафия§7.");
                unset($cfg["fsb"][$player->getName()]);
                $this->cfg->setAll($cfg);
                $this->cfg->save();
                return false;
              }
            }
        }

        if ($command->getName() == "makeleader") {
          $folder = $this->getDataFolder();
          $cfg = $this->cfg->getAll();
          $admins = $this->admins->getAll();
          $ban = $this->bans->getAll();
          if (count($args) != 2){$sender->sendMessage("§7Использование: /makeleader <nick> <1-9>"); return false;}
          $player = $this->getServer()->getPlayer($args[0]);
          if ($player == NULL){$sender->sendMessage("§7Данный игрок не найден."); return false;}
          if (isset($admins["admin"][$sender->getName()])) {
            if ($admins["admin"][$sender->getName()] < 4){$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
            if($cfg["warn"][$player->getName()] != 0){$sender->sendMessage("§7У данного игрока §bварн§7."); return false;}
            if (isset($cfg["army"][$player->getName()]) or isset($cfg["hospital"][$player->getName()]) or isset($cfg["massmedia"][$player->getName()]) or isset($cfg["police"][$player->getName()]) or isset($cfg["pravo"][$player->getName()]) or isset($cfg["tbg"][$player->getName()]) or isset($cfg["vla"][$player->getName()]) or isset($cfg["gst"][$player->getName()]) or isset($cfg["fsb"][$player->getName()])){$sender->sendMessage("§7Данный Игрок Уже Где То Работает§4!"); return false;}
            if($args[1] == "1"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в §cБольнице§7.");
              $player->setNameTag('§cРаботник больницы: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §bбольницы§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bбольницы §6".$player->getName()."!");
              $cfg["hospital"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }elseif($args[1] == "2"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в §bПолиции§7.");
              $player->setNameTag('§1Полицейский: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §bПолиции§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bПолиции §6".$player->getName()."!");
              $cfg["police"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }elseif($args[1] == "3"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в §bАрмии§7.");
              $player->setNameTag('§2Военнаслужайщий: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §bАрмиии§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bАрмии ".$player->getName()."!");
              $cfg["army"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }elseif($args[1] == "4"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в §bСМИ§7.");
              $player->setNameTag('§6Работник СМИ: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §bСМИ§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bСМИ§6 ".$player->getName()."!");
              $cfg["massmedia"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }elseif($args[1] == "5"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в §bПравительства§7.");
              $player->setNameTag('§9Правительство: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §bПравительства§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bПравительства§6 ".$player->getName()."!");
              $cfg["pravo"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }elseif($args[1] == "6"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в мафии §bMexico§7.");
              $player->setNameTag('§eMexico: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §bMexico§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bMexico ".$player->getName()."!");
              $cfg["gsf"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }elseif($args[1] == "7"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в банду §bVLA§7.");
              $player->setNameTag('§bVLA: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §bVLA§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bVLA§7 ".$player->getName()."!");
              $cfg["vla"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }elseif($args[1] == "8"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в  банду §bGLA§7.");
              $player->setNameTag('§aGLA: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §BGLA§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bGLA §7 ".$player->getName()."!");
              $cfg["tbg"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }elseif($args[1] == "9"){
              $sender->sendMessage("§7Ты принял §6" . $player->getName() . "§7 на пост лидера в мафию §bИталия§7.");
              $player->setNameTag('§l§cИтальянская мафия: '.$player->getName());
              $player->sendMessage("§6" . $sender->getName() . "§7 Принял вас на на пост лидера §bИтальянская мафия§7.");
              $this->getServer()->broadCastMessage("§6Администратор ". $sender -> getName().": "."Был назначен на пост лидера §bИтальянской мафии§7 ".$player->getName()."!");
              $cfg["fsb"][$player->getName()] = 10;
              $this->cfg->setAll($cfg);
              $this->cfg->save();
              return false;
            }
          }else{$sender->sendmessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
        }

        if ($command->getName() == "akick") {
          if(!isset($args[0])){
            $player = $this->getServer()->getPlayer($args[0]);
            if($player != NULL){
              if(isset($admins["admin"][$sender->getName()])){
                if($admins["admin"][$sender->getName()] > 2){
                  $player = $this->getServer()->getPlayer($args[0]);
                  unset($args[0]);
                  $msg = implode(" ", $args);
                  $this->GetServer()->broadCastMessage("§cАдминистратор ". $sender -> getName()." кикнул игрока ".$player->getName()." по причине: ".$msg);
                  $player->kick("§cАдминистратор ". $sender -> getName()." кикнул вас "."по причине: ".$msg);
                }else{$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
              }else{$sender->sendmessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
            }else{
              $sender->sendMessage("§7Данного игрока не найден!");
              return false;
            }
          }else{
            $sender->sendMessage("§7Использование: /akick <nick> <причина>");
            return false;
          }
        return false;}

        if ($command->getName() == "aban") {
          $ban = $this->bans->getAll();
          if($args[0] != ""){
            $player = $this->getServer()->getPlayer($args[0]);
            if($player != NULL){
              if(isset($admins["admin"][$sender->getName()])){
                if($admins["admin"][$sender->getName()] > 5){
                  unset($args[0]);
                  $msg = implode(" ", $args);
                  $this->getServer()->broadCastMessage("§cАдминистратор ". $sender -> getName()." забанил игрока ".$player->getName()." по причине: ".$msg);
                  $player->kick("§cАдминистратор ". $sender -> getName()." забанил вас "."по причине: ".$msg);
                  $ban["ban"][$player->getName()] = $msg;
                  $this->bans->setAll($ban);
                  $this->bans->save();
                  return false;
                }else{$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
              }else{$sender->sendmessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
            }else{
              $sender->sendMessage("§7Данный игрок не найден");
              return false;
            }
          }else{$sender->sendmessage("§7Использование: /unaban <nick> <причина>"); return false;}
        }

        if($command->getName() == "unaban"){
          $ban = $this->bans->getAll();
          $player = $this->getServer()->getPlayer($args[0]);
          if($args[0] != ""){
            if($player != NULL){
              if(isset($admins["admin"][$sender->getName()])){
                if($admins["admin"][$sender->getName()] > 5){
                  unset($args[0]);
                  $msgi = implode(" ", $args);
                  $this->getServer()->broadcastMessage("§cАдминистратор " . $sender->getName() . " снял бан с игрока " . $player->getName() . " по причине: " . $msgi);
                  unset($ban["ban"][$player->getName()]);
                  $this->bans->setAll($ban);
                  $this->bans->save();
                  return false;
                }else{$sender->sendMessage("§cТвой §aLVL§c не дастаточно высок§3!"); return false;}
              }else{$sender->sendmessage("§cУ тебя нет прав использовать эту команду§3!"); return false;}
            }else{
              $sender->sendMessage("§7Данный игрок не найден");
              return false;
            }
          }else{$sender->sendmessage("§7Использование: /unaban <nick> <причина>"); return false;}
        }

        if ($command->getName() == "leave") {
          if(isset($args[0])){$sender->sendMessage("§cИспользование: /leave"); return false;}
          if (isset($cfg["army"][$sender->getName()])) {
            $sender->sendMessage("§7Ты уволился из армии по собственному желанию§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["army"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["hospital"][$sender->getName()])) {
            $sender->sendMessage("§7Ты уволился из больницы по собственному желанию§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["hospital"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["massmedia"][$sender->getName()])) {
            $sender->sendMessage("§7Ты уволился из радиоцентра по собственному желанию§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["massmedia"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["police"][$sender->getName()])) {
            $sender->sendMessage("§7Ты уволился из полиции по собственному желанию§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["police"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["pravo"][$sender->getName()])) {
            $sender->sendMessage("§7Ты уволился из правительства по собственному желанию§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["pravo"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["tbg"][$sender->getName()])) {
            $sender->sendMessage("§7Ты предал семью §aGLA§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["tbg"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["vla"][$sender->getName()])) {
            $sender->sendMessage("§7Ты предал семью §bVLA§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["vla"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["gsf"][$sender->getName()])) {
            $sender->sendMessage("§7Ты предал семью §eMexico§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["gsf"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          } elseif (isset($cfg["fsb"][$sender->getName()])) {
            $sender->sendMessage("§7Ты предал семью §cИтальянская мафия§c!");
            $sender->setNameTag('Гражданин: '.$sender->getName());
            unset($cfg["fsb"][$sender->getName()]);
            $this->cfg->setAll($cfg);
            $this->cfg->save();
            return false;
          }else{ $sender->sendMessage("§7Ты не где не работаешь."); return false;}
          }

        if ($command->getName() == "heal") {
        if(!isset($args[0])){$sender->sendMessage("§cИспользование: /heal nick."); return false;}
        if(isset($cfg["hospital"][$sender->getName()])) {
        $player = $this->getServer()->getPlayer($args[0]);
        $sender->sendMessage("§7Ты Вылечил Игрока §6" . $player->getName() . "§c!");
        $player->sendMessage("§7Тебя Вылечил §6" . $sender->getName() . "§c!");
        $player->setHealth(100);
        return false;
        }else{$sender->sendMessage("§7Ты не работник §cбольницы§7."); return false;}
        }

        if ($command->getName() == "gov") {
          if(!isset($args[0])){$sender->sendMessage("§7Использование: /gov <text>"); return false;}
          $name = $sender->getName();
          if (isset($cfg["army"][$sender->getName()])) {
            if ($cfg["army"][$sender->getName()] < 9){$sender->sendMessage("§6Твой Ранг Нe Достаточно Высок"); return false;}
            $text = implode(" ", $args);
            $this->getServer()->broadcastMessage("§7[§aГосударственные Новости Армии§7]§b " . $name . ": " . $text . "");
            return false;
          } elseif (isset($cfg["hospital"][$sender->getName()])) {
            if ($cfg["hospital"][$sender->getName()] < 9){$sender->sendMessage("§6Твой Ранг Нe Достаточно Высок"); return false;}
            $text = implode(" ", $args);
            $this->getServer()->broadcastMessage("§7[§cГосударственные Новости Больницы§7]§b " . $name . ": " . $text . "");
            return false;
          } elseif (isset($cfg["massmedia"][$sender->getName()])) {
            if ($cfg["massmedia"][$sender->getName()] < 9){$sender->sendMessage("§6Твой Ранг Нe Достаточно Высок"); return false;}
            $text = implode(" ", $args);
            $this->getServer()->broadcastMessage("§7[§6Государственные Новости СМИ§7]§b " . $name . ": " . $text . "");
            return false;
         } elseif (isset($cfg["police"][$sender->getName()])) {
           if ($cfg["police"][$sender->getName()] < 9){$sender->sendMessage("§6Твой Ранг Нe Достаточно Высок"); return false;}
           $text = implode(" ", $args);
           $this->getServer()->broadcastMessage("§7[§2Государственные Новости Полиции§7]§b " . $name . ": " . $text . "");
           return false;
         } elseif (isset($cfg["pravo"][$sender->getName()])) {
           if ($cfg["pravo"][$sender->getName()] < 9){$sender->sendMessage("§6Твой Ранг Нe Достаточно Высок"); return false;}
           $text = implode(" ", $args);
           $this->getServer()->broadcastMessage("§7[§9Государственные Новости Правительства§7]§b " . $name . ": " . $text . "");
           return false;
         }else{$sender->sendMessage("§7Ты §cнегде§7 не работаешь."); return false;}
        }
      }
    }
