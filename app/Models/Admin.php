<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;//
use Illuminate\Foundation\Auth\User as Authenticatable;//
use Illuminate\Notifications\Notifiable;//
use Laravel\Sanctum\HasApiTokens;//

//class Admin extends Model
class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable=[
        'name',
        'email',
        'password',
        'role'
        ];


        protected $hidden = [
            'password',
            'remember_token',
        ];
    
        public function isManager()
        {
            return $this->role === 'manager';
        }
    
        public function isSeller()
        {
            return $this->role === 'seller';
        }
}
