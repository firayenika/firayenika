<?php

// app/Models/Data.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $fillable = ['weight1', 'weight2', 'percent_weight1', 'percent_weight2'];
}
