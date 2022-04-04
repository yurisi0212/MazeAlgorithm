<?php
declare(strict_types=1);

namespace space\yurisi\generator;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\player\Player;

class MazeGenerator {

    private Block $wall;
    private Block $road;

    public function __construct(
        private int    $x,
        private int    $y,
        private int    $z,
        private Player $player
    ) {
        $this->wall = BlockFactory::getInstance()->get(BlockLegacyIds::OBSIDIAN, 0);
        $this->road = BlockFactory::getInstance()->get(BlockLegacyIds::STONE, 0);
    }

    public function generate() {
        $width = $this->x;
        $height = $this->z;
        if ($width % 2 === 0) $width++;
        if ($height % 2 === 0) $height++;

        $position = $this->player->getPosition();
        for ($z = 0; $z < $height; $z++) {
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $this->y; $y++) {
                    $pos = clone $position->add($x, $y, $z);
                    if ($x == 0 || $z == 0 || $x == $width - 1 || $z == $height - 1) {
                        $position->getWorld()->setBlock($pos->asVector3(), $this->wall);
                    }else{
                        $position->getWorld()->setBlock($pos->asVector3(), $this->road);
                    }
                }
            }
        }
    }

}