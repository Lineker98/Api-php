<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero_conta',
        'valor',
        'moeda',
        'tipo_transacao',
        'data_transacao',
    ];

}
