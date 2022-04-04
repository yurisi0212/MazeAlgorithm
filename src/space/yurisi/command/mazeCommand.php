<?php
declare(strict_types=1);

namespace space\yurisi\command;

use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\player\Player;
use space\yurisi\generator\MazeGenerator;

class mazeCommand extends VanillaCommand {

    public function __construct() {
        parent::__construct("maze", "迷路を生成します", "/maze [x > 5] [z > 5] [y > 1]");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!isset($args[0]) || !isset($args[1])) return;
        $y = $args[2] ?? 3;
        if (!is_numeric($args[0]) || !is_numeric($args[1]) || !is_numeric($y)) return;
        if ($args[0] <= 5 || $args[1] <= 5 || $y <= 1) return;
        if (!$sender instanceof Player) return;

        $generator = new MazeGenerator((int)$args[0], (int)$y, (int)$args[1], $sender);
        $generator->generate();
    }
}