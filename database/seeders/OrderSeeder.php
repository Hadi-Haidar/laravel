<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing orders and order items to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        OrderItem::truncate();
        Order::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Get all users and products
        $users = User::all();
        $products = Product::where('status', 'available')->get();
        
        // Check if we have users and products
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }
        
        if ($products->isEmpty()) {
            $this->command->error('No available products found. Please run ProductSeeder first.');
            return;
        }
        
        // Statuses for orders
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        // Generate 50 orders
        for ($i = 0; $i < 50; $i++) {
            // Select a random user
            $user = $users->random();
            
            // Create a new order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0, // Will calculate based on items
                'status' => $statuses[array_rand($statuses)],
                'shipping_address' => fake()->address(),
                'shipping_phone' => fake()->phoneNumber(),
                'notes' => rand(0, 1) ? fake()->sentence() : null,
                'created_at' => fake()->dateTimeBetween('-3 months', 'now')
            ]);
            
            // Add 1-5 products to this order
            $orderTotal = 0;
            $numProducts = rand(1, 5);
            
            // Get random products without repetition
            $orderProducts = $products->random(min($numProducts, $products->count()));
            
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $totalPrice = $price * $quantity;
                $orderTotal += $totalPrice;
                
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_price' => $totalPrice
                ]);
            }
            
            // Update the order with the total amount
            $order->update(['total_amount' => $orderTotal]);
        }
        
        $this->command->info('50 sample orders created successfully!');
    }
} 