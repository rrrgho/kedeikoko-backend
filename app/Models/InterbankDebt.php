<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterbankDebt extends Model
{
    use HasFactory;
    protected $table = "interbank_debt";
    protected $guarded = [];
}
