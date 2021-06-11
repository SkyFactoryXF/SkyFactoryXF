<?php

interface Animal {

}

class AnyAnimal implements Animal {
    public $id;
    public $type;
    private $limits;

    public function __construct(int $minProduct, int $maxProduct, string $type)
    {
        $this->id = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->limits = array($minProduct, $maxProduct);
        $this->type = $type;
    }

    public function getProduct()
    {
        return rand($this->limits[0], $this->limits[1]);
    }
}

interface Storage {
    public function addTypeProduct(string $type, int $maxCount);

    public function addProduct(string $type, int $count);

    public function getFreeSpaceProduct(string $type);

    public function howMuchProduct(string $type);

    public function howMuchProducts();
}

class Barn implements Storage { // Амбар
    private $storage = [];

    public function __construct()
    {
        
    }

    public function addTypeProduct(string $type, int $maxCount){
        $this->storage[] = array('type' => $type, 'storage' => 0, 'max' => $maxCount);
    }

    public function addProduct(string $type, int $count) {
        $index = array_search($type, array_column($this->storage, 'type'));

        $freeSpace = $this->getFreeSpaceProduct($type);

        if($freeSpace === 0){
            return;
        } else if ($freeSpace < $count){
            $this->storage[$index]['storage'] = $this->storage[$index]['max'];
            return;
        }

        $this->storage[$index]['storage'] += $count;
    }

    public function getFreeSpaceProduct(string $type){
        $index = array_search($type, array_column($this->storage, 'type'));

        return $this->storage[$index]['max'] - $this->storage[$index]['storage'];
    }

    public function howMuchProduct(string $type){
        $index = array_search($type, array_column($this->storage, 'type'));

        return $this->storage[$index]['storage'];
    }
    
    public function howMuchProducts() {
        return $this->storage;
    }
}

class Farm {
    private $name;
    private $storage;
    private $animals = [];

    public function __construct(string $name, Storage $storage){
        $this->name = $name;
        $this->storage = $storage;
    }

    public function returnProduct(string $type){
        return $this->storage->howMuchProduct($type);
    }

    public function returnProducts(){
        return $this->storage->howMuchProducts();
    }

    public function addStorageType(string $type, int $maxCount){
        $this->storage->addTypeProduct($type, $maxCount);
    }

    public function addAnimal(Animal $animal){
        $this->animals[] = $animal;
    }

    public function collectProduct()
    {
        foreach ($this->animals as $animal)
        {
            $count = $animal->getProduct();
            $this->storage->addProduct($animal->type, $count);
        }
    }
}

/* Create Storage And Farm */

$barn = new Barn(); // Storage Class
$myFarm = new Farm('Farm №1', $barn); // Farm Class

/*---------------------*/


/* Add Storage Types */

    $myFarm->addStorageType('eggs', 100); // Type: eggs      | MaxStorage: 100
    $myFarm->addStorageType('milk', 250); // Type: milk      | MaxStorage: 250

/*---------------------*/


/* Add Animals To Farm */

for($i=0;$i<20;$i++) { $myFarm->addAnimal(new AnyAnimal(0, 1, 'eggs')); }; // AnimalTypeProduct: eggs    | 0-1
for($i=0;$i<10;$i++) { $myFarm->addAnimal(new AnyAnimal(8, 12, 'milk')); }; // AnimalTypeProduct: milk   | 8-12

/*---------------------*/ 

$myFarm->collectProduct(); // Collect All Animals Product

foreach($myFarm->returnProducts() as $product)
{
    echo 'Type: '.$product['type'].' | In Storage: '.$product['storage'].' | Max Storage: '.$product['max'].'<br>';
}