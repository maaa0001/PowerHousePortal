<?php 

// Simple OOP - Property listings
class Property {
    public function __construct(
        public string $title,
        public string $type,   // "apartment", "house", "studio" whatever
        public float  $price,
        public int    $bedrooms
    ) {}

    public function getSummary(): string {
        return "$this->bedrooms-bed $this->type: $this->title — $$this->price/mo";
    }
}

$apt  = new Property("Sunset Apartment", "apartment", 1200, 2);
$studio = new Property("Downtown Studio", "studio", 850, 1);

echo $apt->getSummary();    // 2-bed apartment: Sunset Apartment — $1200/mo
echo $studio->getSummary(); // 1-bed studio: Downtown Studio — $850/mo

// Simple OOP - different behaviours 

class Listing {
    public function __construct(
        public string $title,
        public float  $price
    ) {}

    public function getLabel(): string {
        return $this->title;
    }
}

class RentalListing extends Listing {
    public function getLabel(): string {
        return "$this->title — $$this->price/month (Rent)";
    }
}

class SaleListing extends Listing {
    public function getLabel(): string {
        return "$this->title — $$this->price (For Sale)";
    }
}

$listings = [
    new RentalListing("Cozy Studio, Nørrebro", 950),
    new SaleListing("Family Home, Frederiksberg", 425000),
    new RentalListing("Modern 2-bed, Vesterbro", 1400),
];

foreach ($listings as $listing) {
    echo $listing->getLabel() . "\n";
}

// Output: 
// Cozy Studio, Nørrebro — $950/month (Rent)
// Family Home, Frederiksberg — $425000 (For Sale)
// Modern 2-bed, Vesterbro — $1400/month (Rent)

class User {
    private $name;
    private $email;
    private $password;

    public function __construct($name, $email, $password) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getName(){
        return $this->name;
    }

    public function getEmail(){
        return $this->email;
    }

    public function checkPassword($password){
        return $this->password === $password;
    }
}

$user = new User("Emil", "emil@example.com", "password123");

echo $user->getName();
echo $user->getEmail();

if ($user->checkPassword("password123")) {
    echo "Password is correct";
} else {
    echo "Wrong password";
}