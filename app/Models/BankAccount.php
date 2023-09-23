<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'bank_account';

    protected $fillable = ["title", "description", "balance", "userAccountID"];

    public $timestamps = false;
}
