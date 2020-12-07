<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransactionBetween extends Model
{
    use HasFactory;
    protected $table = "transaction_bank_inbetween";
    protected $guarded = [];
    protected $appends = ['destination','from'];

    // Relation Bank
    public function bank_destination_appends(){
        return $this->belongsTo(BankAccount::class, 'joinbank_id', 'id');
    }
    public function getDestinationAttribute(){
        return $this->bank_destination_appends->holder_name ?? '-';
    }
    public function bank_from_appends(){
        return $this->belongsTo(BankAccount::class, 'thisbank_id', 'id');
    }
    public function getFromAttribute(){
        return $this->bank_from_appends->holder_name ?? '-';
    }
}
