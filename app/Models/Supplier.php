<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'supplier_name',
        'contact_person',
        'address',
        'email',
        'phone_number',
    ];

    // Relationship: A supplier can have many purchases
    public function purchases(): HasMany
    {
        return $this->hasMany(purchase::class);
    }
}
