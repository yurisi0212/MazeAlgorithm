<?php
declare(strict_types=1);

namespace space\yurisi;

use pocketmine\plugin\PluginBase;
use space\yurisi\command\mazeCommand;

class MazeAlgorithm extends PluginBase {

    protected function onEnable(): void {
        $this->getServer()->getCommandMap()->register("MazeAlgorithm", new mazeCommand());
    }

}