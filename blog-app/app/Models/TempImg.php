<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempImg extends Model
{
    use HasFactory;

    protected $table = 'temp_images'; // Ensure it matches the migration
    protected $fillable = ['name'];
}
