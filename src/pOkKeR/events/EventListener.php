<?php

namespace pOkKeR\events;

use pOkKeR\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\world\World;

class EventListener implements Listener {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onSignChange(SignChangeEvent $event): void {
        $signText = $event->getNewText();
        $lines = $signText->getLines();
        
        if (strtolower($lines[0]) === '[elevator]') {
            if (strtolower($lines[1]) === 'up') {
                $event->setNewText(Sign::fromText(TextFormat::colorize('&c[Elevator]' . "\n" . '&7up')));
            } elseif (strtolower($lines[1]) === 'down') {
                $event->setNewText(Sign::fromText(TextFormat::colorize('&c[Elevator]' . "\n" . '&7down')));
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     * @priority HIGH
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $action = $event->getAction();
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getNamedTag()->getTag('ppackage') !== null) {
            return;
        }

        $tile = $player->getWorld()->getTile($block->getPosition());
        
        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $tile instanceof Sign) {
            $text = $tile->getText();
            $lines = $text->getLines();

            if ($lines[0] === TextFormat::colorize('&c[Elevator]')) {
                $event->cancel();

                if ($lines[1] === TextFormat::colorize('&7up')) {
                    for ($i = $block->getPosition()->getFloorY() + 1; $i < World::Y_MAX; $i++) {
                        $firstBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i + 1, $block->getPosition()->getFloorZ());
                        $secondBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i, $block->getPosition()->getFloorZ());
                        $thirdBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i - 1, $block->getPosition()->getFloorZ());

                        if (((($firstBlock instanceof FenceGate || $firstBlock instanceof Door) && $firstBlock->isOpen()) || !$firstBlock->isSolid()) &&
                            (($secondBlock instanceof FenceGate || $secondBlock instanceof Door) && $secondBlock->isOpen() || !$secondBlock->isSolid()) &&
                            $thirdBlock->isSolid()) {
                            $player->teleport(new Position($block->getPosition()->getFloorX() + 0.5, $i, $block->getPosition()->getFloorZ() + 0.5, $player->getWorld()));
                            break;
                        }
                    }
                } elseif ($lines[1] === TextFormat::colorize('&7down')) {
                    for ($i = $block->getPosition()->getFloorY() - 1; $i >= World::Y_MIN; $i--) {
                        $firstBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i + 1, $block->getPosition()->getFloorZ());
                        $secondBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i, $block->getPosition()->getFloorZ());
                        $thirdBlock = $player->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $i - 1, $block->getPosition()->getFloorZ());

                        if (((($firstBlock instanceof FenceGate || $firstBlock instanceof Door) && $firstBlock->isOpen()) || !$firstBlock->isSolid()) &&
                            (($secondBlock instanceof FenceGate || $secondBlock instanceof Door) && $secondBlock->isOpen() || !$secondBlock->isSolid()) &&
                            $thirdBlock->isSolid()) {
                            $player->teleport(new Position($block->getPosition()->getFloorX() + 0.5, $i, $block->getPosition()->getFloorZ() + 0.5, $player->getWorld()));
                            break;
                        }
                    }
                }
            }
        }
    }
}