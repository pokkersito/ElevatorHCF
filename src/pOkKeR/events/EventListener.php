<?php

namespace pOkKeR\events;

use pOkKeR\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\block\Block;
use pocketmine\block\Door;
use pocketmine\block\FenceGate;

class EventListener implements Listener {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onSignChange(SignChangeEvent $event): void {
        $signText = $event->getNewText();
        $lines = $signText->getLines();

        // Obtén el color de la configuración
        $elevatorColor = $this->plugin->getConfig()->get("elevator")["sign-color"];

        if (strtolower($lines[0]) === '[elevator]') {
            if (strtolower($lines[1]) === 'up') {
                $event->setNewText(new SignText([
                    TextFormat::colorize($elevatorColor . '[Elevator]'),
                    TextFormat::colorize('&7up'),
                    '', // Línea 3
                    '', // Línea 4
                ]));
            } elseif (strtolower($lines[1]) === 'down') {
                $event->setNewText(new SignText([
                    TextFormat::colorize($elevatorColor . '[Elevator]'),
                    TextFormat::colorize('&7down'),
                    '', // Línea 3
                    '', // Línea 4
                ]));
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

        $tile = $player->getWorld()->getTile($block->getPosition());

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $tile instanceof Sign) {
            $text = $tile->getText();
            $lines = $text->getLines();

            // Obtén el color de la configuración
            $elevatorColor = $this->plugin->getConfig()->get("elevator")["sign-color"];

            if ($lines[0] === TextFormat::colorize($elevatorColor . '[Elevator]')) {
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