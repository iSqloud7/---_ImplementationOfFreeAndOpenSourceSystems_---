<?php

enum CoffeeType: string {
    case ESPRESSO = 'espresso';
    case LATTE = 'latte';
    case AMERICANO = 'americano';
}

enum TeaType: string {
    case BLACK = 'black';
    case GREEN = 'green';
}

trait Discountable {
    private float $discountApplied = 0;

    public function applyDiscount(float $amount): void {
        // Ensure we don’t go below zero
        $amount = min($amount, $this->price);
        $this->price -= $amount;
        $this->discountApplied = $amount;
    }

    public function getDiscount(): float {
        return $this->discountApplied;
    }
}

abstract class Beverage {
    protected string $name;
    protected float $price;

    public function __construct(string $name, float $price) {
        $this->name = $name;
        $this->price = $price;
    }

    abstract function calculateTotalPrice(int $quantity): float;

    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
}

class Coffee extends Beverage {
    use Discountable;
    private CoffeeType $coffee_type;

    public function __construct(string $name, float $price, CoffeeType $coffee_type) {
        parent::__construct($name, $price);
        $this->coffee_type = $coffee_type;
    }

    public function calculateTotalPrice(int $quantity): float {
        return $this->price * $quantity;
    }

    public function getType(): string { return $this->coffee_type->value; }
}

class Tea extends Beverage {
    use Discountable;
    private TeaType $tea_type;

    public function __construct(string $name, float $price, TeaType $tea_type) {
        parent::__construct($name, $price);
        $this->tea_type = $tea_type;
    }

    public function calculateTotalPrice(int $quantity): float {
        return $this->price * $quantity;
    }

    public function getType(): string { return $this->tea_type->value; }
}

class Order {
    private array $items = [];

    public function addItem(Beverage $beverage, int $quantity): void {
        $this->items[] = ['beverage' => $beverage, 'quantity' => $quantity];
    }

    public function calculateOrderTotal(): float {
        $total_sum = 0;
        foreach ($this->items as $item) {
            $total_sum += $item['beverage']->calculateTotalPrice($item['quantity']);
        }
        return $total_sum;
    }

    public function displayOrderCards(): void {
        echo "<div style='display:flex; flex-wrap:wrap; gap:20px; margin-top:20px;'>";

        foreach ($this->items as $item) {
            $bev = $item['beverage'];
            $qty = $item['quantity'];
            $total = $bev->calculateTotalPrice($qty);
            $price = number_format($bev->getPrice(), 2);
            $discount = method_exists($bev, 'getDiscount') ? $bev->getDiscount() : 0;
            $original = number_format($bev->getPrice() + $discount, 2);
            $color = ($bev instanceof Coffee) ? "#e9f6ff" : "#e6ffec";
            $type = ($bev instanceof Coffee) ? ucfirst($bev->getType()) . " Coffee" : ucfirst($bev->getType()) . " Tea";

            echo "
            <div style='flex:1 1 240px; background:$color; border:1px solid #ccc; border-radius:10px;
                        padding:15px; box-shadow:2px 2px 8px rgba(0,0,0,0.1); transition:transform 0.2s;'>
                <h3 style='color:#007bff; margin:0 0 10px 0;'>{$bev->getName()}</h3>
                <p style='margin:4px 0;'><strong>Type:</strong> $type</p>
                <p style='margin:4px 0;'><strong>Original Price:</strong> <del>{$original} MKD</del></p>
                <p style='margin:4px 0;'><strong>Discount:</strong> <span style='color:red;'>-{$discount} MKD</span></p>
                <p style='margin:4px 0;'><strong>New Price:</strong> <span style='color:green;'>{$price} MKD</span></p>
                <p style='margin:4px 0;'><strong>Quantity:</strong> $qty</p>
                <p style='margin:4px 0;'><strong>Total:</strong> <span style='color:#28a745; font-weight:bold;'>{$total} MKD</span></p>
            </div>";
        }

        echo "</div>";
    }
}

// ---------- TESTING ----------

$coffee = new Coffee("Espresso", 140.0, CoffeeType::ESPRESSO);
$tea = new Tea("Green Tea", 100.0, TeaType::GREEN);

// Apply discount
$coffee->applyDiscount(20.0);  // Espresso now 120 MKD

$order = new Order();
$order->addItem($coffee, 2);
$order->addItem($tea, 1);

echo "<h2 style='color:darkblue;'>☕ Coffee & Tea Order Summary</h2>";
$order->displayOrderCards();

echo "<h2 style='color:darkred; margin-top:20px;'>💰 Total order amount: <span style='color:green;'>" . $order->calculateOrderTotal() . " MKD</span></h2>";

?>
