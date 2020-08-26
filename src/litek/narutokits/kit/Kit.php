<?php


namespace litek\narutokits\kit;


use litek\narutokits\NarutoKits;

class Kit implements Wearable
{
    /** @var string */
    private $name;

    public function __construct(string $name, array $data){
        $this->name = $name;
    }

    /**
     * @return NarutoKits
     */
    public function getPlugin(): NarutoKits
    {
        return NarutoKits::getInstance();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}