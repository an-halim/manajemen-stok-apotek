<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseItem;
use App\Models\User; // Assuming you have a User model
use App\Notifications\ProductExpiringSoonNotification; // Notification class
use Carbon\Carbon;

class CheckExpiredProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:check-expired';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Check for expired products and products expiring soon, and notify users.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get today's date
        $today = now()->toDateString();

        // Check for products expiring within the next 30 days
        $sevenDaysFromNow = Carbon::now()->addDays(90)->toDateString();
        $expiringSoonProducts = PurchaseItem::where('expiry_date', '>=', $today)
            ->where('expiry_date', '<=', $sevenDaysFromNow)
            ->get();

        $this->info('Products expiring soon:');
        foreach ($expiringSoonProducts as $product) {
            $this->info($product->product->name . ' - Expiry Date: ' . $product->expiry_date);
        }
        // Notify all users about products expiring soon
        $users = User::all();
        foreach ($expiringSoonProducts as $product) {
            foreach ($users as $user) {
                $user->notify(new ProductExpiringSoonNotification($product));
            }
        }

        $this->info('Users notified about products expiring soon.');
        return 0;
    }
}
