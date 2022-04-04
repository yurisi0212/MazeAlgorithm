<?php
declare(strict_types=1);

namespace space\yurisi\generator;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;

class MazeGenerator {

    private Block $wall;
    private Block $road;
    private Block $side;

    private Position $position;
    private World $level;

    private array $start = [];

    public function __construct(
        private int    $x,
        private int    $y,
        private int    $z,
        private Player $player
    ) {
        $this->wall = BlockFactory::getInstance()->get(BlockLegacyIds::STONE, 0);
        $this->road = BlockFactory::getInstance()->get(BlockLegacyIds::AIR, 0);
        $this->side = BlockFactory::getInstance()->get(BlockLegacyIds::OBSIDIAN, 0);
        $this->position = $this->player->getPosition();
        $this->level = $this->position->getWorld();
    }

    public function generate() {
        $width = $this->x;
        $height = $this->z;
        if ($width % 2 === 0) $width++;
        if ($height % 2 === 0) $height++;

        $this->setup($width, $height);
        $this->dig(1, 1);
        $this->makeSideWall($width, $height);
    }

    private function setup(int $width, int $height) {
        for ($z = 0; $z < $height; $z++) {
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $this->y; $y++) {
                    $pos = $this->position->add($x, $y, $z);
                    if ($x == 0 || $z == 0 || $x == $width - 1 || $z == $height - 1) {
                        $this->level->setBlock($pos->asVector3(), $this->road);
                    } else {
                        $this->level->setBlock($pos->asVector3(), $this->wall);
                    }
                }
            }
        }
    }

    private function dig(int $x, int $z) {
        $position = $this->position;
        $level = $this->level;
        while (true) {
            $direction = [];
            if ($level->getBlock($position->add($x, 0, $z - 1))->getId() === $this->wall->getId() && $level->getBlock($position->add($x, 0, $z - 2))->getId() === $this->wall->getId()) {
                $direction[] = Direction::Up;
            }
            if ($level->getBlock($position->add($x + 1, 0, $z))->getId() === $this->wall->getId() && $level->getBlock($position->add($x + 2, 0, $z))->getId() === $this->wall->getId()) {
                $direction[] = Direction::Right;
            }
            if ($level->getBlock($position->add($x, 0, $z + 1))->getId() === $this->wall->getId() && $level->getBlock($position->add($x, 0, $z + 2))->getId() === $this->wall->getId()) {
                $direction[] = Direction::Down;
            }
            if ($level->getBlock($position->add($x - 1, 0, $z))->getId() === $this->wall->getId() && $level->getBlock($position->add($x - 2, 0, $z))->getId() === $this->wall->getId()) {
                $direction[] = Direction::Left;
            }

            if (count($direction) === 0) break;

            switch ($direction[mt_rand(0, count($direction) - 1)]) {
                case Direction::Up:
                    $this->setAir($x, --$z);
                    $this->setAir($x, --$z);
                    break;
                case Direction::Right:
                    $this->setAir(++$x, $z);
                    $this->setAir(++$x, $z);
                    break;
                case Direction::Down:
                    $this->setAir($x, ++$z);
                    $this->setAir($x, ++$z);
                    break;
                case Direction::Left:
                    $this->setAir(--$x, $z);
                    $this->setAir(--$x, $z);
                    break;
            }
        }
        $pos = $this->getStartPos();
        if ($pos != null) $this->dig($pos->getFloorX(), $pos->getFloorZ());
    }

    private function setAir(int $x, int $z) {
        for ($y = 0; $y < $this->y; $y++) {
            $this->level->setBlock($this->position->add($x, $y, $z), $this->road);
            if ($x % 2 == 1 && $z % 2 == 1) {
                if (!in_array(new Position($x, $this->y, $z, $this->level), $this->start)) {
                    $this->start[] = new Position($x, $this->y, $z, $this->level);
                }
            }
        }
    }

    private function getStartPos(): ?Position {
        if (count($this->start) == 0) return null;

        $num = mt_rand(0, count($this->start) - 1);
        $pos = $this->start[$num];
        unset($this->start[$num]);
        $this->start = array_values($this->start);
        return $pos;
    }

    private function makeSideWall(int $width, int $height) {
        for ($z = 0; $z < $height; $z++) {
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $this->y; $y++) {
                    $pos = $this->position->add($x, $y, $z);
                    if ($x == 0 || $z == 0 || $x == $width - 1 || $z == $height - 1) {
                        $this->level->setBlock($pos->asVector3(), $this->side);
                    }
                }
            }
        }
    }
}